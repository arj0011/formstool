
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
     <section class="content-header">
      <h1>
        Publish Form
      </h1>
       {{ Breadcrumbs::render('add-form') }}
    </section>
<!-- Main content -->
<section class="content">
<div class="row">
    <div class="col-md-12">
    <div class="nav-tabs-custom">
      <div class="tab-content">
         <div class="tab-pane @if(!isset($form)){{ 'active' }}@endif" id="add-form">
            {{ Form::open(array('url' => route('form/publish-store') , 'novalidate' => true)) }}
             <input type="hidden" name="id" value="{{Request::segment('4')}}">
            <div class="box box-solid">
              <div class="box-body">
                <div class="permission">
                  @isset($form)
                  <?php
                    $access_role = json_decode($form->access_role); ?>
                  @endisset
                    <label>Publish For</label>
                    <div class="checkbox">
                      <label><input type="checkbox" @if(isset($access_role))@if($access_role->all_role) {{ 'checked' }} @endif @endisset name="publish_for[]"  class="chk-access" value="1">All Role</label>
                    </div>
                    <div class="checkbox">
                      <label><input type="checkbox" @if(isset($access_role))@if(count($access_role->roles)>0) {{ 'checked' }} @endif @endisset  name="publish_for[]" class="chk-access" value="2" >Specific Role</label>
                    </div>
                    <div style="display:none;" class="form-group div-role-multi">
                        <label for="role-multi">Select Role:</label>
                          <select name="role_multi[]" multiple class="form-control" id="role-multi">
                          @isset($roles)
                          @forelse($roles as $r)
                          <option value="{{$r->id}}">{{ $r->name }}</option>
                            @empty
                            <option value="">No Roles</option>
                            @endforelse
                            @endisset
                          </select>
                    </div>
                    <div class="checkbox">
                     <label><input type="checkbox" @if(isset($access_role))@if(count($access_role->users)>0) {{ 'checked' }} @endif @endisset name="publish_for[]" class="chk-access"  value="3" >Specific User</label>
                    </div>
                    <div style="display:none;" class="form-group div-user-multi">
                          <label for="user-multi">Select User:</label>
                            <select name="user_multi[]" multiple class="form-control" id="user-multi">
                              @isset($users)
                              @forelse($users as $u)
                            <option value="{{$u->id}}">{{ $u->first_name.' '.$u->last_name }}</option>
                              @empty
                              <option value="">No Users </option>
                              @endforelse
                              @endisset
                            </select>
                    </div>
                </div>
              </div>
            </div>
            <div style="margin: 3px 7px 0px 15px;" class="box-footer">
              <a class="btn btn-default" href="{{ url()->previous() }}">Back</a>
              <button type="submit" class="btn btn-success">Publish</button>
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
<!-- page  js scripts-->
<!-- jQuery 3 -->
<script src="{{ asset('public/bootstrap/js/jquery.min.js')}}"></script>
<script src="{{ asset('public/bootstrap/js/jquery.dataTables.min.js')}}"></script>
     <!-- Bootstrap DataTables -->
<script src="{{ asset('public/bootstrap/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{ asset('public/bootstrap/js/bootstrap-multiselect.js')}}"></script>

 <script>
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
          
            if(chk_val=='2'){
              $('.div-role-multi').show();
            }

            if(chk_val=='3'){
              $('.div-user-multi').show();
            }

            if(chk_val==1){
                $('.chk-access').attr('disabled','true');
                $('.chk-access').prop('checked',false);
                $(this).removeAttr('disabled');
                $(this).prop('checked',true);
            }
          }else{

            if(chk_val=='1'){
            }

            if(chk_val=='2'){
              $('.div-role-multi').hide();
            }

            if(chk_val=='3'){ 
              $('.div-user-multi').hide();
            }

          }
      });

    });
  </script>
@endsection




