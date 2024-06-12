  <!-- Single Modal -->
  <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
      aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-md" role="document">
          <div class="modal-content">
              <div class="modal-body m-1">
                  <h6><b>Filters</b></h6>
                  <hr style="border: 1px solid #707070;">
                  <form id="filterForm" action="GET">
                      <input type="hidden" name="userId" id="unblockUserId">
                      <div class="row mb-2">
                          <div class="col-lg-6 col-md-6 col-6">
                              <div class="form-group mb-0 ">
                                  <label for="">Season</label>
                                  <select class="form-select border-0 selectLook" style="width: 98%" id="seasonId">
                                      <option value="0">All</option>
                                      @foreach ($seasons as $season)
                                          <option value="{{ $season->id }}">{{ $season->title . '' . $season->year }}
                                          </option>
                                      @endforeach
                                  </select>
                              </div>
                          </div>
                          <div class="col-lg-6 col-md-6 col-6">
                              <div class="form-group mb-0 ">
                                  <label for="">Ladder</label>
                                  <select class="form-select border-0 selectLook" onchange="" style="width: 98%"
                                      aria-label="Default select example" id="ladderId">
                                      <option value="">All</option>
                                      @foreach ($ladders as $ladder)
                                          @foreach ($seasons as $season)
                                              @if ($ladder->seasonId == $season->id)
                                                  <option value="{{ $ladder->id }}">
                                                      {{ ucfirst($season->title) . ' ' . $season->year . ' ' . $ladder->name }}
                                                  </option>
                                              @endif
                                          @endforeach
                                      @endforeach
                                  </select>
                              </div>
                          </div>
                      </div>
                      <div class="row mb-2">
                          <div class="col-lg-6 col-md-6 col-6">
                              <div class="form-group mb-0 ">
                                  <label for="">Week/Playoff</label>
                                  <select class="form-select border-0 selectLook" onchange="" style="width: 98%"
                                      aria-label="Default select example" id="weekId">
                                      <option value="">All</option>
                                      @foreach ($weeks as $week)
                                          <option value="{{ $week->id }}">
                                              week{{ $week->WeekAndRoundNo . ' (' . $week->getSeason->title . ' ' . $week->getSeason->year . ')' }}
                                          </option>
                                      @endforeach
                                  </select>
                              </div>
                          </div>
                          <div class="col-lg-6 col-md-6 col-6">
                              <div class="form-group mb-0 ">
                                  <label for="">Country</label>
                                  <select class="form-select border-0 selectLook" onchange="" style="width: 98%"
                                      id="countryId">
                                      <option value="">All</option>
                                      @foreach ($countries as $country)
                                          <option value="{{ $country->id }}">{{ $country->name }}</option>
                                      @endforeach
                                  </select>
                              </div>
                          </div>
                      </div>
                      <div class="row mb-2">
                          <div class="col-lg-6 col-md-6 col-6">
                              <div class="form-group mb-0 ">
                                  <label for="">Region</label>
                                  <select class="form-select border-0 selectLook" onchange="" style="width: 98%"
                                      id="regionId">
                                      <option value="">All</option>
                                      @foreach ($regions as $region)
                                          <option value="{{ $region->id }}">{{ $region->name }}</option>
                                      @endforeach
                                  </select>
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
                                  class="font12 btn btn-block btn-success btn-default rounded greenButton filterButton">Apply</button>
                          </div>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>
