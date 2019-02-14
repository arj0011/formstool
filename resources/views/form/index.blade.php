@extends('layouts.app')
@section('content') 
<!-- Content Header (Page header) -->
<style>
    .view-template{
        background-color: #00c0ef;
        color: #fff;
        margin-right: 3px; 
    }
    .edit-del{
        padding: 6px 7px;
    }
    .edit-del .fa{
        font-size: 16px;
    }
</style>
    <section class="content-header">
      <h1>
       @isset($page_title) {{$page_title}} @endisset
      </h1>
      
      @if(Request::segment(3))
         @isset($group_name)<h4> Group: {{$group_name}} </h4>@endisset
      @endif
       {{ Breadcrumbs::render('forms') }}
    </section>
   <!-- Main content -->
    <section class="content">
      <div class="row">
         @if(Session::has('status'))
            <div class="col-xs-12 alert-message-div">
                <div class="alert @if(Session::get('status')) alert-success @else alert-danger @endif alert-dismissible" role="alert">
                  <strong>@if (Session::get('status')) Success @else Danger @endif!</strong> {{Session::get('msg')}}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
                </div>
            </div>
        @endif
        @can('create', App\Form::class)
          <div class="col-xs-12" style="margin-bottom: 7px;">
          <a  class="btn btn-success pull-right" href="{{route('form/create')}}"><i class="fa fa-plus"></i> <b>New</b></a>
          </div>
        @endcan
        <div class="col-xs-12">
          <div class="box box-solid">
            <div class="box-header">
              <h3 class="box-title"></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="forms-table" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Sr.No</th>
                  <th>Form</th>
                  <th>Group</th>
                  <th>Form Type</th>
                  <th>Status</th>
                 @if(base64_decode(Request::get('type'))=='submissions')
                         @can('viewSubmission',App\FormTable::class)
                                <th>Submissions</th>
                          @endcan
                        @else <th>Actions</th>
                  @endif
                  </tr>
                </thead>
                <tbody>
              
              </tbody>
            </table>
            </div>
            <!-- /.box-body -->
          </div>

           <!-- Modal -->
  <div class="modal fade" id="publishModal" role="dialog">
  <div class="modal-dialog modal-md">
   <form class="form" action="{{ route('form/publish-store') }}" method="POST" id="publish-form">
    <input type="hidden" name="id" value="" id="form-id">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
          <div class="row">
             <div class="col-md-4">
                <div class="form-group">
                   <input type="checkbox" name="all_role" id="all-role" value="_all"> All Role
                </div>
             </div>
             <div class="col-md-4">
                <div class="form-group">
                   <input type="checkbox" name="specific_role" id="specific-role" value="_specific_role"> Specific Role
                </div>
             </div>
             <div class="col-md-4">
                <div class="form-group">
                   <input type="checkbox" name="specific_user" id="specific-user" value="_specific_user"> Specific User
                </div>
             </div>
          </div>
          <div class="row" id="roles-div">
           <div class="col-md-12">
             <div class="form-group">
               <label for="roles">Select Role</label>
               <select class="form-control" name="roles[]" id="roles" multiple="multiple">
                  @forelse($roles as $role)
                   <option value="{{$role->id}}">{{ucwords($role->name)}}</option>
                 @empty
                  <option value="">No any role</option>
                 @endforelse
               </select>
             </div>
           </div>
          </div>
          <div class="row" id="users-div">
           <div class="col-md-12">
             <div class="form-group">
               <label for="users">Select Users</label>
               <select class="form-control" name="users[]" id="users" multiple="multiple">
                  @forelse($users as $user)
                   <option value="{{$user->id}}">{{ucwords($user->first_name)}}</option>
                 @empty
                  <option value="">No any role</option>
                 @endforelse
               </select>
             </div>
           </div>
          </div>

           <div class="row" id="schedule-div">
           <div class="col-md-6">
             <div class="form-group">
               <label for="users">Start Date</label>
               <input type="text" name="start_date" class="form-control" id="start-date">
             </div>
           </div>
           <div class="col-md-6">
             <div class="form-group">
                <label for="users">End Date</label>
                <input type="text" name="end_date" class="form-control" id="end-date">
             </div>
           </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-success">Publish</button>
        </div>
      </div>
      </form>
    </div>
  </div>

        <!-- /.col -->
      </div>
        <div class="col-md-12 text-right" style="margin-bottom: 10px;">
          <a class="btn btn-default" style="border-radius: 10px; background: #ffffff ; padding-left: 20px;padding-right: 20px;" href="{{url()->previous()}}">Back</a>
        </div>
      <!-- /.row -->
      </div>
 <!-- Change Request Model -->
    <!-- Modal -->
  <div class="modal fade" id="changeRequestModal" role="dialog">
  <div class="modal-dialog modal-md">
   <form class="form" action="{{ route('form/changeRequest') }}" method="POST" id="changeRequest-form">
   {{csrf_field()}}
     <input type="hidden" name="form_id" value="" id="form-id">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"> Send a Request For Edit The Form </h4>
        </div>
        <div class="modal-body">
          <div class="row">
           <div class="col-md-12">
              <div class="col-md-6">
                <div class="form-group">
                   <label for="request-message">Request Message</label>
                    <textarea name="message" class="form-control rounded-0" id="request-message"></textarea>
                 </div>
              </div>
           </div>
          </div>
         </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-success">Submit</button>
        </div>
      </div>
      </form>
    </div>
  </div>
    <!-- End Change Request-->
  </section>
 <!--section-->
