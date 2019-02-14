

@extends('layouts.app')
@section('content') 

   <section class="content-header">
         <h1>
           {{ ucwords($form_name) }} <small>Edit form</small>
         </h1>
          {{ Breadcrumbs::render('edit-form') }}
   </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-solid">
          <div class="box-body">
            <table class="table table-striped table-hover table-condensed table-responsive" id="dataTable">
              <thead style="background: #3c8dbc;color:white">
               <tr>
                   <th></th>
                   <th>Field Type</th>
                   <th>Field label</th>
                   <th>Action</th>
               </tr>
              </thead>
              <tbody class="sortable"></tbody>
            </table>
          </div>
          <div class="box-footer">
            <button class="btn" id="fieldModalAddBtn">Add Field</button>
          </div>
       </div>
     </div>
    </div><!--row--> 

    <!-- Fied Add Edit Model -->

      <!-- Modal -->
      <div class="modal fade bd-example-modal-lg" id="fieldAddModal" tabindex="-1" role="dialog" aria-labelledby="fieldAddModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
         <form class="form" id="addEditForm">
           <input type="hidden" name="form_id"  value="{{Request::segment(4)}}" >
           <input type="hidden" name="field_id" value="" id="field-id">
          <div class="modal-content">
            <div class="modal-header text-center">
              <h2 class="modal-title" id="fieldAddModalLabel">Settings</h2>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
               <div class="row">
                 <div class="col-md-12">
                   <p class="text-danger" id="error"></p>
                 </div>
               </div>
               <div class="row">
                 <div class="col-md-4">
                    <div class="form-group">
                      <label>Field Type</label>
                      <select class="form-control field_type" name="field_type" id="field-type" required>
                        <option value="">--select--</option>
                        @foreach ($field_types as $field_type)
                          <option value="{{ $field_type->id }}">{{ $field_type->name }}</option>
                        @endforeach
                      </select>
                      <div class="help-block with-errors"></div>
                    </div>
                 </div>
                 <div class="col-md-4">
                    <div class="form-group">
                      <label>Field lable</label>
                       <input type="text" style="text-transform: capitalize;" name="field_lable" placeholder="filed label" class="form-control" id="field-lable" required>
                      <div class="help-block with-errors"></div>
                    </div>
                 </div>
                 <div class="col-md-4">
                    <div class="form-group">
                      <label>Validation</label>
                         <select class="form-control" name="validation" id="validation">
                         <option value="0">Optional</option>
                         <option value="1" selected>Required</option>
                         <option value="2" id="uniqueValidationOption">Required & Unique</option>
                      </select>
                      <div class="help-block with-errors"></div>
                    </div>
                  </div>
               </div>

                <div class="row" id="min">

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Minimum Length/Value</label>
                       <input type="number" name="mini" placeholder="filed label" class="form-control" id="mini_text_box_length">
                       <div class="help-block with-errors"></div>
                    </div>
                  </div>
                   <div class="col-md-4" id="max">
                    <div class="form-group">
                      <label>Maximum Length/Value</label>
                       <input type="number" name="max" placeholder="filed label" class="form-control" id="max_text_box_length">
                       <div class="help-block with-errors"></div>
                    </div>
                  </div>
               </div>

                <div class="row" id="checkEditor">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Use Check Editor</label>
                      <select class="form-control" name="check_editor" id="check-editor">
                        <option value="yes">Yes</option>
                        <option value="no" selected>No</option>
                      </select>
                    </div>
                 </div>
               </div>

                <div class="row" id="optionBtn">
                 <table class="table radioValueTable lableTable">
                    <thead>
                     <tr>
                       <th>Label or option</th>
                       <th><button class="btn btn-primary btn-row-lable-add"><b>+</b> Add</button></th>
                     </tr>
                   </thead>
                   <tbody>
                   </tbody>
                 </table>

               </div>
            <div class="row" id="image">
              <div class="col-md-3">
                <div class="form-group">
                <label>Maximum Image Size in MB</label>
                  <input type="number" name="img_max_size" class="form-control" id="img-max-size">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                 <label>Image Width in PX</label>
                  <input type="number" name="img_width" class="form-control" id="img-width">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                 <label>Image Height in PX</label>
                  <input type="number" name="img_height" class="form-control" id="img-height">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                   <label>Multiple Image</label>
                   <select class="form-control" name="img_multiple" id="multiple-img">
                     <option value="no" selected>no</option>
                     <option value="yes">yes</option>
                   </select>
                </div>
              </div>
            </div>
            <div class="row" id="file">
              <div class="col-md-4">
                <div class="form-group">
                 <label>Maximum File Size in MB</label>
                  <input type="number" name="max_file_size" class="form-control" id="max-file-size">
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                   <label>Multiple File</label>
                   <select class="form-control" name="file_multiple" id="file-multiple">
                     <option value="no" selected>no</option>
                     <option value="yes">yes</option>
                   </select>
                </div>
              </div>
            </div>
             <div class="row" id="number">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Negative Value</label>
                       <select class="form-control" name="negative_value" id="negative">
                        <option value="no">No</option>
                        <option value="yes" selected>Yes</option>
                        </select>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Minimum Length/Value</label>
                       <input type="number" name="min" placeholder="filed label" class="form-control" id="min_number_value">
                    </div>
                  </div>
                   <div class="col-md-4" id="max">
                    <div class="form-group">
                      <label>Maximum Length/Value</label>
                       <input type="number" name="max" placeholder="filed label" class="form-control" id="max_number_value">
                    </div>
                  </div>
               </div>
                <div class="row" id="float">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Minimum Length/Value</label>
                       <input type="number" name="min" placeholder="filed label" class="form-control" id="min_float_value">
                    </div>
                  </div>
                   <div class="col-md-4" id="max">
                    <div class="form-group">
                      <label>Maximum Length/Value</label>
                       <input type="number" name="max" placeholder="filed label" class="form-control" id="max_float_value">
                    </div>
                  </div>
               </div>
               <div class="row">
                 <div class="col-md-4">
                   <div class="form-group" id="dropdown">
                   <label>Multiselect</label>
                    <select class="form-control" name="multiselect" id="multiselect">
                       <option value="no"  selected>No</option>
                       <option value="yes">Yes</option>
                     </select>
                   </div>
                 </div>
               </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-success" id="btn-row-addEdit">Save changes</button>
          </div>
          </div>
          </form>
        </div>
      </div>
  </section>  
