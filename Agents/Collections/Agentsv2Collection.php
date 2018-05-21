<?php

namespace JEL\Agents\Collections;

use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use JEL\Agents\Repositories\Agentsv2Repo;
use JEL\Core\Repositories\CoreRepo;
use Juegaenlinea\Audits\Audits;
use Juegaenlinea\Security\Enums\Permissions;
use Juegaenlinea\Walletwrapper\Enums\TransactionType;
use Juegaenlinea\Walletwrapper\Wallet;


/**
 * Class AgentsCollection
 *
 * Collection to format and preparte the agents data
 *
 * @package JEL\Agents\Collections
 * @author  Eborio Linarez
 */
class Agentsv2Collection
{

    /**
     * Collection Dependency tree
     *
     * @param $dependency
     * @param $agent
     * @return string
     */
    public function dependenciesTree($dependency, $agent)
    {
        $agentsv2Repo = new Agentsv2Repo();
        $agents = $agentsv2Repo->findAgentsByOwner($agent->user);
        $players = $agentsv2Repo->findPlayersByAgent($agent->user);
        $icondep = '{"icon":"fa fa-sitemap", "opened":true, "disabled":true}';
        $iconag = '{"icon":"fa fa-diamond"}';

        $tree = sprintf(
            "<li data-jstree='%s'> %s",
            $icondep,
            $dependency->username
        );
        $master = ($agent->master == true) ? 'agent-master' : '';
        $tree .= "<ul>";
        $tree .= sprintf(
            "<li data-jstree='%s'><a href='#' data-id='%s' data-type='agent' class='jstree-clicked click-tab master %s'> %s </a>",
            $iconag,
            $agent->user,
            $master,
            $agent->username
        );
        $tree .= "<ul>";
        $tree .= self::itemsTreeAgents($agents);
        $tree .= "</ul>";

        $tree .= "<ul>";
        $tree .= self::itemsTreePlayers($players);
        $tree .= "</ul>";

        $tree .= "</li>";
        $tree .= "</ul>";

        $tree .= "</li>";

        return $tree;
    }

    /**
     * Profit Player
     *
     * @param $providers
     * @param $player
     * @param $startDate
     * @param $endDate
     * @param $credit
     * @param $debit
     * @param $balance
     * @return array
     */
    public function profitByPlayer($providers, $player, $startDate, $endDate, $credit, $debit, $balance)
    {
        $currency = \Session::get('currency');
        $amountDebit = 0;
        $amountCredit = 0;
        $profit = 0;
        $accredit = 0;
        $debited = 0;
        if (!empty($credit) || !is_null($credit)) {
            foreach ($credit as $item) {
                $accredit = $item->amount;
            }
        }

        if (!empty($debit) || !is_null($debit)) {
            foreach ($debit as $item) {
                $debited = $item->amount;
            }
        }

        $access = Audits::lastLogin($player->user);
        if (count($access) > 0) {
            $date_lastdate = new Carbon($access['created_at']);
            $date_lastdate->setTimezone(\Session::get('timezone'));
            $lastdate = $date_lastdate->format('d/m/Y h:i A');
        } else {
            $lastdate = __('No ha Accesado al sistema');
        }

        foreach ($providers as $provider) {
            $providersId[] = $provider->id;
        }

        $financial = json_decode(Wallet::financialStateDates([$player->user], $providersId, $startDate, $endDate));
        $resultTransaction = $financial->data->value;

        foreach ($resultTransaction as $result) {

            if ($result->transactiontype == TransactionType::$credit) {
                $amountCredit = $amountCredit + $result->total;
            }

            if ($result->transactiontype == TransactionType::$debit) {
                $amountDebit = $amountDebit + $result->total;
            }
        }

        $profit = $amountDebit - $amountCredit;

        if ($player->locked) {
            $player->status = "<label class='label label-sm label-danger'>" . __('Inactivo') . "</label>";

        } else {
            $player->status = "<label class='label label-sm label-success'>" . __('Activo') . "</label>";
        }

        $credit = number_format($amountCredit, intval($currency['decimals']), $currency['decimalseparator'], $currency['thousandseparator']);
        $debit = number_format($amountDebit, intval($currency['decimals']), $currency['decimalseparator'], $currency['thousandseparator']);
        $profittotal = number_format($profit, intval($currency['decimals']), $currency['decimalseparator'], $currency['thousandseparator']);
        $balancetotal = number_format($balance, intval($currency['decimals']), $currency['decimalseparator'], $currency['thousandseparator']);

        $data = [
            'player_profit' => [
                'amountCredit' => $credit,
                'amountDebit' => $debit,
                'amountProfit' => $profittotal,
                'balance' => $balancetotal
            ],
            'player' => $player,
            'player_transaction' => [
                'credit' => $accredit,
                'debit' => $debited
            ],
            'lastdate' => $lastdate

        ];


        return $data;
    }

