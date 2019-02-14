

@extends('layouts.app')
@section('content') 

  <section class="content-header">
         <h1>
           Edit&nbsp;<small>{{ucwords($data['form_name'])}}</small></h1>
         </h1>
          {{ Breadcrumbs::render('edit-data',ucwords($data['form_name'])) }}
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
       <form data-toggle="validator" class="form" action="{{ route('data/update') }}" method="POST" enctype="multipart/form-data" >
       {{ csrf_field() }}
       <input type="hidden" name="form_id" value="{{ Request::get('form_id')}}" >
       <input type="hidden" name="data_id" value="{{ Request::get('data_id')}}">
        <div class="box box-solid">
          <div class="box-body">
              @forelse ($data['form_fields'] as $form_field)
          
            <div class="col-md-6">
              <div class="form-group">
                <label for="{{$errors->first(strtolower($form_field->field_name))}}">{{ucwords($form_field->field_title)}}</label>
                 
                 @switch($form_field->field_type)
                     @case('text')
                        <input type="text" value="@if(old($form_field->field_name)){{old($form_field->field_name)}}@else{{$form_field->field_value}}@endif" name="{{strtolower($form_field->field_name)}}" class="form-control" placeholder="{{strtolower($form_field->field_title) }}"
                           @if (!empty($form_field->rule))
                            @foreach ($form_field->rule as $key => $rule)
                               @if ($key == 'validation')
                                 @if($rule == 1 || $rule == 2 )
                                   {{ 'required' }}
                                 @endif
                               @endif
                               @if ($key == 'min')
                                @if(!empty($rule))
                                 {{ 'minlength='.$rule }}
                                 @endif
                               @endif
                               @if ($key == 'max')
                               @if(!empty($rule))
                                 {{ 'maxlength='.$rule }}
                                 @endif
                               @endif
                            @endforeach
                          @endif
                          >
                        <div class="help-block with-errors"></div>
                        @if ($errors->has($form_field->field_name))
                        <span class="help-block text-red">
                          <strong>{{$errors->first($form_field->field_name)}}</strong>
                        </span>
                        @endif
                         @break
                     @case('textarea')
                        <textarea class="form-control" name="{{strtolower($form_field->field_name)}}">@if (old($form_field->field_name)){{old($form_field->field_name)}}@else{{$form_field->field_value}}@endif</textarea>
                          <div class="help-block with-errors"></div>
                          @if ($errors->has($form_field->field_name))
                        <span class="help-block text-red">
                          <strong>{{$errors->first($form_field->field_name)}}</strong>
                        </span>
                        @endif
                        @break

                     @case('email')
                        <input type="email" value="@if (old($form_field->field_name)){{old($form_field->field_name)}}@else{{$form_field->field_value}}@endif" name="{{strtolower($form_field->field_name)}}" class="form-control" placeholder="{{strtolower($form_field->field_title) }}" @if (!empty($form_field->rule))
                            @foreach ($form_field->rule as $key => $rule)
                               @if ($key == 'validation')
                                 @if($rule == 1 || $rule == 2 )
                                   {{ 'required' }}
                                 @endif
                               @endif
                            @endforeach
                          @endif
                        >
                        <div class="help-block with-errors"></div>
                         @if ($errors->has($form_field->field_name))
                        <span class="help-block text-red">
                          <strong>{{$errors->first($form_field->field_name)}}</strong>
                        </span>
                        @endif
                         @break

                    {{--  @case('password')
                        <input type="password" value="{{$form_field->field_value}}" name="{{strtolower($form_field->field_name)}}" class="form-control" placeholder="{{strtolower($form_field->field_title) }}">
                         @break --}}

                      @case('select')
                        <select class="form-control" name="{{strtolower($form_field->field_name)}}">
                          <option value="">--select--</option>
                          @foreach (explode(',', $form_field->field_options) as $option)
                               <option @if( old(strtolower($form_field->field_name)) == $option || strtolower($form_field->field_value) == strtolower($option)){{'selected'}}@endif value="{{$option}}">{{ucwords($option)}}</option>
                          @endforeach
                       </select>
                       <div class="help-block with-errors"></div>
                         @if ($errors->has($form_field->field_name))
                        <span class="help-block text-red">
                          <strong>{{$errors->first($form_field->field_name)}}</strong>
                        </span>
                        @endif
                        @break

                     @case('radio')
                        @foreach (explode(',', $form_field->field_options) as $key => $option)
                              &nbsp;&nbsp;<input type="radio" name="{{strtolower($form_field->field_name)}}" value="{{$option}}" @if(old(strtolower($form_field->field_name)) == $option || strtolower($form_field->field_value) == strtolower($option)){{'checked'}}@endif>{{$option}}&nbsp;&nbsp;
                         @endforeach
                           <div class="help-block with-errors"></div>
                         @if ($errors->has($form_field->field_name))
                        <span class="help-block text-red">
                          <strong>{{$errors->first($form_field->field_name)}}</strong>
                        </span>
                        @endif
                        @break

                     @case('checkbox')
                         @foreach (explode(',', $form_field->field_options) as $option)
                           <input type="checkbox" name="{{strtolower($form_field->field_name)}}[]" 
                           @foreach (explode(',', $form_field->field_value) as $value)
                              @if(old(strtolower($form_field->field_name)) == $option || strtolower($value) == strtolower($option)){{'checked'}}@endif
                           @endforeach
                           value="{{strtolower($option)}}">{{ucwords($option)}}
                         @endforeach
                           <div class="help-block with-errors"></div>
                         @if ($errors->has($form_field->field_name))
                        <span class="help-block text-red">
                          <strong>{{$errors->first($form_field->field_name)}}</strong>
                        </span>
                        @endif
                     @break

                      @case('phone_number')
                       <input type="text" value="@if(old($form_field->field_name)){{old($form_field->field_name)}}@else{{$form_field->field_value}}@endif" name="{{strtolower($form_field->field_name)}}" class="form-control numeric" placeholder="{{strtolower($form_field->field_title) }}">
                         <div class="help-block with-errors"></div>
                         @if ($errors->has($form_field->field_name))
                        <span class="help-block text-red">
                          <strong>{{$errors->first($form_field->field_name)}}</strong>
                        </span>
                        @endif
                       @break

                     @case('number')
                       <input type="text" value="@if(old($form_field->field_name)){{old($form_field->field_name)}}@else{{$form_field->field_value}}@endif" name="{{strtolower($form_field->field_name)}}" class="form-control numeric" placeholder="{{strtolower($form_field->field_title) }}">
                       @break

                      @case('float')
                       <input type="text" value="@if(old($form_field->field_name)){{old($form_field->field_name)}}@else{{$form_field->field_value}}@endif" name="{{strtolower($form_field->field_name)}}" class="form-control numeric" placeholder="{{strtolower($form_field->field_title) }}">
                       @break

                      @case('money')
                       <input type="text" value="@if(old($form_field->field_name)){{old($form_field->field_name)}}@else{{$form_field->field_value}}@endif" name="{{strtolower($form_field->field_name)}}" class="form-control numeric" placeholder="{{strtolower($form_field->field_title) }}">
                       @break 


                     @case('date')
                       <input type="date" value="@if(old($form_field->field_name)){{old($form_field->field_name)}}@else{{$form_field->field_value}}@endif" name="{{strtolower($form_field->field_name)}}" class="form-control" placeholder="{{strtolower($form_field->field_title) }}">
                       @break

                     @case('time')
                       <input type="time" value="@if(old($form_field->field_name)){{old($form_field->field_name)}}@else{{$form_field->field_value}}@endif" name="{{strtolower($form_field->field_name)}}" class="form-control" placeholder="{{strtolower($form_field->field_title) }}">
                       @break

                     @case('file')
                       <input type="file" value="{{$form_field->field_value}}" name="{{strtolower($form_field->field_name)}}" class="form-control" placeholder="{{strtolower($form_field->field_title) }}">
                       @break

                     @case('year')
                       <select class="form-control" name="{{strtolower($form_field->field_name)}}" >
                        <?php $year = date('Y') ?>
                        @for ($i = 1; $i < 105 ; $i++)
                          <?php $y = $year-- ?>
                          <option @if(strtolower($form_field->field_value) == strtolower($y)){{'selected'}}@endif value="{{$y}}">{{ $y }}</option>
                        @endfor
                       </select>
                     @break

                      @case('image')
                       <input type="file" value="" name="{{strtolower($form_field->field_name)}}" class="form-control" placeholder="{{strtolower($form_field->field_title) }}">
                       @break

                      @case('month')
                        <select class="form-control" name="{{strtolower($form_field->field_name)}}">
                          <option value="">--select--</option>
                          <option @if (old(strtolower($form_field->field_name)) == 'january'  || strtolower($form_field->field_value) == strtolower('January')) selected @endif value="January">January</option>
                          <option @if (old(strtolower($form_field->field_name)) == 'february' || $form_field->field_value) == strtolower('February')) selected @endif value="February">February</option>
                          <option @if (old(strtolower($form_field->field_name)) == 'march' || strtolower($form_field->field_value) == strtolower('March')) selected @endif value="March">March</option>
                          <option @if (old(strtolower($form_field->field_name)) == 'april' || strtolower($form_field->field_value) == strtolower('April')) selected @endif value="April">April</option>
                          <option @if (old(strtolower($form_field->field_name)) == 'may' || strtolower($form_field->field_value) == strtolower('May')) selected @endif value="May">May</option>
                          <option @if (old(strtolower($form_field->field_name)) == 'june' || strtolower($form_field->field_value) == strtolower('June')) selected @endif value="June">June</option>
                          <option @if (old(strtolower($form_field->field_name)) == 'july' || strtolower($form_field->field_value) == strtolower('July')) selected @endif value="July">July</option>
                          <option @if (old(strtolower($form_field->field_name)) == 'august' || strtolower($form_field->field_value) == strtolower('August')) selected @endif value="August">August</option>
                          <option @if (old(strtolower($form_field->field_name)) == 'september' || strtolower($form_field->field_value) == strtolower('September')) selected @endif value="September">September</option>
                          <option @if (old(strtolower($form_field->field_name)) == 'october' || strtolower($form_field->field_value) == strtolower('October')) selected @endif value="October">October</option>
                          <option @if (old(strtolower($form_field->field_name)) == 'november' || strtolower($form_field->field_value) == strtolower('November')) selected @endif value="November">November</option>
                          <option @if (old(strtolower($form_field->field_name)) == 'december' || strtolower($form_field->field_value) == strtolower('December')) selected @endif value="December">December</option>
                        </select>  <div class="help-block with-errors"></div>
                         @if ($errors->has($form_field->field_name))
                        <span class="help-block text-red">
                          <strong>{{$errors->first($form_field->field_name)}}</strong>
                        </span>
                        @endif
                       @break
 
                       @case('day')
                         <select class="form-control" name="{{strtolower($form_field->field_name)}}">
                            <option value="">--select--</option>
                            <option @if (old(strtolower($form_field->field_name)) == 'monday' || strtolower($form_field->field_value) == strtolower('Monday')) selected @endif value="Monday">Monday</option>
                            <option @if (old(strtolower($form_field->field_name)) == 'tuesday' || strtolower($form_field->field_value) == strtolower('Tuesday')) selected @endif value="Tuesday">Tuesday</option>
                            <option @if (old(strtolower($form_field->field_name)) == 'wednesday' || strtolower($form_field->field_value) == strtolower('Wednesday')) selected @endif value="Wednesday">Wednesday</option>
                            <option @if (old(strtolower($form_field->field_name)) == 'thrusday' || strtolower($form_field->field_value) == strtolower('Thrusday')) selected @endif value="Thrusday">Thrusday</option>
                            <option @if (old(strtolower($form_field->field_name)) == 'friday' || strtolower($form_field->field_value) == strtolower('Friday')) selected @endif value="Friday">Friday</option>
                            <option @if (old(strtolower($form_field->field_name)) == 'saturday' || strtolower($form_field->field_value) == strtolower('Saturday')) selected @endif value="Saturday">Saturday</option>
                            <option @if (old(strtolower($form_field->field_name)) == 'sunday' || strtolower($form_field->field_value) == strtolower('Sunday')) selected @endif value="Sunday">Sunday</option>
                         </select>  <div class="help-block with-errors"></div>
                         @if ($errors->has($form_field->field_name))
                        <span class="help-block text-red">
                          <strong>{{$errors->first($form_field->field_name)}}</strong>
                        </span>
                        @endif
                       @break

                       @case('time')
                       <input type="time" value="@if(old($form_field->field_name)){{old($form_field->field_name)}}@else{{$form_field->field_value}}@endif" name="{{strtolower($form_field->field_name)}}" class="form-control" placeholder="{{strtolower($form_field->field_title) }}">  <div class="help-block with-errors"></div>
                         @if ($errors->has($form_field->field_name))
                        <span class="help-block text-red">
                          <strong>{{$errors->first($form_field->field_name)}}</strong>
                        </span>
                        @endif
                       @break

                       @case('password')
                       <input type="password" value="@if(old($form_field->field_name)){{old($form_field->field_name)}}@else{{$form_field->field_value}}@endif" name="{{strtolower($form_field->field_name)}}" class="form-control" placeholder="{{strtolower($form_field->field_title) }}">  <div class="help-block with-errors"></div>
                         @if ($errors->has($form_field->field_name))
                        <span class="help-block text-red">
                          <strong>{{$errors->first($form_field->field_name)}}</strong>
                        </span>
                        @endif
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
            <a class="btn btn-default" href="{{ url()->previous() }}">Back</a>
            <input type="reset" class="btn btn-default" value="Reset">
            <input type="submit" class="btn btn-success" value="Update">
          </div>
       </div>
       </form>
     </div>
    </div><!--row--> 
  </section>  
<!-- /. section content -->
@endsection
@section('css-script')
    <!-- Bootstarap Validator script -->
      <link rel="stylesheet" type="text/css" href="{{ asset('public/bootstrap/css/bootstrapValidator.min.css') }}">
@endsection
@section('js-script')
   <!-- Bootstarap Validator script -->
      <script src="{{ asset('public/bootstrap/js/bootstrapValidator.min.js')}}"></script>
@endsection


