@extends('layouts.app')
@section('content')  
<style>
    .edit-del {
        padding: 6px 7px;
    }
    .edit-del .fa {
        font-size: 16px;
    }
</style>
     <!-- Content Header (Page header) -->
      <section class="content-header">
      <h1>
        {{ucwords($form_name)}}
      </h1>
       {{ Breadcrumbs::render('data',ucwords($form_name)) }}
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
            <a href="{{route('tabularData/export',['id' =>Request::get('id'),"type"=>"excel"])}}" class=""><img src="{{url('public/images/excel.png')}}"></img></a>
            <a href="{{route('tabularData/export',['id' =>Request::get('id'),"type"=>'csv'])}}" class=""><img height="32" width="48" src="{{url('public/images/csv.png')}}"></img></a>
            <a id="print-table" href="javascript::void();" class=""><img height="48" width="48" src="{{url('public/images/print.png')}}"></img></a>
          </div>
          <div class="col-xs-8 add-btn-div">
              <a href="{{route('data/create',['id' => base64_encode(Request::get('id')) ])}}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add</a>
          </div>
      <div class="table-content">
        @isset($data)
        @foreach($data as $key=>$table_data)
        <div class="col-xs-12">
          <div class="box box-solid">
            <div class="box-header">
              <h3 class="box-title">{{array_first($table_data['table_titles'])}}</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
              <table class="table table-condensed w-auto" id="table-{{$table_data['table_id']}}">
                <thead>
                  <tr>
                   @foreach($table_data['columns'] as $column)
                   @if($column=="row_label")
                   <th>{{ $table_data['label_heading'] }}</th>
                    @else<th>{{ ucwords(str_replace('_', ' ', strtolower($column))) }}</th>
                   @endif
                   @endforeach
                     <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                 @foreach($table_data['table_data'] as $key=>$value)
                  <tr id="row-{{$key}}-{{$table_data['table_id']}}"> 
                  @foreach($table_data['columns'] as $column)
                 @if($column=="status")
                    <td>
                    @switch($value->$column)
                        @case(0)
                        <span style="color:coral" class="">Pending</span>
                          @break
                        @case(1)
                        <span style="color:yellowgreen" class="">Submitted</span>
                          @break
                          @case(2)
                          <span style="color:green">Accepted</span>
                          @break
                          @case(3)
                          <span style="color:red">Rejected</span>
                        @break
                          
                    @endswitch
                    </td>
                  @else
                  <td>{{ ucwords($value->$column)}}</td>
                  @endif
                  @endforeach
                 <td style="min-width: 250px;">
                     <a href="{{url('tabularData-show?table_id='.base64_encode($table_data['table_id']).'&data_id='.base64_encode($value->id).'&table_name='.$table_data['table_name'])}}" class="edit-del btn btn-xs btn-info"><i class="fa fa-info-circle"></i></a> <a href="{{url('tabularData-edit?table_id='.base64_encode($table_data['table_id']).'&data_id='.base64_encode($value->id).'&table_name='.$table_data['table_name'])}}" class="edit-del btn btn-xs btn-primary"><i class="fa fa-edit"></i></a> <button data-id="{{$value->id}}" table-name="{{$table_data['table_name']}}" class="edit-del btn btn-xs btn-danger btn-dlt"><i class="fa fa-trash"></i></button>
                     @if($value->status!=2)<button table-name="{{$table_data['table_name']}}" data-id="{{$value->id}}" class="btn btn-success btn-accept">Accept</button> @endif
                     @if($value->status!=3)<button table-name="{{$table_data['table_name']}}" data-id="{{$value->id}}" class="btn btn-danger btn-reject">Reject</button>@endif
                 </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
      
        @endforeach
        @endisset
        <!-- /.col -->
      </div>
      <div class="col-xs-4">
         <a class="btn btn-primary" href="{{url('admin/form')}}">Back</a>
     </div>
     </div>
     
    </section>
    <!-- /.content -->
