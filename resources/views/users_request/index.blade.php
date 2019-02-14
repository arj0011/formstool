@extends('layouts.app')
  @section('content')   
   <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Users
      </h1>
       {{ Breadcrumbs::render('users_request') }}
    </section>

  <!-- Main content -->
    <section class="content">
    
      <div class="row">
        @if (Session::has('status'))
          <div class="col-xs-12 alert-message-div">
              <div class="alert @if(Session::get('status')) alert-success @else alert-danger @endif alert-dismissible" role="alert">
                <strong>@if (Session::get('status')) Success @else Danger @endif!</strong> {{Session::get('msg')}}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
              </div>
          </div>
        @endif
        <div class="col-xs-12">
          <div class="box box-solid">
            <div class="box-header">
             {{--  <h3 class="box-title">Data Table With Full Features</h3> --}}
            </div>
            <!-- /.box-header -->
            <div class="box-body">
            {!! $dataTable->table(['class' => 'table table-bordered', 'id' => 'userTable']) !!}
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
    @endsection
    @section('css-script')
      <!-- Bootstarap Validator script -->
      <link rel="stylesheet" type="text/css" href="{{ asset('public/bootstrap/css/bootstrapValidator.min.css') }}">
        <!-- Bootstrap datatable script -->
      <link rel="stylesheet" type="text/css" href="{{ asset('public/bootstrap/css/dataTables.bootstrap.min.css') }}">
        <!-- sweet alert script -->
      <link rel="stylesheet" type="text/css" href="{{ asset('public/bootstrap/css/sweetalert.css') }}">
      <style type="text/css">
          .toolbar {
            float:left;
          }
      </style>
    @endsection
    @section('js-script')
      <!-- Bootstarap Validator script -->
      <script src="{{ asset('public/bootstrap/js/bootstrapValidator.min.js')}}"></script>
      <!-- Bootstrap Jquery DataTables -->
      <script src="{{ asset('public/bootstrap/js/jquery.dataTables.min.js')}}"></script>
      <!-- Bootstrap DataTables -->
      <script src="{{ asset('public/bootstrap/js/dataTables.bootstrap.min.js')}}"></script>
      {!! $dataTable->scripts() !!}
    @endsection
