<!DOCTYPE html>
<html>
<head>
	<title>Schedule Form</title>
</head>
<body>
  <div class="container">
  	 <div class="header">
	  	 <div class="grating">
	  	 	<h2>Hello,</h2>
	  	 </div>
  	 </div>
  	 <div class="body">
	  	 <div class="message">
	  	   <p>{{ ucfirst($data['form_name']) }} has been schedule to {{ $data['user_name'] }}.Last date of submitting form was {{ $data['end_date'] }} but form is not submitted yet.Please ask {{ $data['user_name'] }} to submit form.</p>
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