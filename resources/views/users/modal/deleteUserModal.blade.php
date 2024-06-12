  <!-- Single Modal -->
  <div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
      aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
          <div class="modal-content">
              <div class="modal-body m-1">
                  <h6><b>Delete User</b></h6>
                  <hr style="border: 1px solid #707070;">
                  <form id="deleteUserForm" action="POST">
                      <input type="hidden" name="userId" id="deleteUserId">
                      <div class="row mb-2">
                          <div class="col-lg-12 col-md-12 col-12">
                              <div class="container" style="height:3rem;">
                                  <span class="p-1">Do you really want to delete this user? </span>
                              </div>
                          </div>
                      </div>

                      <div class="row mt-3">
                          <div class="col-6">
                              <a class="font12 btn btn-block btn-default whiteButton btn-default rounded whiteButton"
                                  data-dismiss="modal">Cancel</a>
                          </div>
                          <div class="col-6">
                              <button type="submit"
                                  class="font12 btn btn-block btn-success btn-default rounded greenButton">Delete
                                  User</button>
                          </div>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>
