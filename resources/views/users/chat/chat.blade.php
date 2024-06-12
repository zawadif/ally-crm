  <div class="modal" id="chatModal" data-backdrop="false">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content container">

                            <!-- Modal body -->
                            <div class="modal-body">
                              <h6><b>New Chat</b></h6>
                              <hr style="border: 1px solid #707070;">
                                <input type="hidden" id="chatUser" name="userId" value="{{$user->id}}">
                                <div class="form-group">
                                    <label for="">Player Name</label>
                                    <input type="text" name="" class="form-control" value="{{$user->fullName}}" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="exampleFormControlInput1">Write you message</label>
                                    <textarea type="text" class="form-control" id="startChat" style="height: 100px;"
                                        placeholder="Write the message"></textarea>
                                    <span id="errorMessage" class="text-danger d-none">Please enter message</span>
                                </div>
                            </div>

                            <!-- Modal footer -->
                            <div class="mb-2">
                                <button type="button" id="startChatBtn" class="btn btn-success float-right mb-2">Start
                                    Chat</button>
                                <button type="button" id="closeChatModal" class="btn btn-white border float-right mb-2 mr-2"
                                    data-dismiss="modal">Cancel</button>
                            </div>

                        </div>
                    </div>
                </div>

