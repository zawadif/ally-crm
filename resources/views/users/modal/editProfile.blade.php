<!-- Modal -->
<div class="modal fade profileModal" id="exampleModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header border-0 m-0 pb-0">
                <h6 class="modal-title" id="exampleModalLongTitle">Edit Profile </h6>
            </div>
            <div class="row m-0 p-0">
                <div class="col-md-12 col-sm-12 col-12">
                    <hr style="border: 1px solid #707070;">
                </div>
            </div>
            <div class="modal-body mt-0 pt-0">
                <div class="container mt-0 p-0">
                    <form action="" class="profileForm" id="userForm" >
                        <input type="hidden" name="userId" id="userId">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label for="formGroupExampleInput" class="control-label">Full Name</label>
                                    <input type="text" class="form-control form-control-sm" id="fullName"
                                        name="fullName" />
                                    <span class="text-danger" id="fullNameError"></span>

                                </div>
                                <div class="form-group mb-0">
                                    <label for="formGroupExampleInput">Email</label>
                                    <input type="text" class="form-control form-control-sm" id="email"
                                        name="email"/>
                                    <span class="text-danger" id="emailError"></span>

                                </div>
                            </div>

                            <div class="col-md-6">
{{--                                <div class="form-group mb-0 ">--}}
{{--                                    <label for="">Phone Number</label>--}}
{{--                                    <input type="text" class="form-control form-control-sm" id="phoneNumber"--}}
{{--                                        name="phoneNumber"/>--}}
{{--                                    <span class="text-danger" id="phoneNumberError"></span>--}}
{{--                                </div>--}}
                                <div class="form-group mb-0">
                                    {{--                                    <div class="form-group">--}}
                                    <label for="role">Role</label>
                                    <select class="form-control" name="role" id="role"></select>

                                </div>

                            </div>
                        </div>



                        </div>
                        <div id="validationErrors"></div>

                        <div class="row mt-3">
                            <div class="col-md-4 text-center mx-auto m-2">
                                <button class="btn btn-light btn-block text-center rounded" type="button"
                                    data-dismiss="modal" id="cancelBtn"><span>Cancel
                                    </span></button>
                            </div>
                            <div class="col-md-4 text-center mx-auto m-2">
                                <button class="btn btn-block text-center rounded greenButton"
                                    type="submit" id="saveBtn"><span>Update
                                    </span></button>
                            </div>
                        </div>
                    </form>
                    <div id="loader" class="d-none text-center">
                        <i class="fas fa-spinner fa-5x fa-spin"></i> <!-- Replace this with your loader icon -->
                        <p>Loading...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/datepicker/1.0.10/datepicker.min.js"--}}
{{--    integrity="sha512-RCgrAvvoLpP7KVgTkTctrUdv7C6t7Un3p1iaoPr1++3pybCyCsCZZN7QEHMZTcJTmcJ7jzexTO+eFpHk4OCFAg=="--}}
{{--    crossorigin="anonymous" referrerpolicy="no-referrer"></script>--}}
