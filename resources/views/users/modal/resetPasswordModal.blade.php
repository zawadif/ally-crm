  <!-- Single Modal -->
  <div class="modal fade" id="resetPasswordModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
      aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-md" role="document">
          <div class="modal-content">
              <div class="modal-body m-1">
                  <h6><b>Reset Password</b></h6>
                  <hr style="border: 1px solid #707070;">

                  <form id="resetPasswordForm" action="POST">
                      <input type="hidden" name="userId" id="resetUserId" value="{{ $user->id }}">
                      <div class="form-group mb-0 ">
                          <label for="">New Password</label>
                          <input type="password" class="form-control form-control-sm" id="password" name="password">
                          <span class="text-danger error-message" id="passwordError"></span>
                      </div>
                      <div class="form-group mb-0 ">
                          <label for="">Repeat New Password</label>
                          <input type="password" class="form-control form-control-sm" id="password_confirmation"
                              name="password_confirmation">
                          <span class="text-danger error-message" id="passwordConfirmationError"></span>
                      </div>

                      <div class="row mt-2">
                          <div class="col-6">
                              <a class="font12 btn btn-block btn-default whiteButton btn-default rounded whiteButton"
                                  data-dismiss="modal" id="cancelBtn2">Cancel</a>
                          </div>
                          <div class="col-6">
                              <button type="submit"
                                  class="font12 btn btn-block btn-success btn-default rounded greenButton">Reset
                                  Now</button>
                          </div>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>
