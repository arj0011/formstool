@extends('layouts.app')
@section('content') 
<!-- Content Header (Page header) -->
<style>
    .view-template{
        background-color: #00c0ef;
        color: #fff;
        margin-right: 3px; 
    }
    .edit-del {
        padding: 6px 7px;
    }
    .edit-del .fa {
        font-size: 16px;
    }
</style>
    <section class="content-header">
       @isset($page_title)<h1>{{$page_title}}</h1>@endisset 
      <small>@if($group_name)Group: {{$group_name}} @endif </small>
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
                   <th>Form Type</th>
                  <th>Form Group</th>
                    <th>Schedule Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Record Accept Status</th>
                    {{-- <th>Your Resubmission Reason</th> --}}
                    {{-- <th>admin Resubmission Reason</th> --}}
                    <th style="width: 80px;">Actions</th>
                    <th style="display: none;">Created at</th>  
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
        <!-- /.col -->
      </div>
      <!-- /.row -->
      </div>
 
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
               'type':'get',
              'url' : '{{ url("admin/form/delete/") }}'+'/'+form_id,
              'success' : function(response){
                  if(response.status)
                  {
                   swal("Good job!", response.message ,"success");
                   click.closest('tr').remove();
                   
                  }else{
                    swal(response.message ,"danger");  
                    
                  }
              
               },
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
        "order": [[ 10, 'desc' ]],
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
                  },
                "dataSrc":function(json){

                  console.log(json.data);

                  for(var i=0, ien = json.data.length; i<ien; i++){
                            name         = json.data[i]['name'];
                            id           = json.data[i]['id'];
                            form_id      = json.data[i]['form_id'];
                            is_table     = json.data[i]['is_table'];
                            is_submit    = json.data[i]['submit_status'];
                            form_type    = json.data[i]['form_type'];
                            schedule_id  = json.data[i]['schedule_id'];
                            user_submission_request  = json.data[i]['user_submission_request'];
                            admin_resubmission_request = json.data[i]['admin_resubmission_request'];
                            user_request_status = json.data[i]['user_request_status'];
                            record_accept_status = json.data[i]['record_accept_status'];
                            action       = "";

                      if(parseInt(json.data[i]['status'])==1){
                              json.data[i]['status']='<span style="color:green" class="l">Active</span>';
                      }else{ 
                              json.data[i]['status']='<span style="color:red" class="">Inactive</span>';
                      }

                     if(is_submit == null || is_submit == '' || is_submit == 0){
                        action+='<a href="{{route('form/submit')}}?form_id='+btoa(form_id)+'&schedule_id='+btoa(schedule_id)+'"  data-toggle="tooltip" title="Submit Form!" class="submit-template btn btn-flat btn-xs btn-block btn-success">Submit</a> ';
                     }


                      if(is_submit == 1 || is_submit == 2){
                          if(form_type == 'Vertical'){
                            action+='<a href="{{url('show-data?form_id=')}}'+btoa(form_id)+'&schedule_id='+btoa(schedule_id)+'" data-toggle="tooltip" title="View record!" class="submit-template btn btn-flat  btn-block  btn-xs btn-info">&nbsp;&nbsp;&nbsp;view&nbsp;&nbsp;&nbsp;</a> ';
                          }else{
                           action+='<a href="{{url('admin/form/viewTabularData/')}}/'+btoa(form_id)+'?form_id='+btoa(form_id)+'&schedule_id='+btoa(schedule_id)+'"  data-toggle="tooltip" title="View record!" class="submit-template btn  btn-block  btn-flat btn-xs btn-info">&nbsp;&nbsp;&nbsp;view&nbsp;&nbsp;&nbsp;</a> ';
                          }
                      }
                        
                      if(is_submit == 1 && user_submission_request == 0){
                          action+='<button form-id="'+btoa(form_id)+'" schedule-id="'+btoa(schedule_id)+'" data-toggle="tooltip" title="Request for Resubmission!" class="submit-template btn  btn-block  btn-flat btn-xs btn-danger resubmission">Request for Re-Submission</button> ';
                      }
                   
                      if(is_submit == 2 && user_submission_request == 2 || admin_resubmission_request == 1){
                          action+='<a href="{{route('form/submit')}}?form_id='+btoa(form_id)+'&schedule_id='+btoa(schedule_id)+'"  data-toggle="tooltip" title="Submit Form!" class="submit-template btn  btn-block  btn-flat btn-xs btn-success">Re-submit</a> ';
                      }

                       if(user_submission_request == 1 ){
                        action+='<button class="submit-template btn btn-flat btn-xs  btn-block  btn-warning">Re-submittion request is pending</button> ';
                       }
                  
                     json.data[i]['action']=action;

                     }  
                             return json.data;
                  }
                    },
            columns:[
                {
      "data":null, // can be null or undefined
      },{
                  data:'name', name:'name',
                  render: function (data, type, column, meta){
                               return '<p style="text-transform: capitalize;">'+column.name+'<p>' ;
                             }
                },
                {data:'form_type', name:'form_type'},
                {
                   data:'group_name', name:'group_name',
                   render:function(data, type, column, meta){

                               return '<p style="text-transform: capitalize;">'+column.group_name+'<p>' ;
                             }
                },
                {data:'schedule_name', name:'schedule_name'},
                {data:'start_date', name:'start_date'},
                {data:'end_date', name:'end_date'},
                {data:'status',name:'status'},
                {data:'record_accept_status',name:'record_accept_status',
                  render:function(data, type, column, meta){

                      if(column.record_accept_status == 1 && column.submit_status == 1 && column.user_submission_request == 0){
                        return '<p style="text-transform: capitalize;"  class="text-success">Accepted Record<p>' ;
                       }else if(column.submit_status == 1 && column.record_accept_status == 0)
                       {
                          return '<p style="text-transform: capitalize;"  class="text-danger">Pending<p>' ;
                       }else{
                          return '-';
                       }
                               
                             }
              },
              // {
              //     data : 'user_resubmission_reason' , name : 'user_resubmission_reason' , render : function(data,typt,column,meta){
              //             if(column.user_resubmission_reason != '' && column.user_resubmission_reason != null){
              //                  return column.user_resubmission_reason;
              //             }else{
              //                  return '-';
              //             }
              //     }  
              // },
              // {
              //     data : 'admin_resubmission_reason' , name : 'admin_resubmission_reason' , render : function(data,typt,column,meta){
              //             if(column.admin_resubmission_reason != '' && column.admin_resubmission_reason != null){
              //                  return column.admin_resubmission_reason;
              //             }else{
              //                  return '-';
              //             }
              //     }  
              // },
                {data:'action',name:'action','searchable':false},
                {data:'created_at',name:'created_at','visible':false}
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
      $('#roles-div').hide();
      $('#users-div').hide();

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
            'success' : function(response){
              if(response.status){
              swal("Good job!", response.message , "success");
              $("#publishModal").modal('hide');
              }else{
              swal(response.message , "danger");
              }
            },
            'error' : function(error){
            alert('Something went wrong');                    }
            });
          }

            alert('Something went wrong');
        }

     });

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