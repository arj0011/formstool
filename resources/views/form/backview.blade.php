@extends('layouts.app')
@section('content') 
@if(session()->has('msg'))
    <div class="alert alert-{{session('color')}} fade in alert-dismissible" style="margin-top:18px;  ">
    <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
    {{ session('msg') }}.
    </div>
    @endif
    @if($errors->first())
          <div class="alert alert-danger fade in alert-dismissible">
          <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
          <strong>Failed! </strong>{{ $errors->first() }}.  
          </div><br/>
      @endif
    <section class="content-header">
      <h1>
        Form Title
        <small></small>
      </h1>
     </section>
  <!-- Main content -->
    <section class="content">
      <div class="row">
        <!-- left column -->
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title"></h3>
            </div>
        <div class="box-body">
          @if(isset($user_form))
           {{ Form::open(array('url' => 'admin/form/updateSubmission')) }}
           {{-- Form::hidden('role_id', $role->id) --}}
           @else
            {{ Form::open(array('url' => 'form/saveFormData/')) }}
           @endif
           
             <div style="overflow-x:auto;">
              <table id="forms-field-table" class="table table-bordered table-responsive table-striped table-hover">
                @isset($form)
                @if($form->form_type=="Vertical")
                          <thead>
                          </thead>
                          <tbody>
                       <!--Tabular Form Layout-->
                         @else
                            <tbody>
                               @if(isset($tabular_setting) && isset($form_fields))
                               <?php
                                $row_data=isset($tabular_setting->row_data)?json_decode($tabular_setting->row_data):[];
                                ?>
                                <tr>
                                 <th>Sr.no </th>
                                 <th>#</th>
                                 @foreach($form_fields as $ff)
                                 <th>{{ $ff->field_title }}</th>
                                  @endforeach
                                </tr>
                               @forelse($row_data as $rd)
                                <tr>
                                   <td>{{ $rd->row_no }} </td>
                                   <td>{{ $rd->row_label }} </td>
                                  @foreach($form_fields as $f)
                                  <td>
                                  <div class="form-group">
                                      <input type="text" value="" name="" class="form-control"   placeholder="">
                                   </div> 
                                  </td>
                                  @endforeach
                               </tr>
                               @empty
                               <tr> <td colspan="2">Template Not Found </td> </tr>
                                @endforelse
                         @endif
                       <tfoot>
                       </tfoot>
                @endif
                @endisset
              </tbody>
              </table>
             </div> 
       
          </div>
         <div style="margin: 3px 7px 0px 15px;" class="box-footer">
              <input type="hidden" id="input-form-id" value="@isset($form){{ $form->id}} @endisset"/>
              <input type="hidden" id="form-type" value="@isset($form) {{ $form->form_type}} @endisset" />
              <a href="{{url('admin/form')}}" id="btn-backField" class="btn btn-primary">Back</a>
              <button  id="btn-updateField" class="btn btn-success">Submit</button>
         </div>
         </div>
       </div>
   </div>
        <!--row--> 
  </section>  
<!-- /. section content -->
@endsection


