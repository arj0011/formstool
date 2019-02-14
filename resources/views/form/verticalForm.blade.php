@extends('layouts.app')
@section('content') 

   <section class="content-header">
         <h1>
          Edit Form<small> {{ ucwords(str_replace('_', ' ',$form_name)) }} </small>
         </h1>
          {{ Breadcrumbs::render('edit-form') }}
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

       <div class="col-md-12">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li  class="@if (!Request::get('fieldTypeTab')) active @endif"><a href="#activity" data-toggle="tab">Form Settings</a></li>
              <li class="@if (Request::get('fieldTypeTab')) active @endif"><a href="#settings" data-toggle="tab">Field Settings</a></li>
            </ul>
            <div class="tab-content">
              <div class="@if (!Request::get('fieldTypeTab')) active @endif tab-pane" id="activity">
                          {{ Form::open(array('url' => route('form/update') , 'novalidate' => true)) }}
                          <input type="hidden" name="form_id" value="{{$form_id}}">
            <div class="box box-solid">
                <div class="box-body">

                   <div class="col-md-12">
                        
                        <div class="form-group">
                         <label for="forms">Select Group</label>
                         <select name="group" class="form-control" id="group">
                          @forelse($form_groups as $key => $group)
                            @if ($key == '0')
                             <option value="">--select--</option>
                            @endif
                            <option @if($group->id == old('group') || $form->group_id == $group->id) selected @endif value="{{$group->id}}">{{ucwords($group->group_name)}}</option>
                          @empty
                            <option>group not available</option>
                          @endforelse
                         </select>
                         <span id="form_error" class="text-red"></span>
                        </div>  

                        <div class="form-group">
                        <label for="form-name">*Form Name</label>
                        <input type="text" value="@isset($form){{ $form->name }} @endisset" id="form-name"class="form-control" required name="name" placeholder="Enter Form Name">
                         <span class="text-danger">{{ $errors->first('name') }}</span> 
                        </div>
                    <div class="form-group">
                      <label for="form-type">*Form Type</label>
                      <select @isset($form){{'disabled'}} @endisset  id="form_type" name="form_type" required class="form-control"> 
                          <option value=''> Select Form Type</option>
                          <option @isset($form) @if($form->form_type=="Vertical") {{ 'selected' }}  @endif @endisset value='Vertical'>Vertical</option>
                          <option  @isset($form) @if($form->form_type=="Tabular") {{ 'selected' }}  @endif @endisset value='Tabular'>Tabular(2D)</option>
                      </select>
                        
                        <span class="text-danger">{{ $errors->first('form_type') }}</span> 
                   </div>
            </div>
              </div>
             <div class="box-footer">
              <a class="btn btn-default" href="{{ url()->previous() }}">Back</a>
              <button type="submit" class="btn btn-success">Submit</button>
              </div>
            </div>
        
           {{ Form::close()}}
              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane @if (Request::get('fieldTypeTab')) active @endif" id="settings">
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
                <button class="btn btn-success hide" id="redirecttoForm">Submit</button>
                </div>
                </div>
              </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.nav-tabs-custom -->
       </div>
       <div class="col-md-12 text-right" style="margin-bottom: 10px;">
          <a class="btn btn-default" style="border-radius: 10px; background: #ffffff ; padding-left: 20px;padding-right: 20px;" href="{{url()->previous()}}">Back</a>
       </div>
    </div><!--row--> 

    <!-- Fied Add Edit Model -->

      <!-- Modal -->
      <div class="modal fade bd-example-modal-lg" id="fieldAddModal" tabindex="-1" role="dialog" aria-labelledby="fieldAddModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
         <form class="form" id="addEditForm" novalidate>
           <input type="hidden" name="form_id"  value="{{Request::segment(4)}}" >
           <input type="hidden" name="field_id" value="field_id" id="field-id">
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
                 <p class="text-danger" id="specialCharacter" style="display: none;">Special chracter's are not allowed</p>
                 <div class="col-md-4">
                    <div class="form-group">
                      <label>Field label</label>
                       <input type="text" style="text-transform: capitalize;" name="field_lable" placeholder="field label" class="form-control" id="field-lable" required>
                      <div class="help-block with-errors"></div>
                    </div>
                 </div>
                 <div class="col-md-4">
                    <div class="form-group">
                      <label>Validation</label>
                         <select class="form-control" name="validation" id="validation" required>
                         <option value="">-- select --</option>
                         <option value="0">Optional</option>
                         <option value="1">Required</option>
                         <option value="2" id="uniqueValidationOption">Required & Unique</option>
                      </select>
                      <div class="help-block with-errors"></div>
                    </div>
                  </div>
               </div>

                <div class="row" id="min">

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Minimum Length</label>
                       <input type="number" name="mini" placeholder="field label" class="form-control" id="mini_text_box_length">
                       <div class="help-block with-errors"></div>
                    </div>
                  </div>
                   <div class="col-md-4" id="max">
                    <div class="form-group">
                      <label>Maximum Length</label>
                       <input type="number" name="max" placeholder="field label" class="form-control" id="max_text_box_length">
                       <div class="help-block with-errors"></div>
                    </div>
                  </div>
               </div>

               {{--  <div class="row" id="checkEditor">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Use Check Editor</label>
                      <select class="form-control" name="check_editor" id="check-editor">
                        <option value="yes">Yes</option>
                        <option value="no" selected>No</option>
                      </select>
                    </div>
                 </div>
               </div> --}}

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
                {{--   <div class="col-md-4">
                    <div class="form-group">
                      <label>Negative Value</label>
                       <select class="form-control" name="negative_value" id="negative">
                        <option value="no">No</option>
                        <option value="yes" selected>Yes</option>
                        </select>
                    </div>
                  </div> --}}
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Minimum Value</label>
                       <input type="number" name="min" placeholder="field label" class="form-control" id="min_number_value">
                    </div>
                  </div>
                   <div class="col-md-4" id="max">
                    <div class="form-group">
                      <label>Maximum Value</label>
                       <input type="number" name="max" placeholder="field label" class="form-control" id="max_number_value">
                    </div>
                  </div>
               </div>
                <div class="row" id="float">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Minimum Length/Value</label>
                       <input type="number" name="min" placeholder="field label" class="form-control" id="min_float_value">
                    </div>
                  </div>
                   <div class="col-md-4" id="max">
                    <div class="form-group">
                      <label>Maximum Length/Value</label>
                       <input type="number" name="max" placeholder="field label" class="form-control" id="max_float_value">
                    </div>
                  </div>
               </div>
               <div class="row">
                 <div class="col-md-4">
                   <div class="form-group" id="dropdown">
                   <label>Multiselect</label>
                    <select class="form-control" name="multiselect" id="multiselect">
                       <option value="no">No</option>
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
  <link rel="stylesheet" type="text/css" href="{{ asset('public/bootstrap/css/bootstrap-multiselect.css')}}"/>
