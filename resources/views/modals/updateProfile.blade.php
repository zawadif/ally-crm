  <!-- Modal -->
  <div class="modal fade" id="updateProfileModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modalLargeMiddle" role="document">
      <div class="modal-content">
        <div class="modal-body m-3">
            <h6><b>Edit Profile</b></h6>
            <hr class="solid ">
            <form id="editUserProfileForm">
            <div class="row mt-2">
                <div class="col-lg-12 d-flex justify-content-start">
                    @if (auth()->user()->avatar)
                                        <img src="{{Storage::disk('s3')->url(auth()->user()->avatar)}}" alt="User Image" style="height: 100px !important;width: 100px !important;border-radius: 50%;">
                                    @else
                                        <img src="{{asset('img/avatar/default-avatar.png')}}" alt="User Image" style="height: 100px !important;width: 100px !important;border-radius: 50%;">
                                    @endif
                    <label class="mt-4">
                        <a class="pl-4"> Upload Photo</a>
                        <input type="file" id="avatar" name="avatar" class="form-control"  accept="image/png, image/jpeg, image/jpg"  value="{{ old('avatar') }}" style="display: none;">
                        <br><span style="color:red;    padding-left: 22px;"><small>.jpeg, .jpg and .png </small></span>
                    </label>
                    <a class="deleteUserAvatar pt-4 pl-4"> Delete Photo</a>
                </div>
                <div class="col-lg-12">
                    <div class="invalid-feedback" id="avatarError"></div>
                </div>
            </div>

            <input id="userId" name="userId" type="hidden" >
            <div class="row mt-3">
                <div class="col-lg-6">
                    <label for="exampleInputEmail1" class="m-0">First Name</label>
                    <input class="form-control" id="firstName" name="firstName" type="text" placeholder="first name">
                    <div class="invalid-feedback" id="firstNameError"></div>
                </div>
                <div class="col-lg-6">
                    <label for="exampleInputEmail1" class="m-0">Last Name</label>
                    <input class="form-control " id="lastName" name="lastName" type="text" placeholder="last name">
                    <div class="invalid-feedback" id="lastNameError"></div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-lg-6">
                    <label for="exampleInputEmail1" class="m-0">Email</label>
                    <input class="form-control " id="email" name="email" type="email" placeholder="email@email.com" readonly>
                    <div class="invalid-feedback" id="emailError"></div>
                </div>
                <div class="col-lg-6">
                    <label for="exampleInputEmail1" class="m-0">Gender</label>
                    <select class="form-select form-control" id="gender" name="gender">
                        <option value="MALE">Male</option>
                        <option value="FEMALE">Female</option>
                    </select>
                    <div class="invalid-feedback" id="genderError"></div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-lg-12">
                    <label for="exampleInputEmail1" class="m-0">Address</label>
                    <input class="form-control " id="address" name="address" type="text" placeholder="address">
                    <div class="invalid-feedback" id="addressError"></div>
                </div>
            </div>
            <input id="imageChange" name="imageChange" type="hidden" >
            <div class="row mt-2">
                <div class="col-lg-6">
                    <label for="exampleInputEmail1" class="m-0">Country</label>
                    <select class="form-select form-control" id="country" name="country">
                        @foreach ($countrys as $country)
                            <option value="{{ $country->name }}">{{ $country->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback" id="countryError"></div>
                </div>
                <div class="col-lg-6">
                    <label for="exampleInputEmail1" class="m-0">State</label>
                    <input class="form-control " id="state" name="state"  type="text" placeholder="State">
                    <div class="invalid-feedback" id="stateError"></div>
                </div>
            </div>
            <input id="contactNumberValue" name="contactNumberValue" type="hidden" >
            <div class="row mt-2">
                <div class="col-lg-6">
                    <label for="exampleInputEmail1" class="m-0">Postal Code</label>
                    <input class="form-control " id="postalCode" name="postalCode" type="text" placeholder="postal code">
                    <div class="invalid-feedback" id="postalCodeError"></div>
                </div>
                <input id="contactNumberCode" name="contactNumberCode" type="hidden" >
                <div class="col-lg-6">
                    <label for="exampleInputEmail1" class="m-0">Contact Number</label>
                    <input id="contactnumber" name="contactNumber" type="text" value="{{ old('contactNumber') }}" class="form-control borderLeftNone font12" disabled>
                    <span id="valid-msg" class="hide text-success">Valid</span>
                    <span id="error-msg" class="hide text-danger">Invalid number</span>
                    <div class="invalid-feedback" id="contactNumberError"></div>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-6">
                    <a  class="font12 btn btn-block btn-default whiteButton btn-default rounded whiteButton" data-dismiss="modal">Canel</a>
                </div>
                <div class="col-6">
                    <input type="submit" class="font12 btn btn-block btn-success btn-default rounded greenButton" value="Update Profile">
                </div>
            </div>
            </form>
        </div>
      </div>
    </div>
  </div>
