@extends('easy-crud::layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title text-capitalize">
                        {{--<i class="fas fa-bullhorn"></i>--}}
                        @yield('blockTitle')
                        @if (!Route::is("*.create") && $isCreatable)
                            |
                            <small>
                                <a href="{{ route("{$routePrefix}.create") }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-plus" aria-hidden="true"></i>
                                    Add New
                                </a>
                            </small>
                        @endif
                    </h3>
                
                    @if(Route::is("*.index"))
                        <div class="card-tools">
                            <form method="get" class="form-inline">
                                @if ($dateSearch??null)
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" name="date" class="form-control pull-right" id="datepicker">
                                    
                                        @push("script")
                                            <link href="{{ asset("plugins/datepicker/datepicker3.css") }}"
                                                  rel="stylesheet" />
                                            <script src="{{ asset("plugins/datepicker/bootstrap-datepicker.js") }}"></script>
                                            <script type="text/javascript">
                                                $(function () {
                                                    //Date picker
                                                    $('#datepicker').datepicker({
                                                        autoclose: true,
                                                        format: "yyyy-mm-dd"
                                                    })
                                                });
                                            </script>
                                        @endpush
                                    </div>
                                @endif
                            
                                <div class="input-group">
                                    <input class="form-control" type="text" name="query" id="query"
                                           placeholder="Keywords" />
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            
                @include('partials.action_notification')
                @yield('dataBlock')
            </div>
            <!-- /.card -->
        </div>
    </div>
@endsection