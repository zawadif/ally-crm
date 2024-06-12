  <!-- Single Modal -->
  <div class="modal fade" id="editPartnerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
      aria-hidden="true">
      <div class="modal-dialog modal-md" role="document">
          <div class="modal-content">
              <div class="modal-body m-1">
                  <h6><b>Change Partner</b></h6>
                  <hr style="border: 1px solid #707070;">
                  <form id="editPartnerForm" action="POST">
                      <input type="hidden" name="teamId" id="teamId">
                      <input type="hidden" name="userId" value="{{ $user->id }}">
                      <div class="form-group mb-0 ">
                          <label for="">Choose Partner</label>
                          <select class="form-select form-control" id="partnerId" name="partnerId">
                              @foreach ($allUser as $partner)
                                  <option value="{{ $partner->id }}">{{ $partner->fullName }}</option>
                              @endforeach
                          </select>
                      </div>

                      <div class="row mt-2">
                          <div class="col-6">
                              <a class="font12 btn btn-block btn-default whiteButton btn-default rounded whiteButton"
                                  data-dismiss="modal">Cancel</a>
                          </div>
                          <div class="col-6">
                              <button type="submit"
                                  class="font12 btn btn-block btn-success btn-default rounded greenButton">Change
                                  now</button>
                          </div>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>
