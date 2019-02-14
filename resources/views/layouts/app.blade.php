<!DOCTYPE html>
<html>

    <head>
      @include('layouts.head')
      @section('css-script')@show
      <style type="text/css">
        .preload * {
          -webkit-transition: none !important;
          -moz-transition: none !important;
          -ms-transition: none !important;
          -o-transition: none !important;
        }
      </style>
    </head>

    <body class="hold-transition skin-blue sidebar-mini preload fixed">
      
    <!-- Site wrapper -->
      <div class="wrapper">

        <header class="main-header">
             @include('layouts.header')
        </header>

         <!-- Left side column. contains the sidebar -->
        <aside class="main-sidebar">
                 @include('layouts.aside')
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
                @section('content')@show
        </div>
        <!-- /.content-wrapper -->

        <footer class="main-footer">
             @include('layouts.footer')
        </footer>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
        
          @include('layouts.control_sidebar')
        </aside>
        <!-- /.control-sidebar -->
        <!-- Add the sidebar's background. This div must be placed
             immediately after the control sidebar -->
        <div class="control-sidebar-bg"></div>
     <button id="back-to-top" href="#" class="btn btn-primary btn-lg back-to-top" role="button" title="Click to return on the top page" data-toggle="tooltip" data-placement="left">Top</button>
      </div>
    <!-- ./wrapper -->
     @include('layouts.foot')
     @section('js-script')@show
    </body>
</html>

