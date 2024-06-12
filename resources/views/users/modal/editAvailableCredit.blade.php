  <!-- Single Modal -->
  <div class="modal fade" id="availableCreditModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
      aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-md" role="document">
          <div class="modal-content">
              <div class="modal-body m-1">
                  <h6><b>Edit Credits</b></h6>
                  <hr style="border: 1px solid #707070;">
                  <div class="row mt-2">
                      <div class="col-lg-16 col-md-6 col-6">
                          <h6>Credits Available</h6>
                      </div>
                      <div class="col-lg-6 col-md-6 col-6 text-right">
                          <p id="availableCreditValue"></p>
                      </div>

                  </div>
                  <form id="editCreditForm" action="POST">
                      <input type="hidden" name="userId" id="creditUserId" value="{{ $user->id }}">
                      <input type="hidden" name="remaining" id="remaining" value="">
                      <div class="form-group mb-0 ">
                          <label for="">Amount to Subtract</label>
                          <input type="text" class="form-control form-control-sm" id="subtractCredit"
                              name="subtractCredit" value="">
                      </div>
                      <div class="row mt-2">
                          <div class="col-lg-8 col-md-8 col-8">
                              <h6>Credits Remaining (After Subtraction)</h6>
                          </div>
                          <div class="col-lg-6 col-md-6 col-6 text-right">
                              <p id="remainingCreditValue"></p>
                          </div>

                      </div>
                      <div class="row mt-2">
                          <div class="col-6">
                              <a class="font12 btn btn-block btn-default whiteButton btn-default rounded whiteButton"
                                  data-dismiss="modal">Cancel</a>
                          </div>
                          <div class="col-6">
                              <button type="submit"
                                  class="font12 btn btn-block btn-success btn-default rounded greenButton">Edit
                                  Credit</button>
                          </div>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>
