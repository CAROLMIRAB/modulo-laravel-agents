@extends('template')

@section('styles')
    <link rel="stylesheet" href="{{ asset('packages/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('packages/jstree/dist/themes/default/style.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/core.css') }}"/>
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
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-4 mt-element-ribbon">
                            <div class="ribbon ribbon-vertical-right ribbon-border-vert ribbon-color-default uppercase">
                                <div class="ribbon-sub ribbon-bookmark"></div>
                                <i class="fa fa-sitemap"></i>
                            </div>
                            <div id='tree' class='tree-demo '>
                                <ul>{!! $tree !!}</ul>
                            </div>
                        </div>
                        <div class="col-md-8 tab-part-content">
                            <div class="tabbable-line nav-justified">
                                <ul class='nav nav-tabs nav-justified'>
                                    <li class='dashboard-tab active' class="general-tab">
                                        <a href='#tab-dashboard' data-toggle='tab'
                                           class="a-tab-dashboard">{{ __('Dashboard') }}</a>
                                    </li>
                                    <li class="players-tab">
                                        <a href='#tab-players' data-toggle='tab'
                                           class="a-tab-players">{{ __('Jugadores') }} </a>
                                    </li>
                                    <li class="agents-tab">
                                        <a href='#tab-agents' data-toggle='tab'
                                           class="a-tab-agents">{{ __('Agentes') }} </a>
                                    </li>
                                </ul>
                                <div class='tab-content '>
                                    <div class='tab-pane active' id='tab-dashboard'
                                         data-route="{{ route('agentsv2.agent-panel-dashboard') }}">

                                        <div id='' style='height:500px'>
                                        </div>
                                    </div>
                                    <div class='tab-pane' id='tab-players'>
                                        <a href="#modal-user" data-toggle="modal"
                                           class="btn btn-outline green btn-sm add-player pull-right">
                                            {{ __('Agregar Jugador') }}
                                            <i class="fa fa-plus"></i>
                                        </a>
                                        <table class="table table-bordered table-condensed table-striped datatable-transactions"
                                               data-route="{{route('agentsv2.agent-panel-players')}}">
                                            <thead>
                                            <tr>
                                                <th>
                                                    {{  __('Usuario') }}
                                                </th>
                                                <th>
                                                    {{  __('Correo') }}
                                                </th>
                                                <th>
                                                    {{  __('Balance') }}
                                                </th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <div class='tab-pane' id='tab-agents'>
                                        <a href="#modal-agent" data-toggle="modal"
                                           class="btn btn-outline green btn-sm add-agent pull-right">
                                            {{ __('Agregar Agente') }}
                                            <i class="fa fa-plus"></i>
                                        </a>
                                        <table class="table table-bordered table-condensed table-striped datatable-transactions-agent"
                                               data-route="{{route('agentsv2.agent-panel-agents')}}">
                                            <thead>
                                            <tr>
                                                <th>
                                                    {{  __('Usuario') }}
                                                </th>
                                                <th>
                                                    {{  __('Descripcion') }}
                                                </th>
                                                <th>
                                                    {{  __('Balance') }}
                                                </th>
                                                <th>
                                                    {{  __('Tipo') }}
                                                </th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('agentsv2.modals.createagent')
    @include('agentsv2.modals.createplayer')
@endsection

@section('scripts')
    <script src="{{ asset('packages/moment/min/moment-with-locales.min.js') }}"></script>
    <script src="{{ asset('packages/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('packages/jquery-number/jquery.number.min.js') }}"></script>
    <script src="{{ asset('packages/datatables-plugins/api/fnReloadAjax.js') }}"></script>
    <script src="{{ asset('packages/jstree/dist/jstree.min.js') }}"></script>
    <script src="{{ asset('packages/amcharts3/amcharts/amcharts.js') }}"></script>
    <script src="{{ asset('packages/amcharts3/amcharts/serial.js') }}"></script>
    <script src="{{ asset('packages/amcharts3/amcharts/pie.js') }}"></script>
    <script src="{{ asset('packages/amcharts3/amcharts/themes/light.js') }}"></script>
    <script src="{{ asset('packages/amcharts3/amcharts/plugins/dataloader/dataloader.min.js') }}"></script>
    <script src="{{ asset('js/agentsv2.js') }}"></script>
    <script>
        Agentsv2.treeDependency();
        Agentsv2.initAgentDashboard('{{ \Auth::user()->id }}', 'agent', '{{ $master }}');
        Agentsv2.panelAgents();
        Agentsv2.dataPlayers();
        Agentsv2.dataAgents();
        $(document).on('ready', function () {
            $("#timezone, #country").select2({dropdownParent: "#modal-agent"});
            $("#timezone-player, #country-player").select2({dropdownParent: $("#modal-user")});
        });
        Agentsv2.initOwnerSearch('{{ \Auth::user()->id }}', '{{ \Auth::user()->username }}');
    </script>
@endsection