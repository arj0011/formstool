<!DOCTYPE html>
<html>
<head>
	<title>Welcome To FormTools</title>
</head>
<body>
  <div class="container">
  	 <div class="header">
	  	 <div class="grating">
	  	 	<h2>Hello , {{ ucwords($data['name']) }}</h2>
	  	 </div>
  	 </div>
  	 <div class="body">
	  	 <div class="message">
	  	  <p>You have successfully registered as <b>{{$data['role']}}</b> in formstool with email <b>{{$data['email']}}</b> and mobile number <b>{{$data['mobile']}}</b>.</p>
  	      <p>Your password is <b>{{$data['password']}}<b>.</p>
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
	
</style>