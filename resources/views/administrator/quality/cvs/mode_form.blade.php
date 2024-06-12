<div class="modal fade" id="import_applicant_cv" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Upload CV</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="cvUploadForm" action="{{ route('import_applicantCv') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="applicant_file_id" name="applicant_id" value="">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="applicant_cv" name="applicant_cv" required>
                        <label class="custom-file-label" for="applicant_cv">Choose file</label>
                    </div>
                    <div class="progress mt-3" style="display: none;">
                        <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="uploadCvBtn">Upload</button>
            </div>
        </div>
    </div>
</div>
