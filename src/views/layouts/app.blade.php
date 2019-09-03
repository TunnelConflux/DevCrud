<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>
        {{ config('app.name', 'App Dashboard') }}
        @if($pageTitle ?? null)
            | {{ $pageTitle }}
        @endif
    </title>
    
    <link rel="stylesheet" href="{{ url('/css/app.css') }}">
    
    @stack('css')

</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper" id="app">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom">
        @section("main-navbar")
            @include("dev-crud::partials.navbar")
        @show
    </nav>
    
    <!-- Main Sidebar Container -->
    <aside class="main-sidebar elevation-4 sidebar-dark-info">
        @section("main-sidebar")
            @if (view()->exists("layouts.partials.sidebar"))
                @include("layouts.partials.sidebar")
            @else
                @include("dev-crud::partials.sidebar")
            @endif
        @show
    </aside>
    
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        @section("main-content")
            @include("dev-crud::partials.content")
        @show
    </div>
    
    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
        <div class="p-3">
            <h5>Title</h5>
            <p>Sidebar content</p>
        </div>
    </aside>
    <!-- /.control-sidebar -->
    
    <!-- Main Footer -->
    <footer class="main-footer">
        @section("footer-right")
            <div class="float-right d-none d-sm-inline">Anything you want</div>
        @show
        @section("footer-left")
            <strong>Copyright &copy; 2014-2019 AdminLTE.io.</strong> All rights reserved.
        @show
    </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<script type="text/javascript" src="{{ url('/js/app.js')}}"></script>

<script type="text/javascript">
    String.prototype.capitalize = function () {
        return this.charAt(0).toUpperCase() + this.slice(1);
    }
</script>

@stack('js-lib')
@stack('script')
</body>
</html>