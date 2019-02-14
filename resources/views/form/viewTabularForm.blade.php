@extends('layouts.app')
@section('content') 
 @if(Session::has('status'))
            <div class="col-xs-12 alert-message-div">
                <div class="alert @if(Session::get('status')) alert-success @else alert-danger @endif alert-dismissible" role="alert">
                  <strong>@if (Session::get('status')) Success @else Danger @endif!</strong> {{Session::get('msg')}}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
                </div>
            </div>
        @endif
    @if($errors->first())
          <div class="alert alert-danger fade in alert-dismissible">
          <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
          <strong>Failed! </strong>{{ $errors->first() }}.  
          </div><br/>
      @endif
    <section class="content-header">
      {{-- <h1>
        @isset($data['form']){{ ucwords($data['form']->name) }} @endisset
        <small></small>
      </h1> --}}

      {{-- Admin view submitted schedule form data--}}
      @if(isset($data['view_only']) && Auth::user()->role == 1)
        <h1>Submitted Form
         <small>@isset($data['form']){{ ucwords($data['form']->name) }} @endisset</small></h1>
        {{ Breadcrumbs::render('submitted-form-data') }}
      @endif
      
      {{-- Admin view form template--}}
      @if(!isset($data['view_only']) && Auth::user()->role == 1)
        <h1>Form Template <small>@isset($data['form']){{ ucwords($data['form']->name) }} @endisset</small></h1>
        {{ Breadcrumbs::render('template') }}
      @endif
      
      {{-- User submit schedule form --}}
      @if(!isset($data['view_only']) && Auth::user()->role != 1)
        <h1>Scheduled Form <small>@isset($data['form']){{ ucwords($data['form']->name) }} @endisset</small></h1>
        {{ Breadcrumbs::render('schedule-form') }}
      @endif

      {{-- User view filled data --}}
      @if(isset($data['view_only']) && Auth::user()->role != 1)
        <h1>Submitted Form <small>@isset($data['form']){{ ucwords($data['form']->name) }} @endisset</small></h1> 
        {{ Breadcrumbs::render('submitted-form-data') }}
      @endif
     </section>
  <!-- Main content -->
    <section class="content">
    @if(Auth::user()->role==1)
      <div class="row">
       <div class="col-xs-4">
            <a href="{{route('tabularData/export',["user_id"=>Request::get('user_id'),'id'=>base64_encode($data['form_id']),"type"=>"excel","schedule_id"=>Request::get('schedule_id')])}}" class=""><img src="{{url('public/images/excel.png')}}"></img></a>
            <a href="{{route('tabularData/export',["user_id"=>Request::get('user_id'),'id'=>base64_encode($data['form_id']),"type"=>"csv","schedule_id"=>Request::get('schedule_id')])}}" class=""><img height="32" width="48" src="{{url('public/images/csv.png')}}"></img></a>
            <a id="print-table" href="javascript::void();" class=""><img height="48" width="48" src="{{url('public/images/print.png')}}"></img></a>
          </div>
      </div>
      @endif
      <div class="row">
        <!-- left column -->
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-solid">
            {{-- <div class="box-header with-border">
              <h3 class="box-title"></h3>
            </div> --}}
        <div class="box-body">
        <div class="table-responsive">
             @isset($data['form'])
             @if($data['form']->form_type=="Tabular")
             @forelse($data['tables_setting'] as $key=>$ts)
               <div class="table-form">
                         @if(isset($user_form))
                                 {{ Form::open(array('url' => 'admin/form/updateSubmission' , ' data-toggle' => 'validator')) }}
                                 {{ Form::hidden('table_id', $role->id)}}
                         @else
                         {{ Form::open(array('url' => 'admin/form/saveTableFormData/' , ' data-toggle' => 'validator','class' => 'save_data_as_draft')) }}
                         {{ Form::hidden('table_id',$ts->id)}}
                         {{ Form::hidden('form_id',$data['form']->id)}}
                        @endif
                         <table id="forms-field-table{{$key}}" class="table table-bordered table-striped table-hover" style="width:auto">
                                   <thead>
                                   </thead>
                                    <!--Tabular Form Layout-->
                                         <tbody>
                                            @if(isset($data['form_fields']))
                                            <?php
                                             $row_data=isset($ts->row_data)?json_decode($ts->row_data):[];
                                             ?>
                                             <tr>
                                              @empty($ts->table_titles) 
                                              @else 
                                              @php 
                                                $titleArr = json_decode($ts->table_titles,true);
                                              @endphp 
                                              @foreach($titleArr as $ftitle) 
                                                <h4>{{ $ftitle }}</h4>
                                              @endforeach  
                                              @endempty</tr>
                                             <tr>
                                              <th>Sr.no </th>
                                              <th>@empty($ts->label_heading)# @else {{ucwords($ts->label_heading)}} @endempty</th>
                                              @foreach($data['form_fields'] as $ff)
                                              @if($ts->id==$ff->table_id)
                                              <th>{{ ucwords($ff->field_title) }}</th>
                                              @endif
                                              @endforeach
                                             </tr>
                                            @forelse($row_data as $rowno=>$rd)
                                             <tr>
                                                <td>{{ $rd->row_no }} </td>
                                                <td>{{ ucwords($rd->row_label) }} {{ Form::hidden('row_label[]',$rd->row_label)}} </td>
                                               @foreach($data['form_fields'] as $f)
                                               @if($f->table_id==$ts->id)
                                           <td>
                                               <?php 
                                               $records=$ts->table_data;
                                               
                                                      $field_value="";
                                                      $col_name=strtolower($f->field_name);
                                                    ?>
                                               @if(!empty($records))
                                                    @foreach($records as $key=>$record)
                                                    @if($record->row_label==$rd->row_label)
                                                    @if(property_exists($record,$col_name))
                                                      
                                                      @if($f->field_type_id == 19)
                                                        @if(isset($data['view_only']))
                                                          @switch($record->$col_name)
                                                            @case(1)
                                                              Monday
                                                            @break
                                                            @case(2)
                                                              Tuesday
                                                            @break
                                                            @case(3)
                                                              Wednesday
                                                            @break
                                                            @case(4)
                                                              Thursday
                                                            @break
                                                            @case(5)
                                                              Friday
                                                            @break
                                                            @case(6)
                                                              Saturday
                                                            @break
                                                            @case(7)
                                                              Sunday
                                                            @break
                                                          @endswitch
                                                        @endif
                                                      @endif
                                                        @php 
                                                        $field_value=$record->$col_name;
                                                        @endphp
                                                      @endif
                                                      @endif
                                                   @endforeach
                                                @endif
                                              @if(isset($data['view_only']))
                                                @if($f->field_type_id != 19) {{ ucwords($field_value) }} @endif
                                             @else 
                                                <div class="form-group">
                                                

                                                    @switch($f->field_type)
                                                          @case('text')
                                                             <input type="text" value="{{$field_value}}" name="{{strtolower($f->field_name)}}[]" class="form-control" placeholder="{{ucfirst($f->field_title) }}"
                                                            @if (!empty($f->rule))
                                                            @foreach ($f->rule as $key => $rule)
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
                                                              @break

                                                          @case('textarea')
                                                             <textarea value="{{$field_value}}" class="form-control" name="{{strtolower($f->field_name)}}[]"
                                                              @if (!empty($f->rule))
                                                          @foreach ($f->rule as $key => $rule)
                                                          @if ($key == 'validation')
                                                          @if($rule == 1)
                                                          {{ 'required' }}
                                                          @endif
                                                          @endif
                                                          @endforeach
                                                          @endif
                                                           >{{$field_value}}</textarea>
                                                             @break

                                                          @case('email')
                                                             <input type="email" value="{{$field_value}}" name="{{strtolower($f->field_name)}}[]" class="form-control" placeholder="{{ucfirst($f->field_title) }}"
                                                               @if (!empty($f->rule))
                                                              @foreach ($f->rule as $key => $rule)
                                                              @if ($key == 'validation')
                                                              @if($rule == 1 || $rule == 2 )
                                                              {{ 'required' }}
                                                              @endif
                                                              @endif
                                                              @endforeach
                                                              @endif
                                                               >
                                                              @break

                                                          @case('password')
                                                             <input type="password" value="{{$field_value}}" name="{{strtolower($f->field_name)}}[]" class="form-control" placeholder="{{ucfirst($f->field_title) }}">
                                                              @break

                                                           @case('select')
                                                            <select class="form-control"
                                                             name="{{strtolower($f->field_name)}}[{{$rowno}}][]"   
                                                              @if (!empty($f->rule))
                                                              @foreach ($f->rule as $key => $rule)
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
                                                              @foreach (explode(',', $f->field_options) as $option)
                                                                 <option
                                                                   
                                                                @if(old(strtolower($f->field_name)))
                                                                           {{'selected'}}
                                                                @else
                                                                   @if(isset($field_value))
                                                                     @foreach (explode(',', $field_value) as $v)
                                                                        @if(strtolower($option) == strtolower($v))
                                                                             {{'selected'}}
                                                                        @endif
                                                                     @endforeach
                                                                   @endif
                                                                @endif

                                                                  value="{{$option}}">{{ucwords($option)}}</option>
                                                            @endforeach

                                                            </select>
                                                             @break

                                                           @case('phone_number')
                                                            <input type="text" value="{{$field_value}}" name="{{strtolower($f->field_name)}}[]" class="form-control numeric" placeholder="{{ucfirst($f->field_title) }}"  minlength="10" maxlength="10" pattern="[6789][0-9]{9}" data-pattern-error="please enter valid Mobile Number" 
                                                              @if (!empty($f->rule))
                                                              @foreach ($f->rule as $key => $rule)
                                                              @if ($key == 'validation')
                                                              @if($rule == 1)
                                                              {{ 'required' }}
                                                              @endif
                                                              @endif
                                                              @endforeach
                                                              @endif
                                                               >
                                                            @break

                                                          @case('number')
                                                            <input type="number" value="{{$field_value}}" name="{{strtolower($f->field_name)}}[]" class="form-control numeric" placeholder="{{ucfirst($f->field_title) }}"
                                                            @if (!empty($f->rule))
                                                              @foreach ($f->rule as $key => $rule)
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
                                                            @break

                                                           @case('float')
                                                            <input type="text" value="{{$field_value}}" name="{{strtolower($f->field_name)}}[]" class="form-control numeric" placeholder="{{ucfirst($f->field_title) }}"
                                                            @if (!empty($f->rule))
                                                            @foreach ($f->rule as $key => $rule)
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
                                                            @break

                                                           @case('money')
                                                            <input type="text" value="{{$field_value}}" name="{{strtolower($f->field_name)}}[]" class="form-control numeric" placeholder="{{ucfirst($f->field_title) }}">
                                                            @break 


                                                          @case('date')
                                                            <input type="date" value="{{$field_value}}" name="{{strtolower($f->field_name)}}[]" class="form-control" placeholder="{{ucfirst($f->field_title) }}"
                                                            @if (!empty($f->rule))
                                                            @foreach ($f->rule as $key => $rule)
                                                            @if ($key == 'validation')
                                                            @if($rule == 1)
                                                            {{ 'required' }}
                                                            @endif
                                                            @endif
                                                            @endforeach
                                                            @endif
                                                            >
                                                            @break

                                                          @case('time')
                                                            <input type="time" value="{{$field_value}}" name="{{strtolower($f->field_name)}}[]" class="form-control" placeholder="{{ucfirst($f->field_title) }}"
                                                            @if (!empty($f->rule))
                                                            @foreach ($f->rule as $key => $rule)
                                                            @if ($key == 'validation')
                                                            @if($rule == 1)
                                                            {{ 'required' }}
                                                            @endif
                                                            @endif
                                                            @endforeach
                                                            @endif
                                                            >
                                                            @break

                                                          @case('file')
                                                            <input type="file" value="" name="{{strtolower($f->field_name)}}[]" class="form-control" placeholder="{{ucfirst($f->field_title) }}">
                                                            @break

                                                          @case('year')
                                                            <select class="form-control" name="{{strtolower($f->field_name)}}[]" 
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

                          <option @if(old($f->field_name)) {{'selected'}}  @else @if($field_value == $year) 
                                     {{'selected'}}
                                  @endif @endif value="{{$year}}">{{ $year }}</option>
                        @endfor
                                                            </select>
                                                          @break

                                                           @case('image')
                                                            <input type="file" value="" name="{{strtolower($f->field_name)}}[]" class="form-control" placeholder="{{ucfirst($f->field_title) }}">
                                                            @break

                                                           @case('month')
                                                             <select class="form-control" name="{{strtolower($f->field_name)}}[]"
                                                              @if (!empty($f->rule))
                                                              @foreach ($f->rule as $key => $rule)
                                                              @if ($key == 'validation')
                                                              @if($rule == 1)
                                                              {{ 'required' }}
                                                              @endif
                                                              @endif
                                                              @endforeach
                                                              @endif
                                                              >
  <option  value="">--select--</option>
  <option @if (old($f->field_name) == 'january'){{'selected'}} @else @if($field_value == 'january') {{'selected'}} 
    ) @endif @endif  value="january">January</option>

  <option @if (old($f->field_name) == 'february'){{'selected'}} @else @if($field_value == 'february') {{'selected'}} 
    ) @endif @endif  value="february">February</option>

  <option @if (old($f->field_name) == 'march'){{'selected'}} @else @if($field_value == 'march') {{'selected'}} 
    ) @endif @endif  value="march">March</option>

  <option @if (old($f->field_name) == 'april'){{'selected'}} @else @if($field_value == 'april') {{'selected'}} 
    ) @endif @endif  value="april">April</option>

  <option @if (old($f->field_name) == 'may'){{'selected'}} @else @if($field_value == 'may') {{'selected'}} 
    ) @endif @endif  value="may">May</option>

  <option @if (old($f->field_name) == 'june'){{'selected'}} @else @if($field_value == 'june') {{'selected'}} 
    ) @endif @endif  value="june">June</option>

  <option @if (old($f->field_name) == 'july'){{'selected'}} @else @if($field_value == 'july') {{'selected'}} 
    ) @endif @endif  value="july">July</option>

  <option @if (old($f->field_name) == 'august'){{'selected'}} @else @if($field_value == 'august') {{'selected'}} 
    ) @endif @endif  value="august">August</option>

  <option @if (old($f->field_name) == 'september'){{'selected'}} @else @if($field_value == 'september') {{'selected'}} 
    ) @endif @endif  value="september">September</option>

  <option @if (old($f->field_name) == 'october'){{'selected'}} @else @if($field_value == 'october') {{'selected'}} 
    ) @endif @endif  value="october">October</option>
  <option @if (old($f->field_name) == 'november'){{'selected'}} @else @if($field_value == 'november') {{'selected'}} 
    ) @endif @endif  value="november">November</option>

  <option @if (old($f->field_name) == 'december'){{'selected'}} @else @if($field_value == 'december') {{'selected'}} 
    ) @endif @endif  value="december">December</option>
                                                             </select>
                                                            @break

                                                            @case('day')
                                                              <select class="form-control" name="{{strtolower($f->field_name)}}[]">
                                                                <option value="">--select--</option>
                                                                 <option @if ($field_value == 1) selected @endif value="1">Monday</option>
                                                                 <option @if ($field_value == 2) selected @endif value="2">Tuesday</option>
                                                                 <option @if ($field_value == 3) selected @endif value="3">Wednesday</option>
                                                                 <option @if ($field_value == 4) selected @endif value="4">Thrusday</option>
                                                                 <option @if ($field_value == 5) selected @endif value="5">Friday</option>
                                                                 <option @if ($field_value == 6) selected @endif value="6">Saturday</option>
                                                                 <option @if ($field_value == 7) selected @endif value="7">Sunday</option>
                                                              </select>
                                                            @break

                                                            @case('time')
                                                            <input type="time" value="{{$field_value}}" name="{{strtolower($f->field_name)}}[]" class="form-control" placeholder="{{strtolower($f->field_title) }}">
                                                            @break

                                                          @default
                                                              <input type="hidden" name="hidden" value="">
                                                      @endswitch

                                                     <span class="text-danger">{{$errors->first(ucwords($f->field_name))}}</span>
                                                </div>
                                             @endif
                                       </td>
                                       @endif
                                       @endforeach
                                        </tr>
                                            @empty
                                            <tr> <td colspan="2">Template Not Found </td> </tr>
                                             @endforelse
                                      @endif
                                    <tfoot>

                                    </tfoot>
                             </tbody>
                          </table>
                        <input type="hidden" id="input-form-id" value="@isset($form){{ $form->id}} @endisset"/>
                        <input type="hidden" id="form-type" value="@isset($form) {{ $form->form_type}} @endisset" />
                        <input type="hidden" name="schedule_id" value="{{Request::get('schedule_id')}}">
                       @if(count($data['tables_setting'])==1 && !isset($data['view_only']) )
                        @if (auth::user()->role != 1)
                        {{--  <button type="submit" id="btn-submit" class="btn btn-success">                       {{$data['button_title']}}
                         </button> --}} 
                          <button type="submit" id="btn-submit" name="record_store_as" value="1" class="btn btn-success">                       {{$data['button_title']}}
                        </button>
                        <button type="button" id='save_as_draft' formnovalidate="formnovalidate"  class="btn btn-default">Save as draft
                        </button>
                        @endif
                       @endif
                </form>   
              </div>
               @empty
                <p>Template not found</p>
               </div> 
              @endforelse
              @endif
             @endisset
        </div>

    <div class="box-footer">
    <div class="row">
        @if(Auth::user()->role==1 && !isset($data['view_template']))
         {{--    <button form-id="{{$data['form_id']}}" user-id="{{Request::get('user_id')}}" class="btn btn-success btn-accept">Accept</button>
            <button form-id="{{$data['form_id']}}" user-id="{{Request::get('user_id')}}" class="btn btn-primary btn-resubmission">Resubmission</button> --}}
       @endif
  @if(!isset($data['view_only'])) 
  @if(Auth::user()->role!=1)
  @if(count($data['tables_setting'])!=1)
                 <button type="submit" id="btn-submit-ajax" value="0" class="btn btn-success multipleformsbmtcls">Save as draft
                </button>
                <button type="submit" disabled="disabled" value="1" id="mltplformsubmit" class="btn btn-success multipleformsbmtcls">{{$data['button_title']}}
                </button> 
            @endif
         @endif 
      @endif
        </div>
       </div>
      </div>
   </div>
  
    {{-- <div>
      @forelse($data['comments'] as $comment)
        <p style="text-align: {{($comment->comment_by == Auth::user()->id) ? 'right':'left'}};">{{ $comment->comment }}{{ date('d-m-Y H:i a',strtotime($comment->created_at)) }}</p>
      @empty
        <p>No conversation found</p>
      @endforelse
    </div> --}}

    <!-- chat box row -->
    @if(!isset($data['view_template']))
          <div class="row">
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
          </div>
    @endif
          <!-- /.row -->
        <div class="row">
          <div class="col-md-12 text-right" style="margin-bottom: 10px;">
           <a class="btn btn-default" style="border-radius: 10px; background: #ffffff ; padding-left: 20px;padding-right: 20px;" href="{{url()->previous()}}">Back</a>
          </div>
        </div>


        <!--row--> 
  </section>  
