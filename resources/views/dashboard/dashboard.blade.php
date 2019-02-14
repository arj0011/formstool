@extends('layouts.app')
@section('content')   
     <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Dashboard
        <small>it all starts here</small>
      </h1>
        {{ Breadcrumbs::render('dashboard') }}
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

      @can('index', App\User::class)
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-yellow">
            <div class="inner">

              <h3>{{$data['users']}}</h3>

              <p>User's</p>
            </div>
            <div class="icon">
              <i class="fa fa-users"></i>
            </div>
            <a href="{{ route('user/index')}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
      @endcan
        
        {{-- @can('index', App\Form::class) --}}
          
         <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">

              <h3>{{ $data['forms'] }}</h3>

              <p> @if(Auth::user()->role == 1) Form's @else My Form's @endif</p>
            </div>
            <div class="icon">
              <i class="fa fa-wpforms"></i>
            </div>
            <a href="{{ route('form/index')}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
         </div>
       {{-- @endcan --}} 
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">

              <h3>{{ $data['form_groups'] }}</h3>
              
              <p> @if(Auth::user()->role == 1) Form Group's @else My Form Group's @endif</p>
            </div>
            <div class="icon">
             <i class="fa fa-layer-group"></i>
             <i class="fas fa-layer-group"></i>
            </div>
            <a href="{{ route('formGroup/index')}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
         </div>

         <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-primary">
            <div class="inner">

              <h3>{{ $data['schedule_forms'] }}</h3>
              <p> @if(Auth::user()->role == 1) All Schedule List @else Schedule List @endif</p>
            </div>
            <div class="icon">
             <i class="fa fa-calendar"></i>
            </div>
            <a href="{{ route('schedule/index')}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
         </div>

     </div><!-- row -->
    </section>
    <!-- /.content -->
@endsection





