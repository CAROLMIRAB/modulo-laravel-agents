@extends('template')

@section('styles')
    <link rel="stylesheet" href="{{ asset('packages/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/agents.css') }}">
    <style>
        #chartsales {
            width: 100%;
            height: 500px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="portlet light bordered">
                <div class="portlet-title" id="tree-agents">
                    <div class="caption caption-md">
                        <span class="caption-subject font-blue-madison bold uppercase">{{ __('Agentes') }}</span>
                    </div>
                    <div class="actions">
                        <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;"></a>
                    </div>
                </div>
                <div class="portlet-body">
                    <form class="form-horizontal" action="{{ route('agentsv2.convert-to-agent-data') }}"
                          method="POST"
                          enctype="multipart/form-data" id="form-convert-agent">
                        <div class="form-group">
                            <label for="casino_name"
                                   class="col-md-4 control-label">{{  __('Depende de') }}</label>
                            <div class="col-md-8">
                                <select class="form-control" id="owner" name="owner"
                                        data-route="{{ route('agentsv2.search-owner') }}">

                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="casino_name"
                                   class="col-md-4 control-label">{{  __('Usuario') }}</label>
                            <div class="col-md-8">
                                <select class="form-control" name="user" id="user-search"
                                        data-route="{{ route('searchUsers') }}" required></select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="casino_description"
                                   class="col-md-4 control-label">{{  __('Descripción') }}
                            </label>
                            <div class="col-md-8">
                                <textarea class="form-control" name="description"
                                          id="description"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="balance"
                                   class="col-md-4 control-label">{{ __('Balance de operación') }}</label>
                            <div class="col-md-8">
                                <input id="balance" name="balance" class="form-control"
                                       type="number" value="0"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-4">
                            </div>
                            <div class="radio-inline">
                                <label>
                                    <input type="radio" name="dealtype" id="percentageofprofit" value="1"
                                           checked>
                                    {{ __('Porcentaje de ganancia') }}
                                </label>
                            </div>
                            <div class="radio-inline">
                                <label>
                                    <input type="radio" name="dealtype" id="customnetamount" value="2">
                                    {{ __('Monto definido') }}
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="descMto"
                                   class="col-md-4 control-label">{{ __('Monto y/o Porcentaje') }}</label>
                            <div class="col-md-8">
                                <input id="descMto" name="dealvalue" class="form-control"
                                       data-slider-id='ex1Slider'
                                       type="text" data-slider-min="0" data-slider-max="100"
                                       data-slider-step="1"
                                       data-slider-value="0"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">{{ __('Agente Master') }}</label>
                            <div class="radio-inline">
                                <label>
                                    <input type="radio" name="master" value='true' checked>
                                    {{ __('Si') }}
                                </label>
                            </div>
                            <div class="radio-inline">
                                <label>
                                    <input type="radio" name="master" value="false">
                                    {{ __('No') }}
                                </label>
                            </div>
                        </div>

                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-3 col-md-offset-6 ">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">
                                        {{  __('Cancelar') }}
                                    </button>

                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-primary btn-create"
                                            data-loading-text="<i class='fa fa-spin fa-spinner'></i>">
                                        {{  __('Crear') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('packages/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('packages/amcharts3/amcharts/amcharts.js') }}"></script>
    <script src="{{ asset('packages/amcharts3/amcharts/serial.js') }}"></script>
    <script src="{{ asset('packages/amcharts3/amcharts/pie.js') }}"></script>
    <script src="{{ asset('packages/amcharts3/amcharts/themes/light.js') }}"></script>
    <script src="{{ asset('packages/amcharts3/amcharts/plugins/dataloader/dataloader.min.js') }}"></script>
    <script src="{{ asset('js/agentsv2.js') }}"></script>
    <script>
        $(document).on('ready', function () {
            $('#timezone, #country').select2();
        });
        Agentsv2.initOwnerSearch('{{ \Auth::user()->id }}', '{{ \Auth::user()->username }}');
        Agentsv2.initUsersSearch();
        Agentsv2.convertToAgent();

    </script>
@endsection