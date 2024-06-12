 @extends('layouts.master')
 @section('title', 'User')

 @section('style')
     <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
     <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/css/bootstrap.min.css" />
     <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker.css"
         rel="stylesheet">
     <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>

     <link href="{{ asset('css/manageAdministrator.css') }}" rel="stylesheet">
     <link href="{{ asset('css/user.css') }}" rel="stylesheet">
 @endsection
 @section('content')

     <!-- Content Wrapper. Contains page content -->
     <div class="content-wrapper">

         <!-- Main content -->
         <section class="content-header">
             <div class="container-fluid">
                 <div class="row min-vh-500">

                     <div class="col-lg-4 col-md-4">
                         <div class="card mb-4" style="min-height: 44rem">

                             <div class="card-body  border border-lightblue rounded-lg">


                                 @if ($user->status == 'ACTIVE')
                                     <a href="javascript:void(0)"
                                         class="btn btn-sm greenButton float-right text-white">{{ $user->status }}</a>
                                 @else
                                     <a href="javascript:void(0)"
                                         class="btn btn-sm redButton float-right text-white">{{ $user->status }}</a>
                                 @endif
                                 <div class="d-flex justify-content-center align-items-center py-3">
                                     <img class="rounded-circle border border-dark mt-4"
                                         src="{{ $user->avatar ? Storage::disk('s3')->url($user->avatar) : asset('img/avatar/default-avatar.png') }}"
                                         alt=" no image" height="100" width="100">
                                 </div>
                                 <h5 class="text-center font-weight-bold mr-5" id="userName">{{ $user->fullName }}</h5>
                                 <div class="d-flex justify-content-between">
                                     <h6>Member Since</h6>
                                     <span id="memberSince">{{ $user->createdAt->format('m/d/y') }}</span>
                                 </div>
                                 <div class="d-flex justify-content-between">
                                     <h6>User ID</h6>
                                     <span id="userId">{{ $user->id }}</span>
                                 </div>
                                 <div class="d-flex justify-content-between">
                                     <h6>Double Partner</h6>
                                     <span id="doublePartner" class="text-dark">
                                         <p><a href="javascript:void(0)" class="pe-auto text-dark">View all</a></p>
                                     </span>
                                 </div>
                                 <div class="d-flex justify-content-between">
                                     <h6>Mixed Double Partner</h6>
                                     <span id="mixedDoublePartner" class="text-black">
                                         <p><a href="javascript:void(0)" class="pe-auto text-dark">View all</a></p>
                                     </span>
                                 </div>
                                 <div class="d-flex justify-content-between">
                                     <h6>Password</h6>
                                     <span id="reset" class="text-black">
                                         <p><a href="javascript:void(0)" class="pe-auto text-dark">Reset Password</a></p>
                                     </span>
                                 </div>
                                 @if ($user->status == 'ACTIVE')
                                     <div class="d-flex justify-content-between mt-1">
                                         <h6>Currently Participating</h6>
                                     </div>
                                     @if ($purchases)
                                         @foreach ($purchases as $purchase)
                                             <div class="" style="color: white;">

                                                 <div class="card p-1"
                                                     style="background-color: #9170BE;border-radius: 8px;">
                                                     <div class="row ">
                                                         <div class="col-md-6 px-1">
                                                             <div class="card-block px-3">
                                                                 <h6 class="card-title ">
                                                                     {{ $purchase->getPurchaseSeason->title . ' ' . $purchase->getPurchaseSeason->year }}
                                                                 </h6>
                                                                 <p class="card-text">{{ $purchase->getLadderId->name }}
                                                                 </p>
                                                             </div>
                                                         </div>
                                                         <div class="col-md-4 ml-4 mt-2">
                                                            @php
                                                                $purchasePrice = 0;
                                                                if($purchase->purchasedType == 'CREDIT'){
                                                                    $availableCredits = $user->getUserDetail->availableCredit + $purchase->creditAmount;
                                                                }else{
                                                                    $availableCredits = $user->getUserDetail->availableCredit + $purchase->price + $purchase->creditAmount;
                                                                }
                                                            @endphp
                                                             <div class="d-flex justify-content-end align-items-center">
                                                                 <div class="p-1"> {{ $availableCredits }}</div>
                                                                 <div class="currentllyParticipating"><i
                                                                         class="fas fa-edit"></i>
                                                                     <input type="hidden" name=""
                                                                         value="{{ $purchase->id }}">
                                                                     <input type="hidden" name=""
                                                                         value="{{ $availableCredits }}"
                                                                         class="purchaseAmount">
                                                                 </div>
                                                             </div>
                                                         </div>

                                                     </div>
                                                 </div>
                                             </div>
                                         @endforeach

                                     @else
                                             <span style="color:blue; "> Not Participating till now </span>
                                     @endif
                                 @endif
                                 <div class="row mt-2">
                                     <div class="col-md-7 col-6 ">
                                         <button class="btn btn-block text-center rounded  pb-1 text-white greenButton"
                                             type="button" style="height: 42px;"
                                             onclick="openEditProfileModal('{{ $user->id }}')"><span>Edit
                                                 Profile</span></button>
                                     </div>

                                     <div class="col-md-4 col-5 d-flex mr-1">
                                         <a class="btn btn-default btn-sm border-dark bg-white rounded mr-1 pb-0" id="chatBtn" type="button" data-id="{{$user->id }}" data-name="{{ $user->fullName }}"
                                            data-toggle="modal" data-target="#chatModal" ><i class="fas fa-comment" style="
                                            padding-top: 9px;
                                        "></i></a>
                                         <div class="dropdown dropdown-block-aside dropleft border border-dark p-2 bg-white rounded"
                                             id="checkplaylis" style="float: right !important">
                                             <i class="fas fa-ellipsis-h" id="dropdownMenuButton" data-toggle="dropdown"
                                                 aria-haspopup="true" aria-expanded="false">
                                             </i>
                                             <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                 <a class="dropdown-item btn-block-aside" href="javascript:void(0)"
                                                     type="button" onclick="openBlockModal('{{ $user->id }}')"><i
                                                         class="fa fa-ban" aria-hidden="true"></i> Block
                                                     User</a>
                                                 <a class="dropdown-item btn-block-aside text-danger"
                                                     href="javascript:void(0)" type="button"
                                                     onclick="deleteUser('{{ $user->id }}')"><i
                                                         class="fa fa-trash"></i> Delete User</a>

                                             </div>
                                         </div>

                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>

                     <div class="col-lg-8
                                     col-md-8">
                         <div class="card mb-4 " style="min-height: 44rem; max-height: 44rem;">
                             <div class="card-body border border-lightblue rounded-lg overflow-auto scrollingWrapper">
                                 <div class="row">
                                     <div class="col-md-12">
                                         <div class="tabbable-panel">
                                             <div class="tabbable-line">
                                                 <ul class="nav nav-tabs">
                                                     <li class="active">
                                                         <a href="#tab_default_1" data-toggle="tab" id="generalTab"
                                                             class="text-decoration-none text-black"
                                                             data-url="users/general_data" data-id="{{ $user->id }}">
                                                             General </a>
                                                     </li>
                                                     <li>
                                                         <a href="#tab_default_2" data-toggle="tab"
                                                             class="text-decoration-none text-black" id="matchTab"
                                                             data-url="/users/matches_data/{{ $user->id }}">
                                                             Matches </a>
                                                     </li>
                                                     <li>
                                                         <a href="#tab_default_3" data-toggle="tab"
                                                             class="text-decoration-none text-black" id="proposalTab"
                                                             data-url="/users/proposals_data/{{ $user->id }}">
                                                             Proposal </a>
                                                     </li>
                                                     <li>
                                                         <a href="#tab_default_4" data-toggle="tab"
                                                             class="text-decoration-none text-black" id="challengeTab"
                                                             data-url="/users/challenges_data/{{ $user->id }}">
                                                             Challenges </a>
                                                     </li>
                                                     <li>
                                                         <a href="#tab_default_5" data-toggle="tab"
                                                             class="text-decoration-none text-black" id="rankingTab"
                                                             data-url="/users/rankings_data/{{ $user->id }}">
                                                             Rankings </a>
                                                     </li>
                                                     <li>
                                                         <a href="#tab_default_6" data-toggle="tab"
                                                             class="text-decoration-none text-black" id="historyTab"
                                                             data-url="/users/buying_history_data/{{ $user->id }}">
                                                             Buying History </a>
                                                     </li>
                                                 </ul>

                                                 <hr class="mt-0" style="color: #0000007A;width:100%">
                                                 <div class="tab-content">
                                                     {{-- general tab  --}}
                                                     <div class="tab-pane active" id="tab_default_1">
                                                         <div class="container">
                                                             <div class="row">
                                                                 <div class="col-md-12">
                                                                     <div class="row d-flex justify-content-between">
                                                                         <div class="col-lg-3 col-md-6">
                                                                             <h6 class="magentaColor">Available</h6>
                                                                         </div>
                                                                         <div class="col-lg-9 col-md-6">
                                                                             <p id="available"
                                                                                 class="magentaColor float-left mr-1 ">
                                                                                 {{ $user->getUserDetail->availableCredits }}

                                                                             </p>
                                                                             <p id="element"
                                                                                 class="pr-1 ml-1 show-modal magentaColor">
                                                                                 Edit
                                                                             </p>

                                                                         </div>
                                                                     </div>
                                                                     <div class="row d-flex justify-content-between">
                                                                         <div class="col-lg-3 col-md-6">
                                                                             <h6>Phone #</h6>
                                                                         </div>
                                                                         <div class="col-lg-9 col-md-6 ">
                                                                             <p id="userPhone" class="float-left">
                                                                                 {{ $user->getUserDetail->phoneNumber }}
                                                                             </p>
                                                                         </div>
                                                                     </div>
                                                                     <div class="row d-flex justify-content-between">
                                                                         <div class="col-lg-3 col-md-6">
                                                                             <h6>Email</h6>
                                                                         </div>
                                                                         <div class="col-lg-9 col-md-6 ">
                                                                             <p id="userEmail" class="float-left">
                                                                                 {{ $user->email }}
                                                                             </p>
                                                                         </div>
                                                                     </div>
                                                                     <div class="row d-flex justify-content-between">
                                                                         <div class="col-md-3 col-6">
                                                                             <h6>Gender</h6>
                                                                         </div>
                                                                         <div class="col-md-9 col-6">
                                                                             <p id="userGender" class="float-left">
                                                                                 {{ $user->getUserDetail->gender }}
                                                                             </p>
                                                                         </div>
                                                                     </div>
                                                                     <div class="row d-flex justify-content-between">
                                                                         <div class="col-lg-3 col-md-3 col-6">
                                                                             <h6>Date of Birth</h6>
                                                                         </div>
                                                                         <div class="col-lg-9 col-md-9 col-6">
                                                                             <p id="userDob" class="float-left">
                                                                                 {{ $user->getUserDetail ? Carbon\Carbon::parse($user->getUserDetail->dob)->format('d/m/Y') : 'N/A' }}
                                                                             </p>
                                                                         </div>
                                                                     </div>
                                                                     <div class="row d-flex justify-content-start">
                                                                         <div class="col-md-3 col-6">
                                                                             <h6 class="magentaColor">Experty Level</h6>
                                                                         </div>
                                                                         <div class="col-md-9 col-6">
                                                                             <hr class="mt-2 magentaColorHr">
                                                                         </div>
                                                                     </div>
                                                                     <div class="row d-flex justify-content-between">
                                                                         <div class="col-md-3 col-6">
                                                                             <h6>Single</h6>
                                                                         </div>
                                                                         <div class="col-md-9 col-6 text-left">
                                                                             <p id="singleExperty">@php
                                                                                 $single = $user->experties->where('type', 'SINGLE')->first();
                                                                                 if ($single) {
                                                                                     echo $single->level;
                                                                                 } else {
                                                                                     echo 'N/A';
                                                                                 }
                                                                             @endphp

                                                                             </p>
                                                                         </div>
                                                                     </div>
                                                                     <div class="row d-flex justify-content-between">
                                                                         <div class="col-md-3 col-5 ">
                                                                             <h6>Double</h6>
                                                                         </div>
                                                                         <div class="col-md-9 col-5 text-left">
                                                                             <p id="doubleExperty">@php
                                                                                 $double = $user->experties->where('type', 'DOUBLE')->first();
                                                                                 if ($double) {
                                                                                     echo $double->level;
                                                                                 } else {
                                                                                     echo 'N/A';
                                                                                 }
                                                                             @endphp
                                                                             </p>
                                                                         </div>
                                                                     </div>
                                                                     <div class="row d-flex justify-content-start">
                                                                         <div class="col-md-2">
                                                                             <h6 class="magentaColor">Address</h6>
                                                                         </div>
                                                                         <div class="col-md-10">
                                                                             <hr class="mt-2 magentaColorHr">
                                                                         </div>
                                                                     </div>
                                                                     <div class="row d-flex justify-content-between">
                                                                         <div class="col-md-3 col-6 pr-2">
                                                                             <h6>Address</h6>
                                                                         </div>
                                                                         <div class="col-md-9 col-6 pr-2">
                                                                             <p id="userAddress">
                                                                                 {{ $user->getUserDetail->completeAddress }}
                                                                             </p>
                                                                         </div>
                                                                     </div>
                                                                     <div class="row d-flex justify-content-between">
                                                                         <div class="col-md-3 col-6 pr-2">
                                                                             <h6>Postal Code</h6>
                                                                         </div>
                                                                         <div class="col-md-9 col-6 pr-2">
                                                                             <p id="postalCode">
                                                                                 {{ $user->getUserDetail->postalCode }}
                                                                             </p>
                                                                         </div>
                                                                     </div>
                                                                     <div class="row d-flex justify-content-between">
                                                                         <div class="col-md-3 col-5">
                                                                             <h6>City</h6>
                                                                         </div>
                                                                         <div class="col-md-9 col-5 ">
                                                                             <p id="userCity">
                                                                                 {{ $user->getUserDetail->city }}
                                                                             </p>
                                                                         </div>
                                                                     </div>
                                                                     <div class="row d-flex justify-content-between">
                                                                         <div class="col-md-3 col-5">
                                                                             <h6>State</h6>
                                                                         </div>
                                                                         <div class="col-md-9 col-5 ">
                                                                             <p id="userState">
                                                                                 {{ $user->getUserDetail->state }}
                                                                             </p>
                                                                         </div>
                                                                     </div>
                                                                     <div class="row d-flex justify-content-between">
                                                                         <div class="col-md-3 col-5">
                                                                             <h6>Region</h6>
                                                                         </div>
                                                                         <div class="col-md-9 col-5 ">
                                                                             <p id="region">
                                                                                 {{ $user->getUserDetail->region->name }}
                                                                             </p>
                                                                         </div>
                                                                     </div>
                                                                     <div class="row d-flex justify-content-between">
                                                                         <div class="col-md-3 col-5 ">
                                                                             <h6>Country</h6>
                                                                         </div>
                                                                         <div class="col-md-9 col-5 ">
                                                                             <p id="userCountry">
                                                                                 {{ $user->getUserDetail->country }}
                                                                             </p>
                                                                         </div>
                                                                     </div>
                                                                     <div class="row d-flex justify-content-start">
                                                                         <div class="col-md-3">
                                                                             <h6 class="magentaColor">Emergency Contact
                                                                             </h6>
                                                                         </div>
                                                                         <div class="col-md-9">
                                                                             <hr class="mt-2 magentaColorHr">
                                                                         </div>

                                                                     </div>

                                                                     <div class="row d-flex justify-content-between">
                                                                         <div class="col-md-3 col-5 ">
                                                                             <h6>FullName</h6>
                                                                         </div>
                                                                         <div class="col-md-9 col-5">
                                                                             <p id="emergencyName">
                                                                                 {{ $user->getUserDetail->emergencyContactName }}
                                                                             </p>
                                                                         </div>
                                                                     </div>
                                                                     <div class="row d-flex justify-content-between">
                                                                         <div class="col-md-3 col-5">
                                                                             <h6>Relation</h6>
                                                                         </div>
                                                                         <div class="col-md-9 col-5">
                                                                             <p id="relation">
                                                                                 {{ $user->getUserDetail->emergencyContactRelation }}
                                                                             </p>
                                                                         </div>
                                                                     </div>
                                                                     <div class="row d-flex justify-content-between">
                                                                         <div class="col-md-3 col-5">
                                                                             <h6>Contact#</h6>
                                                                         </div>
                                                                         <div class="col-md-9 col-5">
                                                                             <p id="emergencyContact">
                                                                                 {{ $user->getUserDetail->emergencyContactNumber }}
                                                                             </p>
                                                                         </div>
                                                                     </div>
                                                                     <div class="row  d-flex justify-content-start">
                                                                         <div class="col-md-2">
                                                                             <h6 class="magentaColor">Bio</h6>
                                                                         </div>
                                                                         <div class="col-md-10">
                                                                             <hr class="mt-2 magentaColorHr">
                                                                         </div>
                                                                     </div>

                                                                     <div class="row d-flex justify-content-between">
                                                                         <div class="col-md-9 col-5">
                                                                             <p>
                                                                                 {{ $user->getUserDetail->bio }}
                                                                             </p>
                                                                         </div>
                                                                     </div>
                                                                 </div>
                                                             </div>
                                                         </div>
                                                     </div>

                                                     {{-- matches tab  --}}
                                                     <div class="tab-pane" id="tab_default_2">
                                                         @include('users.tables.matches')
                                                     </div>

                                                     {{-- proposals tab  --}}
                                                     <div class="tab-pane" id="tab_default_3">
                                                         @include('users.tables.proposals')
                                                     </div>

                                                     {{-- challenges tab  --}}
                                                     <div class="tab-pane" id="tab_default_4">
                                                         @include('users.tables.challenges')
                                                     </div>

                                                     {{-- rankings tab  --}}
                                                     <div class="tab-pane" id="tab_default_5">
                                                         @include('users.tables.rankings')
                                                     </div>

                                                     {{-- buying history tab  --}}
                                                     <div class="tab-pane" id="tab_default_6">
                                                         @include('users.tables.buyingHistory')
                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>

                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>

    @include('users.chat.chat')
         </section>

         @include('users.modal.editProfile')

         @include('users.modal.blockUserModal')

         @include('users.modal.deleteUserModal')
         @include('users.modal.editAvailableCredit')
         @include('users.modal.filter')
         @include('users.modal.withdrawProposal')
         @include('users.modal.withdrawChallenge')
         @include('users.modal.updatescore')
         @include('users.modal.partnerModal')
         @include('users.modal.changePartnerModal')
         @include('users.modal.cancelSubscriptionModal')
         @include('users.modal.resetPasswordModal')

         <!-- /.Main content -->
        </div>
 @endsection

 @section('script')
     <script src="{{ asset('js/activeUser.js') }}"></script>
     <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
 @endsection
