@extends('layouts.app')
@section('content') 
@section('css-script')
<link rel="stylesheet" type="text/css" href="{{ asset('public/bootstrap/css/bootstrap-multiselect.css')}}"/>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
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
</style>
@endsection 
     <section class="content-header">
     <h1>
       @if(!isset($form))
       Add New Form <small> {{ ucwords(str_replace('_', ' ',$form_name)) }} </small>
       @else Edit Form Template <small> {{ ucwords(str_replace('_', ' ',$form_name)) }} </small>
       @endif
       <small> </small>
     </h1>
     @if(!isset($form))
       {{ Breadcrumbs::render('add-form') }}
     @else
       {{ Breadcrumbs::render('edit-form') }}
     @endif
    </section>
     
<!-- Main content -->
<section class="content">
<div class="row">
    
    <div class="col-md-12">
      @if(session()->has('msg'))
        <div class="alert alert-{{session('color')}} fade in alert-dismissible" style="margin-top:18px;  ">
        <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
        {{ session('msg') }}
        </div>
      @endif
      @if($errors->first())
       <div class="alert alert-danger fade in alert-dismissible">
         <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
         <strong>Failed!</strong>{{ $errors->first() }}.  
       </div> 
       <br/>
      @endif
    </div>

    <div class="col-md-12">
    <div class="nav-tabs-custom">
       <ul class="nav nav-tabs">
           @if(isset($form)&& isset($form_fields))@if(count($form_fields)>0)<li class="active"><a href="#add-form" data-toggle="tab" aria-expanded="true">Update Form</a></li>@endif @endif
           @isset($form_fields)<li class=""><a href="#field-settings" data-toggle="tab" aria-expanded="false">Field Setting</a></li> @endif
       </ul>
    <div class="tab-content">
         <div class="tab-pane @if(count($form_fields)>0){{'active'}}@endif" id="add-form">
             @isset($form)
             {{ Form::open(array('url' => 'admin/form/update')) }}
             {{-- {!! Form::hidden('form_id',$form->id) !!} --}}
             {!! Form::hidden('form_id',$form_id) !!}
             @endisset
             <div class="box box-solid">
                <div class="box-body">
                 
                   <div class="col-md-6">
                      <div class="form-group">
                        <label for="forms">Select Group</label>
                        <select name="group" class="form-control" id="group" required>
                          @forelse($form_groups as $key => $group)
                            @if ($key == '0')
                              <option value="">--select--</option>
                            @endif
                              <option @if($group->id == old('group') || $group->id == $form->group_id){{'selected'}}@endif value="{{$group->id}}">{{ucwords($group->group_name)}}</option>
                            @empty
                              <option value="">Group not available</option>
                          @endforelse
                        </select>
                        <span class="text-danger">{{ $errors->first('group') }}</span> 
                      </div>
                        
                        <div class="form-group">
                        <label for="form-name">*Form Name</label>
                        <input type="text" value="@isset($form){{ $form->name}} @endisset" id="form-name"class="form-control" required name="name" placeholder="Enter Form Name">
                         <span class="text-danger">{{ $errors->first('name') }}</span> 
                         <span class="text-danger" id="row_dim">Special characters are not allowed.</span>
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
                    @if(!isset($form))
                        <div style="display:none;" id="div_no_of_fields" class="form-group">
                            <label for="Contact">*Number of Fields </label>
                            <input type="number" width="50" min="0" value="" id="no_of_fields" name="no_of_fields" class="form-control no_of_fields"   placeholder="Enter Number of Fields  ">
                           <span class="text-danger">{{ $errors->first('no_of_fields') }}</span> 
                        </div>
                       @endif
                      <div style="display:none;" id="tabular-form">
                          <div class="form-group">
                              <label for="no_rows">No Of Tables </label>
                              <input type="number" width="50" min="0" id="no_of_rows" class="form-control no_of_rows"  name="no_of_rows" value="" placeholder="Enter Number of Rows">
                              <span class="text-danger">{{ $errors->first('no_of_tables') }}</span> 
                          </div>
                   
                    </div>
            </div>
            </div>
              </div>
             <div class="box-footer">
              <button type="submit" class="btn btn-success">Submit</button>
              </div>
            </div>
        
           {{ Form::close()}}
