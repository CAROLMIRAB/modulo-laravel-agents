<div class="modal fade" id="modal-user">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="form-horizontal" action="{{ route('agentsv2.create-player-data') }}"
                  method="POST"
                  enctype="multipart/form-data" id="form-create-player">
                <div class="modal-header">
                    <button type="button" class="close"></button>
                    <h3 class="modal-title">
                        {{ __('Agregar Jugador') }}
                    </h3>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name"
                               class="col-md-4 control-label">{{  __('Nombre') }}
                        </label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="name" id="name"
                                   placeholder="{{ __('Nombre') }}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="lastname"
                               class="col-md-4 control-label">{{  __('Apellido') }}
                        </label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="lastname" id="lastname"
                                   placeholder="{{ __('Apellido') }}" required>
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
                            <select class="form-control" id="country-player" name="country" style="width: 100%;">
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
                            <select class="form-control" id="timezone-player" name="timezone" style="width: 100%;">
                                <option value="">{{ __('Seleccione') }}</option>
                                @foreach($timezones as $timezone)
                                    <option value="{{ $timezone  }}">{{ $timezone  }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="balance" class="col-md-4 control-label">{{ __('Balance') }}</label>
                        <div class="col-md-8">
                            <input id="balance" name="balance" class="form-control"
                                   type="number" value="0"/>
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