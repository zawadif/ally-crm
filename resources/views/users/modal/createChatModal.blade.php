  <div class="modal" id="myChatModal" data-backdrop="false">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content container">

                            <!-- Modal Header -->
                            {{-- <div class="modal-header">
                                <h4 class="modal-title"></h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div> --}}

                            <!-- Modal body -->
                            <div class="modal-body">
                              <h6><b>New Chat</b></h6>
                              <hr style="border: 1px solid #707070;">
                                <input type="hidden" id="chatUserId" name="userId">
                                <div class="form-group">
                                    <label for="chatUserName">Player Name</label>
                                    <input type="text" name="" class="form-control" id="chatUserName" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="exampleFormControlInput1">Write you message</label>
                                    <textarea type="text" class="form-control" id="startMessage" style="height: 100px;"
                                        placeholder="Write the message"></textarea>
                                    <span id="errorMessage" class="text-danger d-none">Please enter message</span>
                                </div>
                            </div>

                            <!-- Modal footer -->
                            <div class="">
                                <button type="button" id="startChat" class="btn btn-success float-right mb-2">Start
                                    Chat</button>
                                <button type="button" id="closeModal" class="btn btn-white border float-right mb-2 mr-2"
                                    data-dismiss="modal">Cancel</button>
                            </div>

                        </div>
                    </div>
                </div>
{{-- @section('script')
     <script src="{{ asset('js/activeUser.js') }}"></script>
@endsection --}}