<!-- form field setting -->
         <div class="tab-pane @if(count($form_fields)<=0){{'active'}} @endif" id="field-settings">
            {{-- Form::open(array('url' => 'admin/form/field_settings')) --}}
            <div class="box box-solid">
            <div class="box-body">
            <div>
             @isset($form)
             @if($form->form_type=="Tabular")
              <!--Tabular Form Layout Setting-->
                         <button id="" class="btn add-table btn-success">+Table</button>
                         <!-- start accordion-->
                         <div class="container">
                             <div class="row">
                                <div class="col-md-1">
                                  <span><b>Sr.no</b></span>
                                </div>
                               <div class="col-md-8">
                               <span><b>Table</b></span>
                               </div>
                               <div class="col-md-2">
                                   <span><b>Remove</b></span>
                               </div>
                             </div>
                             <div class=""row>
                                <div class="panel-group" id="accordion">
                                @isset($tables_setting)
                                @if(count($tables_setting)>0)
                                @foreach($tables_setting as $key=>$value)
                                 <div table-id="{{$value->id}}" class="row">
                                    <div class="col-md-1 row-no">
                                     <span>{{$key+1}}</span>
                                    </div>
                                    <div class="panel panel-default col-md-8">
                                      <div class="panel-heading">
                                        <h4 data-toggle="collapse" data-parent="#accordion" href="#collapse{{$key+1}}" class="panel-title expand">
                                            <div class="right-arrow pull-right"><i class="fa fa-plus text-success"></i></div>
                                            <a href="#">Table{{$key+1}}</a>
                                        </h4>
                                      </div>
                                <div id="collapse{{$key+1}}" class="panel-collapse collapse">
                                  <div class="panel-body">
                                  <!-- Table Rows Setting -->
                                  <div class="row col-md-12">
                                     <div class="control-group" id="fields">
                                           <label class="control-label" for="field1">Add Title</label>
                                           <div class="controls"> 
                                               <form role="form" autocomplete="off">
                                                   <?php $table_titles=json_decode($value->table_titles);?>
                                                  @if(count(json_decode($value->table_titles))>0)
                                                  @foreach($table_titles as $key=>$tt)
                                                   <div class="entry input-group col-xs-3">
                                                       <input class="form-control table-title" value="{{$tt}}" name="fields[]" class="table-title" type="text" placeholder="Type something" required/>
                                                       <span class="input-group-btn">
                                                         @if($key==count($table_titles)-1)
                                                           <button class="btn btn-success btn-add" type="button">
                                                             <span class="glyphicon glyphicon-plus"></span>
                                                           </button>
                                                         @else
                                                         <button class="btn btn-remove btn-danger" type="button">
                                                             <span class="glyphicon glyphicon-minus"></span>
                                                         </button>
                                                           @endif
                                                       </span>
                                                   </div>
                                                   @endforeach
                                                   @else
                                                   <div class="entry input-group col-xs-3">
                                                    <input class="form-control table-title" value="" name="fields[]" class="table-title" type="text" placeholder="Type something" required />
                                                     <span class="input-group-btn">
                                                          <button class="btn btn-success btn-add" type="button">
                                                             <span class="glyphicon glyphicon-plus"></span>
                                                           </button>
                                                     </span>
                                                   
                                                    </div>
                                                   @endif
                                               </form>
                                           <br/>
                                           </div>
                                    </div>
                                </div>

                                <div class="clearfix"></div>
                                <h4 class="row-col-title">Row Settings</h4>
                                
                                <div class="row">
                                  <div class="form-group col-md-4">
                                      <lable for="label-heading"><b>Label Heading</b></lable>
                                      <input type="text" name="" value="{{$value->label_heading}}" id="label-heading" class="form-control" placeholder="" required>
                                  </div> 
                                </div>
                               <table id="tbl-rows-setting" class="table table-bordered table-responsive table-striped table-hover">
                                       <thead>
                                          <tr>
                                             <th>Rows</th>
                                             <th>Label</th>
                                             <th>Actions</th>
                                          </tr>
                                       </thead>
                                       <tbody>
                                      <?php $row_data=json_decode($value->row_data); 
                                      ?>
                                       @if(count($row_data)>0)
                                       @foreach($row_data as $key=>$rd)
                                          <tr>
                                             <td>{{$key+1}}</td>
                                             <td>
                                                <div class="form-group">
                                                  {{--  <label id="row-label">label{{$key+1}}:</label> --}}
                                                   <input type="text" value="{{$rd->row_label}}" id="row-label1" class="form-control" placeholder="" required>
                                                   <span class="text-danger"></span>  
                                                </div>
                                             </td>
                                             <td>
                                                <a href="#" class="btn btn-xs btn-danger btn-remove"><i class="fa fa-trash"></i></a>
                                             </td>
                                          </tr>
                                        @endforeach
                                          @else
                                          <tr>
                                             <td>1</td>
                                             <td>
                                                <div class="form-group">
                                                   <input type="text" value="" id="row-label1" class="form-control" placeholder="" required>
                                                   <span class="text-danger"></span>  
                                                </div>
                                             </td>
                                             <td>
                                                <a href="#" class="btn btn-xs btn-danger btn-remove"><i class="fa fa-trash"></i></a>
                                             </td>
                                          </tr>
                                      
                                          @endif
                                        </tbody>
                                       <tfoot>
                                          <tr>
                                             <td colspan="3">
                                                <button id="btn-rows-addField" class="btn btn-primary">Add</button>
                                             </td>
                                          </tr>
                                          <tr>
                                          </tr>
                                       </tfoot>
                               </table>
                                  <!-- Table Colums Setting-->
                                  <div class="clearfix"></div>
                                 <h4 class="row-col-title">Column Settings</h4>
                                 <table class="table table-striped table-hover table-condensed table-responsive" id="col-table{{$key+1}}">
                                    <thead style="">
                                     <tr>
                                         <th></th>
                                         <th>Field Label</th>
                                         <th>Field Type</th>
                                         <th>Action</th>
                                     </tr>
                                    </thead>
                                    <tbody class="sortable">
                                        @forelse($form_fields as $key=>$ff)
                                        @if($ff->table_id==$value->id)
                                       <tr id="row-{{$ff->id}}">
                                            <td><i class="fa fa-arrows"></i></td>
                                            <td style="text-transform: capitalize;">{{$ff->field_title}}</td>
                                            <td style="text-transform: capitalize;">{{$ff->field_type['name']}}</td>
                                            <td>&nbsp;&nbsp;<button data-id="{{ $ff->id }}" class="btn btn-primary btn-editTable"><i class="fa fa-edit"></i></button>&nbsp;&nbsp;<button data-id="{{$ff->id}}" class="btn btn-danger btn-deleteTable"><i class="fa fa-trash"></i></button></td>
                                       </tr>
                                       @endif
                                        @empty
                                        @endforelse
                                    </tbody>
                                     <tfoot>
                                          <tr>
                                             <td colspan="3">
                                                <button table-id="col-table{{$key+1}}"   class="btn btn-primary fieldModalAddBtn">Add</button>
                                             </td>
                                          </tr>
                                         
                                       </tfoot>
                                </table>
                                 <input type="hidden" class="table-id uniqueTableId" value="{{$value->id}}" name="table_id">
                                 <button id="btn-table-setting" class="btn btn-success update-table-setting">Save Template</button>
                                 
                                <!-- Colums Setting -->
                               </div>
                                    </div>
                                  </div>
                                     <div class="col-md-2 remove-btn">
                                    <a class="remove-table text-danger" dynamicTab="false"="false" href="#"><i class="fa fa-remove"></i></a>    
                                   </div>
                                </div>
                                @endforeach
                                @endif
                                @endisset    
                                 </div>
                                </div>
                                 
                             </div>
                          
                <!--end accordion -->
               @endif
               @endisset
               <div class="col-md-12 text-left" style="margin-bottom: 10px;margin-left: 80px;">
          <a class="btn btn-primary" style="border-radius: 10px; padding-left: 20px;padding-right: 20px;" href="{{url('admin/form/'.$form->id.'?type='.base64_encode('forms').'')}}">Forms List</a>
        </div>
