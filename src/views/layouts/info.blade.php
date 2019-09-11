@extends('dev-crud::layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title py-2">
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
                                @stack('filter')
                                @if ($dateSearch_demo??null)
                                    <div class="input-group date pl-3">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" name="date" class="form-control pull-right" id="datepicker">
                                        
                                        @push("script")
                                            <link href="{{ asset("plugins/datepicker/datepicker3.css") }}"
                                                  rel="stylesheet" />
                                            <script
                                                src="{{ asset("plugins/datepicker/bootstrap-datepicker.js") }}"></script>
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
                                
                                @if ($dateSearch??null)
                                    <div class="input-group date">
                                        <input name="starting-day" id="starting-day" type="hidden" />
                                        <input name="ending-day" id="ending-day" type="hidden" />
                                        
                                        <div class="input-group pl-3">
                                            <button type="button" class="btn btn-default float-right"
                                                    id="daterange-btn">
                                                <i class="far fa-calendar-alt"></i>
                                                <span>Date range picker</span>
                                                <i class="fas fa-caret-down"></i>
                                            </button>
                                        </div>
                                        
                                        @push("script")
                                            <link href="{{ asset("plugins/daterangepicker/daterangepicker.css") }}"
                                                  rel="stylesheet" />
                                            <script
                                                src="{{ asset("plugins/daterangepicker/moment.min.js") }}"></script>
                                            <script
                                                src="{{ asset("plugins/daterangepicker/daterangepicker.js") }}"></script>
                                            <script type="text/javascript">
                                                $(function () {
                                                    //Date range as a button
                                                    $('#daterange-btn').daterangepicker(
                                                        {
                                                            "autoApply": true,
                                                            ranges: {
                                                                'Today': [moment(), moment()],
                                                                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                                                                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                                                                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                                                                'This Month': [moment().startOf('month'), moment().endOf('month')],
                                                                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                                                            },
                                                            startDate: moment().subtract(6, 'days'),
                                                            endDate: moment()
                                                        },
                                                        function (start, end) {
                                                            setDate(start, end);
                                                        }
                                                    ).on('apply.daterangepicker', function (ev, picker) {
                                                        setDate(picker.startDate, picker.endDate)
                                                    });

                                                    function setDate(start, end) {
                                                        $('#daterange-btn span').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
                                                        $('#starting-day').val(start.format('YYYY-MM-DD'));
                                                        $('#ending-day').val(end.format('YYYY-MM-DD'));
                                                    }
                                                });
                                            </script>
                                        @endpush
                                    </div>
                                @endif
                                
                                <div class="input-group pl-3">
                                    <input class="form-control" type="text" name="query" id="query"
                                           placeholder="Keywords" />
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default"><i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
                @include('dev-crud::partials.action_notification')
                @yield('dataBlock')
            </div>
            <!-- /.card -->
        </div>
    </div>
@endsection
