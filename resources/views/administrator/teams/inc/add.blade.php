<!-- Modal -->
<div class="modal fade" id="AddSub" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Subcategory</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form  class="products" method="post" id="addCategoriesForm"  action="#"  enctype="multipart/form-data">
      <div class="modal-body">

            <div class="col-12">
                @csrf
                <div class="row">
                    <div class="col-md-12 col-lg-12 col-sm-12">
                        <div class="form-group">
                             <input type="text" name="name" class="form-control" placeholder="Enter subcategory name.." required autocomplete="off">
                             <input type="hidden" name="categoryId" class="form-control" id="categoryId">
                         </div>
                    </div>
                 </div>
             </div>
               </div>
				     <div class="modal-footer">
				      <button type="submit" class="btn btn-success btn-flat btn-lg">Done</button>
				      </div>
              </form>
				    </div>
				  </div>
				</div>

