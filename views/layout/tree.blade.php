@extends('template')

@section('styles')
    <link rel="stylesheet" href="{{ asset('packages/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('packages/jstree/dist/themes/default/style.min.css') }}">
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
        <div class="col-md-4">
            <div class="portlet light bordered">
                <div class="portlet-title" id="tree-agents">
                    <div class="caption caption-md">
                        <span class="caption-subject font-blue-madison bold uppercase">{{ __('Dependencias') }}</span>
                    </div>
                    <div class="actions">
                        <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;"></a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div id='tree' class='tree-demo'>
                        <ul>{!! $tree !!}</ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('packages/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('packages/jstree/dist/jstree.min.js') }}"></script>
    <script src="{{ asset('packages/amcharts3/amcharts/amcharts.js') }}"></script>
    <script src="{{ asset('packages/amcharts3/amcharts/serial.js') }}"></script>
    <script src="{{ asset('packages/amcharts3/amcharts/pie.js') }}"></script>
    <script src="{{ asset('packages/amcharts3/amcharts/themes/light.js') }}"></script>
    <script src="{{ asset('packages/amcharts3/amcharts/plugins/dataloader/dataloader.min.js') }}"></script>
    <script src="{{ asset('js/agentsv2.js') }}"></script>
    <script>
        Agentsv2.treeDependency();
        Agentsv2.initOwnerSearch('{{ \Auth::user()->id }}', '{{ \Auth::user()->username }}');
        Agentsv2.initAgentFormCreate();
    </script>
@endsection