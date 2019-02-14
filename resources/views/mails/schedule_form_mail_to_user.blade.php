<!DOCTYPE html>
<html>
<head>
	<title>Schedule Form</title>
</head>
<body>
  <div class="container">
  	 <div class="header">
	  	 <div class="grating">
	  	 	<h2>Hello , {{ ucwords($data['user_name']) }}</h2>
	  	 </div>
  	 </div>
  	 <div class="body">
	  	 <div class="message">
	  	   <p>You have assigned new forms @foreach($data['form_data'] as $form){{ucwords($form->form_name)}}, @endforeach with schedule <b>{{ucwords($data['schedule_name'])}}</b></p>
	  	   <p>Form submission start date is <b>{{date('d-m-y' , strtotime($data['start_date']))}}</b > and last submission date is <b>{{date('d-m-y' , strtotime($data['end_date']))}}</b > </p>
  		   <p>For more information contact to your administrator or <a href="{{url('/')}}">Login</a></p>
	  	 </div>
  	 </div>
  	 <div class="footer">
	  	 <div class="regards">
	  	 	<p>Regards,<br>
	  	 	   <b>FormsTool</b></p>
	  	 </div>
  	 </div>
  </div>
</body>
</html>
<style type="text/css">
	.container{
		margin: 5%;
		background: lightgray;
		padding: 20px;
		border-radius: 5px;
		line-height: 2;
	}
	a{
		 width: 200px;
		 background: skyblue;
		 padding: 10px;
		 text-decoration: none;
		 text-align: center;
		 color:white;
		 border-radius: 5px;
	}
	.message{
	}
</style>