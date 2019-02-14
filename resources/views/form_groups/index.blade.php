@extends('layouts.app')
@section('content')  
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        @isset($data['page_title']){{ $data['page_title']}} @endisset
        <small></small>
      </h1>
        {{ Breadcrumbs::render('form-groups') }}
    </section>
   <!-- Main content -->
    <section class="content">
        <div class="row">
        @if (Session::has('status'))
          <div class="col-xs-12 alert-message-div">
              <div class="alert @if(Session::get('status')) alert-success @else alert-danger @endif alert-dismissible" form="alert">
                <strong>@if (Session::get('status')) Success @else Danger @endif!</strong> {{Session::get('msg')}}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
              </div>
          </div>
        @endif
        @php
        $role=Auth::user()->role;
        @endphp
        @if($role == 1)
         <div class="col-xs-12" style="margin-bottom: 7px;">
          <button  class="btn btn-success pull-right modal-btn" btn-action="create"><i class="fa fa-plus"></i> <b>New</b></button>
         </div>
         @endif

        <div class="col-xs-12">
          <div class="box box-solid">
              <div class="box-header">
                <h3 class="box-title"></h3>
              </div>
              <!-- /.box-header -->
              <div class="box-body">
                  {!! $dataTable->table(['class' => 'table table-bordered', 'id' => 'formGroupTable']) !!}
              </div>
              <!-- /.box-body -->
          </div>
          <!-- /.col -->
        </div>
                 <!-- Modal -->
  <div class="modal fade" id="storeModal" form="dialog">
    <div class="modal-dialog modal-md">
    <form class="form" action="{{ route('formGroup/store') }}" method="POST" id="storeForm">
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

          {{--  <div class="col-md-12">
             <div class="form-group">
               <label for="forms">Select form</label>
               <select name="forms[]" class="form-control" id="forms" multiple="multiple">
                 @forelse($data['forms'] as $form)
                  <option value="{{$form->id}}">{{$form->name}}</option>
                @empty
                  <option>form not available</option>
                @endforelse
               </select>
               <span id="form_error" class="text-red"></span>
             </div>
           </div> --}}

          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-success btn-submit">Create</button>
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
      <!-- Bootstarap Validator script -->
      <link rel="stylesheet" type="text/css" href="{{ asset('public/bootstrap/css/bootstrapValidator.min.css') }}">
        <!-- Bootstrap datatable script -->
      <link rel="stylesheet" type="text/css" href="{{ asset('public/bootstrap/css/dataTables.bootstrap.min.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ asset('public/bootstrap/css/bootstrap-multiselect.css')}}"/>
        <!-- sweet alert script -->
      <link rel="stylesheet" type="text/css" href="{{ asset('public/bootstrap/css/sweetalert.css') }}">
      <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">
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
      <!-- Bootstrap DataTables -->
      <script src="{{ asset('public/bootstrap/js/dataTables.bootstrap.min.js')}}"></script>
      <!-- Multiselect dropdown script --> 
      <script src="{{ asset('public/bootstrap/js/bootstrap-multiselect.js')}}"></script>
  
      <!-- DataTables Script -->
      {!! $dataTable->scripts() !!}

<script>
 $(document).ready(function(){

     $('body').on('click','.modal-btn',function(){
         
         $('#group_name_error').html('');

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
          'url' : '{{route('formGroup/edit')}}',
          'data' : { method : '_GET' , id : id },
          'success' : function(response){
             console.log(response);
              $( "#group-name" ).val(response.data.group_name);
              let html = [];
              $("#forms option").each(function()
              { 
                 let option = $(this).val();
                 let text   = $(this).text();
                 var selecte = false;
                  response.data.formData.map(function(item){
                      if(item.id == option){
                          return selecte = true;
                      }
                  });
                  html.push({label: ''+text+'', title: ''+text+'', value: ''+option+'' , selected : selecte});
              });
              
              $('#forms').multiselect({
                                includeSelectAllOption : true,
                                       enableFiltering : true,
                        enableCaseInsensitiveFiltering : true,
                                             maxHeight : 400
              });

              $('#forms').multiselect('dataprovider', html);

          },
          'error' : function(error){
             console.log(error);
             alert('Something went wrong, please try leter');
          }
          });

         }

         if(btnAction == 'create'){
                         $('.modal-title').html('Create Group');
                         $('.btn-submit').html('Create');
                         $('#group-name').val();
                         $('#id').attr('value','');
                         $( "#form" ).val('');
                         $( "#group-name" ).val('');
         }

            let html = [];
              $("#forms option").each(function()
              { 
                  let option = $(this).val();
                  let text   = $(this).text();
                  html.push({label: ''+text+'', title: ''+text+'', value: ''+option+''});
              });
              
              $('#forms').multiselect({
                                includeSelectAllOption : true,
                                       enableFiltering : true,
                        enableCaseInsensitiveFiltering : true,
                                             maxHeight : 400
              });

              $('#forms').multiselect('dataprovider', html);
              $('#storeModal').modal('show');
     
     });

     // store or update data

      $('body').on('submit','#storeForm',function(e){

          e.preventDefault();

          let submitBtnText = $('.btn-submit').text();
         
          let form = $(this);
         
          let data = form.serialize();

          $.ajax({
            "headers":{
            'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
            },
            'type':'POST',
            'url' : form.attr('action'),
            'data' : data,
            beforeSend: function() {
              $('.btn-submit').attr("disabled","disabled").html('<span class="fa fa-circle-o-notch fa-spin"></span>');
            },
            'success' : function(response){
                
                if(response.status == 'success'){
                   swal("Good job!", response.message , "success");
                   $("#storeModal").modal('hide');
                   $('#formGroupTable').DataTable().draw(false);
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
        text: "You will not be able to recover this form group!",
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
              'url' : '{{ route("formGroup/delete") }}',
              'data' : { method : '_DELETE' , id : id},
              'beforeSend': function() {
               $('.confirm').attr("disabled","disabled");
               },
              'success' : function(response){
                  if(response.status == 'success'){
                       $('#formGroupTable').DataTable().draw(false);
                       swal("Good job!", response.message , "success");
                  }else{
                       swal(response.message , "danger");
                  }

            },
            'complete': function() {
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
        text: "Are you sure to change the status of this form group!",
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
            'type':'post',
            'data' : { id : id},
            'url' : '{{ route("formGroup/status")}}',
             'beforeSend': function() {
               $('.confirm').attr("disabled","disabled");
             },
            'success' : function(response){
            if(response.status){
              swal("Good job!", response.message , "success");
              $('#formGroupTable').DataTable().draw(false);
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

 });

</script>
@endsection