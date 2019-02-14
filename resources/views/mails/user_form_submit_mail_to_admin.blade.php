<!DOCTYPE html>
<html>
<head>
	<title>Submit Form</title>
</head>
<body>
  <div class="container">
  	 <div class="header">
	  	 <div class="grating">
	  	 	<h2>Hello Admin</h2>
	  	 </div>
  	 </div>
  	 <div class="body">
	  	 <div class="message">
	  	   <p>A form <b>{{ ucwords($data['form_name']) }}</b> schedule with <b>{{ucwords($data['schedule_name'])}}</b> has been submitted by the <b>{{ucwords('user_name')}}</b></p>
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