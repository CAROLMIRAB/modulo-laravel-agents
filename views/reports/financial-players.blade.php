@extends('template')

@section('styles')
    <link rel="stylesheet" href="{{ asset('packages/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/agents.css') }}">
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption caption-md">
                        <span class="caption-subject font-blue-madison bold uppercase">{{ __('Barra de búsqueda') }}</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <form name="form-search" role="form" class="form-inline">
                        <div class="form-group col-md-2">
                            <label class="sr-only" for="agent_username"> {{ __('Agente:') }} </label>
                            <select id="agent_username" name="agent_username" class="form-control">
                                <option value="">{{ __('Seleccione') }}</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->user }}">{{ $agent->username }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group ">
                            <button type="button" class="btn blue bold uppercase" id="bt_search_financial_state">
                                {{ __('Buscar') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption caption-md">
                        <span class="caption-subject font-blue-madison bold uppercase">{{__('Resumen')}}</span>
                    </div>
                </div>
                <div class="portlet-body flip-scroll">
                    <div class="mt-element-list">
                        <div class="mt-list-container list-todo" id="accordion1" role="tablist"
                             aria-multiselectable="true"
                             data-route="{{ route('agentsv2.financial-state-players-data') }}">
                            <div class="list-todo-line"></div>
                            <ul>
                                <li class="mt-list-item">
                                    <div class="list-todo-icon bg-white">
                                        <i class="fa fa-calendar-times-o"></i>
                                    </div>
                                    <div class="list-todo-item dark">
                                        <a class="list-toggle-container" data-toggle="collapse"
                                           data-parent="#accordion1" onclick=" " href="#task-1" aria-expanded="false">
                                            <div class="list-toggle done uppercase">
                                                <div class="list-toggle-title bold">{{ __('DIA') }}</div>
                                            </div>
                                        </a>
                                        <div class="task-list panel-collapse collapse" id="task-1"
                                             aria-expanded="false">
                                            <ul>
                                                <li class="task-list-item done">
                                                    <div class="task-content">
                                                        <ul class="horizontal-list-ul" id="agents_financial_state_day">
                                                            {{ __('Cargando...') }}
                                                        </ul>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                                <li class="mt-list-item">
                                    <div class="list-todo-icon bg-white">
                                        <i class="fa fa-calendar-check-o"></i>
                                    </div>
                                    <div class="list-todo-item dark">
                                        <a class="list-toggle-container collapsed" data-toggle="collapse"
                                           data-parent="#accordion1" href="#task-2" aria-expanded="false">
                                            <div class="list-toggle done uppercase">
                                                <div class="list-toggle-title bold">{{ __('SEMANA') }}</div>
                                            </div>
                                        </a>
                                        <div class="task-list panel-collapse collapse" id="task-2" aria-expanded="false"
                                             style="height: 0px;">
                                            <ul>
                                                <li class="task-list-item done">
                                                    <div class="task-content">
                                                        <ul class="horizontal-list-ul" id="agents_financial_state_week">
                                                            {{ __('Cargando...') }}
                                                        </ul>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                                <li class="mt-list-item">
                                    <div class="list-todo-icon bg-white">
                                        <i class="fa fa-calendar-plus-o"></i>
                                    </div>
                                    <div class="list-todo-item dark">
                                        <a class="list-toggle-container font-white collapsed" data-toggle="collapse"
                                           data-parent="#accordion1" href="#task-3" aria-expanded="false">
                                            <div class="list-toggle done uppercase">
                                                <div class="list-toggle-title bold">{{ __('TOTAL') }}</div>
                                            </div>
                                        </a>
                                        <div class="task-list panel-collapse collapse" id="task-3" aria-expanded="false"
                                             style="height: 0px;">
                                            <ul>
                                                <li class="task-list-item done">
                                                    <div class="task-content">
                                                        <ul class="horizontal-list-ul"
                                                            id="agents_financial_state_total">
                                                            {{ __('Cargando...') }}
                                                        </ul>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('packages/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/agentsv2.js') }}"></script>
    <script>
        $(function () {
            Agentsv2.initFinancialState(true);
            $('#agent_username').select2({placeholder: ' {{ __('Seleccione') }} '});
        });
    </script>
@endsection