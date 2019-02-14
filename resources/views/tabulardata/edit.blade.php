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
       <form class="form" action="{{ route('tabularData/update') }}" method="POST" enctype="multipart/form-data" novalidate>
       {{ csrf_field() }}
       <input type="hidden" name="table_id" value="{{ Request::get('table_id')}}" >
       <input type="hidden" name="data_id" value="{{ Request::get('data_id')}}">
       <input type="hidden" name="table_name" value="{{ Request::get('table_name')}}">
       <div class="box box-solid">
          <div class="box-body">
                        <table id="forms-field-table" class="table table-bordered table-responsive table-striped table-hover">
                                   <thead>
                                       <tr>
                                        <th>Sr.no </th>
                                        <th>{{$data['label_headig']}}</th>
                                        @foreach($data['form_fields'] as $ff)
                                        <th>{{ $ff['field_title'] }}</th>
                                        @endforeach
                                       </tr>
                                   </thead>
                                    <!--Tabular Form Layout-->
                                   <tbody>
                                   <tr>
                                    <td>1</td>
                                    <?php  $row_data=(array)$data['row_data']; ?>
                                    <td>{{$row_data['row_label']}}</td>
                                    @forelse($data['form_fields'] as $ff) 
                                   <td>
                                    @if(!isset($data['show_only']))
                                      <div class="form-group">

                                              @switch($ff['field_type']['field_type_identifier'])
                                                  @case('text')
                                                     <input type="text" value="{{ $row_data[strtolower($ff['col_name'])]}}" name="{{strtolower($ff['field_name'])}}" class="form-control" placeholder="{{ strtolower($ff['field_title']) }}">
                                                      @break

                                                  @case('textarea')
                                                     <textarea class="form-control" value="{{ $row_data[strtolower($ff['col_name'])]}}" name="{{strtolower($ff['field_name'])}}">{{$f->field_value}}</textarea>
                                                     @break

                                                  @case('email')
                                                     <input type="email" value="{{ $row_data[strtolower($ff['col_name'])]}}" name="{{strtolower($ff['field_name'])}}" class="form-control" placeholder="{{strtolower($ff['field_title']) }}">
                                                      @break

                                                  @case('password')
                                                     <input type="password" value="{{ $row_data[strtolower($ff['col_name'])]}}" name="{{strtolower($ff['field_name'])}}" class="form-control" placeholder="{{strtolower($ff['field_title']) }}">
                                                      @break

                                                   @case('select')
                                                    <select class="form-control" name="{{strtolower($ff['field_name'])}}">
                                                       <option value="1">first</option>
                                                       <option value="2">second</option>
                                                       <option value="3">third</option>
                                                    </select>
                                                     @break

                                                  @case('radio')
                                                    <input type="radio" name="{{strtolower($ff['field_name'])}}">
                                                    <input type="radio" name="{{strtolower($ff['field_name'])}}">
                                                     @break

                                                  @case('checkbox')
                                                    <input type="checkbox" name="{{strtolower($ff['field_name'])}}">
                                                    <input type="checkbox" name="{{strtolower($ff['field_name'])}}">
                                                    @break

                                                   @case('phone_number')
                                                    <input type="text" value="{{ $row_data[strtolower($ff['col_name'])]}}" name="{{strtolower($ff['field_name'])}}" class="form-control numeric" placeholder="{{strtolower($ff['field_title']) }}">
                                                    @break

                                                  @case('number')
                                                    <input type="text" value="{{ $row_data[strtolower($ff['col_name'])]}}" name="{{strtolower($ff['field_name'])}}" class="form-control numeric" placeholder="{{strtolower($ff['field_title']) }}">
                                                    @break

                                                   @case('float')
                                                    <input type="text" value="{{ $row_data[strtolower($ff['col_name'])]}}" name="{{strtolower($ff['field_name'])}}" class="form-control numeric" placeholder="{{strtolower($ff['field_title']) }}">
                                                    @break

                                                   @case('money')
                                                    <input type="text" value="{{ $row_data[strtolower($ff['col_name'])]}}" name="{{strtolower($ff['field_name'])}}" class="form-control numeric" placeholder="{{strtolower($ff['field_title']) }}">
                                                    @break 


                                                  @case('date')
                                                    <input type="date" value="{{ $row_data[strtolower($ff['col_name'])]}}" name="{{strtolower($ff['field_name'])}}" class="form-control" placeholder="{{strtolower($ff['field_title']) }}">
                                                    @break

                                                  @case('time')
                                                    <input type="time" value="{{ $row_data[strtolower($ff['col_name'])]}}" name="{{strtolower($ff['field_name'])}}" class="form-control" placeholder="{{strtolower($ff['field_title']) }}">
                                                    @break

                                                  @case('file')
                                                    <input type="file" value="{{ $row_data[strtolower($ff['col_name'])]}}" name="{{strtolower($ff['field_name'])}}" class="form-control" placeholder="{{strtolower($ff['field_title']) }}">
                                                    @break

                                                  @case('year')
                                                    <select class="form-control" name="{{strtolower($ff['field_name'])}}" >
                                                     <?php $year = 2015 ?>
                                                     @for ($i = 1; $i < 60 ; $i++)
                                                       <option value="1">{{ $year-- }}</option>
                                                     @endfor
                                                    </select>
                                                  @break

                                                   @case('image')
                                                    <input type="file" value="{{ $row_data[strtolower($ff['col_name'])]}}" name="{{strtolower($ff['field_name'])}}" class="form-control" placeholder="{{strtolower($ff['field_title']) }}">
                                                    @break



                                                   @case('month')
                                                     <select class="form-control" name="{{strtolower($ff['field_name'])}}">
                                                       <option @if ($f->field_value == 1) selected @endif value="1">January</option>
                                                       <option @if ($f->field_value == 2) selected @endif value="2">February</option>
                                                       <option @if ($f->field_value == 3) selected @endif value="3">March</option>
                                                       <option @if ($f->field_value == 4) selected @endif value="4">April</option>
                                                       <option @if ($f->field_value == 5) selected @endif value="5">May</option>
                                                       <option @if ($f->field_value == 6) selected @endif value="6">June</option>
                                                       <option @if ($f->field_value == 7) selected @endif value="7">July</option>
                                                       <option @if ($f->field_value == 8) selected @endif value="8">August</option>
                                                       <option @if ($f->field_value == 9) selected @endif value="9">September</option>
                                                       <option @if ($f->field_value == 10) selected @endif value="10">October</option>
                                                       <option @if ($f->field_value == 11) selected @endif value="11">November</option>
                                                       <option @if ($f->field_value == 12) selected @endif value="12">December</option>
                                                     </select>
                                                    @break

                                                    @case('day')
                                                      <select class="form-control" name="{{strtolower($ff['field_name'])}}">
                                                         <option @if ($f->field_value == 1) selected @endif value="1">Monday</option>
                                                         <option @if ($f->field_value == 2) selected @endif value="2">Tuesday</option>
                                                         <option @if ($f->field_value == 3) selected @endif value="3">Wednesday</option>
                                                         <option @if ($f->field_value == 4) selected @endif value="4">Thrusday</option>
                                                         <option @if ($f->field_value == 5) selected @endif value="5">Friday</option>
                                                         <option @if ($f->field_value == 6) selected @endif value="6">Saturday</option>
                                                         <option @if ($f->field_value == 7) selected @endif value="7">Sunday</option>
                                                      </select>
                                                    @break

                                                    @case('time')
                                                    <input type="time" value="{{ $row_data[strtolower($ff['col_name'])]}}" name="{{strtolower($ff['field_name'])}}" class="form-control" placeholder="{{strtolower($ff['field_title']) }}">
                                                    @break

                                                  @default
                                                      <input type="hidden" name="hidden" value="">
                                              @endswitch

                                             <span class="text-danger">{{$errors->first(ucwords($ff['field_name']))}}</span>
                                        </div>  
                                   
                                    @else {{ $row_data[strtolower($ff['col_name'])]}}
                                    @endif
                                   </td>
                                  @empty
                                  @endforelse
                             <tfoot>

                            </tfoot>
                            </tbody>
                          </table>
          </div>
          <div class="box-footer">
            <input type="submit" class="btn btn-success" value="Update">
            <a class="btn btn-default" href="{{ url()->previous() }}">Back</a>
          </div>
       </div>
       </form>
     </div>
    </div><!--row--> 
  </section>  
<!-- /. section content -->
@endsection