<!-- /. section content -->
@endsection
@section('css-script')
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
@endsection
@section('js-script')
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.js"></script>
 <script type="text/javascript">


    $(document).ready(function(){

      //      declare all global variable!
           
           var option = [];

     $('#fieldModalAddBtn').on('click' , function(){
          $('#btn-row-addEdit').addClass('btn-row-add');
          $('#btn-row-addEdit').text('Add');
          $( "#field-id" ).removeAttr('name');
          $( "#field-id" ).val('');
          $( "#field-type" ).attr('name' , 'field_type');
          $( "#field-type" ).removeAttr('disabled');
          $('#fieldAddModal').modal('show');
     });

      $('body').on('submit', '#addEditForm' ,function(e) {
        e.preventDefault();
         
         if($( "#field-id" ).val() > 0){
          var action = "{{route("form/updateVerticalFormField")}}";
          var actionType = 'update';
         }else{
          var action = "{{route("form/storeVerticalFormField")}}";
          var actionType = 'store';
         }

         let form = $(this);
         let data = form.serialize();

        $.ajax({
            "headers":{
            'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
            },
            'type':'POST',
            'url' : action,
            'data' : data,
            'success' : function(response){
                if(response.status){

                  if(actionType == 'store'){
                    $("#dataTable tbody").append(dynamicRow(response.data.type,response.data.lable,response.data.id));
                  }
                   swal("Good job!", response.message , "success");
                   $("#fieldAddModal").modal('hide');
                }else{
                  if(response.type == 'error'){
                      $('#error').html(response.message);
                  }else{
                   swal(response.message , "danger");
                  }
                }
          },
          'error' : function(error){
            console.log(error);
          }
        });

      });

     $('body').on('click', '.btn-row-lable-remove' ,function(){

            
            let click = $(this);
            console.log(click);
            let length = $('.lableTable tbody tr').length;
             if(length > 1){
              click.closest('tr').remove();
              console.log(option);
             }else{
               alert('Minimum one lable or option is required');
             }

     });

      $('.btn-row-lable-add').on('click', function(e){

        e.preventDefault();
            $(".radioValueTable tbody").append(addRadioValueRow());
     })

      var addRadioValueRow = function(lable = ''){
             return '<tr><td><input class="form-control option-input" name="option[]" type="text" value="'+lable+'" placeholder="lable" required></td><td><button class="btn btn-danger btn-row-lable-remove">Remove</button></td></tr>';
      }

      var hideAllInput = function(){
         $('#min').css('display','none');
         $('#max').css('display','none');
         $('#checkEditor').css('display','none');
         $('#optionBtn').css('display','none');
         $('#image').css('display','none');
         $('#file').css('display','none');
         $('#number').css('display','none');
         $('#float').css('display','none');
         $('#dropdown').css('display','none');
         $('#uniqueValidationOption').css('display','none');

         $('#mini_text_box_length').removeAttr('name');
         $('#max_text_box_length').removeAttr('name');
         $('#check-editor').removeAttr('name');
         $('#min_number_value').removeAttr('name');
         $('#max_number_value').removeAttr('name');
         $('#min_float_value').removeAttr('name');
         $('#max_float_value').removeAttr('name');
         $('#multiple-img').removeAttr('name');
         $('#max-file-size').removeAttr('name');
         $('#negative').removeAttr('name');
         $('#min_number_value').removeAttr('name');
         $('#max_number_value').removeAttr('name');
         $('#min_float_value').removeAttr('name');
         $('#max_float_value').removeAttr('name');
         $('#img-height').removeAttr('name');
         $('#img-width').removeAttr('name');
         $('#img-max-size').removeAttr('name');
         $('#file-multiple').removeAttr('name');
         $('#multiselect').removeAttr('name');
         $('#field-id').removeAttr('name');

      }

      // hide all model input field
       hideAllInput();

      $('.field_type').on('change',function(){

           let field_type = $(this).val();

           if(field_type.length > 0){

             if(field_type == 4 || field_type == 5 || field_type == 6){
                $(".radioValueTable tbody").append(addRadioValueRow());       
             }

               showInputFiels(field_type);

           }else{
                  hideAllInput();
           }

     });

      let showInputFiels = function(field_type = null){
          
           hideAllInput();

           switch (parseInt(field_type)) {

                  case 1:
                        $('#min').show();
                        $('#max').show();
                        $('#mini_text_box_length').attr('name','min');
                        $('#max_text_box_length').attr('name' , 'max');
                        $('#uniqueValidationOption').show();
                      break;
                  case 2:
                        $('#checkEditor').show();
                        $('#check-editor').attr('name' , 'check_editor');
                      break;
                  case 3:
                        $('#uniqueValidationOption').show();
                      break;
                  case 4:
                        $('#dropdown').show();
                        $( '#optionBtn').show();
                        $('#multiselect').attr('name','multiselect');
                      break;
                  case 5:
                        $( '#optionBtn').show();
                      break;
                  case 6:
                       $( '#optionBtn').show();
                      break;
                  case 7:
                       $('#uniqueValidationOption').show();
                      break;
                  case 8:
                       $('#number').show();
                       $( '#negative' ).attr('name', 'negative_value');
                       $('#min_number_value').attr('name','min');
                       $('#max_number_value').attr('name' , 'max');
                      break;
                  case 9:
                       $('#float').show();
                       $('#min_float_value').attr('name','min');
                       $('#max_float_value').attr('name' , 'max');
                      break;
                  case 10:
                      break;
                  case 11:
                      $('#file').show();
                      $( '#max-file-size' ).attr('name' , 'max_file_size');
                      $( '#file-multiple').attr('name' , 'file_multiple');
                      break;
                  case 12:
                      break;
                  case 13:
                      break;
                  case 14:
                      break;
                  case 15:
                      break;
                  case 16:
                      $('#image').show();
                      $( '#img-max-size' ).attr('name' , 'img_max_size' );
                      $( '#multiple-img' ).attr('name' , 'img_multiple' );
                      $( '#img-height' ).attr('name' , 'img_height' );
                      $( '#img-width' ).attr('name' , 'img_width' );
                      break;
                  case 17:
                      break;
                  case 18:
                      break;
                  case 19:
                      break;
                  default:
                    hideAllInput();
                }
      }

    //store form feild data
      $(document).on('click','#store',function(){
          let url = $(location).attr('href');
          let segments = url.split( '/' );
          let id = segments[7];
          swal({
          title: "Are you sure?",
          text: "Your will not be able to recover this Settings!",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-success",
          confirmButtonText: "Yes, update it!",
          closeOnConfirm: false
          },
          function(){
             $.ajax({
                "headers":{
                'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                },
                'type':'PUT',
                'url' : '{{ route("form/storeVerticalFormField") }}',
                'data' : { method : '_POST' , data : filedDataArray },
                'success' : function(response){
                  console.log(response);
                    if(response.status){
                         $("#dataTable tbody").append(dynamicRow(response.data.type,response.data.lable,response.data.id));
                         swal("Good job!", response.message , "success");
                    }else{
                         swal(response.message , "danger");
                    }
              },
           });
          });
        });

      //store form feild data
      $(document).on('click','.btn-edit',function(){

          let id = $(this).attr('data-id');

             $.ajax({
                "headers":{
                'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                },
                'type':'GET',
                'url' : '{{route('form/editVerticalFormField')}}',
                'data' : { method : '_GET' , id : id },
                'success' : function(response){
                  console.log(response);
                  $('#btn-row-addEdit').addClass('btn-update');
                  $('#btn-row-addEdit').text('Save Changes');
                  $('#fieldAddModal').modal('show');
                  $( "#field-type" ).val(response.FormField.field_type_id);
                  $( "#field-lable" ).val(response.FormField.field_title);
                  $( "#field-type" ).removeAttr('name');
                  $( "#field-type" ).attr('disabled' , true);
                  $( "#field-id" ).val(id);
                  showInputFiels(response.FormField.field_type_id);
                  $( "#field-id" ).attr('name','field_id');
                  
                  if(response.FormField.field_options){
                     response.FormField.field_options.map(function(index,value){
                       $(".radioValueTable tbody").append(addRadioValueRow(index));
                     });
                  }

              },
           });

        });

      //store form feild data
      $(document).on('click','.btn-delete',function(){
 
          let id    = $(this).attr('data-id');
          let click = $(this);
          swal({
          title: "Are you sure?",
          text: "Your will not be able to recover this field!",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-success",
          confirmButtonText: "Yes, delete it!",
          closeOnConfirm: false
          },
          function(){
             $.ajax({
                "headers":{
                'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                },
                'type':'DELETE',
                'url' : '{{ route("form/deleteVerticalFormField") }}',
                'data' : { method : '_DELETE' , id : id },
                'success' : function(response){
                  console.log(response);
                    if(response.status){
                         click.closest('tr').remove();
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
  
      let fixHelperModified = function(e, tr) {
      let $originals = tr.children();
      let $helper = tr.clone();
      
      $helper.children().each(function(index) {
      $(this).width($originals.eq(index).width())
      });

       return $helper;
    },

    updateIndex = function(e, ui) {
      $('td.index', ui.item.parent()).each(function (i) {
        $(this).html(i + 1);
      });
    };
   

    // arrange form field squence
    $("#dataTable tbody").sortable({
      helper: fixHelperModified,
      stop: updateIndex
    }).disableSelection();

    $("tbody").sortable({
      distance: 5,
      delay: 100,
      opacity: 0.6,
      cursor: 'move',
      update: function() {}
    });
      
       $(document).ready(function(){
             
        @forelse ($form_fields as $form_field)
              $("#dataTable tbody").append(dynamicRow('{{$form_field->field_type_text}}' , '{{$form_field->field_title}}' , '{{$form_field->id}}'));
        @empty
        @endforelse
           
       });

      // Dynamic row generater function
      var dynamicRow = function (type,lable,id){

             return '<tr row-id="'+id+'"><td><i class="fa fa-arrows"></i></td><td style="text-transform: capitalize;">'+type+'</td><td style="text-transform: capitalize;">'+lable+'</td><td></button>&nbsp;&nbsp;<button data-id="'+id+'" class="btn btn-primary btn-edit btn-edit"><i class="fa fa-edit"></i></button>&nbsp;&nbsp;<button data-id="'+id+'" class="btn btn-danger btn-delete"><i class="fa fa-trash"></i></td><tr>';
       }

        $('body').on('change', '.option-input' ,function(e){
          let val  = $(this).val();
          let click = $(this);
          if(option.length > 0){
            let inserStatus = true;
            option.map(function(item){
                  if(item.toLowerCase() == val.toLowerCase()){
                     inserStatus = false;
                     return false
                  }
            });

            if(inserStatus)
                option.push(val.toLowerCase());
              else{
                alert('This option is already exist');
                click.val('');
              }
          }else{
             option.push(val.toLowerCase());
          }
        
      });

      $('#mini_text_box_length').on('change',function(evt){
        
           let min = $(this).val();
           let max = $('#max_text_box_length').val();
           if(max){
               if(min > max)
                   alert('Invalid length');               
           }

           return true;

      });

      $('#max_text_box_length').on('change',function(evt){
         
           let max = $(this).val();
           let min = $('#mini_text_box_length').val();
           if(max){
               if(min < max)
                   alert('Invalid length');               
           }

           return true;

      });

        });

     
  </script>
@endsection



