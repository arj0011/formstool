@extends('layouts.app')
@section('content') 

    <section class="content-header">
        @if(!isset($data['role']))
         <h1>
           Add New Role
         </h1>
         {{ Breadcrumbs::render('add-role') }}
        @else 
        <h1>
           Edit Role
        </h1>
         {{ Breadcrumbs::render('edit-role') }}
        @endif
        <small></small>
     </section>
  <!-- Main content -->
    <section class="content">

       @if (Session::has('msg'))
         <div class="row">
            <div class="col-md-10">
                <div  class="alert alert-{{ Session::get('color') }}">
                   <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                   <p>{{ Session::get('msg') }}</p>
                </div>
            </div>
          </div>
        @endif

      <div class="row">
        <!-- left column -->
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-solid">
          {{--   <div class="box-header with-border">
              <h3 class="box-title"></h3>
            </div> --}}
            <div class="box-body">
              @if(isset($data['role']))
                {{ Form::open([ 'url' => route('role/update') , 'method' => 'PUT' ])  }}
                {{ Form::hidden( 'id' , $data['role']->id ) }}
                @else
                {{ Form::open([ 'url' => route('role/create') , 'method' => 'POST' ]) }}
              @endif

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="exampleInputRole">New Role</label><span class="text-danger">*</span>
                    <input type="text" value="@if(isset($data['role']->name)){{ $data['role']->name }}@else{{old('role')}}@endif""  required name="role" class="form-control" placeholder="Enter Role">
                     <span class="text-danger">{{ $errors->first('role') }}</span> 
                  </div>
                </div>
             <!--   <div class="col-md-12">
                 <div class="row">
                     <div class="col-md-12">
                       <label>Permissions</label><span>(optional)</span>
                     </div>
                 </div>
                  @foreach ($data['modules'] as $module)
                  <div class="col-md-3">
                      <h4>{{ $module->module_name }}</h4> 
                               <div class="scroll-div">
                                <div class="form-group">
                            @foreach ($module->permissions as $permission)
                                  <div class="checkbox">
                                    <input type="checkbox" name="permissions[]" value="{{$permission->id}}"
                                      @isset ($data['role'])
                                          @foreach ($data['role']->permissions as $role_permit)
                                            @if ($role_permit->id == $permission->id)
                                              checked
                                            @endif 
                                         @endforeach
                                     @endisset
                                    >{{$permission->name}}
                                    </div>
                            @endforeach
                            </div>
                               </div>
                  </div>
                    @endforeach
               </div> -->
               <div class="col-md-12">
                    <div class="box-footer">
                      <button  type="submit" class="btn btn-success">Submit</button>
                    </div>
                </div>
                  {{ Form::close()}}
              </div>
            </div>
          </div>
        </div>
         <div class="col-md-12 text-right" style="margin-bottom: 10px;">
          <a class="btn btn-default" style="border-radius: 10px; background: #ffffff ; padding-left: 20px;padding-right: 20px;" href="{{url()->previous()}}">Back</a>
        </div>
   </div>
        <!--row--> 
  </section>  
<!-- /. section content -->
@endsection