@endsection
@section('js-script')
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <script src="{{ asset('public/bootstrap/js/bootstrap-multiselect.js')}}"></script>
 <script type="text/javascript">


    $(document).ready(function(){

      //      declare all global variable!
           
           var option = [];

     $('#fieldModalAddBtn').on('click' , function(){
        
          $('#error').html('');
          $('#btn-row-addEdit').addClass('btn-row-add');
          $('#btn-row-addEdit').text('Add');
          $( "#field-id" ).removeAttr('name');
          $( "#field-id" ).val('');
          $( "#field-type" ).attr('name' , 'field_type');
          $( "#field-type" ).removeAttr('disabled');
          $( '#field-type').val('');
          $( '#field-lable' ).val('');
          $( '#validation' ).val('');
          $( '#validation option:eq(0)');
          $( '#multiselect option').prop("selected",false);
          $(".radioValueTable tbody").html('');
          hideAllInput();
          $('#fieldAddModal').modal('show');
          $('#specialCharacter').css('display','none');
          $('#mini_text_box_length').val('');
          $('#max_text_box_length').val('');
          $('#min_number_value').val('');
          $('#max_number_value').val('');
     });

      $('body').on('submit', '#addEditForm' ,function(e) {
           
           e.preventDefault();

           let min = $('#mini_text_box_length').val();
           let max = $('#max_text_box_length').val();
           let minValue = $('#min_number_value').val();
           let maxValue = $('#max_number_value').val();

           if(max){
               if(parseInt(min) > parseInt(max)){
                   alert('Minimum length can not be greater than maximum length');
                   return false;            
               }
           }

            if(min){
               if(parseInt(min) > parseInt(max)){
                   alert('Maximum length can not be less than minimum length');
                   return false;            
               }
            }

            if(maxValue){
               if(parseInt(minValue) > parseInt(maxValue)){
                   alert('Minimum value can not be greater than maximum value');
                   return false;            
               }
            }

            if(minValue){
               if(parseInt(minValue) > parseInt(maxValue)){
                   alert('Maximum value can not be less than minimum value');
                   return false;            
               }
            }

          $('#error').html('');
        let id = $( "#field-id" ).val();
        field_type = $('.field_type').val();
        field_lable = $('#field-lable').val();
        var regex = new RegExp("^[A-Za-z0-9 _]*[A-Za-z0-9][A-Za-z0-9 _]*$");
        var key = field_lable;

        if (!regex.test(key)) {
          return $('#specialCharacter').css('display','block');false;
        }
        
         if($( "#field-id" ).val() > 0){
          var action = "{{route("form/updateVerticalFormField")}}";
          var actionType = 'update';
         }else{
          var action = "{{route("form/storeVerticalFormField")}}";
          var actionType = 'store';
         }

         let form = $(this);
         let data = form.serialize()+"&field_type="+field_type;

        $.ajax({
            "headers":{
            'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
            },
            'type':'POST',
            'url' : action,
            'data' : data,
            'beforeSend': function() {
               $('#btn-row-addEdit').attr("disabled","disabled");
               },
            'success' : function(response){
              console.log(response);
                if(response.status){

                  if(actionType == 'store'){
                    $("#dataTable tbody").append(dynamicRow(response.data.type,response.data.lable,response.data.id));
                  }

                  if(actionType == 'update'){
                      let text = $('#field-lable').val();
                        $('#row-'+id).find('td').eq(2).text(text);
                  }

                   swal("Good job!", response.message , "success");
                   $("#fieldAddModal").modal('hide');
                   $('#redirecttoForm').removeClass('hide');
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
          },'complete': function() {
                  $('#btn-row-addEdit').removeAttr("disabled","disabled");
               }
        });

      });

     $('body').on('click', '.btn-row-lable-remove' ,function(){

            
            let click = $(this);
            let length = $('.lableTable tbody tr').length;
             if(length > 1){
              click.closest('tr').remove();
             }else{
               alert('Minimum one lable or option is required');
             }

     });

      $('.btn-row-lable-add').on('click', function(e){

        e.preventDefault();
            $(".radioValueTable tbody").append(addRadioValueRow());
     })

      var addRadioValueRow = function(lable = ''){
             return '<tr><td><input class="form-control option-input" name="option[]" type="text" value="'+lable+'" placeholder="label" required></td><td><button class="btn btn-danger btn-row-lable-remove" type="button">Remove</button></td></tr>';
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
    //     $('#field-id').removeAttr('name');

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
      $(document).on('click','.btn-edit',function(){
               $('#error').html('');
          let id = $(this).attr('data-id');
             
             $.ajax({
                "headers":{
                'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                },
                'type':'GET',
                'url' : '{{route('form/editVerticalFormField')}}',
                'data' : { method : '_GET' , id : id },
                'success' : function(response){
                  $('#btn-row-addEdit').addClass('btn-update');
                  $('#btn-row-addEdit').text('Save Changes');
                  $('#specialCharacter').css('display','none');
                //  $('#fieldAddModal').modal('show');
                  $( "#field-type" ).val(response.FormField.field_type_id);
                  $( "#field-lable" ).val(response.FormField.field_title);
                  $( "#field-type" ).removeAttr('name');
                  $( "#field-type" ).attr('disabled' , true);
                  $( "#field-id" ).attr('name','field_id');
                  $( "#field-id" ).val(id);

                  showInputFiels(response.FormField.field_type_id);
                   
                   $(".radioValueTable tbody").html('');

                  if(response.FormField.field_options){
                     response.FormField.field_options.map(function(index,value){
                       $(".radioValueTable tbody").append(addRadioValueRow(index));
                     });
                  }
                    console.log(response.FormField.field_rules.validation);
                    // $("#validation option[value="+response.FormField.field_rules.validation+"]").attr('selected','selected');
                    $( '#validation option').prop("selected",false);
                    $( "#validation").find('option[value="'+response.FormField.field_rules.validation+'"]').prop("selected",true);


                   switch (parseInt(response.FormField.field_type_id)) {

                  case 1:
                        $('#mini_text_box_length').val(response.FormField.field_rules.min);
                        $('#max_text_box_length').val(response.FormField.field_rules.max);
                      break;
                  case 2:
                        $('#checkEditor').show();
                        $('#check-editor').attr('name' , 'check_editor');
                      break;
                  case 3:
                        $('#uniqueValidationOption').show();
                      break;
                  case 4:
                        console.log('mul = '+response.FormField.field_rules.multiselect);
                        // $("#multiselect option[value="+response.FormField.field_rules.multiselect+"]").attr('selected','selected');
                        
                        $( '#multiselect option').prop("selected",false);
                        $( "#multiselect").find('option[value="'+response.FormField.field_rules.multiselect+'"]').prop("selected",true);
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
                        $('#min_number_value').val(response.FormField.field_rules.min);
                        $('#max_number_value').val(response.FormField.field_rules.max);
                      break;
                  case 9:
                       $('#min_float_value').val(response.FormField.field_rules.min);
                       $('#max_float_value').val(response.FormField.field_rules.max);
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
                    $('#fieldAddModal').modal('show');
              },
              'error' : function(error){
                  console.log(error);
              }
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
               'beforeSend': function() {
                 $('.confirm').attr("disabled","disabled");
               },
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
              },'complete': function() {
                  $('.confirm').removeAttr("disabled","disabled");
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
    // $("#dataTable tbody").sortable({
    //   helper: fixHelperModified,
    //   stop: updateIndex
    // }).disableSelection();

    // $("tbody").sortable({
    //   distance: 5,
    //   delay: 100,
    //   opacity: 0.6,
    //   cursor: 'move',
    //   update: function() {}
    // });
      
       $(document).ready(function(){
             
        @forelse ($form_fields as $form_field)
              $("#dataTable tbody").append(dynamicRow('{{$form_field->field_type_text}}' , '{{$form_field->field_title}}' , '{{$form_field->id}}'));
        @empty
        @endforelse
           
       });

      // Dynamic columns generater function
      var dynamicColumn = function (type,lable,id){

             return '<td ondrop="dropFiled(event)"><i class="fa fa-arrows"></i></td><td style="text-transform: capitalize;">'+type+'</td><td style="text-transform: capitalize;">'+lable+'</td><td></button>&nbsp;&nbsp;<button data-id="'+id+'" class="btn btn-primary btn-edit btn-edit"><i class="fa fa-edit"></i></button>&nbsp;&nbsp;<button data-id="'+id+'" class="btn btn-danger btn-delete"><i class="fa fa-trash"></i></td>';
       }

      // Dynamic row generater function
      var dynamicRow = function (type,lable,id){

             return '<tr ondrop="dropFiled(event)" id="row-'+id+'"><td><i class="fa fa-arrows"></i></td><td style="text-transform: capitalize;">'+type+'</td><td id="lableCellId'+id+'" style="text-transform: capitalize;">'+lable+'</td><td></button>&nbsp;&nbsp;<button data-id="'+id+'" class="btn btn-primary btn-edit btn-edit"><i class="fa fa-edit"></i></button>&nbsp;&nbsp;<button data-id="'+id+'" class="btn btn-danger btn-delete"><i class="fa fa-trash"></i></td><tr>';
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

      // $('#mini_text_box_length').on('change',function(evt){
          
      //      let min = $(this).val();
      //      let max = $('#max_text_box_length').val();
      //      if(max){
      //          if(parseInt(min) > parseInt(max)){
      //              alert('Minimum length can not be greater than maximum length');
      //              return false;            
      //          }
      //      }

      //      return true;

      // });

      // $('#max_text_box_length').on('change',function(evt){
         
      //      let max = $(this).val();
      //      let min = $('#mini_text_box_length').val();
      //      if(min){
      //          if(parseInt(min) > parseInt(max)){
      //             alert('Maximum length can not be less than minimum length');
      //              return false;  
      //          }
      //      }

      //      return true;

      // });
      
      //  $('#min_number_value').on('change',function(evt){
          
      //      let min = $(this).val();
      //      let max = $('#max_number_value').val();
      //      if(max){
      //          if(parseInt(min) > parseInt(max)){
      //             alert('Minimum value can not be greater than maximum value');
      //              return false;            
      //          }
      //      }

      //      return true;

      // });

      // $('#max_number_value').on('change',function(evt){
         
      //      let max = $(this).val();
      //      let min = $('#min_number_value').val();
      //      if(min){
      //          if(parseInt(min) > parseInt(max)){
      //             alert('Maximum value can not be less than minimum value');
      //              return false;  
      //          }
      //      }

      //      return true;

      // });

    });

     
  </script>
  <script type="text/javascript">
$(function(){
 $(".expand").on("click",function(){

    // $(this).next().slideToggle(200);

    $expand=$(this).find(">:first-child");
    if($expand.text()=="+"){
      $expand.text("-");
    }else{
      $expand.text("+");
    }
  });
});
 $(document).ready(function(){
    $('#role-multi').multiselect();
    $('#user-multi').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            maxHeight: 400
      });
      $('.chk-access').change(function(){
      $('.chk-access').removeAttr('disabled');
        chk_val=$(this).val();
        if($(this).prop('checked'))
        {
        
          if(chk_val=='3')
          {
          $('.div-role-multi').show();
          }
          if(chk_val=='4')
          {
            $('.div-user-multi').show();
          }if(chk_val==2){
              $('.chk-access').attr('disabled','true')
              $('.chk-access').prop('checked',false);
              $(this).removeAttr('disabled');
              $(this).prop('checked',true);
          }
        }else{
         if(chk_val=='3'){
              $('.div-role-multi').hide();

            }if(chk_val=='4'){ 
            $('.div-user-multi').hide();
            
            }

          }

        });

       $(document).ready(function(){
        $('#role-multi').multiselect();
        $('#user-multi').multiselect({
                includeSelectAllOption: true,
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                maxHeight: 400
          });
          $('.chk-access').change(function(){
          $('.chk-access').removeAttr('disabled');
            chk_val=$(this).val();
            if($(this).prop('checked'))
            {
            
              if(chk_val=='3')
              {
              $('.div-role-multi').show();
              }
              if(chk_val=='4')
              {
                $('.div-user-multi').show();
              }if(chk_val==2){
                  $('.chk-access').attr('disabled','true')
                  $('.chk-access').prop('checked',false);
                  $(this).removeAttr('disabled');
                  $(this).prop('checked',true);
              }
            }else{
             if(chk_val=='3'){
                  $('.div-role-multi').hide();
                }if(chk_val=='4'){ 
                $('.div-user-multi').hide();
                }

              }

            });
        });
       
    });

   let dropFiled = function(e){
      console.log(':)');
   }

  </script>
  <script type="text/javascript">
    
    $( window ).on( "load", function() {
      group_id = '{{(isset($form->group_id)) ? base64_encode($form->group_id) : ''}}';
      emptybody = $("#dataTable tbody tr:eq(0) td:eq(1)").html();
      if((group_id != '' && group_id != undefined) && (emptybody != '' && emptybody != undefined))   
        $('#redirecttoForm').removeClass('hide');

      $(document).on('click','#redirecttoForm',function(){
        swal("Good job!", "Form submitted successfully!" , "success");
        window.location = '{{ url('admin/form') }}/'+group_id+'? type='+'{{ base64_encode('form') }}';
      });

    });
  </script>
@endsection



