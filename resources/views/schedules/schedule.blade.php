@extends('layouts.app')
@section('content')   
     <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Schedule Details
      </h1>
      {{-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Schedule</a></li>
        <li class="active">Schedule Details</li>
      </ol> --}}
      {{ Breadcrumbs::render('schedule-details') }}
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

    <div class="row">
        
        <div class="col-md-12">
	        <!-- Box For Schedule Basic Details -->
	        <div class="box box-solid">
	          <div class="box-header">
	          	<h4>Schedule Details <a class="pull-right collapse-btn" data-toggle="collapse" href="#scheduleDetails">-</a></h4>
	          </div>
	          <div id="scheduleDetails" class="collapse">
		          <div class="box-body">
		            <table class="table">
		              <body>
		                <tr>
		            		<th>Schedule Name</th>
		            		<td>{{ucwords($data['schedule_data']->schedule_name)}}</td>
		            	</tr>
		            	<tr>
		            		<th>Schedule For</th>
		            		<td>{{ucwords(str_replace('_' , ' ' ,$data['schedule_data']->schedule_for))}}</td>
		            	</tr>
		            	<tr>
		            	<th>Schedule Date</th>
		            		<td>{{date('d M Y', strtotime($data['schedule_data']->schedule_date))}}</td>
		            	</tr>
		            	<tr>
		            	<th>Start Date</th>
		            		<td>{{date('d M Y',strtotime($data['schedule_data']->start_date))}}</td>
		            	</tr>
		            	   <th>Expiry Date</th>
		            		<td class="text-danger">{{date('d M Y',strtotime($data['schedule_data']->end_date)) }}</td>
		            	</tr>
		              </body>
		            </table>
		          </div>

		       </div>
	        </div>

         <!-- Box For Schedule Forms Details -->
	        <div class="box box-solid">
	          <div class="box-header">
	          	<h4>Schedule Forms <a  class="pull-right collapse-btn" data-toggle="collapse" href="#scheduleForms">-</i></a></h4>
	          </div>
	          <div id="scheduleForms" class="collapse">
		          <div class="box-body">
		            <table class="table" id="formDataTable">
		              <thead>
		              	<tr>
		              		<th>Sr.</th>
		              		<th>Form ID</th>
		              		<th>Name</th>
		              	</tr>
		              </thead>
		              <body>
		                @forelse($data['forms'] as $key => $form)
			                <tr>
			                	<td>{{ ++$key }}</td>
			                	<td>{{ $form->id }}</td>
			                	<td>{{ ucwords($form->name) }}</td>
			                </tr>
			            @empty
		                @endforelse
		              </body>
		            </table>
		          </div>
	           </div>
	        </div>

	         <!-- Box For Schedule Forms Details -->
	        <div class="box box-solid">
	          <div class="box-header">
	          	<h4>Schedule Users <a  class="pull-right  collapse-btn" data-toggle="collapse" href="#scheduleUsers">-</i></a></h4>
	          </div>
	          <div id="scheduleUsers" class="collapse">
		          <div class="box-body">
		            <table class="table" id="userDataTable">
		              <thead>
		              	<tr>
		              		<th>Sr.</th>
		              		<th>Name</th>
		              		<th>Group Name</th>
		              		<th>Form Name</th>
		              		<th>Authority Email</th>
		              		<th>Submit Status</th>
		              	</tr>
		              </thead>
		              <tbody>
		                @forelse($data['users'] as $key => $user)
			                <tr>
			                	<td>{{ ++$key }}</td>
			                	<td>{{ ucwords($user->first_name) }}</td>
			                	<td>
			                		@if ($user->group_name && is_null($user->group_deleted_at))
			                			{{ucwords($user->group_name)}}
			                		@else
			                		    {{ 'N/A' }}
			                		@endif
			                	</td>
			                	<td>{{ ucwords($user->form_name) }}</td>
			                	<td>
			                	@if (!empty($user->high_authority_user) && !is_null($user->high_authority_user))
			                	{{-- @php $high_authority_users = unserialize($user->high_authority_user)@endphp --}}

			                	@php 
			                	$high_authority_users_Str = '';
			                	$high_authority_users = json_decode($user->high_authority_user,true);
			                	$high_authority_users_Arr = array_column($high_authority_users, 'email');
			                	$high_authority_users_Str = implode(',',$high_authority_users_Arr);
			                	@endphp

			                	 {{-- @forelse($high_authority_users as $auth_user)
                                      <span>{{ ucwords($auth_user->email ) }}</span>
                                    @empty
                                      <p>N/A</p>
                                 @endforelse
                                @else
                                    <p>N/A</p> --}}
                                    <p>{{ $high_authority_users_Str }}</p>
			                	@endif
			                	</td>
			                	<td>
			                	  @if ($user->submit_status == 1)
			                	   <span class="text-success">{{ 'Submited' }}</span>
			                	  @elseif($user->submit_status == 2)
			                	    <span class="text-warning">{{ 'Resubmit' }}</span>
			                	  @else
			                	    <span class="text-danger">{{ 'Pending' }}</span>
			                	  @endif
			                	</td>
			                </tr>
			            @empty
		                @endforelse
		              </tbody>
		            </table>
		          </div>
	           </div>
	        </div>
        </div>
        <div class="col-md-12 text-right" style="margin-bottom: 10px;margin-top:15px;">
          <a class="btn btn-default" style="border-radius: 10px; background: #ffffff ; padding-left: 20px;padding-right: 20px;" href="{{url()->previous()}}">Back</a>
        </div>
        </div>

    </section>
    <!-- /.content -->
@endsection
@section('css-script')
   <!-- Bootstrap datatable script -->
      <link rel="stylesheet" type="text/css" href="{{ asset('public/bootstrap/css/dataTables.bootstrap.min.css') }}">
      <style type="text/css">
        .auth-user span {
        	 margin-right: 5px;
        }
        .collapse-btn{
        	font-size: 30px;
        }
      </style>
@endsection
@section('js-script')
  <!-- Bootstrap Jquery DataTables -->
  <script src="{{ asset('public/bootstrap/js/jquery.dataTables.min.js')}}"></script>
  <!-- Bootstrap DataTables -->
  <script src="{{ asset('public/bootstrap/js/dataTables.bootstrap.min.js')}}"></script>

  <script type="text/javascript">
  	$('#scheduleDetails').collapse("show");
  	$('#scheduleUsers').collapse("show");
  	$('#scheduleForms').collapse("show");

  	$(document).ready(function() {
	    // $('#formDataTable').DataTable();
	    // $('#userDataTable').DataTable();
	       $('#formDataTable,#userDataTable').DataTable();

	        

	    $('.collapse-btn').click(function(){
		    $(this).text(function(i,old){
		        return old=='+' ?  '-' : '+';
		    });
		});

} );
  </script>

@endsection


