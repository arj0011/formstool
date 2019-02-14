@extends('layouts.app')
@section('content')  
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      User's Role Group
        <small></small>
      </h1>
        {{ Breadcrumbs::render('role-groups') }}
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
        
         <div class="col-xs-12" style="margin-bottom: 7px;">
          <button  class="btn btn-success pull-right modal-btn" btn-action="create"><i class="fa fa-plus"></i> <b>New</b></button>
         </div>

        <div class="col-xs-12">
          <div class="box box-solid">
              <div class="box-header">
                <h3 class="box-title"></h3>
              </div>
              <!-- /.box-header -->
              <div class="box-body">
                  {!! $dataTable->table(['class' => 'table table-bordered', 'id' => 'userGroupTable']) !!}
              </div>
              <!-- /.box-body -->
          </div>
          <!-- /.col -->
        </div>
                 <!-- Modal -->
  <div class="modal fade" id="storeModal" role="dialog">
    <div class="modal-dialog modal-md">
    <form class="form" action="{{ route('roleGroup/store') }}" method="POST" id="storeForm">
    <input type="hidden" name="id" value="" id="id">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"></h4>
        </div>

        <div class="modal-body">
          
          <div class="row">

           <div class="col-md-12">
             <div class="form-group">
               <label for="users">Group Name</label>
               <input type="text" name="group_name" class="form-control" id="group-name">
               <span id="group_name_error" class="text-red"></span>
             </div>
           </div>

           <div class="col-md-12">
             <div class="form-group">
               <label for="roles">Select Role</label>
               <select name="roles[]" class="form-control" id="role" multiple>
               </select>
               <span id="role_error" class="text-red"></span>
             </div>
           </div>


          </div>

        </div>

        <div class="modal-footer">
        <div class="footer-btn">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-success btn-submit">Create</button>
        </div>
        </div>

      </div>
      </form>
    </div>
  </div>
      <!-- /.row -->
      </div>
    </section>
    <!-- section  -->
@endsection
@section('css-script')
   <!-- Bootstrap Custome Multiselect CSS -->
      <link rel="stylesheet" type="text/css" href="{{ asset('public/bootstrap/css/bootstrap-multiselect.css')}}"/>
      <link rel="stylesheet" type="text/css" href="{{ asset('public/bootstrap/css/bootstrap-multiselect-custome-script.css')}}"/>
@endsection
@section('js-script')
  <!-- Multiselect dropdown script --> 
      <script src="{{ asset('public/bootstrap/js/bootstrap-multiselect.js')}}"></script>
  <!-- Bootstarap Validator script -->
      <script src="{{ asset('public/bootstrap/js/bootstrapValidator.min.js')}}"></script>
      <!-- Bootstrap Jquery DataTables -->
      <script src="{{ asset('public/bootstrap/js/jquery.dataTables.min.js')}}"></script>
      <!-- Bootstrap DataTables -->
      <script src="{{ asset('public/bootstrap/js/dataTables.bootstrap.min.js')}}"></script>
      <!-- DataTables Script -->
      {!! $dataTable->scripts() !!}

