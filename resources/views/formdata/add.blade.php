

@extends('layouts.app')
@section('content') 

   <section class="content-header">
         {{-- <h1>
           {{ucwords(str_replace('_', ' ',$data['form_name']))}}
         </h1>
          {{ Breadcrumbs::render('add-data',ucwords(str_replace('_', ' ',$data['form_name']))) }} --}}
          @if(Auth::user()->role == 1)
            <h1>Form Template <small>{{ ucwords(str_replace('_', ' ',$data['form_name']))}}</small></h1>
            {{ Breadcrumbs::render('template') }}
          @endif
          @if(Auth::user()->role != 1)
            <h1>Schedule Form <small>{{ ucwords(str_replace('_', ' ',$data['form_name']))}}</small></h1>
            {{ Breadcrumbs::render('schedule-form') }}
          @endif
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

     <!--    <div class="col-md-12">
        @if ($errors->any())
          <ul>
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
       @endif
        </div> -->
        
      <div class="col-md-12">
       <form data-toggle="validator" class="form" action="{{ route('data/store') }}" method="POST" enctype="multipart/form-data" id="submitForm">
       {{ csrf_field() }}
       <input type="hidden" name="form_id" value="{{ Request::get('form_id')}}">
       <input type="hidden" name="schedule_id" value="{{ Request::get('schedule_id')}}">
        <div class="box box-solid">
          <div class="box-body">
              @forelse ($data['form_fields'] as $form_field)
            <div class="col-md-8 co-md-offset-2">
              <div class="form-group">
                <label style="color:#606163;" for="{{$errors->first(strtolower($form_field->field_name))}}">{{ucwords($form_field->field_title)}}</label>
                 
                 @switch($form_field->field_type)
                     @case('text')
                        <input type="text" @if(old($form_field->field_name))
                                              value="{{old($form_field->field_name)}}"
                                           @else
                                               @if($form_field->field_value)
                                              value="{{$form_field->field_value}}"
                                               @endif
                                           @endif 
                                              name="{{strtolower($form_field->field_name)}}" class="form-control" placeholder="{{ucfirst($form_field->field_title)}}" 
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
                        @if ($errors->has(strtolower($form_field->field_name)))
                        <span class="help-block text-red">
                          <strong>{{$errors->first(strtolower($form_field->field_name))}}</strong>
                        </span>
                        @endif
                         @break

                     @case('textarea')
                        <textarea class="form-control" name="{{strtolower($form_field->field_name)}}"
                          @if (!empty($form_field->rule))
                            @foreach ($form_field->rule as $key => $rule)
                               @if ($key == 'validation')
                                 @if($rule == 1)
                                   {{ 'required' }}
                                 @endif
                               @endif
                            @endforeach
                          @endif
                        >@if($form_field->field_value){{$form_field->field_value}}@endif</textarea>
                         <div class="help-block with-errors"></div>
                          @if ($errors->has(strtolower($form_field->field_name)))
                        <span class="help-block text-red">
                          <strong>{{$errors->first(strtolower($form_field->field_name))}}</strong>
                        </span>
                        @endif
                        @break

                     @case('email')
                        <input type="email" value="@if(old($form_field->field_name))@else @if($form_field->field_value)@endif{{$form_field->field_value}}@endif" name="{{strtolower($form_field->field_name)}}" class="form-control" placeholder="{{ucfirst($form_field->field_title) }}"
                          @if (!empty($form_field->rule))
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
                         @if ($errors->has(strtolower($form_field->field_name)))
                        <span class="help-block text-red">
                          <strong>{{$errors->first(strtolower($form_field->field_name))}}</strong>
                        </span>
                        @endif
                         @break

                     @case('password')
                        <input type="password" value="@if(old($form_field->field_name))@else @if($form_field->field_value)@endif{{$form_field->field_value}}@endif" name="{{strtolower($form_field->field_name)}}" class="form-control" placeholder="{{ucfirst($form_field->field_title) }}"
                        
                        >
                         <div class="help-block with-errors"></div>
                          @if ($errors->has(strtolower($form_field->field_name)))
                        <span class="help-block text-red">
                          <strong>{{$errors->first(strtolower($form_field->field_name))}}</strong>
                        </span>
                        @endif
                         @break

                      @case('select')
                        <select class="form-control multiselect-class" name="{{strtolower($form_field->field_name)}}[]"

                          @if (!empty($form_field->rule))
                            @foreach ($form_field->rule as $key => $rule)
                               @if ($key == 'validation')
                                 @if($rule == 1)
                                   {{ 'required' }}
                                 @endif
                               @endif
                               @if($key == 'multiselect')
                                  @if ($rule == 'yes')
                                    {{ 'multiple' }}
                                  @endif
                               @endif
                            @endforeach
                          @endif
                        >
                        <option value="">--select--</option>
                          @foreach (explode(',', $form_field->field_options) as $option)
                               <option
                                 
                              @if(old(strtolower($form_field->field_name)))
                                         {{'selected'}}
                              @else
                                 @if(isset($form_field->field_value))
                                   @foreach (explode(',', $form_field->field_value) as $v)
                                      @if(strtolower($option) == strtolower($v)))
                                           {{'selected'}}
                                      @endif
                                   @endforeach
                                 @endif
                              @endif

                                value="{{$option}}">{{ucwords($option)}}</option>
                          @endforeach
                       </select>
                         <div class="help-block with-errors"></div>
                        @if ($errors->has(strtolower($form_field->field_name)))
                        <span class="help-block text-red">
                          <strong>{{$errors->first(strtolower(strtolower($form_field->field_name)))}}</strong>
                        </span>
                        @endif
                        @break

                     @case('radio')
                        @foreach (explode(',', $form_field->field_options) as $key => $option)
                              &nbsp;&nbsp;<input type="radio" name="{{strtolower($form_field->field_name)}}" value="{{$option}}"
                               @if (!empty($form_field->rule))
                            @foreach ($form_field->rule as $key => $rule)
                               @if ($key == 'validation')
                                 @if($rule == 1)
                                   {{ 'required' }}
                                 @endif
                               @endif
                                 @if ($form_field->field_name == old($form_field->field_name))
                                   {{ 'checked' }}
                                 @endif
                            @endforeach
                          @endif
                              >{{$option}}&nbsp;&nbsp;
                         @endforeach
                          <div class="help-block with-errors"></div>
                           @if ($errors->has(strtolower($form_field->field_name)))
                        <span class="help-block text-red">
                          <strong>{{$errors->first(strtolower($form_field->field_name))}}</strong>
                        </span>
                        @endif
                        @break

                     @case('checkbox')
                         @foreach (explode(',', $form_field->field_options) as $option)
                           <input type="checkbox" name="{{strtolower($form_field->field_name)}}[]" value="{{strtolower($option)}}"
                          
                            @if (!empty($form_field->rule))
                              @foreach ($form_field->rule as $key => $rule)
                                @if ($key == 'validation')
                                  @if($rule == 1)
                                  {{ 'required' }}
                                  @endif
                                @endif
                              @endforeach
                               @if ($form_field->field_name == old($form_field->field_name))
                                 {{ 'checked' }}
                               @endif
                            @endif
                           >{{ucwords($option)}}
                         @endforeach
                          <div class="help-block with-errors"></div>
                           @if ($errors->has(strtolower($form_field->field_name)))
                        <span class="help-block text-red">
                          <strong>{{$errors->first(strtolower($form_field->field_name))}}</strong>
                        </span>
                        @endif
                       @break

                      @case('phone_number')
                       <input type="text" @if(old($form_field->field_name)) value="{{old($form_field->field_name)}}" @else @if($form_field->field_value) @endif value="{{$form_field->field_value}}" @endif name="{{strtolower($form_field->field_name)}}" class="form-control numeric" placeholder="{{ucfirst($form_field->field_title) }}" minlength="10" maxlength="10" pattern="[6789][0-9]{9}" data-pattern-error="please enter valid Mobile Number"
                          @if (!empty($form_field->rule))
                              @foreach ($form_field->rule as $key => $rule)
                                @if ($key == 'validation')
                                  @if($rule == 1 || $rule == 2)
                                  {{ 'required' }}
                                  @endif
                                @endif
                              @endforeach
                          @endif
                       >
                        <div class="help-block with-errors"></div>
                         @if ($errors->has(strtolower($form_field->field_name)))
                        <span class="help-block text-red">
                          <strong>{{$errors->first(strtolower($form_field->field_name))}}</strong>
                        </span>
                        @endif
                       @break

                     @case('number')
                       <input type="number" @if(old($form_field->field_name))value="{{old($form_field->field_name)}}"@else @if($form_field->field_value)value="{{$form_field->field_value}}" @endif @endif name="{{strtolower($form_field->field_name)}}" class="form-control numeric" placeholder="{{ucfirst($form_field->field_title) }}"
                        @if (!empty($form_field->rule))
                              @foreach ($form_field->rule as $key => $rule)
                                @if ($key == 'validation')
                                  @if($rule == 1)
                                  {{ 'required' }}
                                  @endif
                                @endif
                                @if ($key == 'min')
                                @if(!empty($rule))
                                 {{ 'min='.$rule }}
                                 @endif
                               @endif
                               @if ($key == 'max')
                               @if(!empty($rule))
                                 {{ 'max='.$rule }}
                                 @endif
                               @endif
                              @endforeach
                        @endif
                       >
                        <div class="help-block with-errors"></div>
                         @if ($errors->has(strtolower($form_field->field_name)))
                        <span class="help-block text-red">
                          <strong>{{$errors->first(strtolower($form_field->field_name))}}</strong>
                        </span>
                        @endif
                       @break

                      @case('float')
                       <input type="number" value="@if(old($form_field->field_name))@else @if($form_field->field_value)@endif{{$form_field->field_value}}@endif" name="{{strtolower($form_field->field_name)}}" class="form-control numeric" placeholder="{{ ucfirst($form_field->field_title) }}"
                        @if (!empty($form_field->rule))
                              @foreach ($form_field->rule as $key => $rule)
                                @if ($key == 'validation')
                                  @if($rule == 1)
                                  {{ 'required' }}
                                  @endif
                                @endif
                                @if ($key == 'min')
                                @if(!empty($rule))
                                 {{ 'min='.$rule }}
                                 @endif
                               @endif
                               @if ($key == 'max')
                               @if(!empty($rule))
                                 {{ 'max='.$rule }}
                                 @endif
                               @endif
                              @endforeach
                        @endif
                       >
                        <div class="help-block with-errors"></div>
                         @if ($errors->has(strtolower($form_field->field_name)))
                        <span class="help-block text-red">
                          <strong>{{$errors->first(strtolower($form_field->field_name))}}</strong>
                        </span>
                        @endif
                       @break

                      @case('money')
                       <input type="text" value="@if(old($form_field->field_name))@else @if($form_field->field_value)@endif{{$form_field->field_value}}@endif" name="{{strtolower($form_field->field_name)}}" class="form-control numeric" placeholder="{{ucfirst($form_field->field_title) }}">
                       <div class="help-block with-errors"></div>
                        @if ($errors->has($form_field->field_name))
                        <span class="help-block text-red">
                          <strong>{{$errors->first($form_field->field_name)}}</strong>
                        </span>
                        @endif
                       @break 

                     @case('date')
                       <input type="date" @if(old($form_field->field_name)) value="{{$form_field->field_name}}" @else @if($form_field->field_value) value="{{trim($form_field->field_value)}}" @endif @endif name="{{strtolower($form_field->field_name)}}" class="form-control" placeholder="{{ucfirst($form_field->field_title) }}"
                          @if (!empty($form_field->rule))
                              @foreach ($form_field->rule as $key => $rule)
                                @if ($key == 'validation')
                                  @if($rule == 1)
                                  {{ 'required' }}
                                  @endif
                                @endif
                              @endforeach
                          @endif
                       >
                        <div class="help-block with-errors"></div>
                         @if ($errors->has(strtolower($form_field->field_name)))
                        <span class="help-block text-red">
                          <strong>{{$errors->first(strtolower($form_field->field_name))}}</strong>
                        </span>
                        @endif
                       @break

                     @case('time')
                       <input type="time" value="@if(old($form_field->field_name))@else @if($form_field->field_value)@endif{{$form_field->field_value}}@endif" name="{{strtolower($form_field->field_name)}}" class="form-control" placeholder="{{ucfirst($form_field->field_title) }}"
                        @if (!empty($form_field->rule))
                              @foreach ($form_field->rule as $key => $rule)
                                @if ($key == 'validation')
                                  @if($rule == 1)
                                  {{ 'required' }}
                                  @endif
                                @endif
                              @endforeach
                          @endif
                          >
                          <div class="help-block with-errors"></div>
                           @if ($errors->has(strtolower($form_field->field_name)))
                        <span class="help-block text-red">
                          <strong>{{$errors->first(strtolower($form_field->field_name))}}</strong>
                        </span>
                        @endif
                       @break

                     @case('file')
                       <input type="file" value="" name="{{strtolower($form_field->field_name)}}" class="form-control" placeholder="{{ucfirst($form_field->field_title) }}"
                        @if (!empty($form_field->rule))
                              @foreach ($form_field->rule as $key => $rule)
                                @if ($key == 'validation')
                                  @if($rule == 1)
                                  {{ 'required' }}
                                  @endif
                                @endif
                              @endforeach
                          @endif
                          >
                          <div class="help-block with-errors"></div>
                           @if ($errors->has(strtolower($form_field->field_name)))
                        <span class="help-block text-red">
                          <strong>{{$errors->first(strtolower($form_field->field_name))}}</strong>
                        </span>
                        @endif
                       @break

                     @case('year')
                       <select class="form-control" name="{{strtolower($form_field->field_name)}}" 
                        @if (!empty($form_field->rule))
                              @foreach ($form_field->rule as $key => $rule)
                                @if ($key == 'validation')
                                  @if($rule == 1)
                                  {{ 'required' }}
                                  @endif
                                @endif
                              @endforeach
                          @endif
                          >
                           <option value="">--select--</option>
                         <?php $year = date('Y', strtotime('+100 years'))?>
                        @for ($i = 1; $i < 200 ; $i++)
                        @php --$year @endphp

                          <option @if(old($form_field->field_name)) {{'selected'}}  @else @if($form_field->field_value == $year) 
                                     {{'selected'}}
                                  @endif @endif value="{{$year}}">{{ $year }}</option>
                        @endfor
                       </select>
                        <div class="help-block with-errors"></div>
                         @if ($errors->has(strtolower($form_field->field_name)))
                        <span class="help-block text-red">
                          <strong>{{$errors->first(strtolower($form_field->field_name))}}</strong>
                        </span>
                        @endif
                     @break

                      @case('image')
                       <input type="file" value="" name="{{strtolower($form_field->field_name)}}" class="form-control" placeholder="{{ucfirst($form_field->field_title) }}"
                        @if (!empty($form_field->rule))
                              @foreach ($form_field->rule as $key => $rule)
                                @if ($key == 'validation')
                                  @if($rule == 1)
                                  {{ 'required' }}
                                  @endif
                                @endif
                              @endforeach
                          @endif
                          >
                           <div class="help-block with-errors"></div>
                            @if ($errors->has(strtolower($form_field->field_name)))
                        <span class="help-block text-red">
                          <strong>{{$errors->first(strtolower($form_field->field_name))}}</strong>
                        </span>
                        @endif
                       @break  

                      @case('month')
                        <select class="form-control" name="{{strtolower($form_field->field_name)}}"
                         @if (!empty($form_field->rule))
                              @foreach ($form_field->rule as $key => $rule)
                                @if ($key == 'validation')
                                  @if($rule == 1)
                                  {{ 'required' }}
                                  @endif
                                @endif
                              @endforeach
                          @endif
                          >
                          <option value="">--select--</option>
  <option @if (old($form_field->field_name) == 'january'){{'selected'}} @else @if($form_field->field_value == 'january') {{'selected'}} 
    ) @endif @endif  value="january">January</option>

  <option @if (old($form_field->field_name) == 'february'){{'selected'}} @else @if($form_field->field_value == 'february') {{'selected'}} 
    ) @endif @endif  value="february">February</option>

  <option @if (old($form_field->field_name) == 'march'){{'selected'}} @else @if($form_field->field_value == 'march') {{'selected'}} 
    ) @endif @endif  value="march">March</option>

  <option @if (old($form_field->field_name) == 'april'){{'selected'}} @else @if($form_field->field_value == 'april') {{'selected'}} 
    ) @endif @endif  value="april">April</option>

  <option @if (old($form_field->field_name) == 'may'){{'selected'}} @else @if($form_field->field_value == 'may') {{'selected'}} 
    ) @endif @endif  value="may">May</option>

  <option @if (old($form_field->field_name) == 'june'){{'selected'}} @else @if($form_field->field_value == 'june') {{'selected'}} 
    ) @endif @endif  value="june">June</option>

  <option @if (old($form_field->field_name) == 'july'){{'selected'}} @else @if($form_field->field_value == 'july') {{'selected'}} 
    ) @endif @endif  value="july">July</option>

  <option @if (old($form_field->field_name) == 'august'){{'selected'}} @else @if($form_field->field_value == 'august') {{'selected'}} 
    ) @endif @endif  value="august">August</option>

  <option @if (old($form_field->field_name) == 'september'){{'selected'}} @else @if($form_field->field_value == 'september') {{'selected'}} 
    ) @endif @endif  value="september">September</option>

  <option @if (old($form_field->field_name) == 'october'){{'selected'}} @else @if($form_field->field_value == 'october') {{'selected'}} 
    ) @endif @endif  value="october">October</option>

  <option @if (old($form_field->field_name) == 'november'){{'selected'}} @else @if($form_field->field_value == 'november') {{'selected'}} 
    ) @endif @endif  value="november">November</option>

  <option @if (old($form_field->field_name) == 'december'){{'selected'}} @else @if($form_field->field_value == 'december') {{'selected'}} 
    ) @endif @endif  value="december">December</option>
                        </select>
                        <div class="help-block with-errors"></div>
                         @if ($errors->has(strtolower($form_field->field_name)))
                        <span class="help-block text-red">
                          <strong>{{$errors->first(strtolower($form_field->field_name))}}</strong>
                        </span>
                        @endif
                       @break

                       @case('day')
                         <select class="form-control" name="{{strtolower($form_field->field_name)}}" 
                          @if (!empty($form_field->rule))
                              @foreach ($form_field->rule as $key => $rule)
                                @if ($key == 'validation')
                                  @if($rule == 1)
                                  {{ 'required' }}
                                  @endif
                                @endif
                              @endforeach
                          @endif
                          >
                           <option value="">--select--</option>