</div>
            </div>   
              </div>
             <div class="box-footer">
              <input type="hidden" id="input-form-id" value="@isset($form){{ $form->id}} @endisset"/>
              <input type="hidden" id="form-type" value="@isset($form) {{ $form->form_type}} @endisset" />
           @isset($form)
              @if($form->form_type=="Vertical")
         
            @endif
            @endisset
              </div>
            </div>

        
           {{-- Form::close() --}}
         </div>
       </div>
   </div>



  <div class="col-md-12 text-right" style="margin-bottom: 10px;">
          <a class="btn btn-default" style="border-radius: 10px; background: #ffffff ; padding-left: 20px;padding-right: 20px;" href="{{url()->previous()}}">Back</a>
        </div>
</div>

    
    
    <!-- Add table dynamic-->
    <div style="display:none;" id="tbl-div">
        <div class="row" style="margin-top: 5px;">
        <div class="col-md-1 row-no">
          <span>{sr_no}</span>
            
        </div>
     <div class="panel panel-default col-md-8">
       <div class="panel-heading">
         <h4 data-toggle="collapse" data-parent="#accordion" href="#{id}" class="panel-title expand">
             <div class="right-arrow pull-right"><i class="fa fa-plus text-success"></i></div>
             <a href="#">{table_title}</a>
         </h4>
       </div>
    <div id="{id}" class="panel-collapse collapse">
   <div class="panel-body">
   <!-- Table Rows Setting -->
   <div class="row col-md-12">
      <div class="control-group" id="fields">
            <label class="control-label" for="field1">Add Title</label>
            <div class="controls"> 
                <form role="form" autocomplete="off">
                    <div class="entry input-group col-xs-3">
                        <input class="form-control table-title" name="fields[]" class="table-title" type="text" placeholder="Type something" / required>
                    	<span class="input-group-btn">
                            <button class="btn btn-success btn-add" type="button">
                                <span class="glyphicon glyphicon-plus"></span>
                            </button>
                        </span>
                    </div>
                </form>
            <br />
            </div>
     </div>
 </div>
   
   <div class="clearfix"></div>
   <h4 class="row-col-title">Row Settings</h4>
   <div class="row">
    <div class="form-group col-md-4">
      <lable for="label-heading"><b>Label Heading</b></lable>
     <input type="text" value="" id="label-heading" class="form-control" placeholder="">
   </div> 
   </div>
  <table id="tbl-rows-setting" class="table table-bordered table-responsive table-striped table-hover">
        <thead>
           <tr>
              <th>Rows</th>
              <th>Label</th>
              <th>Actions</th>
           </tr>
        </thead>
        <tbody>
           <tr>
              <td>1</td>
              <td>
                 <div class="form-group">
                    <input type="text" value="" id="row-label1" class="form-control" placeholder="" required>
                    <span class="text-danger"></span>  
                 </div>
              </td>
              <td>
                 <a href="#" class="btn btn-xs btn-danger btn-remove"><i class="fa fa-trash"></i></a>
              </td>
           </tr>

        </tbody>
        <tfoot>
           <tr>
              <td>
