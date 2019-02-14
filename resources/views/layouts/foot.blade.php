<!-- jQuery 3 -->
<script src="{{ asset('public/bootstrap/js/jquery.min.js')}}"></script>
 <!-- Bootstrap 3.3.7 -->
<script src="{{ asset('public/bootstrap/js/bootstrap.min.js')}}"></script>
<!-- SlimScroll -->
<script src="{{ asset('public/bootstrap/js/jquery.slimscroll.min.js')}}"></script>
<!-- FastClick -->
<script src="{{ asset('public/bootstrap/js/fastclick.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('public/bootstrap/js/adminlte.min.js')}}"></script>
<!--- Custome Js -->
<script src="{{ asset('public/js/custome.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<!-- sweet alert -->
<script src="{{ asset('public/bootstrap/js/sweetalert.min.js')}}"></script>

<script>
  // Declare a Base Url
 $(document).ready(function(){
  var base_url = '{{ URL::to('/') }}';
  
 // $('[data-toggle="tooltip"]').tooltip();

 /** add active class and stay opened when selected */
   var url = window.location;

// for sidebar menu entirely but not cover treeview
$('ul.sidebar-menu a').filter(function() {
   return this.href == url;
}).parent().addClass('active');

// for treeview
$('ul.treeview-menu a').filter(function() {
   return this.href == url;
}).parentsUntil(".sidebar-menu > .treeview-menu").addClass('active');
   
 @if (auth::user()->role != 1)
   $.ajax({
	              "headers":{
	                  'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
	                  },
	               'type':'get',
	               'url' : '{{ route("getNotifications")}}',
	               'success' : function(response){
	                  if(response.status){
	                  	  $('.notification-count').html(response.data.length);
	                  	  $('.notification-message').html('You have '+response.data.length+ ' new form assign');
	                  	  let html = '';
	                  	      response.data.map(function(item){
	                               html += '<li><a href="{{url('admin/form-submit?form_id=')}}'+item.form_id+'&schedule_id='+item.schedule_id+'"><i class="fa fa-wpforms text-aqua"></i>'+item.name+' (<b>'+item.start_date+'</b>)<small class="pull-right"><small class="pull-right">'+item.schedule_date+'</small></small></a></li>';
	                  	      });
	                  	  $('.notification-list').html(html);
	                  }else{
	                        $('.notification-count').html('0');
	                  	    $('.notification-message').html('You have no new form assign');
	                  	  let html = '';
                              html += '<li><a href="#">No any new form assign</a></li>';
	                  }
	                },
	                'error' : function(error){
	                   console.log(error);
	                }
	});
   @endif
 });

    window.addEventListener("beforeunload", function(e){
        $('input[type=submit]').attr('disabled','disabled');
        $('button[type=submit]').attr('disabled','disabled');
    }, false);

</script>


