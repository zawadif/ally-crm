@extends('layouts.registerMaster')

@section('title','Sign Up')

@section('style')
@endsection


@section('content')
<div class="col-lg-8">
    <div class="d-flex justify-content-center" style="height: 100%">
        <div class="half mr-lg-5" style="width: 100%;padding-top:50px;padding-bottom:20px" data-aos="fade-right">
            <div class="row">
                <div class="col-lg-12">
                    <h5 class="text-white mb-0 font-weight-bold">Complete Your Profile</h5>
                    <small class="text-white" style="letter-spacing: 1px;font-size: 13px;">Welcome to Tennis Fights Admin Panel.Kindly complete the profile before further proceeding.</small>
                </div>
                <div class="col-lg-12">
                    @if(session()->has('message'))
                    <div class="alert alert-danger">
                     {{ session()->get('message') }}
                  </div>
                   @endif
                    <form class="mt-3" action="{{route('register.NewUser')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row mt-2 mb-2">
                            <div class="col-lg-12 d-flex justify-content-start">
                                <img src="{{asset('img/default-avatar.png')}}" id="image-show"  alt="Product Image" class="img-size-50" style="height: 65px !important;width: 65px !important;border-radius: 50px;">
                                <label class="mt-4">
                                    <a class="pl-4"> Upload Photo</a>
                                    <input type="file" id="avatar" name="avatar" class="form-control" value="{{ old('avatar') }}" style="display: none;">
                                </label>
                            </div>
                            @if ($errors->has('avatar'))
                                <span class="invalid-feedback ml-3" style="display: block;" role="alert">
                                    <strong>{{ $errors->first('avatar') }}</strong>
                                </span>
                            @endif
                        </div>
                        <input id="userId" name="userId" type="hidden" value="{{ $user->id }}">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group mb-1">
                                    <label class="text-white " for="exampleInputEmail1"><small>Full Name</small></label>
                                    <div class="input-group ">
                                        <div class="input-group-prepend inputFieldHeight borderRightNone">
                                          <span class="input-group-text bg-white"><i class="bi bi-person"></i></span>
                                        </div>
                                        <input type="text" id="fullName" name="fullName" value="{{ old('fullName') }}" class="form-control inputFieldHeight borderLeftNone font12" placeholder="Enter Full Name">
                                      </div>
                                </div>
                                @if ($errors->has('fullName'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('fullName') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-1">
                                    <label class="text-white " for="exampleInputEmail1"><small>Email</small></label>
                                    <div class="input-group ">
                                        <div class="input-group-prepend inputFieldHeight borderRightNone">
                                          <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
                                        </div>
                                        <input type="email" id="email" name="email" class="form-control inputFieldHeight borderLeftNone font12"  placeholder="Enter Email" value="{{ $user->email }}" readonly>
                                      </div>
                                </div>
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group mb-1">
                                    <label class="text-white " for="exampleInputEmail1"><small>Password</small></label>
                                    <div class="input-group " id="show_hide_password">
                                        <div class="input-group-prepend inputFieldHeight borderRightNone">
                                            <span class="input-group-text bg-white"><i class="fa-solid fa-lock"></i></span>
                                        </div>
                                        <input type="password" id="password" name="password" class="form-control inputFieldHeight borderLeftNone borderRightNone font12" placeholder="Password">
                                        <div class="input-group-append inputFieldHeight borderLeftNone">
                                            <span class="input-group-text bg-white"><a href="javascript:void(0)"><i id="passwordEye" class="bi bi-eye-slash"></i></a></span>
                                        </div>
                                    </div>
                                </div>
                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-1">
                                    <label class="text-white " for="exampleInputEmail1"><small>Confirm Password</small></label>
                                    <div class="input-group " id="show_hide_confirm_password">
                                        <div class="input-group-prepend inputFieldHeight borderRightNone">
                                            <span class="input-group-text bg-white"><i class="fa-solid fa-lock"></i></span>
                                        </div>
                                        <input type="password" id="confirmPassword" name="confirmPassword" class="form-control inputFieldHeight borderLeftNone borderRightNone font12" placeholder="Enter Confirm Password">
                                        <div class="input-group-append inputFieldHeight borderLeftNone">
                                            <span class="input-group-text bg-white"><a href="javascript:void(0)"><i id="confirmPasswordEye" class="bi bi-eye-slash"></i></a></span>
                                        </div>
                                    </div>
                                </div>
                                @if ($errors->has('confirmPassword'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('confirmPassword') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-lg-6">
                                <div class="form-group mb-1">
                                    <label class="text-white" for="exampleInputPassword1"><small>Date of birth</small></label>
                                    <div class="input-group date" id="dateOfBirthPicker" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input"  value="{{ old('dateOfBirth') }}" style="font-size: 12.5px;border-right: none;height:31px; " data-target="#dateOfBirthPicker" id="dateOfBirth" name="dateOfBirth" placeholder="Date of Birth">
                                        <div class="input-group-append" data-target="#dateOfBirthPicker" data-toggle="datetimepicker">
                                            <div class="input-group-text red-text" style="background-color: white;border-left:none;height:31px; "><i class="bi bi-calendar3"></i></div>
                                        </div>
                                    </div>
                                </div>
                                @if ($errors->has('dateOfBirth'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('dateOfBirth') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group mb-1">
                                            <label class="text-white" for="exampleInputPassword1"><small>Gender</small></label>
                                            <div class="input-group">
                                                <p class="px-5 py-1 rounded" style="background-color: white;width:100%">
                                                    <input type="radio" id="test1" value="Male" name="gender"  checked>
                                                    <label class="m-0" for="test1">Male</label>
                                                  </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group mt-2 mb-1">
                                            <label class="text-white" for="exampleInputPassword1"><small></small></label>
                                            <div class="input-group">
                                                <p class="px-5 py-1 rounded" style="background-color: white;width:100%">
                                                    <input type="radio" id="test2" value="Female" name="gender">
                                                    <label class="m-0" for="test2">Female</label>
                                                  </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if ($errors->has('gender'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('gender') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group  mb-1">
                                    <label class="text-white " for="exampleInputEmail1"><small>Address</small></label>
                                    <div class="input-group ">
                                        <div class="input-group-prepend inputFieldHeight borderRightNone">
                                          <span class="input-group-text bg-white"><i class="bi bi-geo-alt"></i></span>
                                        </div>
                                        <input type="text" id="address" name="address" value="{{ old('address') }}" class="form-control inputFieldHeight borderLeftNone font12" placeholder="Enter Address">
                                    </div>
                                </div>
                                @if ($errors->has('address'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('address') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group  mb-1">
                                    <label class="text-white " for="exampleInputEmail1"><small>Region</small></label>
                                    <div class="input-group ">
                                        <select class="form-select form-control inputFieldHeight borderLeftNone font12" value="{{ old('region') }}" id="region" name="region">
                                            @foreach ($regions as $region)
                                                <option value="{{ $region->id}}">{{ $region->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @if ($errors->has('region'))
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $errors->first('region') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group  mb-1">
                                    <label class="text-white " for="exampleInputEmail1"><small>State</small></label>
                                    <div class="input-group ">
                                        <input type="text" id="state" name="state" class="form-control inputFieldHeight borderLeftNone font12" value="{{ old('state') }}" placeholder="Enter State">
                                    </div>
                                </div>
                                @if ($errors->has('state'))
                                <span class="invalid-feedback" style="display: block;" role="alert">
                                    <strong>{{ $errors->first('state') }}</strong>
                                </span>
                            @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group  mb-1">
                                    <label class="text-white " for="exampleInputEmail1"><small>City</small></label>
                                    <div class="input-group ">
                                        <div class="input-group-prepend inputFieldHeight borderRightNone">
                                          <span class="input-group-text bg-white"><i class="fa-solid fa-city"></i></span>
                                        </div>
                                        <input type="text" id="city" name="city" class="form-control inputFieldHeight borderLeftNone font12" value="{{ old('city') }}" placeholder="Enter City">
                                    </div>
                                </div>
                                @if ($errors->has('city'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('city') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group  mb-1">
                                    <label class="text-white " for="exampleInputEmail1"><small>Country</small></label>
                                    <div class="input-group ">
                                        <select class="form-select form-control inputFieldHeight borderLeftNone font12" id="country" name="country"  value="{{ old('country') }}">
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->name}}">{{ $country->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @if ($errors->has('country'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('country') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group  mb-1">
                                    <label class="text-white " for="exampleInputEmail1"><small>Postal Code</small></label>
                                    <div class="input-group ">
                                        <div class="input-group-prepend inputFieldHeight borderRightNone">
                                          <span class="input-group-text bg-white"><i class="fa-solid fa-code"></i></span>
                                        </div>
                                        <input type="text" id="postalCode" name="postalCode" class="form-control inputFieldHeight borderLeftNone font12" value="{{ old('postalCode') }}" placeholder="Enter Postal Code">
                                    </div>
                                </div>
                                @if ($errors->has('postalCode'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('postalCode') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <input id="inviteToken" name="inviteToken" type="hidden" value="{{$user->inviteToken}}">
                            <div class="col-lg-6">
                                <div class="form-group  mb-1">
                                    <label class="text-white " for="exampleInputEmail1"><small>Phone Number</small></label>
                                    <div class="input-group ">
                                        <input id="phoneNumber" name="phoneNumber"  type="tel" class="form-control inputFieldHeight borderLeftNone font12" value="{{ old('phoneNumber') }}">
                                        <span id="valid-msg" class="hide text-success">Valid</span>
                                        <span id="error-msg" class="hide text-danger">Invalid number</span>
                                    </div>
                                </div>
                                @if ($errors->has('phoneNumber'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('phoneNumber') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <input id="phoneNumberValue" name="phoneNumberValue" type="hidden" >
                        <div class="row">
                            <div class="col-lg-12">
                                <hr style="border:1px solid white">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <h5 class="text-white mb-0 font-weight-bold">Emergency Contact Details</h5>
                                <small class="text-white" style="letter-spacing: 1px;font-size: 13px;">You can add your family or friend name and contact number. So that in case of emergency your partner or your opponent can contact your friend or family.</small>
                            </div>
                        </div>
                        <input id="phoneNumberCode" name="phoneNumberCode" type="hidden" >
                        <div class="row mt-2">
                            <div class="col-lg-12">
                                <div class="form-group  mb-1">
                                    <label class="text-white " for="exampleInputEmail1"><small>Full Name</small></label>
                                    <div class="input-group ">
                                        <div class="input-group-prepend inputFieldHeight borderRightNone">
                                          <span class="input-group-text bg-white"><i class="bi bi-person"></i></span>
                                        </div>
                                        <input type="text" id="emergencyFullName" name="emergencyFullName" value="{{ old('emergencyFullName') }}" class="form-control inputFieldHeight borderLeftNone font12" placeholder="Enter Full Name">
                                    </div>
                                </div>
                                @if ($errors->has('emergencyFullName'))
                                    <span class="invalid-feedback ml-3" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('emergencyFullName') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-6">
                                <div class="form-group  mb-1">
                                    <label class="text-white " for="exampleInputEmail1"><small>ReleationShip</small></label>
                                    <div class="input-group ">
                                        <select class="form-select form-control inputFieldHeight borderLeftNone font12" id="relationShip" value="{{ old('relationShip') }}" name="relationShip">
                                            @foreach ($relations as $relation)
                                                <option value="{{ $relation}}">{{ $relation}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @if ($errors->has('relationShip'))
                                    <span class="invalid-feedback ml-3" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('relationShip') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <input id="emergencyPhoneNumberCode" name="emergencyPhoneNumberCode" type="hidden" >
                            <div class="col-lg-6">
                                <div class="form-group  mb-1">
                                    <label class="text-white " for="exampleInputEmail1"><small>Phone Number</small></label>
                                    <div class="input-group ">
                                        <input id="emergencyPhoneNumber" name="emergencyPhoneNumber" type="tel" value="{{ old('emergencyPhoneNumber') }}" class="form-control inputFieldHeight borderLeftNone font12" >
                                            <span id="valid-msg1" class="hide text-success">Valid</span>
                                            <span id="error-msg1" class="hide text-danger">Invalid number</span>
                                    </div>
                                </div>
                                <input id="emergencyPhoneNumberValue" name="emergencyPhoneNumberValue" type="hidden" >
                                @if ($errors->has('emergencyPhoneNumber'))
                                    <span class="invalid-feedback ml-3" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('emergencyPhoneNumber') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <input type="submit" class="font12 btn btn-block btn-success btn-lg rounded greenButton" value="Continue">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('script')
@endsection


