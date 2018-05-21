<?php

namespace JEL\Http\Controllers;

use Illuminate\Http\Request;

use DateTimeZone;
use JEL\Agents\Collections\Agentsv2Collection;
use JEL\Agents\Repositories\Agentsv2Repo;
use JEL\Agents\Repositories\CountriesRepo;
use JEL\Agents\Repositories\GroupsRepo;
use JEL\Core\Enums\StatusReponse;
use JEL\Core\Enums\Utils;
use JEL\Core\Repositories\CoreRepo;
use JEL\Paginator\PaginatorDataTable;
use JEL\Users\Collections\UsersCollection;
use JEL\Users\Managers\UserManager;
use JEL\Users\Repositories\ProfileRepo;
use JEL\Users\Repositories\UsersRepo;
use Juegaenlinea\Audits\Audits;
use Juegaenlinea\Audits\Enums\AuditModificationTypes;
use Dotworkers\Configurations\Configurations;
use Juegaenlinea\Security\Enums\Permissions;
use Juegaenlinea\Security\Security;
use Juegaenlinea\Store\Store;
use Juegaenlinea\Walletwrapper\Enums\Actions;
use Juegaenlinea\Walletwrapper\Enums\Providers;
use Juegaenlinea\Walletwrapper\Enums\TransactionType;
use Juegaenlinea\Walletwrapper\Wallet;
use Carbon\Carbon;
use PhpSpec\Exception\Exception;
use Yajra\Datatables\Datatables;


/**
 * Class AgentsController
 *
 * Manage the agents requests
 *
 * @package JEL\Http\Controllers
 * @author  Carol Mirabal
 */
class Agentsv2Controller extends Controller
{
    /**
     * Agents collection instance
     *
     * @var AgentsCollection
     */
    private $agentsv2Collection;

    /**
     * Agents repo instance
     *
     * @var AgentsRepo
     */
    private $agentsv2Repo;

    /**
     * Countries repo instance
     *
     * @var CountriesRepo
     */
    private $countriesRepo;

    /**
     * Groups repo instance
     *
     * @var GroupsRepo
     */
    private $groupsRepo;

    /**
     * Users repository
     *
     * @var UsersRepo
     */
    private $usersRepo;

    /**
     * Core repository
     *
     * @var CoreRepo
     */
    private $coreRepo;

    /**
     * Profile Repository
     *
     * @var
     */
    private $profileRepo;

    /**
     * AgentsController constructor.
     *
     * @param AgentsCollection $agentsCollection
     * @param AgentsRepo $agentsRepo
     * @param GroupsRepo $groupsRepo
     * @param CountriesRepo $countriesRepo
     * @param UsersRepo $usersRepo
     * @param CoreRepo $coreRepo
     */
    public function __construct(
        Agentsv2Collection $agentsv2Collection,
        Agentsv2Repo $agentsv2Repo,
        GroupsRepo $groupsRepo,
        CountriesRepo $countriesRepo,
        UsersRepo $usersRepo,
        CoreRepo $coreRepo,
        ProfileRepo $profileRepo
    )
    {
        $this->agentsv2Collection = $agentsv2Collection;
        $this->agentsv2Repo = $agentsv2Repo;
        $this->countriesRepo = $countriesRepo;
        $this->groupsRepo = $groupsRepo;
        $this->usersRepo = $usersRepo;
        $this->coreRepo = $coreRepo;
        $this->profileRepo = $profileRepo;

    }

