@extends('layouts.master')
@section('title','Sales Decline Management')
@section('style')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.7/css/responsive.dataTables.min.css">


    <style>
        .page-item.active .page-link {
            /*z-index: 3;*/
            color: white;

            background-color:purple;
            border-color: purple;
        }
    </style>
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Decline Sales Management</h1>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- category create form -->

                    <!-- end category create form -->
                    <div class="col">

                        <div class="card">

                            <div class="card-header" style="background-color: purple">
                                <h3 class="card-title" style="color: white">Sales</h3>
{{--                                <div style="float: right">--}}
{{--                                    @can('sale_create')--}}
{{--                                        <a  href="{{route('sales.create')}}"  class="btn btn-sm btn-primary greenButton"><i class="fa fa-plus-circle"></i>  Add sale </a>--}}
{{--                                    @endcan--}}
{{--                                </div>--}}

                            </div>

                            <!-- /.box-header -->
                            <div class="card-body">
                                <table class="table" id="sale_table_1">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Time</th>
                                        {{--                                        <th>Sale Added Date</th>--}}
                                        {{--                                        <th>Updated At</th>--}}
                                        <th>Job Category</th>
                                        <th>Job Title</th>
                                        <th>Office Name</th>
                                        <th>Unit Name</th>
                                        <th>Postcode</th>
                                        <th>Job Type</th>
                                        <th>Experience</th>
                                        <th>Qualification</th>
                                        <th>Salary</th>
                                        <th>Cv limit</th>
                                        <th>Notes</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>


    <!-- Your modal code, you may need to adapt it based on your modal library -->
    <div class="modal" id="notesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Notes before Open</h5>

                </div>
                <div class="modal-body">
                    <!-- Your form to add notes -->
                    <form id="notesForm">
                        <input type="hidden" id="saleIdInput" name="sale_id">
                        <!-- Add other form fields as needed -->
                        <div class="form-group">
                            <label for="notes">Notes:</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                        <div style="text-align: right;">
                            <button type="button" class="btn btn-primary greenButton" onclick="closeSaleWithNotes()">Open Sale</button>
                            <button type="button" class="btn btn-secondary" onclick="closeModal()">Close</button>
                        </div>
{{--                        <button type="button" class="btn btn-primary greenButton" style="float: right" onclick="closeSaleWithNotes()">Open Sale</button>--}}
{{--                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Close</button>--}}

                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.7/js/dataTables.responsive.min.js"></script>

    <script>

        var columns = [
            { "data": "sale_added_date", "name": "Date" },
            { "data": "updated_at", "name": "Time" },
            { "data": "job_category", "name": "job_category" },
            { "data": "job_title", "name": "job_title" },
            { "data": "office_name", "name": "office_name" },
            { "data": "unit_name", "name": "unit_name" },
            { "data": "postcode", "name": "postcode" },
            { "data": "job_type", "name": "job_type" },
            { "data": "experience", "name": "experience" },
            { "data": "qualification", "name": "qualification" },
            { "data": "salary", "name": "salary" },
            { "data":"no_of_sent_cv", "name": "no_of_sent_cv" },
            { "data":"sale_note", "name": "sale_note" },
            { "data":"action", "name": "action" }
        ];

        $(document).ready(function() {
            // $.fn.dataTable.ext.errMode = 'none';

            $('#sale_table_1').DataTable({
                // responsive: true,
                "processing": true,
                "serverSide": true,
                "responsive": true,
                "ajax":"closed-sales",
                // "order": [],
                "columns": columns
            });

        });

        $(document).on('click', '.notes_history', function (event) {
            var office = $(this).data('unit');

            $.ajax({
                url: "{{ route('notesHistory') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    module_key: office,
                    module: "Unit"
                },
                success: function(response){
                    $('#unit_notes_history'+office).html(response);
                },
                error: function(response){
                    var raw_html = '<p>WHOOPS! Something Went Wrong!!</p>';
                    $('#unit_notes_history'+office).html(raw_html);
                }
            });
        });

    </script>
    <script>
        function confirmCloseSale(saleId) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You are about to open the sale. Do you want to proceed?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, Open it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    showNotesModal(saleId);
                }
            });
        }

        function showNotesModal(saleId) {
            // Add logic to show your modal for notes
            // You can use a modal library like Bootstrap modal or any other
            // Example with Bootstrap modal:
            $('#notesModal').modal('show');

            // Set saleId in the modal form
            $('#saleIdInput').val(saleId);
        }
        //on hold sales notes
        function confirmOnHoldSale(saleId) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You are about to on hold  the sale. Do you want to proceed?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, Hold  it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    showNotesOnHoldeModal(saleId);
                }
            });
        }

        function showNotesOnHoldeModal(saleId) {

            // Add logic to show your modal for notes
            // You can use a modal library like Bootstrap modal or any other
            // Example with Bootstrap modal:
            $('#onHoldModal').modal('show');

            // Set saleId in the modal form
            $('#saleIdOnHold').val(saleId);
        }
        function confirmAddNote(saleId){
            alert(1);
            $('#addNoteModel').modal('show');
            $('#noteSaleId').val(saleId);

        }
    </script>

    <script>
        function closeSaleWithNotes() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // Add AJAX logic here to submit the form data (notes) and close the sale
            $.ajax({
                type: 'POST',
                url: '/close-sale-with-notes',
                data: $('#notesForm').serialize(),
                dataType: 'json',
                success: function(response) {
                    Swal.fire({
                        title: 'Sale Open!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Reload or update your DataTable here
                        location.reload();
                    });
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    // Handle other errors as needed
                }
            });
        }
        function confirmOnHoldWithNotes() {



            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // Add AJAX logic here to submit the form data (notes) and close the sale
            $.ajax({
                type: 'POST',
                url: '/sale-on-hold-with-notes',
                data: $('#onHoldForm').serialize(),
                dataType: 'json',
                success: function(response) {
                    Swal.fire({
                        title: 'Sale Closed!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        $('#onHoldModal').modal('hide');
                        $('#sale_table_1').DataTable().ajax.reload();
                        // Reload or update your DataTable here
                        // location.reload();
                    });
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    // Handle other errors as needed
                }
            });
        }

        function closeModal() {
            $('#notesModal').modal('hide');
        }
    </script>




@endsection
