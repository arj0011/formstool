@extends('layouts.app')
  @section('content')   
   <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Users
      </h1>
       {{ Breadcrumbs::render('users') }}
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

      {{--   @can('create', App\User::class)
          <div class="col-xs-12 col-md-2">
          <form action="{{route('user/index')}}" method="GET" id="filter-form">
             <div class="form-group">
                <select name="role" class="form-control" id="role-filter">
                   @forelse($data['roles'] as $key => $role)
                     @if ($key == 0)
                        <option value="">All Role</option>
                     @endif
                    <option value="{{$role->id}}" 
                     @if ($data['role'] == $role->id)
                       {{ 'selected' }}
                     @endif>{{ucwords($role->name)}}</option>
                   @empty
                     <option disabled>Empty</option>
                   @endforelse
                </select>
             </div>
          </form>
          </div>
          <div class="col-xs-12  col-md-3">
              <a href="{{ route('user/create')}}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add</a>
          </div>
        @endcan --}}

         @can('create', App\User::class)
          <div class="row">
          <div class="col-md-12  add-btn-div">
          <form action="{{route('user/index')}}" method="GET" id="filter-form">
            <div class="col-md-2 form-group">
                <select name="role" class="form-control">
                   @forelse($data['roles'] as $key => $role)
                     @if ($key == 0)
                        <option value="">All Role</option>
                     @endif
                    <option value="{{$role->id}}" 
                     @if ($data['role'] == $role->id)
                       {{ 'selected' }}
                     @endif>{{ucwords($role->name)}}</option>
                   @empty
                     <option disabled>Empty</option>
                   @endforelse
                </select>
                </div>
                <div class="col-md-2 form-group">
                <select name="group" class="form-control" id="group-filter">
                   @forelse($data['groups'] as $key => $group)
                     @if ($key == 0)
                        <option value="">All Group</option>
                     @endif
                    <option value="{{$group->id}}" 
                     @if ($data['group'] == $group->id)
                       {{ 'selected' }}
                     @endif>{{ucwords($group->group_name)}}</option>
                   @empty
                     <option disabled>Empty</option>
                   @endforelse
                </select>
             </div>
               <div class="col-md-2 form-group">
                <select name="distric" class="form-control" id="distric-filter">
                   @forelse($data['districs'] as $key => $distric)
                     @if ($key == 0)
                        <option value="">All District</option>
                     @else
                        <option @if(Request::get('distric') == $distric->id) {{'selected'}} @endif value="{{$distric->id}}">{{ucwords($distric->distric)}}</option>
                     @endif
                   @empty
                     <option disabled>Empty</option>
                   @endforelse
                </select>
             </div>
          
             <div class="col-md-2">
              <button type="submit" id="filter_data" class="btn btn-md btn-primary">Filter</button>&nbsp;&nbsp;
              <button type="button" id="reset_filter" class="btn btn-md btn-default">Reset</button>
          </div>
          </form>
          <div class="col-md-3 pull-left">
            <a href="javascript:void(0);" id="export_excel" data-type="xls" class="exportreport btn btn-default">Export Excel</a>
          <a href="javascript:void(0);" id="export_csv" data-type="csv" class="exportreport btn btn-default">Export CSV</a>
          </div>
          <div class="col-xs-1  col-md-1 pull-right">
              <a href="{{ route('user/create')}}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add</a>
          </div>
        </div>
      </div>
        @endcan
        
        <div class="col-xs-12">
          <div class="box box-solid">
            <div class="box-header">
             {{--  <h3 class="box-title">Data Table With Full Features</h3> --}}
            </div>
            <!-- /.box-header -->
            <div class="box-body">
             {!! $dataTable->table(['class' => 'table table-sm table-striped table-hover', 'id' => 'userTable']) !!}
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
      <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">
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
  
      <!-- datatable script -->
      <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
      <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
      <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
      <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
      <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
      <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
      
      {!! $dataTable->scripts() !!}

      <script type="text/javascript">
          $(document).ready(function(){
          $(document).on('click','.btn-dlt',function(){
              let id = $(this).attr('data-id');
              let click = $(this);
              swal({
              title: "Are you sure?",
              text: "You will not be able to recover this user!",
              type: "warning",
              showCancelButton: true,
              confirmButtonClass: "btn-danger",
              confirmButtonText: "Yes, delete it!",
              closeOnConfirm: false
              },
              function(){
                 $.ajax({
                    "headers":{
                    'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                    },
                    'type':'DELETE',
                    'url' : '{{ route("user/delete") }}',
                    'data' : { method : '_DELETE' , id : id},
                    'beforeSend': function() {
                     $('.confirm').attr("disabled","disabled");
                     },
                    'success' : function(response){
                        if(response.status){
                              $('#userTable').DataTable().draw(false);
                             swal("Good job!", response.message , "success");
                        }else{
                             swal(response.message , "danger");
                        }
                  },
                  'complete': function() {
                  $('.confirm').removeAttr("disabled","disabled");
               }
               });
              });
            });

          $('body').on('click' , '.btn-status' , function(e){
           e.preventDefault();
           let id = $(this).attr('data-id');
                 swal({
            title: "Are you sure?",
            text: "Are you sure to change the status of this user!",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-success",
            confirmButtonText: "Yes, change it!",
            closeOnConfirm: false,
            },
        function(){
            $.ajax({
            "headers":{
            'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
            },
            'type':'post',
            'data' : { id : id},
            'url' : '{{ route("user/status")}}',
            'beforeSend': function() {
               $('.confirm').attr("disabled","disabled");
               },
            'success' : function(response){
            if(response.status){
              swal("Good job!", response.message , "success");
              $('#userTable').DataTable().draw(false);
            }else{
            swal(response.message , "danger");
            }
            },
            'error' : function(error){
            console.log(error);
            },'complete': function() {
                  $('.confirm').removeAttr("disabled","disabled");
               }
            });
          });

          });
        });
          
          //Filter Records  
          $('body').on('click' , '#filter_data', function(e){
              $(this).submit();
          });

          //Reset filter
          $(document).on('click','#reset_filter',function(){
            window.location.href= '{{ route('user/index') }}';
          });

          //for export to excel/csv
          $('.exportreport').click(function () {
            type = $(this).data('type');
            var param_str = $('#filter-form').serialize();
            param_str+= '&type='+type;  
            url = '{{ route("user/exportUsers") }}'+'?'+param_str;
            window.location = url;
          });

        </script>
    @endsection