<script>
 $(document).ready(function(){

      $('#role').multiselect({
         includeSelectAllOption  : true,
       enableFiltering           : true,
  enableCaseInsensitiveFiltering : true,
          maxHeight              : 400
      });

    let getRoleGroups = function(selectedOption = null){
                console.log(selectedOption);
                let option = [];
                @forelse($data['roles'] as $role)
                var select = false;
                    if(selectedOption){
                         selectedOption.map(function(value){
                              if({{$role->id}} == value){
                                 return select = true
                              }
                         });
                    }

                    option.push({label : '{{$role->name}}' , title : '{{$role->name}}' , value : '{{$role->id}}' ,  selected : select});
                 @empty
                 @endforelse
                 return option;
            }


           $('body').on('click','.modal-btn',function(){


            $('#group_name_error').text('');
            $('#role_error').text('');
          

             $('#role').multiselect('dataprovider',getRoleGroups());

         let btnAction = $(this).attr('btn-action');

         if(btnAction == 'update'){
              let id   = $(this).attr('data-id');
                         $('.btn-submit').html('Update');
                         $('#id').attr('value',id);
                         $('.modal-title').html('Edit Group');

          $.ajax({
          "headers":{
          'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
          },
          'type':'GET',
          'url' : '{{route('roleGroup/edit')}}',
          'data' : { method : '_GET' , id : id },
          'success' : function(response){
             $('#role').multiselect('dataprovider',getRoleGroups(response.data.role_id));
             $( "#group-name" ).val(response.data.group_name);
          },
          'error' : function(error){
             alert('Something went wrong, please try leter');
          }
          });

         }

         if(btnAction == 'create'){
                         $('.modal-title').html('Create Group');
                         $('.btn-submit').html('Create');
                         $('#group-name').val();
                         $('#id').attr('value','');
                         $( "#role" ).val('');
                         $( "#group-name" ).val('');
         }

          $('#storeModal').modal('show');
     
     });

     // store or update data

      $('body').on('submit','#storeForm',function(e){
          e.preventDefault();

          let form = $(this);

          let submitBtnText = $('.btn-submit').text();
         
          let data = form.serialize();

          $.ajax({
            "headers":{
            'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
            },
            'type' :'POST',
            'url'  : form.attr('action'),
            'data' : data,
            beforeSend: function() {
              $('.btn-submit').attr("disabled","disabled").html('<span class="fa fa-circle-o-notch fa-spin"></span>');
            },
            'success' : function(response){
                 

                if(response.status == 'success'){
                   swal("Good job!", response.message , "success");
                   $("#storeModal").modal('hide');
                   $('#userGroupTable').DataTable().draw(false);
                }

                if(response.status == 'error'){

                    $.each(response.data, function (key, val) {
                        $("#" + key + '_error').text(val[0]);
                    });
                
                }
                
                if(response.status == 'failed'){
                  swal(response.message , "danger");
                }


          },
          'error' : function(error){
            console.log(error);
          },
          complete: function() {
                  $('.btn-submit').removeAttr("disabled","disabled").html(submitBtnText);
               },
        });

      });

     // delete group
     $(document).on('click','.btn-dlt',function(){

        var id = $(this).attr('data-id');
        var click = $(this);
        swal({
        title: "Are you sure?",
        text: "You will not be able to recover this group!",
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
              'type':'DELETE',
              'url' : '{{ route("roleGroup/delete") }}',
              'data' : { method : '_DELETE' , id : id},
               beforeSend: function() {
               $('.confirm').attr("disabled","disabled");
               },
              'success' : function(response){
                  if(response.status == 'success'){
                       $('#userGroupTable').DataTable().draw(false);
                       swal("Good job!", response.message , "success");
                  }else{
                       swal(response.message , "danger");
                  }

                  $('#userGroupTable').DataTable().draw(false);

            },
             complete: function() {
                  $('.confirm').removeAttr("disabled","disabled");
               },
         });
        });
      });

       $('body').on('click' , '.btn-status' , function(e){
           e.preventDefault();
           let id = $(this).attr('data-id');
           swal({
            title: "Are you sure?",
            text: "Are you sure to change the status of this group!",
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
              'url' : '{{ route("roleGroup/status")}}',
               beforeSend: function() {
               $('.confirm').attr("disabled","disabled");
               },
              'success' : function(response){
              if(response.status){
                swal("Good job!", response.message , "success");
                $('#userGroupTable').DataTable().draw(false);
              }else{
                swal(response.message , "danger");
              }
              },
              'error' : function(error){
                   console.log(error);
                        },
              complete: function() {
                  $('.confirm').removeAttr("disabled","disabled");
               },
              });
            });
       });

 });

</script>
@endsection