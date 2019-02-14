
@extends('layouts.app')
@section('content') 
@section('css-script')
<link rel="stylesheet" type="text/css" href="{{ asset('public/bootstrap/css/bootstrap-multiselect.css')}}"/>
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
        Add Form
      </h1>
       {{ Breadcrumbs::render('add-form') }}
    </section>
<!-- Main content -->
<section class="content">
<div class="row">
    <div class="col-md-12">
      @if(session()->has('msg'))
        <div class="alert alert-{{session('color')}} fade in alert-dismissible" style="margin-top:18px;  ">
        <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
        {{ session('msg') }}.
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
         
       </ul>
    <div class="tab-content">
         <div class="tab-pane @if(!isset($form)){{ 'active' }}@endif" id="add-form">
            {{ Form::open(array('url' => 'admin/form/add' )) }}
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
                        <option @if($group->id == old('group')){{'selected'}}@endif value="{{$group->id}}">{{ucwords($group->group_name)}}</option>
                      @empty
                        <option value="">Group not available</option>
                      @endforelse
                     </select>
                     <span class="text-danger">{{ $errors->first('group') }}</span> 
                    </div>  

                    <div class="form-group">
                    <label for="form-name">*Form Name</label>
                    <input type="text" @if(old('name')) value="{{old('name')}}" @elseif(isset($form)) value="{{$form->name}}" @endif" id="form-name" class="form-control" name="name" placeholder="Enter Form Name" style="text-transform: capitalize;" required>
                     <span class="text-danger">{{ $errors->first('name') }}</span> 
                     <span class="text-danger" id="row_dim">Special characters are not allowed.</span>
                    </div>
                    <div class="form-group">
                      <label for="form-type">*Form Type</label>
                      <select @isset($form){{'disabled'}} @endisset  id="form_type" name="form_type" required class="form-control"> 
                          <option value=''> Select Form Type</option>
                           <option @if(in_array('Tabular' , [isset($form->form_type) , old('form_type')])) {{'selected'}} @endif  value='Tabular'>Tabular(2D)</option>
                           <option  @if(in_array('Vertical' , [isset($form->form_type) , old('form_type')])) {{'selected'}} @endif value='Vertical'>Vertical</option>
                      </select>
                        <span class="text-danger">{{ $errors->first('form_type') }}</span> 
                   </div>
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
     </div>
   </div>
</div>
</div>
</section>
@endsection
@section('js-script')
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