@endsection
@section('css-script')
<!-- data tables script -->
<link rel="stylesheet" type="text/css" href="{{ asset('public/bootstrap/css/dataTables.bootstrap.min.css')}}"/>
<link rel="stylesheet" type="text/css" href="{{ asset('public/bootstrap/css/bootstrap-multiselect.css')}}"/>
<link rel="stylesheet" type="text/css" href="{{ asset('public/bootstrap/css/bootstrap-datepicker.min.css')}}"/>

<style type="text/css">
<style>
.entry:not(:first-of-type)
{
margin-top:10px;
}
.glyphicon
{
font-size:12px;
}
 .row-col-title{
    background-color: #3c8dbc;
    padding: 8px 10px;
    color: #fff;
    font-weight: bold;
   }
.show{
      display: block;
   }
.hide{
      display: none;
   }
.btn-group,
.multiselect {
  width: 100%;
}

.multiselect {
  text-align: left;
  padding-right: 32px;
  overflow: hidden;
  text-overflow: ellipsis;
}

.multiselect .caret {
  right: 12px;
  top: 45%;
  position: absolute;
}

.multiselect-container.dropdown-menu {
    min-width: 0px;
}

.multiselect-container>li>a>label {
    white-space: normal;
    padding: 5px 15px 5px 35px;
}

.multiselect-container > li > a > label > input[type="checkbox"] {
    margin-top: 3px;
}

</style>
@endsection 
@section('js-script')
<!-- page  js scripts-->
<!-- jQuery 3 -->
<script src="{{ asset('public/bootstrap/js/jquery.min.js')}}"></script>
<script src="{{ asset('public/bootstrap/js/jquery.dataTables.min.js')}}"></script>
     <!-- Bootstrap DataTables -->
<script src="{{ asset('public/bootstrap/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{ asset('public/bootstrap/js/bootstrap-multiselect.js')}}"></script>
{{-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> --}}
<script src="{{ asset('public/bootstrap/js/bootstrap-datepicker.min.js')}}"></script>