<!--                 <button id="btn-rows-updateField" class="btn btn-success">Update</button>-->
                 <button id="btn-rows-addField" class="btn btn-primary">Add</button>
              </td>
           </tr>
           <tr>
           </tr>
        </tfoot>
</table>
   <!-- Table Colums Setting-->
   <div class="clearfix"></div>
  <h4 class="row-col-title">Column Settings</h4>
   <table class="table table-striped table-hover table-condensed table-responsive" id="{col-table}">
                                    <thead style="">
                                     <tr>
                                         <th></th>
                                         <th>Field Type</th>
                                         <th>Field label</th>
                                         <th>Action</th>
                                     </tr>
                                    </thead>
                                    <tbody class="sortable"></tbody>
                                     <tfoot>
                                          <tr>
                                             <td>
                               <!--            <button id="btn-rows-updateField" class="btn btn-success">Update</button>-->
                                                <button   class="btn btn-primary fieldModalAddBtn">Add</button>
                                             </td>
                                          </tr>
                                         
                                       </tfoot>
                       </table>
  <input type="hidden" class="table-id uniqueTableId" value="" name="table_id">
  <button id="btn-table-setting" class="btn btn-success update-table-setting">Update Setting</button>
   <!-- Colums Setting -->
</div>
     </div>
   </div>
    <div class="col-md-2 remove-btn">
     <a class="remove-table text-danger" dynamicTab="true" href="#"><i class="fa fa-remove"></i></a>    
    </div>
    </div>
   </div>
 <!-- end table title -->
 <!-- Fied Add Edit Model -->
     <!-- Modal -->
      <div class="modal fade bd-example-modal-lg" id="fieldAddModal" tabindex="-1" role="dialog" aria-labelledby="fieldAddModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
         <form class="form" id="addEditForm">
           <input type="hidden" name="form_id"  value="{{Request::segment(4)}}" >
           <input type="hidden" name="field_id" value="" id="field-id">
          <div class="modal-content">
            <div class="modal-header text-center">
              <h2 class="modal-title" id="fieldAddModalLabel">Column Settings</h2>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <p class="text-danger" id="alredyexistmsg" style="display: none;">Field name already exist !</p>
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
                    </div>
                 </div>
                 <p class="text-danger" id="specialCharacter" style="display: none;">Special chracter's are not allowed</p>
                 <div class="col-md-4">
                    <div class="form-group">
                      <label>Field label</label>
                       <input type="text" style="text-transform: capitalize;" name="field_lable" placeholder="field label" class="form-control" id="field-lable" required>
                    </div>
                 </div>
                 <div class="col-md-4">
                    <div class="form-group">
                      <label>Validation</label>
                         <select class="form-control" name="validation" id="validation" required>
                         <option value="">--select--</option>
                         <option value="0">Optional</option>
                         <option value="1">Required</option>
                         <option value="2" id="uniqueValidationOption">Required & Unique</option>
                      </select>
                    </div>
                  </div>
               </div>

                <div class="row" id="min">

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Minimum Length</label>
                       <input type="number" name="mini" placeholder="field label" class="form-control" id="mini_text_box_length">
                    </div>
                  </div>
                   <div class="col-md-4" id="max">
                    <div class="form-group">
                      <label>Maximum Length</label>
                       <input type="number" name="max" placeholder="field label" class="form-control" id="max_text_box_length">
                    </div>
                  </div>
               </div>

               <!--  <div class="row" id="checkEditor">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Use Check Editor</label>
                      <select class="form-control" name="check_editor" id="check-editor">
                        <option value="yes">Yes</option>
                        <option value="no" selected>No</option>
                      </select>
                    </div>
                 </div>
               </div> -->

                <div class="row" id="optionBtn">
                 <table class="table radioValueTable table-condensed lableTable">
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
  <!-- End Validation Model-->
