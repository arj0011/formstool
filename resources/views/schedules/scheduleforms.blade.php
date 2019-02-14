@extends('layouts.app')
@section('content')   
   <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Scheduled Forms
        @if($data['schedule_name'])<small>{{ $data['schedule_name'] }}</small>@endif
      </h1>
      @isset($data['form'])<small style="color:#3c8dbc;">{{ $data['form']->name }}</small>@endisset
       {{ Breadcrumbs::render('schedule') }}
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
              {!! $dataTable->table(['class' => 'table table-bordered', 'id' => 'formGroupTable']) !!}
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->

         <!-- resubmission modal -->
          <!-- Modal -->
        <div class="modal fade" id="myModal" role="dialog">
          <div class="modal-dialog">
            <!-- Modal content-->
            <form class="form" id="resubmissionFormRequest" action="{{route('form/userResubmissionRequest')}}" method="get">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">Please give reason for re-submission</h4>
                </div>
                <div class="modal-body">
                   <input type="hidden" name="form_id" value="">
                   <input type="hidden" name="schedule_id" value="">
                   <div class="form-group">
                      <textarea class="form-control" name="resubmission_reason" required></textarea>
                   </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                  <input type="submit" class="btn btn-success" value="send">
                </div>
              </div>
            </form>
          </div>
        </div>

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
      <link rel="stylesheet" type="text/css" href="{{ asset('public/bootstrap/css/bootstrap-multiselect.css')}}"/>
        <!-- sweet alert script -->
      <link rel="stylesheet" type="text/css" href="{{ asset('public/bootstrap/css/sweetalert.css') }}">
      <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">
      <!-- Bootstrap Datepicker -->
      <link rel="stylesheet" type="text/css" href="{{ asset('public/bootstrap/css/bootstrap-datepicker.min.css')}}"/>
       <!-- Bootstrap Custome Multiselect CSS -->
      <link rel="stylesheet" type="text/css" href="{{ asset('public/bootstrap/css/bootstrap-multiselect-custome-script.css')}}"/>
      <style type="text/css">
          .toolbar {
            float:left;
          }
          input[type="date"]::-webkit-calendar-picker-indicator {
              color: rgba(0, 0, 0, 0);
              opacity: 1;
              display: block;
              background: url(https://mywildalberta.ca/images/GFX-MWA-Parks-Reservations.png) no-repeat;
              width: 20px;
              height: 20px;
              border-width: thin;
          }

          input[type=date]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            display: none;
          }

      </style>
    @endsection
    @section('js-script')
      <!-- Bootstarap Validator script -->
      <script src="{{ asset('public/bootstrap/js/bootstrapValidator.min.js')}}"></script>
      <!-- Bootstrap Jquery DataTables -->
      <script src="{{ asset('public/bootstrap/js/jquery.dataTables.min.js')}}"></script>
      <!-- Multiselect dropdown script --> 
      <script src="{{ asset('public/bootstrap/js/bootstrap-multiselect.js')}}"></script>
      <!-- Bootstrap DataTables -->
      <script src="{{ asset('public/bootstrap/js/dataTables.bootstrap.min.js')}}"></script>
      <!-- Bootstrap Datepicker -->
      <script src="{{ asset('public/bootstrap/js/bootstrap-datepicker.min.js')}}"></script>
      <!-- Moment Script -->
      <script src="https://rawgit.com/moment/moment/2.2.1/min/moment.min.js"></script>
  
      {!! $dataTable->scripts() !!}
    <script type="text/javascript">
      $('document').ready(function(){
         $('body').on('click' , '.resubmission' , function(e){
              e.preventDefault();
              let form_id     = $(this).attr('form-id');
              let schedule_id = $(this).attr('schedule-id');
              let click = $(this);

              $( "input[type=hidden][name=form_id]" ).val(form_id);
              $( "input[type=hidden][name=schedule_id]" ).val(schedule_id);

              $('#myModal').modal('show');
         });
      });
    </script>  
    @endsection