 @php
        use App\Helpers\Helper;
        $customHelper =  new Helper;
    @endphp
<nav  class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>

    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
            <div class="user-panel  pb-3  d-flex">
              @if (auth()->user()->avatar)
                <img src="{{$customHelper->getUserImage(auth()->id())}}" alt="User Image" class="img-size-50" style="width: 30px; height:30px;border-radius: 50%;">
              @else
                <img src="{{asset('img/avatar/default-avatar.png')}}" alt="User Image" class="img-size-50" style="width: 30px; height:30px;border-radius: 50%;">
              @endif
              </div>
        </a>
        <div class="dropdown-menu dropdown-menu-md dropdown-menu-right">
          <div class="dropdown-item disabled">
            <div class="media">
              @if (auth()->user()->avatar)
                <img src="{{$customHelper->getUserImage(auth()->id())}}" alt="User Image" class="img-size-50" style="width: 50px; height:50px;border-radius: 50%;">
              @else
                <img src="{{asset('img/avatar/default-avatar.png')}}" alt="User Image" class="img-size-50" style="width: 50px; height:50px;border-radius: 50%;">
              @endif
              <div class="media-body">
                <p class="dropdown-item-title">
                    {{auth()->user()->fullName? auth()->user()->fullName : "Administrator"}}
                </p>
                <small class="text-sm">{{auth()->user()->email? auth()->user()->email : "Email"}}</small>

              </div>
            </div></div>
          <div class="dropdown-divider"></div>
          <div class="dropdown-item" >
              <form action="{{route('logout')}}" method="post" enctype="multipart/form-data">
                <input type="hidden" name="type" value="webportal">
                  @csrf
                  <button class="btn btn-block btn-sucess greenButton btn-sm rounded text-white" >Logout</button>
              </form>
           </div>
        </div>
      </li>
       </ul>
  </nav>
  <!-- /.navbar -->


