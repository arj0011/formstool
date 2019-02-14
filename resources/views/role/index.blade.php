@extends('layouts.app')
@section('content')  
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
         User's Role
        <small></small>
      </h1>
        {{ Breadcrumbs::render('roles') }}
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
        
        @can('create', App\Role::class)
          <div class="col-xs-12" style="margin-bottom: 7px;">
          <a  class="btn btn-success pull-right" href="{{url('admin/role/add/')}}"><i class="fa fa-plus"></i> <b>New</b></a>
          </div>
        @endcan
        <div class="col-xs-12">
            
        <div class="box box-solid">
            <div class="box-header">
              <h3 class="box-title"></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="role-table" class="table table-bordered table-striped table-hover table-condensed">
                <thead>
                <tr>
                  <th>Sr.No</th>
                  <th>Name</th>
                  <th>Status</th>
                  @if (auth::user()->can('update' , App\Role::class) || auth::user()->can('delete' , App\Role::class))
                     <th>Actions</th>
                  @endif
                </tr>
                </thead>
                <tbody>
              
              </tbody>
            </table>
            </div>
            <!-- /.box-body -->
          </div>
       
        <!-- /.col -->
      </div>
      <!-- /.row -->
      </div>
    </section>
    <!-- section  -->
@endsection

@section('js-script')
<!-- page  js scripts-->
<!-- jQuery 3 -->
<script src="{{ asset('public/bootstrap/js/jquery.dataTables.min.js')}}"></script>
     <!-- Bootstrap DataTables -->
 <script src="{{ asset('public/bootstrap/js/dataTables.bootstrap.min.js')}}"></script>

<script>

 $(document).ready(function(){
  
   $(document).on('click','.delete-res',function(){
    role_id=$(this).attr('data-id');
    // var res=confirm("Are you sure to delete this role ?");
    swal({
        title: "Are you sure?",
        text: "You will not be able to recover this role!",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes, delete it!",
        closeOnConfirm: false,
    },
    function(){

      $.ajax({
              "headers":{
              'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
              },
              'type':'get',
              'url' : '{{ url("admin/role/delete/") }}'+'/'+role_id,
               beforeSend: function() {
               $('.confirm').attr("disabled","disabled");
               },
              'success' : function(response){
                $('#role-table').DataTable().ajax.reload();
                if(response.status){
                  swal("Good job!", response.message , "success");
                }else{
                  swal(response.message , "danger");
                }
            },
             complete: function() {
                  $('.confirm').removeAttr("disabled","disabled");
               },
      });
    });
    // if(res)
    // {
    //  $(this).closest('tr').remove();
    //  deleteRole(role_id);   
    // }
 });


 
 // ajax role data table
    var t=$('#role-table').DataTable({
         "columnDefs": [ {
            "searchable": false,
            "orderable": false,
            "targets": 0
        } ],
        "order": [[ 1, 'asc' ]],
                "destroy": true,
                "bPaginate": true,
               "bLengthChange": true,
               "sPaginationType": "full_numbers",
               "bFilter": true,
               "bSort": true,
               "bInfo": true,
               "bAutoWidth": false,
               "processing": false,
               "serverSide": true,
               "stateSave": false,
               "stateSave": false,
               "pageLength": 10,
                processing: true,
                serverSide: true,
                ajax:{
                "url":"{{url('admin/ajax_Role')}}",
                "dataSrc":function(json){

                  for(var i=0, ien=json.data.length; i<ien; i++){
                            name=json.data[i]['name'];
                            id=json.data[i]['id'];
                          let status = json.data[i]['status'];
                              statusText = status == 1 ? 'ban' : 'check';
                          let statusName = status == 1 ? 'Inactive' : 'Active';
                            status = json.data[i]['status'];
                          @if(auth::user()->can('update' , App\Role::class) || auth::user()->can('delete' , App\Role::class))
                            action="";
                          @endif
                       if(parseInt(json.data[i]['status'])==1){
                              json.data[i]['status']='<span style="color:green" class="l">Active</span>';
                            }else{ 
                              json.data[i]['status']='<span style="color:red" class="">Inactive</span>';
                               }

                      @can('update' , App\Role::class)
                         action  ='<a href="{{url('admin/role/edit/')}}/'+btoa(id)+'" data-toggle="tooltip" title="Edit Role!" class="edit-categ btn btn-primary btn-xs"><i class="fa fa-edit"></i></a>';
                      @endcan
                      @can('status' , App\Role::class)
                         action += ' <a href="#" data-id="'+id+'" data-toggle="tooltip" title="'+statusName+'" class="btn btn-xs btn-default btn-status"><i class="fa fa-'+statusText+'"></i></a>';
                      @endcan
                      @can('delete' , App\Role::class)
                         action += ' <a href="#" data-id="'+id+'"  id="delete-categ" data-toggle="tooltip" title="Delete!" class="delete-res btn btn-xs btn-danger"><i class="fa fa-trash"></i></a>';
                      @endcan
                          
                      @if(auth::user()->can('update' , App\Role::class) || auth::user()->can('delete' , App\Role::class))
                        json.data[i]['action'] = action;
                      @endif

                        }  
                             return json.data;
                  }
                    },
            columns:[
                {
      "data":null, // can be null or undefined
      },
                {data:'name', name:'name'},
                {data:'status',name:'status'},
                @if(auth::user()->can('update' , App\Role::class) || auth::user()->can('delete' , App\Role::class))
                  {data:'action', name:'action',searchable:false}
                @endif
                ]
    });
     t.on('draw.dt',function(){
    var PageInfo=$('#role-table').DataTable().page.info();
         t.column(0, { page: 'current' }).nodes().each( function (cell, i) {
            cell.innerHTML = i + 1 + PageInfo.start;
        } );
    });
   
   });
function deleteRole(role_id)
 {
 
      $.ajax({
              "headers":{
                  'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                  },
               'type':'get',
              'url' : '{{ url("admin/role/delete/") }}'+'/'+role_id,
              'success' : function(response){
                  $('#role-table').DataTable().ajax.reload();
               },
            });
 }
 
 $('body').on('click' , '.btn-status' , function(e){
     e.preventDefault();

     let id = $(this).attr('data-id');
      swal({
            title: "Are you sure?",
            text: "Are you sure to change the status of this role!",
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
               'url' : '{{ route("role/status")}}',
                'beforeSend': function() {
                 $('.confirm').attr("disabled","disabled");
               },
               'success' : function(response){
                  if(response.status){
                       swal("Good job!", response.message , "success");
                       $('#role-table').DataTable().ajax.reload();
                  }else{
                      swal(response.message , "danger");
                  }
                },
                'error' : function(error){
                   console.log(error);
                },
                'complete': function() {
                  $('.confirm').removeAttr("disabled","disabled");
               },
      });
   });
     
 });

</script>
@endsection