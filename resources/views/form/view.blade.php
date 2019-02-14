

@extends('layouts.app')
@section('content') 

   <section class="content-header">
         <h1>
           Templete of {{ucwords($data['form_name'])}}
         </h1>
          {{ Breadcrumbs::render('template') }}
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
{{--        <form class="form" action="{{ route('data/store') }}" method="POST">
       {{ csrf_field() }}
       <input type="hidden" name="form_id" value="{{ Request::segment(4)}}"> --}}
        <div class="box box-solid">
          <div class="box-body">
              @forelse ($data['form_fields'] as $form_field)
          
            <div class="col-md-6">
              <div class="form-group">
                <label for="{{$errors->first(strtolower($form_field->field_name))}}">{{ucwords($form_field->field_title)}}</label>
                 
                 @switch($form_field->field_type)
                     @case('text')
                        <input type="text" value="" name="{{strtolower($form_field->field_name)}}" class="form-control" placeholder="{{strtolower($form_field->field_title) }}">
                         @break

                     @case('textarea')
                        <textarea class="form-control" name="{{strtolower($form_field->field_name)}}"></textarea>
                        @break

                     @case('email')
                        <input type="email" value="" name="{{strtolower($form_field->field_name)}}" class="form-control" placeholder="{{strtolower($form_field->field_title) }}">
                         @break

                     @case('password')
                        <input type="password" value="" name="{{strtolower($form_field->field_name)}}" class="form-control" placeholder="{{strtolower($form_field->field_title) }}">
                         @break

                      @case('select')
                       <select class="form-control" name="{{strtolower($form_field->field_name)}}">
                          <option value="">--select--</option>
                          @foreach (explode(',', $form_field->field_options) as $option)
                               <option value="{{$option}}">{{ucwords($option)}}</option>
                          @endforeach
                       </select>
                        @break

                     @case('radio')
                         @foreach (explode(',', $form_field->field_options) as $option)
                              &nbsp;&nbsp;<input type="radio" name="{{strtolower($form_field->field_name)}}">{{ucwords($option)}}&nbsp;&nbsp;
                         @endforeach
                       @break

                     @case('checkbox')
                         @foreach (explode(',', $form_field->field_options) as $option)
                           <input type="checkbox" name="{{strtolower($form_field->$option)}}">{{ucwords($option)}}
                         @endforeach
                       @break

                      @case('phone_number')
                       <input type="text" value="" name="{{strtolower($form_field->field_name)}}" class="form-control numeric" placeholder="{{strtolower($form_field->field_title) }}">
                       @break

                      @case('phone_number')
                       <input type="text" value="" name="{{strtolower($form_field->field_name)}}" class="form-control numeric" placeholder="{{strtolower($form_field->field_title) }}">
                       @break

                     @case('number')
                       <input type="text" value="" name="{{strtolower($form_field->field_name)}}" class="form-control numeric" placeholder="{{strtolower($form_field->field_title) }}">
                       @break

                      @case('float')
                       <input type="text" value="" name="{{strtolower($form_field->field_name)}}" class="form-control numeric" placeholder="{{strtolower($form_field->field_title) }}">
                       @break

                      @case('money')
                       <input type="text" value="" name="{{strtolower($form_field->field_name)}}" class="form-control numeric" placeholder="{{strtolower($form_field->field_title) }}">
                       @break 


                     @case('date')
                       <input type="date" value="" name="{{strtolower($form_field->field_name)}}" class="form-control" placeholder="{{strtolower($form_field->field_title) }}">
                       @break

                     @case('time')
                       <input type="time" value="" name="{{strtolower($form_field->field_name)}}" class="form-control" placeholder="{{strtolower($form_field->field_title) }}">
                       @break

                     @case('file')
                       <input type="file" value="" name="{{strtolower($form_field->field_name)}}" class="form-control" placeholder="{{strtolower($form_field->field_title) }}">
                       @break

                     @case('year')
                       <select class="form-control" name="{{strtolower($form_field->field_name)}}" >
                        <?php $year = 2015 ?>
                        @for ($i = 1; $i < 60 ; $i++)
                          <option value="1">{{ $year-- }}</option>
                        @endfor
                       </select>
                     @break

                      @case('image')
                       <input type="file" value="" name="{{strtolower($form_field->field_name)}}" class="form-control" placeholder="{{strtolower($form_field->field_title) }}">
                       @break
                      
                      

                      @case('month')
                        <select class="form-control" name="{{strtolower($form_field->field_name)}}">
                          <option value="1">January</option>
                          <option value="2">February</option>
                          <option value="3">March</option>
                          <option value="4">April</option>
                          <option value="5">May</option>
                          <option value="6">June</option>
                          <option value="7">July</option>
                          <option value="8">August</option>
                          <option value="9">September</option>
                          <option value="10">October</option>
                          <option value="11">November</option>
                          <option value="12">December</option>
                        </select>
                       @break

                       @case('day')
                         <select class="form-control" name="{{strtolower($form_field->field_name)}}">
                            <option value="1">Monday</option>
                            <option value="2">Tuesday</option>
                            <option value="3">Wednesday</option>
                            <option value="4">Thrusday</option>
                            <option value="5">Friday</option>
                            <option value="6">Saturday</option>
                            <option value="7">Sunday</option>
                         </select>
                       @break

                       @case('time')
                       <input type="time" value="" name="{{strtolower($form_field->field_name)}}" class="form-control" placeholder="{{strtolower($form_field->field_title) }}">
                       @break
                 
                     @default
                         <input type="hidden" name="hidden" value="">
                 @endswitch

                <span class="text-danger">{{$errors->first(ucwords($form_field->field_name))}}</span>
              </div>
            </div>
              @empty
              <p>Filed not available</p>
              @endforelse
          </div>
          <div class="box-footer">
           {{--  <input type="submit" class="btn btn-success" value="Add"> --}}
            <a class="btn btn-default" href="{{ url()->previous() }}">Back</a>
          </div>
       </div>
       </form>
     </div>
    </div><!--row--> 
  </section>  
<!-- /. section content -->
@endsection