</section>
@endsection
@section('js-script')
<!-- page  js scripts-->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.js"></script>
<!-- jQuery 3 -->

<script src="{{ asset('public/bootstrap/js/jquery.dataTables.min.js')}}"></script>
     <!-- Bootstrap DataTables -->
 <script src="{{ asset('public/bootstrap/js/dataTables.bootstrap.min.js')}}"></script>
 <script src="{{ asset('public/bootstrap/js/bootstrap-multiselect.js')}}"></script>

 <script>
$(function(){
 $("body").on("click",'.expand',function(){

    // $(this).next().slideToggle(200);
   $expand=$(this).find(">:first-child i");
    if($expand.hasClass('fa-plus')){
      $expand.toggleClass("fa-minus");
      $expand.toggleClass("text-danger text-success");
    }else{
      $expand.toggleClass("fa-plus");
      $expand.toggleClass("text-danger text-success");
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
           $('#role-multi').multiselect("deselectAll",false).multiselect("refresh");
           }if(chk_val=='4'){ 
            $('.div-user-multi').hide();
            $('#user-multi').multiselect("deselectAll", false).multiselect("refresh");
             }
            }

        });

// onchane form type
$('#form_type').change(function(e){
  form_type=$(this).val();
  $('#tabular-form').hide();
  $('#div_no_of_fields').hide();
  if(form_type=="Vertical")
  {
  $('#div_no_of_fields').show();
  $('#no_of_fields').attr('required','true');
  $('#no_of_rows').removeAttr('required');
  $('#no_of_cols').removeAttr('required');
  }else if(form_type=="Tabular")
  { 
  $('#no_of_fields').removeAttr('required');
  }
});
//add  new row in Rows setting
$('body').on('click','#btn-rows-addField',function(e)
{   table=$(this).closest('.table');
    e.preventDefault();
    RowCount=table.find('tbody tr').length;
    order=parseInt(RowCount)+1;
    field='<div class="form-group"><input type="text" value="" name="lablel'+order+'" id="label'+order+'" class="form-control" required placeholder=""> </div>';;
    action='<a href="#"  class="btn btn-xs btn-danger btn-rowsRemove"><i class="fa fa-trash"></i></a>';
    row='<tr><td>'+order+'</td><td>'+field+'</td><td>'+action +'</td></tr>';
    table.append(row);
});
//Colums setting js
        $('body').on('click','.fieldModalAddBtn',function(e)
        {   
           e.preventDefault();
           table=$(this).closest('.table').attr('id');
           table_id=$(this).closest('.panel-body').find('.table-id').val();
           table_id=$(this).closest('.panel-body').find('.uniqueTableId').val();
           $('#addEditForm').attr('db-table-id',table_id);
           $('#addEditForm').attr('table-id',table);
           $('#btn-row-addEdit').text('Add');
           $( "#field-id" ).removeAttr('name');
           $( "#field-id" ).val('');
           $( "#field-type" ).attr('name' , 'field_type');
           $( "#field-type" ).removeAttr('disabled');
           $( '#field-type').val('');
           $( '#field-lable' ).val('');
           $( '#validation' ).val('');
           $( '#mini_text_box_length' ).val('');
           $( '#max_text_box_length' ).val('');
           $( '#min_number_value' ).val('');
           $( '#max_number_value' ).val('');
           
           $( '#validation option:eq(0)');
           $('.radioValueTable tbody').html(''); 
           $( '#multiselect option').prop("selected",false);
           hideAllInput();
           $('#alredyexistmsg').css('display','none');
           $('#specialCharacter').css('display','none');
           $('#fieldAddModal').modal('show');
        });
       
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
    hideAllInput();
    $('body').on('submit', '#addEditForm' ,function(e){
        e.preventDefault();
        table=$(this).attr('table-id');
        // table_id=$(this).closest('.panel-body').find('.table-id').val();
        // table_id=$('.table-id').val();
        field_type = $('.field_type').val();
        field_lable = $('#field-lable').val();
        var regex = new RegExp("^[A-Za-z0-9 _]*[A-Za-z0-9][A-Za-z0-9 _]*$");
        var key = field_lable;

        if (!regex.test(key)) {
          return $('#specialCharacter').css('display','block');false;
        }
   
        db_table_id=$(this).attr('db-table-id');
        if(db_table_id==undefined) db_table_id=0;
        if($( "#field-id" ).val()>0){
          var action = "{{route("form/updateTabularFormField")}}";
         }else{
          var action = "{{route("form/storeTabularFormField")}}";
         }
         let form = $(this);
         let data = form.serialize()+"&table_id="+db_table_id+"&field_type="+field_type;
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
                table_id=response.data.table_id;
                if(response.data.action.toLowerCase()==='update')
                {
                 $('#row-'+response.data.id).find('td').eq(1).text(response.data.lable); 
                }
                else{
                        $("#"+table).closest('.panel-body').find('.table-id').val(table_id);
                        $("#"+table).closest('.panel-body').find('.uniqueTableId').val(table_id);
                        
                        $("#"+table+" tbody").append(dynamicRow(response.data.type,response.data.lable,response.data.id));
                     }
                $('.radioValueTable tbody').html('');     
                $("#fieldAddModal").modal('hide'); 
                swal("Good job!", response.message , "success");
                }else{
                   // swal(response.message,"danger");
                   console.log(response.status);
                   $('#alredyexistmsg').css('display','block');

                }
          },
          'error' : function(error){
            console.log(error);
          },'complete': function() {
                  $('#btn-row-addEdit').removeAttr("disabled","disabled");
               }
        });

      });

      $('.field_type').on('change',function(){
            let field_type = $(this).val();
          if(field_type.length > 0){
            showInputFiles(field_type);
            }else{
              hideAllInput();
           }

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

   
    // var addRadioValueRow = function(lable = ''){
    //          return '<tr><td><input class="form-control" name="option[]" type="text" value="'+lable+'" placeholder="label" required></td><td><button class="btn btn-danger btn-row-lable-remove" type="button">Remove</button></td></tr>';
    //   }
   window.showInputFiles=function(field_type=null){
          
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

 //Dynamic row generater function
  var dynamicRow=function(type,lable,id){
        return '<tr><td><i class="fa fa-arrows"></i></td><td style="text-transform: capitalize;">'+lable+'</td><td style="text-transform: capitalize;">'+type+'</td><td></button>&nbsp;&nbsp;<button data-id="'+id+'" class="btn btn-primary btn-editTable btn-edit"><i class="fa fa-edit"></i></button>&nbsp;&nbsp;<button data-id="'+id+'" class="btn btn-danger btn-deleteTable"><i class="fa fa-trash"></i></td><tr>';
       }

// end colums setting js

// Update Tabular Form Settings
$(document).on('click','.update-table-setting',function(){
    var data=[];
    data['row_data']=[];
    form_type=$('#form-type').val().trim();
    form_id=$('#input-form-id').val();
    table_id=$(this).closest('.panel-body').find('input[name="table_id"]').val();
    if(table_id==undefined) table_id=0;
    select=$(this).closest('.panel-body');   
    select.find('#tbl-rows-setting tbody tr').each(function(rowIndex,r){
    td=$(this).find('td');
    row_no=td.eq(0).text();
    row_label=td.eq(1).find('input').val();
    data['row_data'].push({"row_no":row_no,"row_label":row_label});
    });
 var tbl_titles=select.find('.table-title').map(function(){
        return this.value;
}).get();
data['tbl_titles']=tbl_titles;
data['label_heading']=select.find('#label-heading').val();
if(form_id!='')
  {
      $.ajax({
              "headers":{
                  'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                  },
              'type':'post',
              'url' :'{{ trim(route("form/tableSetting"))}}',
              'data':{'table_id':table_id,'form_id':form_id,'form_type':form_type,'table_titles':data['tbl_titles'],'row_data':data['row_data'],'label_heading':data['label_heading']},
              'success' : function(response){
                  console.log(response);
                  if(response.status)
                  {
                    swal("Good job!", response.message,"success");
                  } else swal("error!", response.message,"danger");
              
               },error: function (error){
                    console.log(error);
                }
            });
  }

});

$('.btn-addRule').click(function(){
  $('#modal-validate').modal('show');
});
// remove a row from field setting

$('body').on('click','.btn-remove,.btn-rowsRemove',function(){
$(this).closest('tr').remove();
})
// add  table in tabular form layout.
c=1;
$('.add-table').click(function(){
    len=$('#accordion :last-child').find('.row-no span').text();
    if(len=='') len=c;
    else len=parseInt(len)+1;
    table_title="Table"+len;
    id="collapse"+len;
    tbl_html=$('#tbl-div').html(); 
    tbl_html=tbl_html.trim().replace(/{id}/g,id);
    tbl_html=tbl_html.trim().replace(/{sr_no}/g,len);
    tbl_html=tbl_html.trim().replace(/{table_title}/g,table_title);
    tbl_html=tbl_html.trim().replace(/{col-table}/g,"col-table"+len);
    tbl_html=tbl_html.trim().replace(/{table-id}/g,"col-table"+len);

    $('#accordion').append(tbl_html);
});
// remove table
$("body").on("click",'.remove-table',function(e){

   click=$(this);
   let dynamicTab = $(this).attr('dynamicTab');

   if(dynamicTab == 'true'){
          swal({
               title: "Are you sure?",
               text: "You will not be able to recover this table!",
               type: "warning",
               showCancelButton: true,
               confirmButtonClass: "btn-danger",
               confirmButtonText: "Yes, delete it!",
               closeOnConfirm: false
               },
                function(){
                 click.closest('.row').remove();
                 swal("Good job!", 'Successfully Removed' , "success");
              });
   }else{


   console.log($('#input-form-id').val());

   if($('#input-form-id').val() == undefined){
      console.log(':)');
   }else{
     form_id=$('#input-form-id').val().trim();
     table_id=$(this).closest('.row').attr('table-id').trim();
     if(table_id!='' || table_id!= undefined)
     var click=$(this);
               swal({
               title: "Are you sure?",
               text: "You will not be able to recover this table!",
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
                      'url' :'{{ url("admin/form/deleteTable")}}/'+form_id+'/'+table_id,
                      'beforeSend': function() {
                        $('.confirm').attr("disabled","disabled");
                      },
                      'success' : function(response){
                        if(response.status){
                           click.closest('.row').remove();
                           swal("Good job!", response.message , "success");
                          }else{
                              swal(response.message , "danger");
                          }
                    },'complete': function() {
                        $('.confirm').removeAttr("disabled","disabled");
                      }
                 });
     });
   }
  }
});

});