<!-- /. section content -->
@endsection
@section('css-script')
<!-- Bootstarap Validator script -->
  <link rel="stylesheet" type="text/css" href="{{ asset('public/bootstrap/css/bootstrapValidator.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('public/bootstrap/css/bootstrap-multiselect.css')}}"/>
  <link rel="stylesheet" type="text/css" href="{{ asset('public/bootstrap/css/bootstrap-multiselect-custome-script.css')}}"/>
  <style type="text/css">
   /*select,input{
    min-width: 100px
   }*/
    input[type="date"]::-webkit-calendar-picker-indicator {
   color: rgba(0, 0, 0, 0);
   opacity: 1;
   display: block;
   background: url(https://mywildalberta.ca/images/GFX-MWA-Parks-Reservations.png) no-repeat;
   width: 20px;
   height: 20px;
   border-width: thin;
}
  </style>
@endsection
@section('js-script')
 <!-- Bootstarap Validator script -->
      <script src="{{ asset('public/bootstrap/js/bootstrapValidator.min.js')}}"></script>
<!-- Multiselect dropdown script --> 
<script src="{{ asset('public/bootstrap/js/bootstrap-multiselect.js')}}"></script>
<script>
 $(document).ready(function(){
  
  /* Submit multiple form submit and save as draft */
  var form_data=[];
  // $('#btn-submit-ajax').click(function(e){
  $('.multipleformsbmtcls').click(function(e){
    var btnVal = $(this).val();
    let click        = $(this);
    let submiBtnText = $(this).text();

    var url;
    var no_of_tables=$('.table-form').length;
    $('.table-form').each(function(index){
      select=$(this).find('form');
      form=select.serialize() + '&record_store_as=' + btnVal;
      url="{{url('admin/form/saveMultiTableFormData')}}";
      var res='';
      $.ajax({
        "headers":{
          'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
        },
        'type':'post',
        'url' :url,
        "data":form,
        async: "false",
         beforeSend: function() {
                     click.attr("disabled","disabled").html('<span class="fa fa-circle-o-notch fa-spin"></span>');
                  },
        'success' : function(response){
          res=response;
          console.log(response);
          if(index==no_of_tables-1){
            if(response.status){
              swal("Good job!", response.message ,"success");
              window.location = "{{url('admin/form')}}";
            }else{
              swal(response.message ,"danger");  
            }
          }
        },error: function (err){
          console.log(err);
        },
         complete: function() {
            click.removeAttr("disabled","disabled").html(submiBtnText);
          },
      });
    })
    
    // if(res.status){
    //   swal("Good job!", res.message ,"success"); 
    // }else{
    //   swal(res.message ,"danger");  
    // }
  });

  // chanage status
$('body').on('click','.btn-accept,.btn-reject',function(e){
    e.preventDefault();
    form_id=$(this).attr('form-id');
    user_id=$(this).attr('user-id');
    btn_type=$(this).text();
    row_id=$(this).closest('tr').attr('id');
    if(btn_type=="Accept") status=2;
      else if(btn_type=="Reject") status=3;
      if(form_id && user_id)
        {
              swal({
              title: "Are you sure ?",
              text: "to change status!",
              type: "warning",
              showCancelButton: true,
              confirmButtonClass: "btn-primary",
              confirmButtonText: "Yes, ok it!",
              closeOnConfirm: false
              },
              function(){
                  window.location.href="{{ route('tabularData/updateStatus') }}?"
                                    +'&user_id='+btoa(user_id)+"&form_id="+form_id+"&status="+btoa(status);
                     });
         }
   });
  //send alert to user for resubmission 
  $('body').on('click','.btn-resubmission',function(e){
    e.preventDefault();
    form_id=$(this).attr('form-id');
    user_id=$(this).attr('user-id');  
    status=4;
  if(form_id && user_id)
        {
              swal({
              title: "Are you sure ?",
              text: "to send alert to for resubmission!",
              type: "warning",
              showCancelButton: true,
              confirmButtonClass: "btn-primary",
              confirmButtonText: "Yes, ok it!",
              closeOnConfirm: false
              },
              function(){
                  window.location.href="{{ route('tabularData/updateStatus') }}?"
                                    +'&user_id='+btoa(user_id)+"&form_id="+form_id+"&status="+btoa(status);
                     });
         }
   });

  });
 </script>
 <script type="text/javascript">
  $(document).ready(function(){
  $('#save_as_draft').click(function(e){
form_data=$('.save_data_as_draft').serialize();
  url="{{url('admin/form/saveTableFormData')}}";
  var res='';
   $.ajax({
             "headers":{
                 'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                 },
              'type':'post',
             'url' :url,
              "data":form_data,
             'success' : function(response){
           
                     if(response.status)
                     { 
                      swal("Good job!", response.message ,"success");
                     }else{
                       swal(response.message ,"danger");  
                     }

                     if(response.save_record_as == 1){
                           $('.btn-submit-ajax').hide();
                     }

              },
               error: function (err){
                   console.log(err);
                 }
           });


});
  });

$(function(){ 
  $("select[multiple]").each(function(){
    $(this).find("option").eq(0).remove(); 
  });
  $("select[multiple]").multiselect({
    includeSelectAllOption         : true,
    enableFiltering                : true,
    enableCaseInsensitiveFiltering : true,
    maxHeight                      : 400
  });
});
</script>
<script type="text/javascript">
  /* Save as Draft for multiple Form */

  $(function(){
    enabledisablebtn();
  })

  $( "input,textarea" ).keypress(function() {
    enabledisablebtn();
  });

  $( "select" ).change(function() {
    enabledisablebtn();
  });

  $( "input,textarea" ).blur(function() {
    enabledisablebtn();
  });

  function enabledisablebtn()
  {
    tabcount = $('.table-form').length;
    for(i = 0;i<tabcount;i++){
      var form = $('.table-form').find('form');
        status = formvalidate(form);
        if(!status) break;
    }
    console.log('status value = '+status);
    if(status == 'true'){
      console.log('inside');
      $('#mltplformsubmit').prop('disabled', false);
    }else{
      console.log('outsde');
      $('#mltplformsubmit').prop('disabled', 'disabled');
    }
  }


  function formvalidate(form)
  {
    form.each(function(){
        inputs = form.find(':input,select,textarea'); 
    });

    // Iterate over the form controls
    for (i = 0; i < inputs.length; i++) {
      
      var require = true;
      var selrequire = true;
      var email = true;
      var number = true;
      var phon = true;
      var txtarequire = true;
          
          if (inputs[i].nodeName === "INPUT" && inputs[i].hasAttribute("required") === true) {
        require =  ((inputs[i].value == '') || (inputs[i].value == undefined) || (inputs[i].value == null)) ? false : true;
        if(!require) return require;
      } 

      if (inputs[i].nodeName === "INPUT" && inputs[i].type === "email") {
        var eml = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        email = (eml.test(String(inputs[i].value).toLowerCase())) ? true : false;
        if(!email) return email;
      }

      if (inputs[i].nodeName === "INPUT" && inputs[i].type === "number") {
        var num = /^\d+$/;
        number = (num.test(inputs[i].value)) ? true : false;
        if(!number) return number;
      }
      if (inputs[i].nodeName === "INPUT" && inputs[i].hasAttribute("pattern") === true) {
        var ph = /^[6-9]\d{9}$/;
        phon = (ph.test(inputs[i].value)) ? true : false;
        if(!phon) return phon;
      }

      if (inputs[i].nodeName === "SELECT" && inputs[i].hasAttribute("required") === true){
        selrequire =  (inputs[i].selectedIndex === 0 ) ? false : true;
        if(!selrequire) return selrequire;
      }

      if (inputs[i].nodeName === "TEXTAREA" && inputs[i].hasAttribute("required") === true){
        txtarequire =  ((inputs[i].value == '') || (inputs[i].value == undefined) || (inputs[i].value == null)) ? false : true;
        if(!txtarequire) return txtarequire;
      }
      
    }
    return true;
  }
</script>
@endsection
