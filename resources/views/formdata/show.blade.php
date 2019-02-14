@extends('layouts.app')
@section('content') 
<section class="content-header">
         <h1>
           Submitted Form
           <small>{{ ucwords(str_replace('_', ' ',$data['form_name']))}}</small>
         </h1>
          {{ Breadcrumbs::render('submitted-form-data') }}
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

      <div class="col-xs-4">
            <a href="{{route('data/export' ,['form_id' => Request::get('form_id') , 'schedule_id' => Request::get('schedule_id')  , 'user_id' => Request::get('user_id') , 'formate' => 'excel'])}}"><img src="{{url('public/images/excel.png')}}"></img></a>
            <a href="{{route('data/export' ,['form_id' => Request::get('form_id') , 'schedule_id' => Request::get('schedule_id')  , 'user_id' => Request::get('user_id') , 'formate' => 'csv'])}}"><img height="32" width="48" src="{{url('public/images/csv.png')}}"></img></a>
      </div>
        
      <div class="col-md-12">
       {{ csrf_field() }}
        <div class="box box-solid">
          <div class="box-body">
              @forelse ($data['form_fields'] as $form_field)
          
              <div class="form-group">
                <label for="{{$errors->first(strtolower($form_field->field_name))}}">{{ucwords($form_field->field_title)}}</label>
                 
                 @switch($form_field->field_type)
                     @case('text')
                        <input type="text" value="{{ucwords($form_field->field_value)}}" name="{{strtolower($form_field->field_name)}}" class="form-control" placeholder="{{strtolower($form_field->field_title) }}" readonly>
                         @if ($errors->has($form_field->field_name))
                         <span class="help-block text-red">
                          <strong>{{ $errors->first($form_field->field_name) }}</strong>
                         </span>
                        @endif
                         @break

                     @case('textarea')
                        <textarea class="form-control" name="{{strtolower($form_field->field_name)}}" readonly>{{$form_field->field_value}}</textarea>
                          @if ($errors->has($form_field->field_name))
                         <span class="help-block text-red">
                          <strong>{{ $errors->first($form_field->field_name) }}</strong>
                         </span>
                        @endif
                        @break

                     @case('email')
                        <input type="email" value="{{$form_field->field_value}}" name="{{strtolower($form_field->field_name)}}" class="form-control" placeholder="{{strtolower($form_field->field_title) }}" readonly>
                         @break

                     @case('password')
                        <input type="password" value="{{$form_field->field_value}}" name="{{strtolower($form_field->field_name)}}" class="form-control" placeholder="{{strtolower($form_field->field_title) }}" readonly>
                          @if ($errors->has($form_field->field_name))
                         <span class="help-block text-red">
                          <strong>{{ $errors->first($form_field->field_name) }}</strong>
                         </span>
                        @endif
                         @break

                      @case('select')
                         <ul>
                            @forelse (explode(',', $form_field->field_value) as $v)
                              <li>{{ucwords($v)}}</li>
                             @empty
                              <li>no selected</li>
                             @endforelse
                         </ul>
                      @break

                     @case('radio')
                       <input type="radio" name="{{strtolower($form_field->field_name)}}" readonly>
                       <input type="radio" name="{{strtolower($form_field->field_name)}}" readonly>
                         @if ($errors->has($form_field->field_name))
                         <span class="help-block text-red">
                          <strong>{{ $errors->first($form_field->field_name) }}</strong>
                         </span>
                        @endif
                        @break

                     @case('checkbox')
                       <input type="checkbox" name="{{strtolower($form_field->field_name)}}" readonly>
                       <input type="checkbox" name="{{strtolower($form_field->field_name)}}" readonly>
                         @if ($errors->has($form_field->field_name))
                         <span class="help-block text-red">
                          <strong>{{ $errors->first($form_field->field_name) }}</strong>
                         </span>
                        @endif
                       @break

                      @case('phone_number')
                       <input type="text" value="{{$form_field->field_value}}" name="{{strtolower($form_field->field_name)}}" class="form-control numeric" placeholder="{{strtolower($form_field->field_title) }}" readonly>
                         @if ($errors->has(strtolower($form_field->field_name)))
                           <span class="help-block text-red">
                            <strong>{{ $errors->first(strtolower($form_field->field_name)) }}</strong>
                           </span>
                         @endif
                       @break

                     @case('number')
                       <input type="text" value="{{$form_field->field_value}}" name="{{strtolower($form_field->field_name)}}" class="form-control numeric" placeholder="{{strtolower($form_field->field_title) }}" readonly>
                         @if ($errors->has($form_field->field_name))
                         <span class="help-block text-red">
                          <strong>{{ $errors->first($form_field->field_name) }}</strong>
                         </span>
                        @endif
                       @break

                      @case('float')
                       <input type="text" value="{{$form_field->field_value}}" name="{{strtolower($form_field->field_name)}}" class="form-control numeric" placeholder="{{strtolower($form_field->field_title) }}" readonly>
                         @if ($errors->has($form_field->field_name))
                         <span class="help-block text-red">
                          <strong>{{ $errors->first($form_field->field_name) }}</strong>
                         </span>
                        @endif
                       @break

                      @case('money')
                       <input type="text" value="{{$form_field->field_value}}" name="{{strtolower($form_field->field_name)}}" class="form-control numeric" placeholder="{{strtolower($form_field->field_title) }}" readonly>
                         @if ($errors->has($form_field->field_name))
                         <span class="help-block text-red">
                          <strong>{{ $errors->first($form_field->field_name) }}</strong>
                         </span>
                        @endif
                       @break 


                     @case('date')
                      <input type="" class="form-control" value="{{$form_field->field_value}}" readonly>
                       @break

                     @case('time')
                       <input type="time" value="{{$form_field->field_value}}" name="{{strtolower($form_field->field_name)}}" class="form-control" placeholder="{{strtolower($form_field->field_title) }}" readonly>
                         @if ($errors->has($form_field->field_name))
                         <span class="help-block text-red">
                          <strong>{{ $errors->first($form_field->field_name) }}</strong>
                         </span>
                        @endif
                       @break

                     @case('file')
                       <input type="file" value="{{$form_field->field_value}}" name="{{strtolower($form_field->field_name)}}" class="form-control" placeholder="{{strtolower($form_field->field_title) }}" readonly>
                         @if ($errors->has($form_field->field_name))
                         <span class="help-block text-red">
                          <strong>{{ $errors->first($form_field->field_name) }}</strong>
                         </span>
                        @endif
                       @break

                     @case('year')
                        <input type="" class="form-control" value="{{$form_field->field_value}}" readonly>
                     @break

                      @case('image')
                       <input type="file" value="" name="{{strtolower($form_field->field_name)}}" class="form-control" placeholder="{{strtolower($form_field->field_title) }}" readonly>
                         @if ($errors->has($form_field->field_name))
                         <span class="help-block text-red">
                          <strong>{{ $errors->first($form_field->field_name) }}</strong>
                         </span>
                        @endif
                       @break
                      
                      @case('month')
                         <input type="" class="form-control" value="{{ucwords($form_field->field_value)}}" readonly>
                       @break
 
                       @case('day')
                       <input type="" class="form-control" value="{{ucwords($form_field->field_value)}}" readonly>
                       @break

                       @case('time')
                       <input type="time" value="" name="{{strtolower($form_field->field_name)}}" class="form-control" placeholder="{{strtolower($form_field->field_title) }}" readonly>
                         @if ($errors->has($form_field->field_name))
                         <span class="help-block text-red">
                          <strong>{{ $errors->first($form_field->field_name) }}</strong>
                         </span>
                        @endif
                       @break
                 
                     @default
                         <input type="hidden" name="hidden" value="">
                 @endswitch

                <span class="text-danger">{{$errors->first(ucwords($form_field->field_name))}}</span>
              </div>
              @empty
              <p>Filed not available</p>
              @endforelse
          </div>
       </div>
     </div>


    <!-- chat box row -->
            <div class="col-md-12">
              <!-- DIRECT CHAT -->
              <div class="box box-solid direct-chat direct-chat-warning">
                <!-- /.box-header -->
                <div class="box-body">
                  <!-- Conversations are loaded here -->
                  <div class="direct-chat-messages">
                    @forelse($data['comments'] as $comment)
                    <!-- Message. Default to the left -->
                    @if($comment->comment_by != Auth::user()->id)
                    <div class="direct-chat-msg">
                      <div class="direct-chat-info clearfix">
                        <span class="direct-chat-name pull-left">{{$comment->first_name}}</span>
                        <span class="direct-chat-timestamp pull-right">{{ date('d M H:i a',strtotime($comment->created_at)) }}</span>
                      </div>
                      <!-- /.direct-chat-info -->
                      <!-- /.direct-chat-img -->
                      <div class="direct-chat-text">
                        {{ $comment->comment }}
                      </div>
                      <!-- /.direct-chat-text -->
                    </div>
                    <!-- /.direct-chat-msg -->
                    @else
                    <!-- Message to the right -->
                    <div class="direct-chat-msg right">
                      <div class="direct-chat-info clearfix">
                        <span class="direct-chat-name pull-right">{{$comment->first_name}}</span>
                        <span class="direct-chat-timestamp pull-left">{{ date('d M H:i a',strtotime($comment->created_at)) }}</span>
                      </div>
                      <!-- /.direct-chat-info -->
                      <!-- /.direct-chat-img -->
                      <div class="direct-chat-text">
                        {{ $comment->comment }}
                      </div>
                      <!-- /.direct-chat-text -->
                    </div>
                    @endif
                    <!-- /.direct-chat-msg -->
                    @empty
                      <p>No conversation found</p>
                    @endforelse
                  </div>
                  <!--/.direct-chat-messages-->
                </div>
              </div>
              <!--/.direct-chat -->
            </div>
            <!-- /.col -->

         <div class="col-md-12 text-right" style="margin-bottom: 10px;">
          <a class="btn btn-default" style="border-radius: 10px; background: #ffffff ; padding-left: 20px;padding-right: 20px;" href="{{url()->previous()}}">Back</a>
        </div>
      <!-- /.row -->
    </div><!--row--> 
  </section>  
<!-- /. section content -->
@endsection
@section('css-script')
   <style type="text/css">
      input,textarea,select{
         border-right: none !important;
         border-left : none !important;
         border-top: none !important;
         background: #ffffff !important;
      }
   </style>
@endsection
@section('js-script')
  <script type="text/javascript">
     $('input') .bind('keypress', false);
  </script>
@endsection


