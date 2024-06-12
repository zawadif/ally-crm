<div class="modal fade" id="editMatchScoreModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-body m-1">
                <h6><b>Edit Match Score</b></h6>
                <hr class="solid">
                <form id="editMatchScoreForm" method="POST">
                    <input type="hidden" name="matchId" id="editScoreMatchId">
                    <input type="hidden" name="updateBy" id="updateBy" value="{{ $user->id }}">
                    <div class="row mb-2">
                        <input type="hidden" name="image-source" value="{{ asset('img/avatar/default-avatar.png') }}"
                            id="image-source" />
                        <div class="col-lg-6 col-md-6">
                            <div><img id="team1Player1" src='{{ asset('img/avatar/default-avatar.png') }}'
                                    height="30" width="30" class="rounded" alt="User Image"><small class="p-1"
                                    id="team1Player1Name">
                                </small><br /><img src='{{ asset('img/avatar/default-avatar.png') }}' id="team1Player2"
                                    height="30" width="30" class="rounded mt-1 " alt="User Image"><small
                                    class="p-1 " id="team1Player2Name"></small></div>

                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div>
                                <img id="team2Player1" src='{{ asset('img/avatar/default-avatar.png') }}' height="30"
                                    width="30" class="rounded" alt="User Image"><small class="p-1"
                                    id="team2Player1Name">
                                </small>
                                <br />
                                <img id="team2Player2" src='{{ asset('img/avatar/default-avatar.png') }}' height="30"
                                    width="30" class="rounded mt-1 " alt="User Image"><small class="p-1 "
                                    id="team2Player2Name"></small>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-6 col-md-6">
                            <label for="exampleInputEmail1" class="m-0">
                                <h6><b>Set1</b></h6>
                            </label>
                            <input class="form-control setScore" id="set1Player1" name="set1Player1Score"
                                type="text">
                            <div class="invalid-feedback" id="set1Player1Error"></div>
                        </div>
                        <div class="col-lg-6 col-md-6"><label for="exampleInputEmail1" class="m-0">
                                <h6><b>Set1</b></h6>
                            </label>
                            <input class="form-control setScore" id="set1Player2" name="set1Player2Score"
                                type="text">
                            <div class="invalid-feedback" id="set1Player2Error"></div>

                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-6 col-md-6"><label for="exampleInputEmail1" class="m-0">
                                <h6><b>Set2</b></h6>
                            </label>
                            <input class="form-control setScore" id="set2Player1" name="set2Player1Score"
                                type="text">
                            <div class="invalid-feedback" id="set2Player1Error"></div>

                        </div>
                        <div class="col-lg-6 col-md-6"><label for="exampleInputEmail1" class="m-0">
                                <h6><b>Set2</b></h6>
                            </label>
                            <input class="form-control setScore" id="set2Player2" name="set2Player2Score"
                                type="text">
                            <div class="invalid-feedback" id="set2Player2Error"></div>

                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-6 col-md-6"><label for="exampleInputEmail1" class="m-0">
                                <h6><b>Set3</b></h6>
                            </label>
                            <input class="form-control setScore" id="set3Player1" name="set3Player1Score"
                                type="text">
                            <div class="invalid-feedback" id="set3Player1Error"></div>

                        </div>
                        <div class="col-lg-6 col-md-6"><label for="exampleInputEmail1" class="m-0">
                                <h6><b>Set3</b></h6>
                            </label>
                            <input class="form-control setScore" id="set3Player2" name="set3Player2Score"
                                type="text">
                            <div class="invalid-feedback" id="set3Player2Error"></div>

                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-6 col-md-6">
                            <div id="set1Score">
                                <span></span>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div id="set2Score">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">

                        <div class="col-6 editScore">

                            <button type="button" class="font12 btn btn-block  btn-default rounded whiteButton"
                                id="editScore" onclick="toggleButton(event);">Edit
                                Score</button>
                        </div>
                        <div class="col-6 uploadScore">
                            <button type="submit" class="font12 btn btn-block btn-default rounded whiteButton"
                                id="uploadScore">Upload Score
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="font12 btn btn-block btn-default rounded greenButton"
                                id="acceptScore">Accept Score</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