    /**
     * Search owner
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchOwner(Request $request)
    {
        try {
            $owner = \Auth::user()->id;
            $agent = $request->agent;
            $res = $this->agentsv2Repo->searchOwner($owner, $agent);
            $response = ['agent' => $res];
        } catch (\Exception $ex) {
            \Log::error('Agentsv2Controller.searchOwner.catch', ['exception' => $ex]);
            $response = [
                'status' => 'FAILED',
                'title' => __('¡Error!'),
                'message' => __('Ocurrió un error buscando owner')
            ];
        }
        return response()->json($response);
    }

    /**
     * adjustments credit
     *
     * @param Request $request
     * @return string
     */
    public function adjustmentsCredit(Request $request)
    {
        try {
            switch ($request->type) {
                case 'agent': {
                    $ownerbalance = $this->agentsv2Repo->findBalanceAgent($request->dep);

                    if ($ownerbalance->balance >= $request->amount) {

                        $balanceowner = $ownerbalance->balance - $request->amount;
                        $this->agentsv2Repo->updatedBalanceOwner($request->dep, $balanceowner);

                        $dataowner = [
                            'agent' => $request->dep,
                            'description' => 'Asignación de balance agente: ' . $request->user,
                            'transactiontype' => TransactionType::$debit,
                            'amount' => $request->amount,
                            'balance' => $balanceowner,
                        ];
                        $this->agentsv2Repo->registerTransaction($dataowner);

                        $agentbalance = $this->agentsv2Repo->findBalanceAgent($request->user);
                        $balanceagent = $agentbalance->balance + $request->amount;

                        $this->agentsv2Repo->updatedBalanceOwner($request->user, $balanceagent);
                        $dataagent = [
                            'agent' => $request->user,
                            'description' => 'Asignación de balance',
                            'transactiontype' => TransactionType::$credit,
                            'amount' => $request->amount,
                            'balance' => $balanceagent,
                        ];
                        $this->agentsv2Repo->registerTransaction($dataagent);

                        return bodyResponseRequest(StatusReponse::$customsuccess, [], 'Se ha Acreditado de forma correcta');
                    } else {
                        return bodyResponseRequest(StatusReponse::$customfailed, [], 'No tiene balance para asignar');
                    }
                }
                case 'player': {
                    $ownerbalance = $this->agentsv2Repo->findBalanceAgent($request->dep);
                    if ($ownerbalance->balance >= $request->amount) {
                        $wallet_transaction = json_decode(Wallet::creditTransactions(
                            $request->user,
                            $request->amount,
                            Providers::$agents,
                            Actions::$generic,
                            "Asignacion de balance"
                        ));
                        $walletstatus = $wallet_transaction->status;

                        if ($walletstatus == 'OK') {

                            $balanceowner = $ownerbalance->balance - $request->amount;

                            $this->agentsv2Repo->updatedBalanceOwner($request->dep, $balanceowner);
                            $dataowner = [
                                'agent' => $request->dep,
                                'description' => 'Asignación de balance usuario: ' . $request->user,
                                'transactiontype' => TransactionType::$debit,
                                'amount' => $request->amount,
                                'balance' => $balanceowner,
                            ];
                            $this->agentsv2Repo->registerTransaction($dataowner);

                            $dataagent = [
                                'agent' => $request->user,
                                'description' => 'Asignación de balance',
                                'transactiontype' => TransactionType::$credit,
                                'amount' => $request->amount,
                                'balance' => $wallet_transaction->data->wallet->balance,
                            ];

                            $this->agentsv2Repo->registerTransaction($dataagent);
                        }
                        return bodyResponseRequest(StatusReponse::$customsuccess, [], 'Se ha Acreditado de forma correcta');
                    } else {
                        return bodyResponseRequest(StatusReponse::$customfailed, [], 'No tiene balance para asignar');
                    }
                }
            }
        } catch (\Exception $ex) {
            \Log::error('Agentsv2Controller.adjustmentsCredit.catch', ['exception' => $ex]);
            $response = [
                'status' => 'FAILED',
                'title' => __('¡Error!'),
                'message' => __('Ocurrió un error buscando owner')
            ];
        }
    }