    /**
     * Profit Agents
     *
     * @param $providers
     * @param $agent
     * @param $startDate
     * @param $endDate
     * @param $credit
     * @param $debit
     * @return array
     */
    public function profitByAgents($providers, $agent, $startDate, $endDate, $credit, $debit)
    {
        $agentsv2Repo = new Agentsv2Repo();
        $currency = \Session::get('currency');
        $agents = $agentsv2Repo->findAgentsByOwner($agent->user);
        $players = $agentsv2Repo->findPlayersByAgent($agent->user);
        $access = Audits::lastLogin($agent->user);
        if (count($access) > 0) {
            $date_lastdate = new Carbon($access['created_at']);
            $date_lastdate->setTimezone(\Session::get('timezone'));
            $lastdate = $date_lastdate->format('d/m/Y h:i A');
        } else {
            $lastdate = __('No ha Accesado al sistema');
        }

        $accredit = 0;
        $debited = 0;
        if (!empty($credit) || !is_null($credit)) {
            foreach ($credit as $item) {
                $accredit = $item->amount;
            }
        }

        if (!empty($debit) || !is_null($debit)) {
            foreach ($debit as $item) {
                $debited = $item->amount;
            }
        }
        if (count($agents) > 0) {
            $result = self::agentsProfit($startDate, $endDate, $providers, $agents, $agent->user);
        } else {
            $result = self::playersProfit($startDate, $endDate, $providers, $players);
        }

        $agent->agentmaster = ($agent->master == true) ? "<label class='label label-success'>" . __('Agente Master') . "</label>" : "<label class='label label-sm label-default'>" . __('Agente') . "</label>";

        if ($agent->locked) {
            $agent->status = "<label class='label label-sm label-danger'>" . __('Inactivo') . "</label>";

        } else {
            $agent->status = "<label class='label label-sm label-success'>" . __('Activo') . "</label>";
        }

        $credit = number_format($result['amountCredit'], intval($currency['decimals']), $currency['decimalseparator'], $currency['thousandseparator']);
        $debit = number_format($result['amountDebit'], intval($currency['decimals']), $currency['decimalseparator'], $currency['thousandseparator']);
        $profittotal = number_format($result['amountProfit'], intval($currency['decimals']), $currency['decimalseparator'], $currency['thousandseparator']);
        $agent->balance = number_format($agent->balance, intval($currency['decimals']), $currency['decimalseparator'], $currency['thousandseparator']);

        $result = [
            'agent' => $agent,
            'agent_profit' => [
                'amountCredit' => $credit,
                'amountDebit' => $debit,
                'amountProfit' => $profittotal
            ],
            'lastdate' => $lastdate,
            'player_transaction' => [
                'credit' => $accredit,
                'debit' => $debited
            ],
        ];

        return $result;
    }

    /**
     * @param $agents
     * @return \Illuminate\Support\Collection
     */
    public function tableAgents($agents)
    {
        $currency = \Session::get('currency');
        $result = collect();

        foreach ($agents as $agent) {
            $agent->agentmaster = ($agent->master == true) ? "<label class='label label-success'>" . __('Agente Master') . "</label>" : "<label class='label label-sm label-default'>" . __('Agente') . "</label>";

            $result->push([
                'name' => $agent->username,
                'description' => $agent->description,
                'balance' => $agent->balance,
                'master' => $agent->agentmaster,
                'decimals' => intval($currency['decimals']),
                'decimalseparator' => $currency['decimalseparator'],
                'thousandseparator' => $currency['thousandseparator']
            ]);

        }
        return $result;
    }

    /**
     * @param $players
     * @return \Illuminate\Support\Collection
     */
    public function tablePlayers($players)
    {
        $currency = \Session::get('currency');
        $result = collect();

        foreach ($players as $player) {
            $user = $player->user;
            $balance = json_decode(\Wallet::getWallet($user))->data->wallet->balance;
            $result->push([
                'name' => $player->username,
                'correo' => $player->email,
                'balance' => $balance,
                'decimals' => intval($currency['decimals']),
                'decimalseparator' => $currency['decimalseparator'],
                'thousandseparator' => $currency['thousandseparator']
            ]);

        }
        return $result;
    }

