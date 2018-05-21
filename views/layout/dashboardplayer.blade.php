<div class="row">
    <div class="col-md-3 col-sm-3">
        <div class="dashboard-stat2 bg-grey-steel">
            <div class="display">
                <div class="number">
                    <h3 class="font-purple-soft" style="font-size:20px">
                        <span data-counter="counterup"
                              style="font-size:16px">  {{ $player_profit['balance'] }}   </span>
                    </h3>
                    <small> {{ __('Balance') }}</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-3">
        <div class="dashboard-stat2 bg-grey-steel">
            <div class="display">
                <div class="number">
                    <h3 class="font-green-jungle" style="font-size:20px">
                        <span data-counter="counterup"
                              style="font-size:16px"> {{ $player_profit['amountDebit'] }} </span>
                    </h3>
                    <small>{{ __("Jugado") }}</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-3">
        <div class="dashboard-stat2 bg-grey-steel">
            <div class="display">
                <div class="number">
                    <h3 class="font-red-thunderbird" style="font-size:20px">
                        <span data-counter="counterup"
                              style="font-size:16px"> {{ $player_profit['amountCredit'] }}  </span>
                    </h3>
                    <small> {{ __("Ganado") }}</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-3">
        <div class="dashboard-stat2 bg-grey-steel">
            <div class="display">
                <div class="number">
                    <h3 class="font-blue-steel" style="font-size:20px">
                                        <span data-counter="counterup"
                                              style="font-size:16px">  {{ $player_profit['amountProfit'] }}  </span>
                    </h3>
                    <small> {{ __("Profit") }}</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-12">
        <div class="form-horizontal">
            <div class="form-group">
                <label class="col-md-4"><span class="pull-right">{{  __('Ùltimo Acceso') }}</span></label>
                <div class="col-md-8">
                    <strong> {{ $lastdate }} </strong>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4"><span class="pull-right">{{  __('Depende de') }}</span></label>
                <div class="col-md-8">
                    <strong> {{ $player->agent }} </strong>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4"><span class="pull-right">{{  __('Detalles') }}</span></label>
                <div class="col-md-8">
                    <strong> {{$player->name ." ".$player->lastname }} </strong>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4"><span class="pull-right">{{  __('Acreditado') }}</span></label>
                <div class="col-md-8">
                    <strong> {{ $player_transaction['credit'] }} </strong>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4"><span class="pull-right">{{  __('Debitado') }}</span></label>
                <div class="col-md-8">
                    <strong> {{ $player_transaction['debit']}} </strong>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4"><span class="pull-right">{{  __('Estado') }}</span></label>
                <div class="col-md-8">
                    <strong> {!! $player->status !!} </strong>
                </div>
            </div>
        </div>
    </div>
    @if($player->agentid == \Auth::user()->id)
        <div class="col-md-6 col-sm-12">
            <div class="mt-element-ribbon bg-grey-steel">
                <div class="ribbon ribbon-border-hor ribbon-clip ribbon-color-default uppercase">
                    <div class="ribbon-sub ribbon-clip"></div> {{ __('Ajustes') }} </div>
                <p class="ribbon-content">
                <div class="form-horizontal"
                     id="form-adjustments">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <div class="col-md-12">
                                <input id="amount" name="amount" class="form-control"
                                       type="number" value="0"/>
                                <input id="userr" name="userr" class="form-control"
                                       type="hidden" value="{{ $player->user }}"/>
                                <input id="type" name="type" class="form-control"
                                       type="hidden" value="player"/>
                                <input id="dep" name="dep" class="form-control"
                                       type="hidden" value="{{ $player->agentid}}"/>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-6">
                                    <button class="btn btn-success btn-create btn-group-justified" type="button"
                                            name="accredit" id="accredit"
                                            data-loading-text="<i class='fa fa-spin fa-spinner'></i>"
                                            data-route="{{ route('agentsv2.adjustments-credit') }}">
                                        {{  __('Acreditar') }}
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button class="btn btn-danger btn-create btn-group-justified" type="button"
                                            name="debit" id="debit"
                                            data-loading-text="<i class='fa fa-spin fa-spinner'></i>"
                                            data-route="{{ route('agentsv2.adjustments-debit') }}">
                                        {{  __('Debitar') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </p>
            </div>
        </div>
    @endif
</div>