@endsection
 @section('css-script')
      <!-- Bootstarap Validator script -->
      <link rel="stylesheet" type="text/css" href="{{ asset('public/bootstrap/css/bootstrapValidator.min.css') }}">
        <!-- Bootstrap datatable script -->
      <link rel="stylesheet" type="text/css" href="{{ asset('public/bootstrap/css/dataTables.bootstrap.min.css') }}">
     
      <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">
      <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">
      <style type="text/css">
          .toolbar {
            float:left;
          }
         th{
          max-width:100%;
         }
      </style>
    @endsection
    @section('js-script')
      <!-- Bootstarap Validator script -->
      <script src="{{ asset('public/bootstrap/js/bootstrapValidator.min.js')}}"></script>
      <!-- Bootstrap Jquery DataTables -->
      <script src="{{ asset('public/bootstrap/js/jquery.dataTables.min.js')}}"></script>
      <!-- Bootstrap DataTables -->
      <script src="{{ asset('public/bootstrap/js/dataTables.bootstrap.min.js')}}"></script>

            <!-- datatable script -->
      <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
      <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
      <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
      <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
      <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
      <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
     <script type="text/javascript">
      $(function(){
        $('.table').dataTable();
        $('body').on('click','.btn-dlt',function(){

              let data_id = $(this).attr('data-id');
              let table_name = $(this).attr('table-name');

              var click = $(this);

              swal({
              title: "Are you sure?",
              text: "Your will not be able to recover this record!",
              type: "warning",
              showCancelButton: true,
              confirmButtonClass: "btn-danger",
              confirmButtonText: "Yes, delete it!",
              closeOnConfirm: false
              },
              function(){
               // $(this).closest('tr').remove();
                 $.ajax({
                    "headers":{
                    'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                    },
                    'type':'GET',
                    'url' : '{{ route('tabularData/delete') }}',
                    'data' : { data_id:data_id,table_name:table_name },
                    'success' : function(response){
                      console.log(response);
                        if(response.status){
                                click.closest('tr').remove();
                             // $('#dataTable').dataTable().ajax.reload()
                              swal("Good job!", response.message , "success");
                        }else{
                              swal(response.message , "danger");
                        }
                  },error:function(err){ 
                      console.log(err);
                    }
               });
              });

            });
  // print a record 
  $('#print-table').click(function() {
    html=$('.table-content').html();
    console.log(html);
    printHtml(html);
});

// chanage status
$('body').on('click','.btn-accept,.btn-reject',function(e){
    e.preventDefault();
    table_name=$(this).attr('table-name');
    data_id=$(this).attr('data-id');
    btn_type=$(this).text();
    row_id=$(this).closest('tr').attr('id');
    if(btn_type=="Accept") status=2;
      else if(btn_type=="Reject") status=3;
        if(table_name && data_id)
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
                                    +'&data_id='+btoa(data_id)+"&table_name="+table_name+"&status="+btoa(status);
                     });
         }
   });
 function dynamicButton(table_name='',data_id='',form_status='')
 {
   if(form_status==1){
    btn_text="Reject";
    class_name="btn btn-danger btn-reject";
   }
   else if(form_status==2){ 
      btn_text="Accept";
      class_name="btn btn-success btn-accept";
   }
   return '<button class="'+class_name+'"data-name="'+table_name+'" data-id="'+data_id+'" >'+btn_text+'</button>'
 }
 // close of document ready function
});

function printHtml(html=''){
        //Get the HTML of div
        //var divElements = document.getElementById(divID).innerHTML;
        //Get the HTML of whole page
       // var oldPage = document.body.innerHTML;
        //Reset the page's HTML with div's HTML only
       document.body.innerHTML="<html><head><title></title></head><body>"+html+"</body>";
        //Print Page
       window.print();
        //Restore orignal HTML
      document.body.innerHTML=html;

    }
  </script>
  @endsection

    




