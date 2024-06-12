 @php
        use App\Helpers\Helper;
        $customHelper =  new Helper;
    @endphp
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('webportal')}}" class="brand-link" style="border: none;padding-bottom: 0px;">
        <h5 class="p-2 text-white">Tennis Fights</h5>
    </a>
    <hr style="color: white !important; border-top: 1px solid;" class="ml-2 mr-2">

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user (optional) -->
        <ul class="products-list product-list-in-card pl-2 pr-2">
            <li class="item pt-0 pb-0" style="background-color: var(--baseColor)">
                <div class="product-img">
                    @if (auth()->user()->avatar)
                        <img src="{{$customHelper->getUserImage(auth()->id())}}" alt="User Image" class="img-size-50" style="height: 41px !important;width: 41px !important;border-radius: 50%;">
                    @else
                        <img src="{{asset('img/avatar/default-avatar.png')}}" alt="User Image" class="img-size-50" style="height: 41px !important;width: 41px !important;border-radius: 50%;">
                    @endif
                </div>
                <div class="product-info pt-2"><a href="{{ route('webportal.profile')}}" >
                    <small href="javascript:void(0)" class="product-title userNameText">{{auth()->user()->fullName? auth()->user()->fullName : "Administrator"}}
                    </a>
                </small>
                </div>
            </li>
        </ul>
    </div>
    <hr style="color: white !important; border-top: 1px solid;" class="ml-2 mr-2">
    <div class="sidebar px-0">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="{{ route('webportal.myMatches') }}" class="nav-link">
                        <p>
                            My Matches
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('webportal.mySubscription') }}" class="nav-link">
                        <p>
                            My Subscription
                        </p>
                    </a>
                </li>
                @if (auth()->user()->role->count() == 2)
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link">
                            <p>
                                Tennis Fights Dashboard
                            </p>
                        </a>
                    </li>
                @else

                @endif
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
