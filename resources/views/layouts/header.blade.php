  <!-- Logo -->
    <a href="{{route('dashboard')}}" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>F</b>T</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>Forms</b>Tool</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- form notification start -->
           @if (auth::user()->role != 1)
         <li class="dropdown notifications-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-bell-o"></i>
              <span class="label label-warning notification-count" ></span>
            </a>
            <ul class="dropdown-menu">
              <li class="header notification-message"></li>
              <li>
                <!-- inner menu: contains the actual data -->
                <ul class="menu notification-list">
                </ul>
              </li>
            </ul>
          </li>
           @endif
          <!-- form notification end -->
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="{{ route('profile/show') }}" class="dropdown-toggle" data-toggle="dropdown">
              @if (!empty(auth::user()->profile_image) && file_exists('public/images/profile_images/'.auth::user()->profile_image))
               <img src="{{ asset('public/images/profile_images/'.auth::user()->profile_image) }}" class="user-image img-circle" alt="User Image">
              @else
               <img src="{{ asset('public/images/profile_images/image_not_available.jpeg') }}" class="user-image img-circle" alt="User Image">
               @endif
               @if (Session::has('adminAuth'))
                <span class="hidden-xs"><b>Login By</b></span>
               @endif
              <span class="hidden-xs">{{ auth::user()->first_name}}</span>
            </a>
            <ul class="dropdown-menu">
              <li>
                  <a href="{{ route('profile/show') }}" class="btn btn-default btn-flat btn-block" style="background: white; line-height: 30px;">Profile</a>
              </li>
               <li>
                  <a href="{{ route('profile/change-password') }}" class="btn btn-default btn-flat btn-block" style="background: white; line-height: 30px;">Change Password</a>
              </li>
              <li>
                   <a style="background: white; line-height: 30px;" href="#" class="btn btn-default btn-flat btn-block" href="{{ route('logout') }}"
                     onclick="event.preventDefault();
                                   document.getElementById('logout-form').submit();">
                      {{ __('Logout') }}
                   </a>
                  @if (Session::has('adminAuth'))
                    <form id="logout-form" action="{{ route('admin-user-logout') }}" method="POST" style="display: none;">
                        {{ csrf_field()}}
                    </form>
                  @else
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field()}}
                    </form>
                  @endif
              </li>
            </ul>
          <!-- Control Sidebar Toggle Button -->
       {{--    <li>
            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
          </li> --}}
        </ul>
      </div>
    </nav>