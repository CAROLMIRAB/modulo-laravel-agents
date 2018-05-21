<div class="modal fade" id="modal-agent">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="form-horizontal" action="{{ route('agentsv2.create-agent-data') }}"
                  method="POST"
                  enctype="multipart/form-data" id="form-create-agent">
                <div class="modal-header">
                    <button type="button" class="close"></button>
                    <h3 class="modal-title">
                        {{ __('Agregar Agent') }}
                    </h3>
                </div>
                <div class="modal-body">
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
                        <label for="username" class="col-md-4 control-label">{{  __('Username') }}</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="username" id="username"
                                   placeholder="{{ __('Username') }}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password" class="col-md-4 control-label">{{ __('Contraseña') }}</label>
                        <div class="col-md-8">
                            <input type="password" class="form-control" id="password" name="password"
                                   placeholder="{{ __('Contraseña') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email"
                               class="col-md-4 control-label">{{ __('Correo electrónico') }}</label>
                        <div class="col-md-8">
                            <input type="email" class="form-control" id="email" name="email"
                                   placeholder="{{ __('Correo electrónico') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="country" class="col-md-4 control-label">{{ __('País') }}</label>
                        <div class="col-md-8">
                            <select class="form-control" id="country" name="country" style="width: 100%;">
                                <option value="">{{ __('Seleccione...') }}</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->value  }}">{{ $country->text }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="timezone"
                               class="col-md-4 control-label">{{ __('Zona horaria') }}</label>
                        <div class="col-md-8">
                            <select class="form-control" id="timezone" name="timezone" style="width: 100%;">
                                <option value="">{{ __('Seleccione') }}</option>
                                @foreach($timezones as $timezone)
                                    <option value="{{ $timezone  }}">{{ $timezone  }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="balance" class="col-md-4 control-label">{{ __('Balance de operación') }}</label>
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
                        <label for="descMto" class="col-md-4 control-label">{{ __('Monto y/o Porcentaje') }}</label>
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
                </div>
                <div class="modal-footer">
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