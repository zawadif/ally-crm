<!-- Modal -->
<div class="modal fade"
     id="teamEdit"
     tabindex="-1"
     role="dialog"
     aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg"
         role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"
                    id="exampleModalLabel"></h5>
                <button type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="products"
                  method="post"
                  id="updateteamForm"
                  enctype="multipart/form-data">
                <div class="modal-body">

                    @csrf
                    <div class="row">
                      <div class="form-group col-md-6">
                            <label>First Name</label>
                            <input type="text"
                                   name="editedfirstName"
                                   class="form-control"
                                   value=""
                                   required
                                   autocomplete="off"
                                   id="teamfirstName"
                                   placeholder="">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Last Name</label>
                            <input type="text"
                                   name="editedlastName"
                                   class="form-control"
                                   value=""
                                   required
                                   autocomplete="off"
                                   id="teamlastName"
                                   placeholder="">
                        </div>
                        <div class="form-group  col-md-12">
                            <label>Role 
                              <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Choose different role or leave it as default"></i>
                            </label>
                            <select class="form-control select2"
                                    id="role"
                                    style="width:100%"
                                    name="editedrole"
                                    required>
                                @foreach ($roles as $p)
                                    <option value="{{ $p->name }}">{{ ucfirst($p->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                     
                      
                        <div class="form-group  col-md-12">
                            <label>Image  
                              <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Choose different image or leave it as default"></i>
                            </label>
                            <div class="imageLoader"></div>
                          

                            <span>Max size: 10MB ratio 1:1</span>
                        </div>

                    </div>

                </div>
               <hr>
                <div class="row m-3">
                    <div class="col-md-6">
                        <a href=""
                           class="btn btn-danger btn-flat btn-lg"
                           id="deleteMember"onclick="return  confirm('Are you sure to delete ?')">Delete Member</a>
                    </div>
                    <div class="col-md-6">
                      <div class="text-right">
                      <button type="submit"
                                class="btn btn-success btn-flat btn-lg">Update</button>
                      </div>
                  </div>
                </div>
                <hr>

            </form>
        </div>
    </div>
</div>
