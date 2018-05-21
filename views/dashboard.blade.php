@extends('template')

@section('styles')
    <style>
        #chartsales {
            width: 100%;
            height: 500px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-3">
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
                    <div id="tree" class="tree-demo">
                        <ul>
                            <li>
                                {{ _('Permisos') }}
                                <ul>
                                    @foreach($permissions as $permission)
                                        <li data-jstree='{ "icon" : "fa fa-key" }'>
                                            {{ $permission }}
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('packages/amcharts3/amcharts/amcharts.js') }}"></script>
    <script src="{{ asset('packages/amcharts3/amcharts/serial.js') }}"></script>
    <script src="{{ asset('packages/amcharts3/amcharts/pie.js') }}"></script>
    <script src="{{ asset('packages/amcharts3/amcharts/themes/light.js') }}"></script>
    <script src="{{ asset('packages/amcharts3/amcharts/plugins/dataloader/dataloader.min.js') }}"></script>
    <script src="{{ asset('js/graphics/agents/dashboard.js') }}"></script>

@endsection