<option @if (old($form_field->field_name) == 'monday'){{'selected'}} @else @if($form_field->field_value == 'monday') {{'selected'}} 
    ) @endif @endif value="monday">Monday</option>
<option @if (old($form_field->field_name) == 'tuesday'){{'selected'}} @else @if($form_field->field_value == 'tuesday') {{'selected'}} 
    ) @endif @endif value="tuesday">Tuesday</option>
<option @if (old($form_field->field_name) == 'wednesday'){{'selected'}} @else @if($form_field->field_value == 'wednesday') {{'selected'}} 
    ) @endif @endif value="wednesday">Wednesday</option>
<option @if (old($form_field->field_name) == 'thrusday'){{'selected'}} @else @if($form_field->field_value == 'thrusday') {{'selected'}} 
    ) @endif @endif value="thrusday">Thrusday</option>
<option @if (old($form_field->field_name) == 'friday'){{'selected'}} @else @if($form_field->field_value == 'friday') {{'selected'}} 
    ) @endif @endif value="friday">Friday</option>
<option @if (old($form_field->field_name) == 'saturday'){{'selected'}} @else @if($form_field->field_value == 'saturday') {{'selected'}} 
    ) @endif @endif value="saturday">Saturday</option>
<option @if (old($form_field->field_name) == 'sunday'){{'selected'}} @else @if($form_field->field_value == 'sunday') {{'selected'}} 
    ) @endif @endif value="sunday">Sunday</option>
                         </select>
                          <div class="help-block with-errors"></div>
                           @if ($errors->has(strtolower($form_field->field_name)))
                        <span class="help-block text-red">
                          <strong>{{$errors->first(strtolower($form_field->field_name))}}</strong>
                        </span>
                        @endif
                       @break

                       @case('time')
                       <input type="time" value="@if(old($form_field->field_name))@else @if($form_field->field_value)@endif{{$form_field->field_value}}@endif" name="{{strtolower($form_field->field_name)}}" class="form-control" placeholder="{{ucfirst($form_field->field_title) }}" 
                        @if (!empty($form_field->rule))
                              @foreach ($form_field->rule as $key => $rule)
                                @if ($key == 'validation')
                                  @if($rule == 1)
                                  {{ 'required' }}
                                  @endif
                                @endif
                              @endforeach
                          @endif
                          >
                          <div class="help-block with-errors"></div>
                           @if ($errors->has(strtolower($form_field->field_name)))
                        <span class="help-block text-red">
                          <strong>{{$errors->first(strtolower($form_field->field_name))}}</strong>
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
          @if (Request::get('schedule_id'))
            <div class="box-footer">
              <button type="submit" class="btn btn-success" name="save_record_as" value="1">Submit</button>
              <button type="submit" class="btn btn-default"  id="save_as_draft" name="save_record_as" value="0">Save as draft</button>
            </div>
          @endif
       </div>
       </form>
     </div>
      <div class="col-md-12 text-right" style="margin-bottom: 10px;">
          <a class="btn btn-default" style="border-radius: 10px; background: #ffffff ; padding-left: 20px;padding-right: 20px;" href="{{url()->previous()}}">Back</a>
      </div>
      <!-- /.row -->
    </div><!--row--> 
  </section>  