$(function()
{
    $(document).on('click', '.btn-add', function(e)
    {
           e.preventDefault();
           var controlForm=$(this).closest('.controls').find('form:first'),
           currentEntry=$(this).parents('.entry:first'),
           newEntry=$(currentEntry.clone()).appendTo(controlForm);
           newEntry.find('input').val('');
           controlForm.find('.entry:not(:last) .btn-add')
           .removeClass('btn-add').addClass('btn-remove')
           .removeClass('btn-success').addClass('btn-danger')
           .html('<span class="glyphicon glyphicon-minus"></span>');
    }).on('click', '.btn-remove', function(e)
    {
		$(this).parents('.entry:first').remove();

		e.preventDefault();
		return false;
    });
});
    //store edit table feild data
      $(document).on('click','.btn-editTable',function(){

          let id = $(this).attr('data-id');

             $.ajax({
                "headers":{
                'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                },
                'type':'GET',
                'url' : '{{route('form/editTableFormField')}}',
                'data' : { method : '_GET' , id : id },
                'success' : function(response){
                  $('.radioValueTable tbody').html('');
                  $('#btn-row-addEdit').addClass('btn-update');
                  $('#btn-row-addEdit').text('Save Changes');
                  $('#alredyexistmsg').css('display','none');
                  $('#specialCharacter').css('display','none');
                  $('#fieldAddModal').modal('show');
                  $( "#field-type" ).val(response.FormField.field_type_id);
                  $( "#field-lable" ).val(response.FormField.field_title);

                  $( "#mini_text_box_length" ).val(response.min);
                  $( "#max_text_box_length" ).val(response.max);

                  $( "#min_number_value" ).val(response.min);
                  $( "#max_number_value" ).val(response.max);
                  

                  $( '#validation option').prop("selected",false);
                  $( '#multiselect option').prop("selected",false);
                  $( "#validation").find('option[value="'+response.validationVal+'"]').prop("selected",true);
                  $( "#multiselect").find('option[value="'+response.multiselectVal+'"]').prop("selected",true);
                  $( "#field-type" ).removeAttr('name');
                  $( "#field-type" ).attr('disabled' , true);
                  $( "#field-id" ).val(id);
                showInputFiles(response.FormField.field_type_id);
                  $( "#field-id" ).attr('name','field_id');
                  
                  if(response.FormField.field_options){
                      // response.FormField.field_options.map(function(index,value){
                      //  $(".radioValueTable tbody").append(addRadioValueRow(value));
                      // });

                      optArr = response.FormField.field_options.split(',');
                      $.each(optArr,function(i){
                        $(".radioValueTable tbody").append(addRadioValueRow(optArr[i]));
                      });

                  }

              },
           });

        });
        
  // delte table field
  
   //store form feild data
      $(document).on('click','.btn-deleteTable',function(e){

          console.log(':)');
  
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
                'url' : '{{ route("form/deleteTableFormField") }}',
                'data' : { method : '_DELETE' , id : id },
                'beforeSend': function() {
                      $('.confirm').attr("disabled","disabled");
                     },
                'success' : function(response){
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

      $('.remove-btn').css('lineHeight' , '2.5');
      $('.panel-heading').css('backgroundColor' , '#fff');

var addRadioValueRow = function(lable = ''){
             return '<tr><td><input class="form-control" name="option[]" type="text" value="'+lable+'" placeholder="label" required></td><td><button class="btn btn-danger btn-row-lable-remove" type="button">Remove</button></td></tr>';
      }
 </script>
 <script type="text/javascript">
  $(document).ready(function(){
        $('#row_dim').hide(); 
    $('#form-name').change(function(){
      var form_name = $('#form-name').val();
      var regex = new RegExp("^[A-Za-z0-9 _]*[A-Za-z0-9][A-Za-z0-9 _]*$");
      var key = form_name;
      if (!regex.test(key)) {
          $('#row_dim').show();
           $(':input[type="submit"]').prop('disabled', true);
        }
       else {
        $(':input[type="submit"]').prop('disabled', false);
            $('#row_dim').hide(); 
        } 
        
    });
  });
</script>
@endsection