    /**
     *Debit Adjustments
     *
     * @param Request $request
     * @return string
     */
    public function adjustmentsDebit(Request $request)
    {
        switch ($request->type) {
            case 'agent': {
                $agentbalance = $this->agentsv2Repo->findBalanceAgent($request->user);

                if ($agentbalance->balance >= $request->amount) {

                    $ownerbalance = $this->agentsv2Repo->findBalanceAgent($request->dep);
                    $balanceowner = $ownerbalance->balance + $request->amount;
                    $this->agentsv2Repo->updatedBalanceOwner($request->dep, $balanceowner);
                    $dataowner = [
                        'agent' => $request->dep,
                        'description' => 'Débito de balance agente: ' . $request->user,
                        'transactiontype' => TransactionType::$credit,
                        'amount' => $request->amount,
                        'balance' => $balanceowner,
                    ];
                    $this->agentsv2Repo->registerTransaction($dataowner);

                    $balanceagent = $agentbalance->balance - $request->amount;
                    $this->agentsv2Repo->updatedBalanceOwner($request->user, $balanceagent);
                    $dataagent = [
                        'agent' => $request->user,
                        'description' => 'Débito de balance',
                        'transactiontype' => TransactionType::$debit,
                        'amount' => $request->amount,
                        'balance' => $balanceagent,
                    ];
                    $this->agentsv2Repo->registerTransaction($dataagent);

                    return bodyResponseRequest(StatusReponse::$customsuccess, [], 'Se ha Acreditado de forma correcta');
                } else {
                    return bodyResponseRequest(StatusReponse::$customfailed, [], 'Esta intentado debitar un monto mayor al balance del agente');
                }
            }

            case 'player': {
                $ownerbalance = $this->agentsv2Repo->findBalanceAgent($request->dep);

                $wallet_transaction = json_decode(Wallet::DebitTransactions(
                    $request->user,
                    $request->amount,
                    Providers::$agents,
                    Actions::$generic,
                    "Débito de balance"
                ));
                $walletstatus = $wallet_transaction->status;

                if ($walletstatus == 'OK') {

                    $balanceowner = $ownerbalance->balance + $request->amount;

                    $this->agentsv2Repo->updatedBalanceOwner($request->dep, $balanceowner);
                    $dataowner = [
                        'agent' => $request->dep,
                        'description' => 'Débito de balance usuario: ' . $request->user,
                        'transactiontype' => TransactionType::$credit,
                        'amount' => $request->amount,
                        'balance' => $balanceowner,
                    ];
                    $this->agentsv2Repo->registerTransaction($dataowner);

                    $datauser = [
                        'agent' => $request->user,
                        'description' => 'Débito de balance',
                        'transactiontype' => TransactionType::$debit,
                        'amount' => $request->amount,
                        'balance' => $wallet_transaction->data->wallet->balance,
                    ];

                    $this->agentsv2Repo->registerTransaction($datauser);
                }
                return bodyResponseRequest(StatusReponse::$customsuccess, [], 'Se ha Acreditado de forma correcta');
            }
        }
    }

    /**
     * Panel agents
     *
     * @param Request $request
     * @return string
     */
    public function agentPanelAgents(Request $request)
    {
        try {
            if (empty($request->startDate)) {
                return PaginatorDataTable::firstLoad();
            } else {
                $agents = $this->agentsv2Repo->findAgentsByOwner($request->id);
                $result = $this->agentsv2Collection->tableAgents($agents);
                $response = Datatables::of($result)->make();
                return $response;
            }
        } catch
        (\Exception $ex) {
            \Log::error('Agentsv2Controller.agentPanelPlayers.catch', ['exception' => $ex]);
            $response = [
                'status' => 'FAILED',
                'title' => __('¡Error!'),
                'message' => __('Ocurrió un error buscando owner')
            ];
        }
    }

