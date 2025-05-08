@extends('voyager::master')

@section('content')
    <div class="page-content container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        @yield('voyager_browse')
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
    @parent
    <script>
        $(document).ready(function () {
            // Custom JS for your widget if needed
        });
    </script>
@stop