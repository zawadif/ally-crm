<div class="container " id="main">
    <div id="mySidebar" class="mysidebar ">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>

        <div class="row ">
            <div class="col-md-2">
                <div><img src="{{ asset('img/avatar/default-avatar.png') }}" height="40" width="40"
                        class="rounded-circle" alt="User Image" id="userImage">
                </div>
            </div>
            <div class="col-md-6 text-black">
                <div class="d-flex flex-column p-0 m-0" style="margin:0px !important;">
                    <small>
                        <h6 class="m-0 p-0" id="userName"></h6>
                    </small>
                    <small>
                        <h6 class="m-0 p-0" id="userEmail"></h6>
                    </small>
                    <small>
                        <h6 class="m-0 p-0" id="userPhone">
                        </h6>
                    </small>
                </div>
            </div>
        </div>
        <div class="card mt-1">
            <div class="row">
                <div class="col-md-12 d-flex">
                    <div class="tabbable-panel">
                        <div class="tabbable-line1">
                            <ul class="nav nav-tabs">
                                <li class="active text-black">
                                    <a class="text-decoration-none text-black" href="#1a"
                                        data-toggle="tab" style="color:#51337a;">General</a>
                                </li>
                                <li>
                                    <a class="text-decoration-none text-black" href="#2a" data-toggle="tab" style="color:#51337a;">Buying
                                        History</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container pt-1">
                <div class="row">
                    <div class="col-md-12">
                        <div class="tab-content clearfix">
                            <div class="tab-pane active" id="1a">
                                <div class="row d-flex justify-content-between">
                                    <div class="col-5">
                                        <h6>Available</h6>
                                    </div>
                                    <div class="col-5">
                                        <h6 id="available" class="float-right">
                                        </h6>
                                    </div>
                                </div>
                                <div class="row d-flex justify-content-between">
                                    <div class="col-5">

                                        <h6>Gender</h6>
                                    </div>
                                    <div class="col-5 ">
                                        <h6 id="gender" class="float-right">
                                        </h6>
                                    </div>
                                </div>
                                <div class="row d-flex justify-content-start">
                                    <div class="col-md-6">
                                        <h6>Experty Level</h6>
                                    </div>
                                    <div class="col-md-6">
                                        <hr class="mt-2" style="color: #0000007A;">
                                    </div>
                                </div>
                                <div class="row d-flex justify-content-between">
                                    <div class="col-5">
                                        <h6>Single</h6>
                                    </div>
                                    <div class="col-5 text-right">
                                        <h6 id="singleExperty">
                                        </h6>
                                    </div>
                                </div>
                                <div class="row d-flex justify-content-between">
                                    <div class="col-5 ">
                                        <h6>Double</h6>
                                    </div>
                                    <div class="col-5 text-right">
                                        <h6 id="doubleExperty">
                                        </h6>
                                    </div>
                                </div>
                                <div class="row d-flex justify-content-start">
                                    <div class="col-md-5">
                                        <h6>Address</h6>
                                    </div>
                                    <div class="col-md-7">
                                        <hr class="mt-2" style="color: #0000007A;">
                                    </div>
                                </div>
                                <div class="row d-flex justify-content-between">
                                    <div class="col-10 pr-2" id="address">

                                    </div>
                                </div>
                                <div class="row d-flex justify-content-between">
                                    <div class="col-5">
                                        <h6>City</h6>
                                    </div>
                                    <div class="col-5 text-right">
                                        <h6 id="city">
                                        </h6>
                                    </div>
                                </div>
                                <div class="row d-flex justify-content-between">
                                    <div class="col-5">
                                        <h6>State</h6>
                                    </div>
                                    <div class="col-5 text-right">
                                        <h6 id="state">
                                        </h6>
                                    </div>
                                </div>
                                <div class="row d-flex justify-content-between">
                                    <div class="col-5 ">
                                        <h6>Country</h6>
                                    </div>
                                    <div class="col-5 text-right">
                                        <h6 id="country">
                                        </h6>
                                    </div>
                                </div>
                                <div class="row d-flex justify-content-start">
                                    <div class="col-md-7">
                                        <h6>Emergency Contact</h6>
                                    </div>
                                    <div class="col-md-5">
                                        <hr class="mt-2" style="color: #0000007A">
                                    </div>

                                </div>

                                <div class="row d-flex justify-content-between">
                                    <div class="col-5 ">
                                        <h6>FullName</h6>
                                    </div>
                                    <div class="col-5 text-right">
                                        <h6 id="emergencyName">
                                        </h6>
                                    </div>
                                </div>
                                <div class="row d-flex justify-content-between">
                                    <div class="col-5">
                                        <h6>Relation</h6>
                                    </div>
                                    <div class="col-5 text-right">
                                        <h6 id="relation">
                                        </h6>
                                    </div>
                                </div>
                                <div class="row d-flex justify-content-between">
                                    <div class="col-5">
                                        <h6>Contact#</h6>
                                    </div>
                                    <div class="col-5 text-right">
                                        <h6 id="emergencyContact">
                                        </h6>
                                    </div>
                                </div>
                                <div class="row  d-flex justify-content-start">
                                    <div class="col-md-3">
                                        <h6>Bio</h6>
                                    </div>
                                    <div class="col-md-9">
                                        <hr class="mt-2" style="color: #0000007A;">
                                    </div>
                                </div>
                                <div class="row d-flex justify-content-between mr-2 pr-2" style="min-height: 1rem;">
                                    <div class="col-10 pr-2" id="bio">

                                    </div>
                                </div>
                                <form action="{{ route('profilePage') }}" method="post">
                                    <div class="row d-flex justify-content-center align-items-center">
                                        @csrf
                                        <input type="hidden" name="userId" id="profileId">
                                        <button type="submit"
                                            class="font12 btn btn-block btn-default rounded greenButton m-3" style="color: white;">View Full Profile</button>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane" id="2a">
                                <div class="row d-flex justify-content-between">
                                    <div class="col-md-8">
                                        <h6>Bought Seasons</h6>
                                    </div>

                                </div><br>
                                <div class="row d-flex justify-content-between" id="boughtSeason">
                                </div>
                                <form action="{{ route('profilePage') }}" method="post">
                                    <div class="row d-flex justify-content-center align-items-center">
                                        @csrf
                                        <input type="hidden" name="userId" id="profileId1">
                                        <button type="submit"
                                            class="font12 btn btn-block btn-default rounded greenButton m-3" style="color: white;">View
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
