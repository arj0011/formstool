@extends('layouts.app')
@section('content')   
     <!-- Content Header (Page header) -->
      <section class="content-header">
      <h1>
        {{ucwords($data['form_name'])}}
      </h1>
       {{ Breadcrumbs::render('data',ucwords($data['form_name'])) }}
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

          <div class="col-xs-12 add-btn-div">
              <a href="{{route('data/create',['id' => base64_encode(Request::get('id')) ])}}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add</a>
          </div>
        
        <div class="col-xs-12">
          <div class="box box-solid">
            <div class="box-header">
             {{--  <h3 class="box-title">Data Table With Full Features</h3> --}}
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table class="table table-condensed" id="dataTable">
                <thead>
                  <tr>
                   @foreach ($data['columns'] as $column)
                      <th>{{ ucwords(str_replace('_', ' ', strtolower($column))) }}</th>
                   @endforeach
                     <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
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
               $('#dataTable').DataTable( {
                 scrollY    : '500px',
          scrollCollapse    : true,
               paging       : true,
                 responsive : true,
                 processing : true,
                 serverSide : true,
                  dom: 'Bfrtip',
                   buttons: [
                      'copy', 'csv', 'excel'
                  ],
                 ajax       :  '{{ url('data-list?id='.Request::get('id')) }}',
                 columns : [
                  @foreach ($data['columns'] as $column)
                    { data : '{{$column}}' , name : '{{$column}}',
                      render :  function(data,type,column,meta){
                           if(data.search('.png') > 0 || data.search('.jpg') > 0 || data.search('.jpeg') > 0 || data.search('.xls') > 0 || data.search('.pdf') > 0 || data.search('.odd') > 0  )
                                return '<a href="{{asset('public/forms/files/')}}'+'/'+data+'" download>Download</a>';
                              else
                                return data;
                      }
                    },
                  @endforeach
                  {
                    data: 'action',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, column, meta) {

                       let button = '<a title="view record" href="{{url('show-data?form_id='.Request::get('id'))}}&data_id='+column.id+'" class="btn btn-xs btn-info"><i class="fa fa-info-circle"></i></a>';

                           button +='<a title="accept" href="" class="btn btn-xs btn-danger btn-dlt"><i class="fa fa-trash"></i></a>';

                           button +='<button title="delete record" data-id="'+column.id+'" form-id="{{Request::get('id')}}" class="btn btn-xs btn-danger btn-dlt"><i class="fa fa-trash"></i></button>';
                       
                       return button;
                    }
                  }
                ]
               });
            });
  

          $(document).ready(function(){
          $('body').on('click','.btn-dlt',function(){

              let data_id = $(this).attr('data-id');
              let form_id = $(this).attr('form-id');

              var click = $(this);

              swal({
              title: "Are you sure?",
              text: "Your will not be able to recover this user!",
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
              'url' : '{{ route('data/delete') }}',
                    'data' : { data_id : data_id , form_id :form_id },
                    'success' : function(response){
                      console.log(response);
                        if(response.status){
                                click.closest('tr').remove();
                             // $('#dataTable').dataTable().ajax.reload()
                              swal("Good job!", response.message , "success");
                        }else{
                              swal(response.message , "danger");
                        }
                  },
               });
              });

            });
        });
        </script>
      
    @endsection

    