<!-- /. section content -->
@endsection
@section('css-script')
    <!-- Bootstarap Validator script -->
      <link rel="stylesheet" type="text/css" href="{{ asset('public/bootstrap/css/bootstrapValidator.min.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ asset('public/bootstrap/css/bootstrap-multiselect.css')}}"/>
         <style type="text/css">

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

   .show{
      display: block;
   }

   .hide{
      display: none;
   }

   .btn-group,
.multiselect {
  width: 100%;
}

.multiselect {
  text-align: left;
  padding-right: 32px;
  overflow: hidden;
  text-overflow: ellipsis;
}

.multiselect .caret {
  right: 12px;
  top: 45%;
  position: absolute;
}

.multiselect-container.dropdown-menu {
    min-width: 0px;
}

.multiselect-container>li>a>label {
    white-space: normal;
    padding: 5px 15px 5px 35px;
}

.multiselect-container > li > a > label > input[type="checkbox"] {
    margin-top: 3px;
}

</style>
@endsection
@section('js-script')
   <!-- Bootstarap Validator script -->
      <script src="{{ asset('public/bootstrap/js/bootstrapValidator.min.js')}}"></script>
          <!-- Multiselect dropdown script --> 
      <script src="{{ asset('public/bootstrap/js/bootstrap-multiselect.js')}}"></script>
      <script type="text/javascript">
        $(function(){ 
          $("select[multiple]").each(function(){
            $(this).find("option").eq(0).remove(); 
          });
          // $('.multiselect-class').multiselect({
          $('select[multiple]').multiselect({
               includeSelectAllOption : true,
                      enableFiltering : true,
       enableCaseInsensitiveFiltering : true,
                            maxHeight : 400
             });
        });

       //  $('.multiselect-class').multiselect({
       //         includeSelectAllOption : true,
       //                enableFiltering : true,
       // enableCaseInsensitiveFiltering : true,
       //                      maxHeight : 400
       //       });


      // store or update data
      $('body').on('click','#save_as_draft',function(e){
          e.preventDefault();

          let form = $('#submitForm');
          let data = form.serialize();

          $.ajax({
            "headers":{
            'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
            },
            'type':'POST',
            'url' : form.attr('action'),
            'data' : data,
            'success' : function(response){

                if(response.status){
                   swal("Good job!", response.message , "success");
                }

            },
            'error' : function(error){
              console.log(error);
            }
          });

        });

    

      </script>
@endsection