<script>
 $(document).ready(function(){


  //Date picker start date
  $('#start-date').datepicker({
      autoclose: true,
      dateFormat: 'dd-mm-yy',
      minDate: 0,
      numberOfMonths: 1,
      onSelect: function(selected) {
        $("#end-date").datepicker("option","minDate", selected)
      }
  });

  //Date picker end date
  $('#end-date').datepicker({
      autoclose: true,
      dateFormat: 'dd-mm-yy',
      minDate: 0,
      numberOfMonths: 1,
      onSelect: function(selected){
           $("#start-date").datepicker("option","maxDate", selected)
        }
  });

 $(document).on('click','.delete-res',function(){
    form_id=$(this).attr('data-id');
     var click = $(this);
      swal({
            title: "Are you sure?",
            text: "You will not be able to recover this form!",
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
               'type':'get',
              'url' : '{{ url("admin/form/delete/") }}'+'/'+form_id,
              'beforeSend': function() {
               $('.confirm').attr("disabled","disabled");
               },
              'success' : function(response){
                  if(response.status)
                  {
                   swal("Good job!", response.message ,"success");
                   click.closest('tr').remove();
                   
                  }else{
                    swal(response.message ,"danger");  
                    
                  }
              
               },'complete': function() {
                  $('.confirm').removeAttr("disabled","disabled");
               }
            });
          });
   
   
 });

 // ajax Form data table
    var t=$('#forms-table').DataTable({
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
                "url":"{{url('admin/ajax_Form')}}",
               "data": function(d){
                   d.group_id ="{{$group_id}}";
                   d.type     = "{{Request::get('type')}}";
                  },
                "dataSrc":function(json){

                  for(var i=0, ien = json.data.length; i<ien; i++){
                            name         = json.data[i]['name'];
                            id           = json.data[i]['id'];
                            is_table     = json.data[i]['is_table'];
                            is_submitted = json.data[i]['is_submitted'];
                            action="";

                     if(parseInt(json.data[i]['status'])==1){
                              json.data[i]['status']='<span style="color:green" class="l">Active</span>';
                      }else{ 
                              json.data[i]['status']='<span style="color:red" class="">Inactive</span>';
                      }

                    role="{{Auth::user()->role}}";
                    if(role==1){
                         action+='<a  target="_blank" href="{{route('form/template')}}?form_id='+btoa(id)+'"  data-toggle="tooltip" title="Submit Form!" class="submit-template btn btn-flat btn-md btn-info">View Template</a> ';
                       }
                    else{
                      action+='<a  target="_blank" href="{{url('admin/form/viewTabularData/')}}/'+btoa(id)+'" data-toggle="tooltip" title="View Submitted Data!" class="view-template btn btn-info">View</a>';
                     if(is_submitted) action+='<a  disabled target="_blank" href="javascript::void(0)" data-toggle="tooltip" title="Submit Form!" class="submit-template btn btn-xs btn-success">Submit</a> '
                        else action+='<a  target="_blank" href="{{url('admin/form/view/')}}/'+btoa(id)+'" data-toggle="tooltip" title="Submit Form!" class="submit-template btn btn-xs btn-success">Submit</a> ';
                      }
                    @can('update', App\Form::class) 
                      action += '<a href="{{url('admin/form/edit')}}/'+btoa(id)+'" data-toggle="tooltip" title="Edit Form!" class="edit-del btn btn-xs btn-primary edit-categ"><i  class="fa fa-edit"></i></a> ';
                     @endcan
                    @can('delete', App\Form::class)
                        action += ' <a href="#" data-id="'+id+'"  id="delete-categ" data-toggle="tooltip" title="Delete!" class="edit-del btn btn-xs btn-danger delete-res"><i class="fa fa-trash"></i></a>';
                    @endcan
                    @can('create', App\UserRequest::class) 
                         action+='<a style="padding: 6px 5px;" target="_blank"  data-id="'+btoa(id)+'" href="{{url('admin/form/change_request')}}/'+btoa(id)+'" data-toggle="tooltip" title="Form Change Request!" class="btn  btn-xs btn-primary change-request">Request For Change</a> ';
                    @endcan
                     json.data[i]['action']=action;

                     }  
                             return json.data;
                  }
                    },
            columns:[
                {
      "data":null, // can be null or undefined
      },
                {
                   data:'name', name:'name',
                   render: function (data, type, column, meta) {
                               return '<p style="text-transform: capitalize;">'+column.name+'<p>' ;
                             }
                },
                {
                   data:'group_name', name:'group_name',
                   render: function (data, type, column, meta) {
                               return '<p style="text-transform: capitalize;">'+column.group_name+'<p>' ;
                             }
                },
                {data:'form_type', name:'form_type'},
                {data:'status',name:'status'},
                @if(base64_decode(Request::get('type'))=='submissions')

                @can('viewSubmission',App\FormTable::class)
                { 
                            data : 'submissions' ,
                            name : 'submissions' , 
                            render:function (data, type, column, meta){
                              if(column.is_table){
                                return '<a href="{{url('admin/submissions/?id=')}}'+btoa(column.id)+'"><span class="badge badge-light">'+column.submissions+'</span> &nbsp;&nbsp;view submission</a>' ;
                                  }else if(column.form_type=="Tabular"){
                                    return '<a href="{{url('admin/submissions/?id=')}}'+btoa(column.id)+'"><span class="badge badge-light">'+column.submissions+'</span> &nbsp;&nbsp;view submission</a>' ;
                                  }
                            }
                },
                @endcan

                @endif
              
    @if(base64_decode(Request::get('type'))!='submissions' || auth::user()->role!=1){data:'action', name:'action',searchable:false},@endif
                ]
    });
     t.on('draw.dt',function(){
    var PageInfo=$('#forms-table').DataTable().page.info();
         t.column(0, { page: 'current' }).nodes().each( function (cell, i) {
            cell.innerHTML = i + 1 + PageInfo.start;
        } );
    });
   
   });