    /**
     * Format financial state
     *
     * @param $providers
     * @param $financialState
     * @return string
     */
    public function formatFinancialState($providers, $financialState)
    {
        $html = '';

        $acumWon = 0;
        $acumPlayed = 0;
        $acumProfit = 0;

        foreach ($providers as $provider) {
            $html .= '<li class="horizontal-list"><h4 class="uppercase bold"><a href="javascript:;">' . $provider->description . '</a></h4>';
            if (count($financialState)) {
                foreach ($financialState->data as $total) {
                    $played = 0;
                    $won = 0;
                    foreach ($total as $value) {
                        if (isset($value->provider)) {
                            if ($value->provider === $provider->id) {
                                if (isset($value->transactiontype)) {
                                    if ($value->transactiontype == TransactionType::$credit) {
                                        $won = (double)$value->total;
                                    }
                                    if ($value->transactiontype == TransactionType::$debit) {
                                        $played = (double)$value->total;
                                    }
                                }
                            }
                        }
                    }

                    $result = $won - $played;

                    $acumWon = $acumWon + $won;
                    $acumPlayed = $acumPlayed + $played;
                    $acumProfit = $acumProfit + $result;

                    $html .= '<p>' . __('Jugado') . ' <i class="fa fa-money"></i> ' . number_format($played, 2, ',', '.') . '</p>';
                    $html .= '<p>' . __('Ganado') . ' <i class="fa fa-money"></i> ' . number_format($won, 2, ',', '.') . '</p>';
                    $html .= '<p> Profit ' . (($result > 0) ? ' <i class="fa fa-arrow-down text-danger"></i> ' : (($result < 0) ? ' <i class="fa fa-arrow-up text-success"></i> ' : '')) . number_format(abs($result), 2, ',', '.') . '</p>';
                }
            }
            $html .= '</li>';
        }

        $html .= '<li class="horizontal-list"><h4 class="uppercase bold"><a href="javascript:;">' . __('Totales generales') . '</a></h4>';
        $html .= '<p>' . __('Jugados') . ' <i class="fa fa-money"></i> ' . number_format($acumPlayed, 2, ',', '.') . '</p>';
        $html .= '<p>' . __('Ganados') . ' <i class="fa fa-money"></i> ' . number_format($acumWon, 2, ',', '.') . '</p>';
        $html .= '<p>' . __('Totales') . (($acumProfit > 0) ? ' <i class="fa fa-arrow-down text-danger"></i> ' : (($acumProfit < 0) ? ' <i class="fa fa-arrow-up text-success"></i> ' : '')) . number_format(abs($acumProfit), 2, ',', '.') . '</p>';

        return $html;
    }

    /**
     *  Method recursive obtain debit and credit by players
     *
     * @param $startDate
     * @param $endDate
     * @param $providers
     * @param $players
     * @return array
     */
    public static function playersProfit($startDate, $endDate, $providers, $players)
    {
        $amountDebit = 0;
        $amountCredit = 0;

        foreach ($providers as $provider) {
            $providersId[] = $provider->id;
        }

        foreach ($players as $player) {
            $financial = json_decode(Wallet::financialStateDates([$player->user], $providersId, $startDate, $endDate));
            $resultTransaction = $financial->data->value;

            foreach ($resultTransaction as $result) {

                if ($result->transactiontype == TransactionType::$credit) {
                    $amountCredit = $amountCredit + $result->total;
                }

                if ($result->transactiontype == TransactionType::$debit) {
                    $amountDebit = $amountDebit + $result->total;
                }
            }
        }
        $profit = $amountDebit - $amountCredit;
        $data = ['amountCredit' => $amountCredit, 'amountDebit' => $amountDebit, 'amountProfit' => $profit];

        return $data;
    }


    /**
     * Recursive Method  obtain profit agent
     *
     * @param $startDate
     * @param $endDate
     * @param $providers
     * @param $agents
     * @return array
     */
    public static function agentsProfit($startDate, $endDate, $providers, $agents)
    {
        $amountDebit = 0;
        $amountCredit = 0;
        $agentsv2Repo = new Agentsv2Repo();
        foreach ($agents as $item) {
            $agentss = $agentsv2Repo->findAgentsByOwner($item->user);
            if (count($agentss) > 0) {
                $result = self::agentsProfit($startDate, $endDate, $providers, $agentss);
            }

            $playerss = $agentsv2Repo->findPlayersByAgent($item->user);
            if (count($playerss) > 0) {
                $res = self::playersProfit($startDate, $endDate, $providers, $playerss);
                $amountDebit = $amountDebit + $res['amountDebit'];
                $amountCredit = $amountCredit + $res['amountCredit'];
            }
        }
        $profit = $amountDebit - $amountCredit;
        $data = [
            'amountCredit' => $amountCredit,
            'amountDebit' => $amountDebit,
            'amountProfit' => $profit
        ];

        return $data;
    }