    /**
     * @param Request $request
     * @return string
     */
    public function agentPanelPlayers(Request $request)
    {
        try {
            if (empty($request->startDate)) {
                return PaginatorDataTable::firstLoad();
            } else {
                $players = $this->agentsv2Repo->findPlayersByAgent($request->id);
                if (count($players) > 0) {
                    $result = $this->agentsv2Collection->tablePlayers($players);
                    $response = Datatables::of($result)->make();
                    return $response;
                } else {
                    return PaginatorDataTable::emptyLoad($request);
                }
            }
        } catch
        (\Exception $ex) {
            \Log::error('Agentsv2Controller.agentPanelPlayers.catch', ['exception' => $ex]);
            $response = [
                'status' => 'FAILED',
                'title' => __('¡Error!'),
                'message' => __('Ocurrió un error buscando owner')
            ];
        }
    }

    /**
     * Panel de agentes
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function agentPanelDashboard(Request $request)
    {
        try {
            switch ($request->type) {
                case 'agent': {
                    $agent = $this->agentsv2Repo->findAgentById($request->id);
                    $providers = $this->coreRepo->getAllGamesProvidersByWl(env('WHITELABEL'));
                    $credit = $this->agentsv2Repo->lastTransaction($request->id, TransactionType::$credit, \Session::get('timezone'), $request->startDate, $request->endDate);
                    $debit = $this->agentsv2Repo->lastTransaction($request->id, TransactionType::$debit, \Session::get('timezone'), $request->startDate, $request->endDate);
                    $result = $this->agentsv2Collection->profitByAgents($providers, $agent, $request->startDate, $request->endDate, $credit, $debit);

                    $view = view('agentsv2.layout.dashboardagent', $result)->render();

                    return response()->json($view);
                }
                case 'player': {
                    $player = $this->agentsv2Repo->findPlayersById($request->id);
                    $providers = $this->coreRepo->getAllGamesProvidersByWl(env('WHITELABEL'));
                    $credit = $this->agentsv2Repo->lastTransaction($request->id, TransactionType::$credit, \Session::get('timezone'), $request->startDate, $request->endDate);
                    $debit = $this->agentsv2Repo->lastTransaction($request->id, TransactionType::$debit, \Session::get('timezone'), $request->startDate, $request->endDate);
                    $balance = json_decode(\Wallet::getWallet($request->id))->data->wallet->balance;
                    $result = $this->agentsv2Collection->profitByPlayer(
                        $providers,
                        $player,
                        $request->startDate,
                        $request->endDate,
                        $credit,
                        $debit,
                        $balance,
                        $request->id);

                    $view = view('agentsv2.layout.dashboardplayer', $result)->render();
                    return response()->json($view);
                }
            }
        } catch (Exception $ex) {
            \Log::error('Agentsv2Controller.agentPanelDashboard.catch', ['exception' => $ex]);
            $response = [
                'status' => 'FAILED',
                'title' => __('¡Error!'),
                'message' => __('Ocurrió un error buscando owner')
            ];
            return $response;
        }
    }

    /**
     * Create agent
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createAgent(Request $request)
    {
        try {
            $owner = \Auth::user()->id;
            $description = $request->description;
            $username = strtolower($request->username);
            $password = $request->password;
            $email = strtolower($request->email);
            $key = (string)\Uuid::generate(4);
            $whiteLabel = Configurations::findWhiteLabelByName(env('WHITELABEL'))->id;
            $currency = \Session::get('currency')['iso'];
            $timezone = $request->timezone;
            $country = $request->country;
            $dealvalue = $request->dealvalue;
            $dealtype = $request->dealtype;
            $balance = $request->balance;
            $master = $request->master;

            $ownerbalance = $this->agentsv2Repo->findBalanceAgent($owner);

            if ($ownerbalance->balance >= $balance) {

                $data = ['username' => $username, 'email' => $email, 'password' => $password, 'key' => $key, 'whitelabel' => $whiteLabel, 'currency' => $currency];

                $system_users = json_decode(Wallet::getSystemUsers())->data->users;

                if (!$this->usersRepo->userExists($whiteLabel, $username) && !$this->usersRepo->emailExists($whiteLabel, $email) && !Utils::in_array_key($system_users, 'username', $username)) {

                    $user = $this->usersRepo->register($username, $password, $email, $key, $whiteLabel, $currency);

                    // Manager instance
                    $manager = new UserManager($user, $data);

                    // Checks that the country is not empty
                    if (!isset($country) || empty($country)) {
                        return bodyResponseRequest(StatusReponse::$customfailed, [], 'Debe seleccionar un país');
                    }

                    // Checks that the timezone is not empty
                    if (!isset($timezone) || empty($timezone)) {
                        return bodyResponseRequest(StatusReponse::$customfailed, [], 'El campo Zona Horaria es obligatorio.');
                    }

                    // If validation pass
                    if ($manager->save()) {

                        $profile = $this->profileRepo->create($user->id, $country, null, null, $timezone, null, null);

                        $this->usersRepo->insertCurrency($user->id, $currency);

                        $dataagent = [
                            'owner' => $owner,
                            'user' => $user->id,
                            'description' => $description,
                            'master' => $master,
                            'balance' => $balance,
                            'dealvalue' => $dealvalue,
                            'dealtype' => $dealtype
                        ];

                        $newbalance = $ownerbalance->balance - $balance;

                        $this->agentsv2Repo->createAgent($dataagent);

                        $this->agentsv2Repo->updatedBalanceOwner($owner, $newbalance);

                        Security::addRole($user->id, 19);

                        Wallet::create($user->id, $user->username, $key, $user->whitelabel, true, $currency);

                        $dataownertransaction = [
                            'agent' => $owner,
                            'description' => 'Balance Inicial',
                            'transactiontype' => TransactionType::$debit,
                            'amount' => $balance,
                            'balance' => $newbalance,
                            'currency' => $currency
                        ];
                        $this->agentsv2Repo->registerTransaction($dataownertransaction);

                        $datatransaction = [
                            'agent' => $user->id,
                            'description' => 'Balance Inicial',
                            'transactiontype' => TransactionType::$credit,
                            'amount' => $balance,
                            'balance' => $balance,
                            'currency' => $currency
                        ];
                        $this->agentsv2Repo->registerTransaction($datatransaction);

                        return bodyResponseRequest(StatusReponse::$customsuccess, [], 'Agente Creado');
                    } else {
                        \Log::error('Agentsv2Controller.createAgent', [$manager->getError()]);

                        return bodyResponseRequest(StatusReponse::$failed, $manager->getError());
                    }
                } else {
                    return bodyResponseRequest(StatusReponse::$customfailed, [], 'Usuario duplicado en el sistema');
                }
            } else {
                return bodyResponseRequest(StatusReponse::$customfailed, [], 'El agente del que depende no tiene suficiente balance para asignarle');
            }
            return bodyResponseRequest(StatusReponse::$success, []);
        } catch (\Exception $e) {
            return bodyResponseRequest(StatusReponse::$error, $e, [], 'AgentsController.createAgent.catch');
        }
    }

    /**
     * Create player
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createPlayer(Request $request)
    {
        try {
            $name = $request->name;
            $lastname = $request->lastname;
            $owner = \Auth::user()->id;
            $username = strtolower($request->username);
            $password = $request->password;
            $email = strtolower($request->email);
            $key = (string)\Uuid::generate(4);
            $whiteLabel = Configurations::findWhiteLabelByName(env('WHITELABEL'))->id;
            $currency = \Session::get('currency')['iso'];
            $timezone = $request->timezone;
            $country = $request->country;
            $balance = $request->balance;

            $ownerbalance = $this->agentsv2Repo->findBalanceAgent($owner);

            if ($ownerbalance->balance >= $balance) {

                $data = ['username' => $username, 'email' => $email, 'password' => $password, 'key' => $key, 'whitelabel' => $whiteLabel, 'currency' => $currency];

                $system_users = json_decode(Wallet::getSystemUsers())->data->users;

                if (!$this->usersRepo->userExists($whiteLabel, $username) && !$this->usersRepo->emailExists($whiteLabel, $email) && !Utils::in_array_key($system_users, 'username', $username)) {

                    $user = $this->usersRepo->register($username, $password, $email, $key, $whiteLabel, $currency);

                    // Manager instance
                    $manager = new UserManager($user, $data);

                    // Checks that the country is not empty
                    if (!isset($country) || empty($country)) {
                        return bodyResponseRequest(StatusReponse::$customfailed, [], 'Debe seleccionar un país');
                    }

                    // Checks that the timezone is not empty
                    if (!isset($timezone) || empty($timezone)) {
                        return bodyResponseRequest(StatusReponse::$customfailed, [], 'El campo Zona Horaria es obligatorio.');
                    }

                    // If validation pass
                    if ($manager->save()) {

                        $profile = $this->profileRepo->createPlayer($user->id, $country, null, null, $timezone, null, null, $name, $lastname);

                        $newbalance = $ownerbalance->balance - $balance;

                        $this->agentsv2Repo->createPlayer(\Auth::user()->id, $user->id);

                        $this->agentsv2Repo->updatedBalanceOwner($owner, $newbalance);

                        Security::addRole($user->id);

                        Wallet::create($user->id, $user->username, $key, $user->whitelabel, true, $currency);

                        Wallet::create($user->id, $user->username, $key, $user->whitelabel, false, 'PTS');

                        // Creating initial historic points balance
                        Store::createBalance($user->id);

                        $wallet_transaction = json_decode(Wallet::creditTransactions(
                            $user->id,
                            $balance,
                            Providers::$agents,
                            Actions::$generic,
                            "Asignacion de balance inicial a usuario"
                        ));

                        $dataownertransaction = [
                            'agent' => $owner,
                            'description' => 'Balance Inicial',
                            'transactiontype' => TransactionType::$debit,
                            'amount' => $balance,
                            'balance' => $newbalance,
                            'currency' => $currency
                        ];
                        $this->agentsv2Repo->registerTransaction($dataownertransaction);

                        $datatransaction = [
                            'agent' => $user->id,
                            'description' => 'Balance Inicial',
                            'transactiontype' => TransactionType::$credit,
                            'amount' => $balance,
                            'balance' => $balance,
                            'currency' => $currency
                        ];
                        $this->agentsv2Repo->registerTransaction($datatransaction);

                        return bodyResponseRequest(StatusReponse::$customsuccess, [], 'Usuario Creado');
                    } else {
                        \Log::error('Agentsv2Controller.createAgent', [$manager->getError()]);

                        return bodyResponseRequest(StatusReponse::$failed, $manager->getError());
                    }
                } else {
                    return bodyResponseRequest(StatusReponse::$customfailed, [], 'Usuario duplicado en el sistema');
                }
            } else {
                return bodyResponseRequest(StatusReponse::$customfailed, [], 'El agente del que depende no tiene suficiente balance para asignarle');
            }
            return bodyResponseRequest(StatusReponse::$success, []);
        } catch (\Exception $e) {
            return bodyResponseRequest(StatusReponse::$error, $e, [], 'AgentsController.createPlayer.catch');
        }
    }

    /**
     * Create agent
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function convertToAgent(Request $request)
    {
        try {
            $owner = $request->owner;
            $description = $request->description;
            $user = $request->user;
            $currency = \Session::get('currency')['iso'];
            $dealvalue = $request->dealvalue;
            $dealtype = $request->dealtype;
            $balance = $request->balance;
            $master = $request->master;

            $ownerbalance = $this->agentsv2Repo->findBalanceAgent($owner);

            if ($ownerbalance->balance >= $balance) {

                $dataagent = [
                    'owner' => $owner,
                    'user' => $user,
                    'description' => $description,
                    'master' => $master,
                    'balance' => $balance,
                    'dealvalue' => $dealvalue,
                    'dealtype' => $dealtype
                ];
                Security::addRole($user->id, 19);

                $newbalance = $ownerbalance->balance - $balance;

                $this->agentsv2Repo->createAgent($dataagent);

                $this->agentsv2Repo->updatedBalanceOwner($owner, $newbalance);

                $datatransaction = [
                    'agent' => $user->id,
                    'description' => 'Balance Inicial',
                    'transactiontype' => TransactionType::$credit,
                    'amount' => $balance,
                    'balance' => $balance,
                    'currency' => $currency
                ];

                return bodyResponseRequest(StatusReponse::$customsuccess, [], 'Agente Creado');
            } else {
                return bodyResponseRequest(StatusReponse::$customfailed, [], 'El agente del que depende no tiene suficiente balance para asignarle');
            }
            return bodyResponseRequest(StatusReponse::$success, []);
        } catch (\Exception $e) {
            return bodyResponseRequest(StatusReponse::$error, $e, [], 'AgentsController.createAgent.catch');
        }
    }

    /**
     * Response data by financial state
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDataFinancialStatePlayers(Request $request)
    {
        try {
            $username = $request->input('agent');
            if (!empty($username)) {
                $getUsers = $this->agentsv2Repo->findPlayersByAgent($username);
            } else {
                $getUsers = $this->agentsv2Repo->findPlayersByAgent(\Auth::user()->id);
            }

            if (count($getUsers) > 0) {

                $providers = $this->coreRepo->getAllGamesProvidersByWl(env('WHITELABEL'));
                $users = [];
                $providersId = [];

                foreach ($getUsers as $userId) {
                    $users[] = $userId->user;
                }
                foreach ($providers as $provider) {
                    $providersId[] = $provider->id;
                }

                $dt = Carbon::now();
                $week = $dt->weekOfYear;
                $today = $dt->format('Y-m-d');

                $financialState2 = json_decode(json_encode(Wallet::financialState($users, $providersId)), TRUE);
                $financialStateWeek2 = json_decode(json_encode(Wallet::financialStateWeek($users, $providersId, $week)), TRUE);
                $financialStateDay2 = json_decode(json_encode(Wallet::financialStateDay($users, $providersId, $today)), TRUE);

                $financial = json_decode($financialState2['body']);
                $finstateweek = json_decode($financialStateWeek2['body']);
                $finstateday = json_decode($financialStateDay2['body']);

                $response = [
                    'financialState' => $this->agentsv2Collection->formatFinancialState($providers, $financial),
                    'financialStateWeek' => $this->agentsv2Collection->formatFinancialState($providers, $finstateweek),
                    'financialStateDay' => $this->agentsv2Collection->formatFinancialState($providers, $finstateday)
                ];

                return bodyResponseRequest(StatusReponse::$success, $response);

            } else {
                return bodyResponseRequest(StatusReponse::$customfailed, [], __('Agente no encontrado, para cargar los estados financieros'));
            }
        } catch (\Exception $ex) {
            return bodyResponseRequest(StatusReponse::$error, $ex, [], 'AgentsController.getDataFinancialStateUsers.catch');
        }
    }

    /**
     * View financial state by admin
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getFinancialStatePlayers()
    {
        $data['agents'] = $this->agentsv2Repo->findAgentsByOwner(\Auth::user()->id);
        return view('agentsv2.reports.financial-players', $data);
    }

    /**
     * Panel de agentes
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function agentPanelView()
    {
        $dependency = $this->agentsv2Repo->findDependency(\Auth::user()->id);
        $agent = $this->agentsv2Repo->findAgentById(\Auth::user()->id);
        $tree = $this->agentsv2Collection->dependenciesTree($dependency, $agent);
        $data['tree'] = $tree;
        $data['master'] = $agent->master;
        $data['countries'] = $this->countriesRepo->all();
        $data['timezones'] = DateTimeZone::listIdentifiers();

        return view('agentsv2.agentpanel', $data);
    }

    /**
     * Return view Create Agent
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewConvertToAgent()
    {
        $data['countries'] = $this->countriesRepo->all();
        $data['timezones'] = DateTimeZone::listIdentifiers();
        return view('agentsv2.converttoagent', $data);
    }

}