</script>
<script type="text/javascript">
$('document').ready(function(){
    // All Role
    $('#all-role').on('click',function(e){
       
       if($(this).prop('checked')){
             
             $('#specific-role').prop('checked' , false);
             $('#specific-user').prop('checked' , false);

             $('#roles-div').hide();
             $('#users-div').hide();

             $('#roles').removeAttr('name');
             $('#users').removeAttr('name');

       }

    });

    // Specific Role
     $('#specific-role').on('click',function(e){
       
       if($(this).prop('checked')){
             $('#all-role').prop('checked' , false);
             $('#roles-div').show();
             $('#roles').attr('name','roles[]');
             $('#roles-div #roles').multiselect({
               includeSelectAllOption : true,
             enableFiltering          : true,
       enableCaseInsensitiveFiltering : true,
                maxHeight             : 400
             });
       }else{
             $('#roles-div').hide();
             $('#roles').removeAttr('name');
       }

    });

     // Specific User
     $('#specific-user').on('click',function(e){
       
       if($(this).prop('checked')){
             $('#all-role').prop('checked' , false);
             $('#users-div').show();
             $('#users').attr('name','users[]');
             $('#users-div #users').multiselect({
               includeSelectAllOption : true,
                      enableFiltering : true,
       enableCaseInsensitiveFiltering : true,
                            maxHeight : 400
             });
       }else{
             $('#users-div').hide();
             $('#users').removeAttr('name');
       }

    });

   });

     $('#publish-form').on('submit',function(e){
           e.preventDefault();

          let form = $(this);
          let data = form.serialize();

          $.ajax({
            "headers":{
            'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
            },
            'type':'POST',
            'url' : form.attr('action'),
            'data' : data,
             'beforeSend': function() {
               $('.confirm').attr("disabled","disabled");
               },
            'success' : function(response){
                if(response.status){
                   swal("Good job!", response.message , "success");
                   $("#publishModal").modal('hide');
                }else{
                   swal(response.message , "danger");
                  }
          },
          'error' : function(error){
            alert('Something went wrong');
          },'complete': function() {
                  $('.confirm').removeAttr("disabled","disabled");
               }
        });

          // fetch assing form users
        
        function getFormAssignUsers($id = ''){
          if(id){
            $.ajax({
              "headers":{
              'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
              },
            'type':'POST',
            'url' : '{{ url('form/assign/form/users/') }}',
            'data' : data,
             'beforeSend': function() {
               $('.confirm').attr("disabled","disabled");
               },
            'success' : function(response){
              if(response.status){
              swal("Good job!", response.message , "success");
              $("#publishModal").modal('hide');
              }else{
              swal(response.message , "danger");
              }
            },
            'error' : function(error){
            alert('Something went wrong');
             },'complete': function() {
                  $('.confirm').removeAttr("disabled","disabled");
               },
            });
          }

            alert('Something went wrong');
        }

     });

  </script>
@endsection