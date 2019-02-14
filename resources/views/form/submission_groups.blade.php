@extends('layouts.app')
@section('content')  
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       Form  Submissions
        @if($data['schedule_name'])<small>{{ $data['schedule_name'] }}</small>@endif
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
          <div class="col-xs-12">
            <a class="export-excel" href="{{route('tabularData/export',["id"=>Request::get('id'),"type"=>"excel","schedule_id"=>Request::get('schedule_id')])}}" class=""><img src="{{url('public/images/excel.png')}}"></img></a>
           
            <a class="export-csv" href="{{route('tabularData/export',["id"=>Request::get('id'),"type"=>"csv","schedule_id"=>Request::get('schedule_id')])}}" ><img height="32" width="48" src="{{url('public/images/csv.png')}}"></img></a>
           
            <a id="print-table" href="javascript::void();" class=""><img height="48" width="48" src="{{url('public/images/print.png')}}"></img></a>
          </div>
       
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
      <!-- /.row -->
      </div>

       <!-- resubmission modal -->
  <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <form class="form" id="resubmissionFormRequest" action="{{route('form/adminResubmissionRequest')}}" method="get">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Please give reason for re-submission</h4>
          </div>
          <div class="modal-body">
             <input type="hidden" name="form_id" value="">
             <input type="hidden" name="schedule_id" value="">
             <input type="hidden" name="user_id" value="">
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
          'beforeSend': function() {
               $('.confirm').attr("disabled","disabled");
               },
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
          },'complete': function() {
                      $('.confirm').removeAttr("disabled","disabled");
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

              console.log(response);
                
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
          },'complete': function() {
                      $('.confirm').removeAttr("disabled","disabled");
                  }
        });

      });

     // delete group
     $(document).on('click','.btn-dlt',function(){

        var id = $(this).attr('data-id');
        var click = $(this);
        swal({
        title: "Are you sure?",
        text: "Your will not be able to recover this form group!",
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

            },'complete': function() {
                      $('.confirm').removeAttr("disabled","disabled");
                  }
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
            'url' : '{{ route("formGroup/status")}}',
            'beforeSend': function() {
               $('.confirm').attr("disabled","disabled");
             },
            'success' : function(response){
            if(response.status){
            alert(response.message);
              $('#formGroupTable').DataTable().draw(false);
            }else{
            alert(response.message);
            }
            },
            'error' : function(error){
            console.log(error);
            },'complete': function() {
                      $('.confirm').removeAttr("disabled","disabled");
                  }
            });

          });
// export record
$('body').on('click','.export-csv,.export-excel',function(e){
e.preventDefault();
 form=$('#form-select option:selected').val();
if(form!='')
 {
    window.location=$(this).attr('href');
}else{
  alert("Please select  form");
 }

});
// get filtered records
// chanage status
$('body').on('click','.btn-accept,.btn-reject',function(e){
    e.preventDefault();
    form_id     = $(this).attr('form-id');
    user_id     = $(this).attr('user-id');
    schedule_id = $(this).attr('schedule-id');
    let click = $(this);
              swal({
              title: "Are you sure?",
              text: "Are you sure accept record",
              type: "warning",
              showCancelButton: true,
              confirmButtonClass: "btn-success",
              confirmButtonText: "Yes, Accept it!",
              closeOnConfirm: false
              },
              function(){
                 $.ajax({
                    "headers":{
                    'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                    },
                    'type':'GET',
                    'url' : '{{ route("form/acceptRecord") }}',
                    'data' : { 'form_id' : form_id , 'schedule_id' : schedule_id , 'user_id' : user_id},
                    'beforeSend': function() {
                       $('.confirm').attr("disabled","disabled");
                     },
                    'success' : function(response){
                        console.log(response);
                        if(response.status){
                              click.hide();
                              swal("Good job!", response.message , "success"); 
                        }else{
                             swal(response.message , "danger");
                        }
                  },'complete': function() {
                      $('.confirm').removeAttr("disabled","disabled");
                  }
               });
              });
   });

$('body').on('click','.btn-accept-request,.btn-reject',function(e){
    e.preventDefault();

    form_id     = $(this).attr('form-id');
    user_id     = $(this).attr('user-id');
    schedule_id = $(this).attr('schedule-id');

    let click = $(this);
              swal({
              title: "Are you sure?",
              text: "Are you sure accept request",
              type: "warning",
              showCancelButton: true,
              confirmButtonClass: "btn-success",
              confirmButtonText: "Yes, Accept it!",
              closeOnConfirm: false
              },
              function(){
                 $.ajax({
                    "headers":{
                    'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                    },
                    'type':'GET',
                    'url' : '{{ route("form/acceptSubmissionRequest") }}',
                    'data' : { 'form_id' : form_id , 'schedule_id' : schedule_id , 'user_id' : user_id},
                    'beforeSend': function() {
                       $('.confirm').attr("disabled","disabled");
                     },
                    'success' : function(response){
                        console.log(response);
                        if(response.status){
                              click.hide();
                              swal("Good job!", response.message , "success"); 
                        }else{
                             swal(response.message , "danger");
                        }
                  },'complete': function() {
                      $('.confirm').removeAttr("disabled","disabled");
                  }
               });
              });
   });

});

  $('document').ready(function(){
         $('body').on('click' , '.btn-resubmission' , function(e){
              e.preventDefault();
              let form_id     = $(this).attr('form-id');
              let schedule_id = $(this).attr('schedule-id');
              let user_id     = $(this).attr('user-id');
              let click = $(this);

              $( "input[type=hidden][name=form_id]" ).val(form_id);
              $( "input[type=hidden][name=schedule_id]" ).val(schedule_id);
              $( "input[type=hidden][name=user_id]" ).val(user_id);

              $('#myModal').modal('show');

         });
  });


// $('document').ready(function(){
//          $('body').on('click' , '.btn-resubmission' , function(e){
//               e.preventDefault();
//               let form_id     = $(this).attr('form-id');
//               let schedule_id = $(this).attr('schedule-id');
//               let user_id     = $(this).attr('user-id');
//               let click = $(this);
//               swal({
//               title: "Are you sure?",
//               text: "Are you sure request for resubmission of this record",
//               type: "warning",
//               showCancelButton: true,
//               confirmButtonClass: "btn-success",
//               confirmButtonText: "Yes, Request it!",
//               closeOnConfirm: false
//               },
//               function(){
//                  $.ajax({
//                     "headers":{
//                     'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
//                     },
//                     'type':'GET',
//                     'url' : '{{ route("form/adminResubmissionRequest") }}',
//                     'data' : { form_id : form_id , schedule_id : schedule_id , user_id : user_id },
//                     'success' : function(response){
//                         console.log(response);
//                         if(response.status){
//                               click.hide();
//                               swal("Good job!", response.message , "success");
//                         }else{
//                              swal(response.message , "danger");
//                         }
//                   },
//                });
//               });

//          });
//      })

</script>
@endsection