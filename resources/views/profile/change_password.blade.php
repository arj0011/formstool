@extends('layouts.app')
@section('content')   
     <!-- Content Header (Page header) -->
    <section class="content-header">
      <!-- <h1>
        Blank page
        <small>it all starts here</small>
      </h1> -->
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        {{-- <li><a href="#">Profile</a></li> --}}
        <li class="active">Change Password</li>
      </ol>
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

      <div class="col-md-10">

      <div class="row">
          
        <!-- Default box -->
        <div class="box box-solid">
          <div class="box-header">
            <h3 class="box-title">Change Password</h3>
          </div>
          <form class="form" action="{{ route('profile/update-password') }}" method="POST">
            {{ csrf_field() }}
             {{method_field('PUT')}}
            <div class="box-body">
              <div class="row">
                <div class="col-md-10">
                   <div class="form-group">
                     <label>Current Password</label>
                     <input type="password" name="current_password" placeholder="current password" class="form-control">
                      @if ($errors->has('current_password'))
                    <span class="help-block text-red">
                    <strong>{{ $errors->first('current_password') }}</strong>
                    </span>
                    @endif
                   </div>
                    <div class="form-group">
                     <label>New Password</label>
                     <input type="password" name="new_password" placeholder="new password" class="form-control">
                      @if ($errors->has('new_password'))
                    <span class="help-block text-red">
                    <strong>{{ $errors->first('new_password') }}</strong>
                    </span>
                    @endif
                   </div>
                    <div class="form-group">
                     <label>Confirm Password</label>
                     <input type="password" name="password_confirmation" placeholder="confirm password" class="form-control">
                      @if ($errors->has('password_confirmation'))
                    <span class="help-block text-red">
                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                    </span>
                    @endif
                   </div>
                </div>
              </div>
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
              <input type="submit" value="Update" class="btn btn-success btn-flat">
            </div>
            <!-- /.box-footer-->
          </form>
          </div>
      </div>
       <div class="col-md-12 text-right" style="margin-bottom: 10px;">
          <a class="btn btn-default" style="border-radius: 10px; background: #ffffff ; padding-left: 20px;padding-right: 20px;" href="{{url()->previous()}}">Back</a>
        </div>
    </div>
    </section>
    <!-- /.content -->
@endsection
@section('css-script')
 <style type="text/css">
 
 </style>
@endsection
@section('js-script')

@endsection


