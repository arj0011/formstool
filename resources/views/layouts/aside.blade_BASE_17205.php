    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
            @if (!empty(auth::user()->profile_image) && file_exists('public/images/profile_images/'.auth::user()->profile_image))
               <img src="{{ asset('public/images/profile_images/'.auth::user()->profile_image) }}" class="user-image img-circle" alt="User Image">
              @else
               <img src="{{ asset('public/images/profile_images/image_not_available.jpeg') }}" class="user-image img-circle" alt="User Image">
               @endif
        </div>
        <div class="pull-left info">
          <p>{{ auth::user()->first_name}}</p>
        </div>
      </div>
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
      
        <li>
          <a href="{{route('dashboard')}}">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
          </a>
        </li>
        
        @can('index' , App\Role::class)
           <li class="">
            <a href="{{ url('admin/roles')}}">
              <i class="fa fa-users"></i>
              <span>Roles</span>
            </a>
           </li>
          @endcan
         
        @can('index' , App\User::class)
          <li class="">
            <a href="{{ route('user/index') }}">
              <i class="fa fa-users"></i>
              <span>Users</span>
            </a>
          </li>
        @endcan

        @can('index' , App\UserGroup::class)
           <li class="treeview">
          <a href="#">
            <i class="fa fa-group"></i> <span>Groups</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
            <ul class="treeview-menu">
             @if(Auth::user()->role==1)<li class=""><a href="{{ route('roleGroup/index')}}"><i class="fa fa-users"></i>Users</a></li>@endif
             <li class=""><a href="{{ route('formGroup/index')}}"><i class="fa fa-wpforms"></i>Forms</a></li> 
            </ul>
          </li>
        @endcan

        
     {{--    @can('index' , App\Form::class)
         <li class="">
          <a href="{{ url('admin/form')}}">
            <i class="fa fa-edit"></i>
            <span>Forms</span>
           </a>
         </li> 
        @endcan --}}
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-wpforms"></i> <span>Forms</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class=""><a href="{{ url('admin/form')}}"><i class="fa fa-circle-o text-white"></i>Forms</a>
            </li> 
            <li><a href="{{ route('schedule/index') }}"><i class="fa fa-circle-o text-yellow"></i> Schedule</a></li>
            <li><a href="#"><i class="fa fa-circle-o text-aqua"></i> Publish</a></li>
          </ul>
        </li>@can('index',App\UserRequest::class)    
            <li class="">
                 <a href="{{ route('users_request/index') }}">
                   <i class="fa fa-users"></i>
                   <span>Change Request</span>
                 </a>
            </li>@endcan
      </ul>
    </section>
    <!-- /.sidebar -->