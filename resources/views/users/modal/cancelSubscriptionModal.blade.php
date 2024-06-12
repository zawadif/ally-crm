  <!-- Single Modal -->
  <div class="modal fade" id="cancelSubscriptionModal" tabindex="-1" role="dialog"
      aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-md" role="document">
          <div class="modal-content">
              <div class="modal-body m-1">
                  <h6><b>Cancel Subscription</b></h6>
                  <hr style="border: 1px solid #707070;">
                  <form id="cancelSubscriptionForm" action="POST">
                      <input type="hidden" name="purchaseId" id="purchaseId">
                      <div class="row mb-2">
                          <div class="col-lg-12 col-md-12 col-12">
                              <div class="row mt-2">
                                  <div class="col-lg-16 col-md-6 col-6">
                                      <h6>Amount Paid</h6>
                                  </div>
                                  <div class="col-lg-6 col-md-6 col-6 text-right">
                                      <p id="amountPaid"></p>
                                  </div>

                              </div>
                          </div>
                      </div>

                      <div class="row mt-3">
                          <div class="col-6">
                              <a class="font12 btn btn-block btn-default whiteButton btn-default rounded whiteButton p-1"
                                  data-dismiss="modal">Cancel</a>
                          </div>
                          <div class="col-6">
                              <button type="submit"
                                  class="font12 btn btn-block redButton btn-default rounded text-white">Cancel
                                  Subscription</button>
                          </div>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>
