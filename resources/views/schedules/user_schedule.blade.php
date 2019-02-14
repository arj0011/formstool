@extends('layouts.app')
  @section('content')   
   <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Scheduled Form
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

        @can('create', App\User::class)
       {{--    <div class="col-xs-3">
              <div class="form-group">
                <select class="form-control" name="filter">
                 @forelse($data['forms'] as $form)
                  <option value="{{$form->id}}">{{$form->name}}</option>
                 @empty
                  <option>Form 1</option>
                 @endforelse
                </select>
              </div> 
          </div> --}}
          <div class="col-xs-12 add-btn-div">
              <button class="btn btn-primary pull-right modal-Btn" btn-type="add"><i class="fa fa-plus"></i> Add</button>
          </div>
        @endcan
        
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
      </div>
      <!-- /.row -->
                 <!-- Modal -->
  <div class="modal fade" id="publishModal" role="dialog">
    <div class="modal-dialog modal-md">
    <form class="form" action="{{ route('schedule/store') }}" method="POST" id="schedule-form">
    <input type="hidden" name="id" value="" id="schedule-id">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
          <div class="row">
             <div class="col-md-4">
                <div class="form-group">
                   <input type="checkbox" name="schedule_for" id="all-role" value="_all_role"> All Role
                </div>
             </div>
             <div class="col-md-4">
                <div class="form-group">
                   <input type="checkbox" name="schedule_for" id="specific-role" value="_specific_role"> Specific Role
                </div>
             </div>
             <div class="col-md-4">
                <div class="form-group">
                   <input type="checkbox" name="schedule_for" id="specific-user" value="_specific_user"> Specific User
                </div>
             </div>
             <div class="col-xs-12">
                <span class="danger">
                  <strong class="text-danger" id="schedule_for_error"></strong>
               </span>
             </div>
          </div>

          <div class="row" id="form-group-div">
           <div class="col-md-12">
             <div class="form-group">
               <label for="roles">Form Group's</label>
               <select class="form-control" name="form_groups[]" id="form-groups" multiple="multiple" >
                  @forelse($data['formGroups'] as $formGroup)
                   <option value="{{$formGroup->id}}">{{ucwords($formGroup->group_name)}}</option>
                 @empty
                  <option value="">No any form</option>
                 @endforelse
               </select>
               <span class="danger">
                  <strong class="text-danger" id="form_groups_error"></strong>
               </span>
             </div>
           </div>
          </div>

          <div class="row" id="form-div">
           <div class="col-md-12">
             <div class="form-group">
               <label for="roles">Form's</label>
               <select class="form-control" name="forms[]" id="forms" multiple="multiple" >
               </select>
                <span class="danger">
                  <strong class="text-danger" id="forms_error"></strong>
               </span>
             </div>
           </div>
          </div>

          <div class="row" id="roles-div">
           <div class="col-md-12">
             <div class="form-group">
               <label for="roles">Role's</label>
               <select class="form-control" name="roles[]" id="roles" multiple="multiple" >
                  @forelse($data['roles'] as $role)
                   <option value="{{$role->id}}">{{ucwords($role->name)}}</option>
                 @empty
                  <option value="">No any role</option>
                 @endforelse
               </select>
                <span class="danger">
                  <strong class="text-danger" id="roles_error"></strong>
                </span>
             </div>
           </div>
          </div>

          <div class="row" id="role-groups-div">
           <div class="col-md-12">
             <div class="form-group">
               <label for="groups">Role Group</label>
               <select class="form-control" name="role_groups[]" id="role-groups" multiple="multiple" >
               </select>
                <span class="danger">
                  <strong class="text-danger" id="role_groups_error"></strong>
               </span>
             </div>
           </div>
          </div>
           <div class="row" id="users-div">
           <div class="col-md-12">
             <div class="form-group">
               <label for="users">Users</label>
               <select class="form-control" name="users[]" id="users" multiple="multiple" >
                @forelse($data['users'] as $key => $value)
                  <option value="{{$value->id}}">{{$value->first_name}}</option>
                @empty
                  <option value="" disabled>user not available</option>
                @endforelse
               </select>
                 <span class="danger">
                  <strong class="text-danger" id="users_error"></strong>
               </span>
             </div>
           </div>
          </div>
            <div class="row" id="schedule-div">
           <div class="col-md-12">
             <div class="form-group">
               <label for="users">Start Date</label>
               <input type="text" name="start_date" class="form-control" id="start-date" >
               <span class="danger">
                  <strong class="text-danger" id="start_date_error"></strong>
               </span>
             </div>
           </div>
         {{--   <div class="col-md-6">
             <div class="form-group">
                <label for="users">End Date</label>
                <input type="text" name="end_date" class="form-control" id="end-date" disabled>
                <span class="danger">
                  <strong class="text-danger" id="end_date_error"></strong>
               </span>
             </div>
           </div> --}}
           <div class="col-md-12">
             <span class="danger">
                  <strong class="text-danger" id="invalid_time_error"></strong>
               </span>
           </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-success">Schedule</button>
        </div>
      </div>
      </form>
    </div>
  </div>
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
        $(document).ready(function(){

            $("#start-date").datepicker({
                format: 'dd/mm/yyyy',
                defaultViewDate: new Date(),
                todayHighlight : true,
                minDate: new Date(),
                numberOfMonths: 1,
                onSelect: function(selected) {
                   $("#end-date").datepicker("option","minDate", selected);
                }
            });

          // $('#start-date').on('change',function(e){
          //     console.log(e.target.value);
          //     console.log(moment(e.target.value).format('MM/DD/YYYY'));
          // });

            $('#form-div').hide();
            $('#role-groups-div').hide(); 
            $('#users-div').hide();
            $('#roles-div').hide();
            $('#all-role').prop('checked' , false);
            $('#specific-role').prop('checked' , false);
            $('#specific-user').prop('checked' , false);

            // All Role
            $('#all-role').on('click',function(e){
             
               if($(this).prop('checked')){
                     
                     $('#specific-role').prop('checked' , false);
                     $('#specific-user').prop('checked' , false);

                     $('#roles-div').hide();
                     $('#users-div').hide();

                     $('#roles').removeAttr('name');
                     $('#role-groups').removeAttr('name');
                     $('#users').removeAttr('name');

               }

            });

                // Specific Role
                 $('#specific-role').on('click',function(e){
                   
                   if($(this).prop('checked')){
                         $('#all-role').prop('checked' , false);
                         $('#specific-user').prop('checked' , false);
                         $('#roles-div').show();
                         $('#users-div').hide();
                         $('#roles').attr('name','roles[]');
                         $('#role-groups').attr('name','role_groups[]');
                         $('#users').removeAttr('name');
                         $('#roles-div #roles').multiselect({
                           includeSelectAllOption : true,
                         enableFiltering          : true,
                   enableCaseInsensitiveFiltering : true,
                            maxHeight             : 400
                         });

                   }else{
                         $('#roles-div').hide();
                         $('#role-groups-div').hide();
                         $('#roles').removeAttr('name');
                         $('#role-groups').removeAttr('name');
                   }

                });

                // Specific User
               $('#specific-user').on('click',function(e){
                  
                 if($(this).prop('checked')){
                       $('#all-role').prop('checked' , false);
                       $('#specific-role').prop('checked' , false);
                       $('#users-div').show();
                       $('#roles-div').hide();
                       $('#role-groups-div').hide();
                       $('#users').attr('name','users[]');
                       $('#roles').removeAttr('name');
                       $('#role-groups').removeAttr('name');
                       $('#users-div #users').multiselect({
                         includeSelectAllOption : true,
                                enableFiltering : true,
                 enableCaseInsensitiveFiltering : true,
                                      maxHeight : 300
                       });
                 }else{
                       $('#users-div').hide();
                       $('#users').removeAttr('name');
                 }

              });

            var getFormGroups = function(){
                 let option = [];
                 @forelse($data['formGroups'] as $form_group)
                    option.push({label : '{{$form_group->group_name}}' , title : '{{$form_group->group_name}}' , value : '{{$form_group->id}}' });
                 @empty
                 @endforelse

                 return option;
            }

            var getRoles      = function(){
                 let option = [];
                 @forelse($data['roles'] as $role)
                    option.push({label : '{{$role->name}}' , title : '{{$role->name}}' , value : '{{$role->id}}' });
                 @empty
                 @endforelse

                 return option;
            }

            var getAuthGroups = function(){
                 let option = [];
                 @forelse($data['authrityGroups'] as $authrity_group)
                    option.push({label : '{{$authrity_group->group_name}}' , title : '{{$authrity_group->group_name}}' , value : '{{$authrity_group->id}}' });
                 @empty
                 @endforelse

                 return option;
            }

            $('body').on('click','.modal-Btn',function(){

                $('#users-div').hide();
                $('#form-div').hide();
                $('#role-groups-div').hide(); 
                $('#users-div').hide();
                $('#roles-div').hide();
                $('#all-role').prop('checked' , false);
                $('#specific-role').prop('checked' , false);
                $('#specific-user').prop('checked' , false);
                $('#start-date').val('');

                $('#form-groups').multiselect({
                 includeSelectAllOption : true,
                        enableFiltering : true,
         enableCaseInsensitiveFiltering : true,
                              maxHeight : 400
                });

                $('#roles-div #roles').multiselect({
               includeSelectAllOption : true,
             enableFiltering          : true,
       enableCaseInsensitiveFiltering : true,
                maxHeight             : 400
             });

              $('#users-div #users').multiselect({
               includeSelectAllOption : true,
             enableFiltering          : true,
       enableCaseInsensitiveFiltering : true,
                maxHeight             : 400
             });


              $('#form-groups').multiselect('dataprovider',getFormGroups());
              $('#roles-div #roles').multiselect('dataprovider',getRoles());

               let btnType = $(this).attr('btn-type');
               let action = '';

               if(btnType == 'edit'){
                   action = "{{ route('schedule/update') }}";
               }else{
                   action = "{{ route('schedule/store') }}";
               }

              if(btnType == 'edit'){
                 let id = $(this).attr('data-id');
                 getScheduleFormDetaile(id);
                 $('#schedule-id').attr('name','id');
                 $('#schedule-id').attr('value',id);
                $('.modal-title').html('Update schedule Form');
              }else{
                $('#schedule-id').removeAttr('name');
                $('#schedule-id').removeAttr('value');
                $('.modal-title').html('Schedule Form');
              }

              $('#schedule-form').attr('action',action);
              $('#schedule-div').show();
              $('#start-date').attr('name','start_date');
              $('#publishModal').modal('show');

            });

      /* Get Form's */
              $('#form-groups').on('change',function(e){
                    let ids = $(this).val();
                    if(ids.length > 0){
                       getForms(ids);
                       $('#form-div').show(); 
                    }else{
                       $('#form-div').hide(); 
                    }
              });

             
             
      /* Get Role Groups */
              $('#roles').on('change',function(e){
                   let ids = $(this).val();
                   if(ids.length > 0){
                      getRoleGroups(ids);
                      $('#role-groups-div').show(); 
                    }else{
                      $('#role-groups-div').hide(); 
                    }
              });

          /*********** schedule form submit **************/
          $('#schedule-form').on('submit',function(e){
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

                console.log(response);
                
                if(response.status == 'error'){

                    $.each(response.data, function (key, val) {
                        $("#" + key + '_error').text(val[0]);
                    });
                
                }

                if(response.status == 'invalidTime'){
                        $('#invalid_time_error').html(response.message);
                }

              if(response.status == 'success'){
                  $('#formGroupTable').DataTable().draw(false);
                 swal("Good job!", response.message , "success");
                   $("#publishModal").modal('hide');
                }

              if(response.status == 'failed'){
                 $("#publishModal").modal('hide');
                 swal(response.message , "danger");
                }

              },
              'error' : function(error){
                  console.log(error);
                  alert('Something went wrong');
              }
            });
          });
          
          
          /*************** Get Roles Groups *******************/
                 var getRoleGroups = function(ids = null , selectedOption = null){
                if(ids.length > 0){
                   $.ajax({
                    "headers":{
                    'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                    },
                    'type':'get',
                    'url' : '{{ route("roleGroup/getGroups") }}',
                    'data' : { ids : ids },
                    'success' : function(response){
                        if(response.status){
                            let html   = [];
                          ids.map(function(id){
                                let text = $("#roles option[value="+id+"]").text();
                             response.data.map(function(item){
                                 if(item.role_id == id){
                                     var select = false;
                                       if(selectedOption){
                                           selectedOption.map(function(value){
                                                if(value.group_id == item.id){
                                                   return select = true
                                                }
                                           });
                                       }
                                     html.push({label: ''+item.group_name+'', title: ''+item.group_name+'', value: ''+item.id+'' , selected : select});
                                 }
                             });
                            });
                                $('#role-groups').multiselect({
                                  includeSelectAllOption         : true,
                                  enableFiltering                : true,
                                  enableCaseInsensitiveFiltering : true,
                                  maxHeight                      : 400
                                });
                                $('#role-groups').multiselect('dataprovider',html);

                        }else{
                             alert('Something went wrong');
                        }
                  },
                      'error' : function(error){
                             console.log(error);
                      },
                   });
                      $('#role-groups-div').show();
                   }else{
                      $('#role-groups-div').hide();
                  }
              }
            /*************** Get Form's ***************/
             var getForms = function(ids = null , selectedOption = null){
                    if(ids.length > 0){
                       $('#form-div').show(); 
                    }else{
                       $('#form-div').hide(); 
                    }

                  if(ids.length > 0){
                      $.ajax({
                        "headers":{
                        'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                        },
                        'type':'get',
                        'url' : '{{ route("schedule/getForms") }}',
                        'data' : { ids : ids },
                          'success' : function(response){
                             if(response.status){
                                  let html = [];
                                  $('#forms').find('option').remove().end();
                                  let option = {};
                              ids.map(function(id){
                                    let text = $("#form-groups option[value="+id+"]").text();
                               response.data.map(function(item){
                                   if(item.group_id == id){
                                       var select = false;
                                       if(selectedOption){
                                           selectedOption.map(function(value){
                                                if(value.form_id == item.id){
                                                   return select = true
                                                }
                                           });
                                       }
                                      html.push({label: ''+item.name+'', title: ''+item.name+'', value: ''+item.id+'' , selected : select});
                                   }
                               });
                              });
                              $('#forms').multiselect({
                                includeSelectAllOption : true,
                                       enableFiltering : true,
                        enableCaseInsensitiveFiltering : true,
                                             maxHeight : 400
                              });
                               $('#forms').multiselect('dataprovider', html);

                            }else{
                               alert('Something went wrong');
                            }
                       },
                       'error' : function(error){
                        console.log(error);
                        },
                      });
                      $('#form-div').show();
                    }else{
                      $('#form-div').hide();
                }
              }

          /*************** Get Form Schedule Details ***********/
          function getScheduleFormDetaile(id = null){
             if(id != null){
               $.ajax({
                    "headers":{
                    'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                    },
                    'type':'get',
                    'url' : '{{ route("schedule/edit") }}',
                    'data' : { id : id },
                    'success' : function(response){

                             if(response.status){

                               let schedule_for            = response.data.schedule_data.schedule_for;
                               let start_date              = response.data.schedule_data.start_date;
                               let form_and_group_ids      = response.data.form_and_group_ids;
                               let role_and_group_ids      = response.data.role_and_group_ids;
                               let user_ids                = response.data.user_ids;

                               // Schedule For 
                               if(schedule_for == '_all_role'){
                                      $('#all-role').prop('checked', true);
                                      $('#specific-role').prop('checked', false);
                                      $('#specific-user').prop('checked', false);
                                }

                                if(schedule_for == '_specific_role'){
                                      $('#specific-role').prop('checked', true);
                                      $('#all-role').prop('checked', false);
                                      $('#specific-user').prop('checked', false);
                                      $('#roles-div').show();
                                }
                                
                                if(schedule_for == '_specific_user'){
                                      $('#all-role').prop('checked', false);
                                      $('#specific-role').prop('checked', false);
                                      $('#specific-user').prop('checked', true);
                                      $('#users-div').show();
                                }
                                
                                //Start Date
                                if(start_date){
                                      var date = new Date(start_date);
                                      let day   = date.getDate();
                                      let month = date.getMonth();
                                      let year  = date.getFullYear();
                                      $('#start-date').val(day+'/'+month+'/'+year);
                                }

                                /**************** form group script ***************/
                                let optionFormGroups = [];
                                @if ($data['formGroups'])
                                  @forelse ($data['formGroups'] as $form_group)
                                    var selecte = false;
                                     form_and_group_ids.map(function(item){
                                       if(item.group_id == '{{$form_group->id}}'){
                                         return  selecte = true;
                                       }
                                     });
                                      optionFormGroups.push({label: '{{$form_group->group_name}}', title: '{{$form_group->group_name}}', value: '{{$form_group->id}}' , selected : selecte});
                                  @empty 
                                     optionFormGroups.push({label: 'No available', title: 'No available', value: ''});
                                  @endforelse
                                @endif
                               $('#form-groups').multiselect('dataprovider', optionFormGroups);
                              
                              let formGroupSelectedValue = $('#form-groups').val();
                              let formGroupAllValue = [];
                              $('#form-groups option').each(function()
                              {  
                                 formGroupAllValue.push(this.value);
                              });
                              
                               if(form_and_group_ids.length > 0){
                                   getForms(formGroupAllValue, form_and_group_ids);
                               }

                              /******************** role group script ***********/
                            if(schedule_for == '_specific_role'){
                              let optionRoleGroups = [];
                                @if ($data['roles'])
                                  @forelse ($data['roles'] as $role)
                                    var selecte = false;
                                     role_and_group_ids.map(function(item){
                                       if(item.role_id == '{{$role->id}}'){
                                         return  selecte = true;
                                       }
                                     });
                                      optionRoleGroups.push({label: '{{$role->name}}', title: '{{$role->name}}', value: '{{$role->id}}' , selected : selecte});
                                  @empty 
                                     optionRoleGroups.push({label: 'No available', title: 'No available', value: ''});
                                  @endforelse
                                @endif
                               $('#roles').multiselect('dataprovider', optionRoleGroups);

                              let rolesSelectedValue = $('#roles').val();
                              let rolesAllValue         = [];
                              $('#roles option').each(function()
                              {  
                                 rolesAllValue.push(this.value);
                              });
                              
                               if(role_and_group_ids.length > 0){
                                   getRoleGroups(rolesAllValue, role_and_group_ids);
                               }
                             }

                             /**************** Specific User Scripts *************/
                             if(schedule_for == '_specific_user'){
                              let optionUsers = [];
                                @if ($data['users'])
                                  @forelse ($data['users'] as $user)
                                    var selecte = false;
                                     user_ids.map(function(item){
                                       if(item.user_id == '{{$user->id}}'){
                                         return  selecte = true;
                                       }
                                     });
                                      optionUsers.push({label: '{{$user->first_name}}', title: '{{$user->first_name}}', value: '{{$user->id}}' , selected : selecte});
                                  @empty 
                                     optionUsers.push({label: 'No available', title: 'No available', value: ''});
                                  @endforelse
                                @endif

                               $('#users').multiselect('dataprovider', optionUsers);

                             }


                        }else{
                             alert('Something went wrong');
                        }
                  },
                  'error' : function(error){
                         console.log(error);
                  },
               });
             }
             return false;
          }

          /******************** schedule delete script ***************************/

              $(document).on('click','.btn-dlt',function(){
              var id = $(this).attr('data-id');
              var click = $(this);
              swal({
              title: "Are you sure?",
              text: "Your will not be able to recover this schedule!",
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
                    'url' : '{{ route("schedule/delete") }}',
                    'data' : { method : '_DELETE' , id : id},
                    'success' : function(response){
                        if(response.status){
                              $('#formGroupTable').DataTable().draw(false);
                             swal("Good job!", response.message , "success");
                        }else{
                             swal(response.message , "danger");
                        }
                  },
                  'error' : function(error){
                     console.log(error);
                  }
               });
              });
            });
    });

    </script>

    @endsection


  
