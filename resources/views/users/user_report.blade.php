@extends('layouts.app')
  @section('content')   
   <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Users Report
      </h1>
       {{ Breadcrumbs::render('users-report') }}
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

        @can('create', App\User::class)
          <div class="row">
          <div class="col-md-12  add-btn-div">
          <form action="{{route('user/userReport')}}" method="GET" id="filter-form">
            <div class="col-md-2 form-group">
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
             <div class="col-md-2">
              <button type="submit" id="filter_data"  class="btn btn-sm btn-primary">Filter</button>&nbsp;&nbsp;
              <button type="button" id="reset_filter" class="btn btn-sm btn-default">Reset</button>
          </div>
          </form>
          <div class="col-md-6 pull-right text-right">
            <a href="javascript:void(0);" id="export_excel" data-type="xls" class="exportreport btn btn-default">Export Excel</a>
          <a href="javascript:void(0);" id="export_csv" data-type="csv" class="exportreport btn btn-default">Export CSV</a>
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
             {!! $dataTable->table(['class' => 'table table-sm table-striped table-hover', 'id' => 'userReport']) !!}
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
     
      
      {!! $dataTable->scripts() !!}

      <script type="text/javascript">
          $(document).ready(function(){
          $(document).on('click','.btn-dlt',function(){
              let id = $(this).attr('data-id');
              let click = $(this);
              swal({
              title: "Are you sure?",
              text: "Your will not be able to recover this user!",
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
                    'data' : { method : '_DELETE' , id : '34344'},
                    'success' : function(response){
                        if(response.status){
                              $('#userTable').DataTable().draw(false);
                             swal("Good job!", response.message , "success");
                        }else{
                             swal(response.message , "danger");
                        }
                  },
               });
              });
            });

          $('body').on('click' , '.btn-status' , function(e){
           e.preventDefault();
           let id = $(this).attr('data-id');
            $.ajax({
            "headers":{
            'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
            },
            'type':'post',
            'data' : { id : id},
            'url' : '{{ route("user/status")}}',
            'success' : function(response){
            if(response.status){
            alert(response.message);
              $('#userTable').DataTable().draw(false);
            }else{
            alert(response.message);
            }
            },
            'error' : function(error){
            console.log(error);
            }
            });

          });
        });

          // $('body').on('change' , '#role-filter', function(e){
          //     $('#filter-form').submit();
          // });

          // $('body').on('change' , '#group-filter', function(e){
          //     $('#filter-form').submit();
          // });

        //Filter Records  
        $(document).on('click','#filter_data',function(){
          $('#filter-form').submit();
        });
        
        //Reset filter
        $(document).on('click','#reset_filter',function(){
          window.location.href= '{{ route('user/userReport') }}';
        });  


        //for export to excel/csv
        $('.exportreport').click(function () {
          type = $(this).data('type');
          var param_str = $('#filter-form').serialize();
          param_str+= '&type='+type;  
          url = '{{ route("user/exportUserReport") }}'+'?'+param_str;
          window.location = url;
        });


        </script>
    @endsection
