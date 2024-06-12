<!-- Modal -->
<div class="modal fade"
     id="passwordEdit"
     tabindex="-1"
     role="dialog"
     aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered"
         role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"
                    id="exampleModalLabel"></h5>
                <button type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form
                  method="post"
                  id="updatepasswordteamForm"
                  enctype="multipart/form-data">
                <div class="modal-body">

                    @csrf
                    <div class="row">
                       <div class="form-group col-md-12">
                                <label>Password</label>
                                <input type="password"
                                       name="password"
                                       class="form-control"
                                       id="password"
                                       placeholder="New password">
                                <span toggle="#password"
                                      class="fa fa-fw fa-eye field-icon password"
                                      style="position: relative;float: right !important;   top: -23px;  left: -11px;"></span>

                            </div>

                            <div class="form-group col-md-12">
                                <label for="inputPassword4">Confirm Password</label>
                                <input type="password"
                                       class="form-control"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       placeholder="Type password again">
                                <span toggle="#password_confirmation"
                                      class="fa fa-fw fa-eye field-icon password_confirmation"
                                      style="position: relative;float: right !important;   top: -23px;  left: -11px;"></span>

                            </div>

                        </div>

                    </div>


                <hr>
                <div class="row m-3">
                    <div class="col-md-6">
                        <button
                           class="btn btn-secondary btn-flat btn-lg "
                           aria-label="Close" type="button"  data-dismiss="modal" id="cancelBtn">Close</button>
                    </div>
                    <div class="col-md-6">
                        <div class="text-right">
                            <button type="submit"
                                    class="btn btn-success btn-flat btn-lg">Update</button>
                        </div>
                    </div>
                </div>
                <hr>

            </form>
        </div>
    </div>
  </div>