    /**
     * Recursive Method  obtain profit agent
     *
     * @param $startDate
     * @param $endDate
     * @param $providers
     * @param $agents
     * @return array
     */
    public static function financialAgentsProfit($startDate, $endDate, $providers, $agents)
    {
        $amountDebit = 0;
        $amountCredit = 0;
        $agentsv2Repo = new Agentsv2Repo();
        foreach ($agents as $item) {
            $agentss = $agentsv2Repo->findAgentsByOwner($item->user);
            if (count($agentss) > 0) {
                $result = self::agentsProfit($startDate, $endDate, $providers, $agentss);
            }

            $playerss = $agentsv2Repo->findPlayersByAgent($item->user);
            if (count($playerss) > 0) {
                $res = self::playersProfit($startDate, $endDate, $providers, $playerss);
                $amountDebit = $amountDebit + $res['amountDebit'];
                $amountCredit = $amountCredit + $res['amountCredit'];
            }
        }
        $profit = $amountDebit - $amountCredit;
        $data = [
            'amountCredit' => $amountCredit,
            'amountDebit' => $amountDebit,
            'amountProfit' => $profit
        ];

        return $data;
    }

    /**
     *  Method recursive obtain debit and credit by players
     *
     * @param $startDate
     * @param $endDate
     * @param $providers
     * @param $players
     * @return array
     */
    public static function financialPlayersProfit($startDate, $endDate, $providers, $players)
    {
        $amountDebit = 0;
        $amountCredit = 0;

        foreach ($providers as $provider) {
            $providersId[] = $provider->id;
        }

        foreach ($players as $player) {
            $financial = json_decode(Wallet::financialStateDates([$player->user], $providersId, $startDate, $endDate));
            $resultTransaction = $financial->data->value;

            foreach ($resultTransaction as $result) {

                if ($result->transactiontype == TransactionType::$credit) {
                    $amountCredit = $amountCredit + $result->total;
                }

                if ($result->transactiontype == TransactionType::$debit) {
                    $amountDebit = $amountDebit + $result->total;
                }
            }
        }
        $profit = $amountCredit - $amountDebit;
        $data = ['amountCredit' => $amountCredit, 'amountDebit' => $amountDebit, 'amountProfit' => $profit];

        return $data;
    }

    /**
     * Recursive Method for agents dependency
     *
     * @param $treeAgents
     * @return null|string
     */
    public static function itemsTreeAgents($treeAgents)
    {
        $tree = null;
        $agentsv2Repo = new Agentsv2Repo();
        foreach ($treeAgents as $item) {
            $iconmaster = ($item->master == true) ? '{"icon":"fa fa-star"}' : '{"icon":"fa fa-users"}';
            $master = ($item->master == true) ? 'agent-master' : '';
            $tree .= sprintf(
                "<li data-jstree='%s'> <a href='#' data-id='%s' data-type='agent' class='click-tab %s'> %s </a>",
                $iconmaster,
                $item->user,
            $master,
                $item->username
            );

            $agents = $agentsv2Repo->findAgentsByOwner($item->user);
            if (count($agents) > 0) {
                $tree .= '<ul>';
                $tree .= self::itemsTreeAgents($agents);
                $tree .= '</ul>';
            }

            $players = $agentsv2Repo->findPlayersByAgent($item->user);
            $tree .= '<ul>';
            $tree .= self::itemsTreePlayers($players);
            $tree .= '</ul>';
            $tree .= '</li>';

        }
        return $tree;
    }

    /**
     *
     *
     * @param $treePlayers
     * @return null|string
     */
    public static function itemsTreePlayers($treePlayers)
    {
        $tree = null;
        foreach ($treePlayers as $item) {
            $icon = '{"icon":"fa fa-user"}';
            $tree .= sprintf(
                " <li data-jstree='%s' ><a data-id='%s' data-type='player' class='click-tab'>%s</a>",
                $icon,
                $item->user,
                $item->username
            );

            $tree .= '</li>';
        }
        return $tree;
    }
}