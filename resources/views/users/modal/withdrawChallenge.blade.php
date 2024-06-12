  <!-- Single Modal -->
  <div class="modal fade" id="withdrawChallengeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
      aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-md" role="document">
          <div class="modal-content">
              <div class="modal-body m-1">
                  <h6><b>Withdraw Proposal</b></h6>
                  <hr class="solid">
                  <form id="withdrawChallengeForm" action="POST">
                      <input type="hidden" name="challengeId" id="challengeId">
                      <input type="hidden" name="userId" id="withdrawchallengeId" value="{{ $user->id }}">
                      <div class="row mb-2">
                          <div class="col-lg-12 col-md-12 col-12">
                              <div class="container" style="height:3rem;">
                                  <span class="p-1">Do you really want to Withdraw this challenge? </span>
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
                                  class="font12 btn btn-block btn-success btn-default rounded greenButton">Withdraw
                                  Challenge</button>
                          </div>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>
