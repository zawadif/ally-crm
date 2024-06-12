@extends('layouts.master')
@section('title','Special List Management')
@section('style')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet"/>


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
                        <h1>Special List Management</h1>
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
                                <h3 class="card-title" style="color: white">Units</h3>
                                <div style="float: right">
                                    <a href="#" class="btn btn-sm btn-primary greenButton" data-bs-toggle="modal" data-bs-target="#addUnitModal">
                                        <i class="fa fa-plus-circle"></i> Add Special
                                    </a>

                                </div>

                            </div>

                            <!-- /.box-header -->
                            <div class="card-body">
                                <table class="table" id="unit_table_1">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Type Name</th>
                                        <th>Title</th>
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

    <!-- Modal Form for Adding Unit -->
    <div class="modal fade" id="addUnitModal" tabindex="-1" aria-labelledby="addUnitFormLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUnitModalLabel">Add Special list</h5>
                    <button type="button" class="" data-bs-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <!-- Your form fields go here -->
                    <form id="addUnitForm">

                        <label for="unitType">Type Name:</label>
                        <select id="unitType" name="unitType" class="form-control">
                            <option value="nurse specialist">Nurse Special</option>
                            <option value="nonnurse specialist">Non Nurse Special</option>
                        </select>

                        <label for="unitName">Title:</label>
                        <input type="text" id="unitName" name="name" class="form-control" required>

                        <!-- Add more input fields as needed --><br>
                        <button type="submit" class="btn btn-primary greenButton" style="float: right;border-radius: 0px 8px 0 8px">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="editSpecialModal" tabindex="-1" aria-labelledby="editSpecialModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUnitModalLabel">Update Special list</h5>
                    <button type="button" class="" data-bs-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <!-- Your form fields go here -->
                    <form id="editUnitForm">
                        @csrf
                        @method('PUT') <!-- Use PUT method for update -->
                        <input type="hidden" id="editUnitId" name="id"> <!-- Hidden field for storing unit id -->


                        <label for="editUnitType">Type Name:</label>
                        <select id="editUnitType" name="editUnitType" class="form-control">

                        </select>
{{--                        <input type="text" id="editUnitType" name="editUnitType" class="form-control" required>--}}

                        <label for="editUnitName">Title:</label>
                        <input type="text" id="editUnitName" name="editUnitName" class="form-control" required>

                        <!-- Add more input fields as needed -->
                        <br>
                        <button type="submit" class="btn btn-primary greenButton" style="float: right;border-radius: 0px 8px 0 8px">Update</button>

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
    <script>


        var columns = [
            { "data":"date", "name": "date" },
            { "data":"time", "name": "time" },
            { "data":"type", "name": "Type Name" },
            { "data":"name", "name": "Name" },
            { "data":"action", "name": "action" }
        ];

        $(document).ready(function() {
            // $.fn.dataTable.ext.errMode = 'none';

            $('#unit_table_1').DataTable({

                "processing": true,
                "serverSide": true,
                "responsive": true,
                "searchable":true,
                "ajax":"getSpecialist",
                "order": [],
                "columns": [
                    { "data":"date", "name": "date" },
                    { "data":"time", "name": "time" },
                    { "data":"type", "name": "type" ,"orderable":true},
                    { "data":"name", "name": "Name","orderable":true },
                    { "data":"action", "name": "action" }
                ]
            });

        });



    </script>

    <script>
        $(document).ready(function () {
            $('#addUnitForm').submit(function (e) {
                e.preventDefault();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                // Perform AJAX request to submit form data
                $.ajax({
                    type: 'POST',
                    url: '{{ route("special_lists.store") }}', // Replace with your route name
                    data: $(this).serialize(),
                    success: function (data) {
                        // Handle success, e.g., close modal, refresh table
                        $('#addUnitModal').modal('hide');
                        $('#unit_table_1').DataTable().ajax.reload();
                    },
                    error: function (xhr, status, error) {
                        // Handle error, e.g., display error message
                        if (xhr.status === 422) {
                            // If validation errors, display them in the modal
                            var errors = xhr.responseJSON.errors;
                            for (var key in errors) {
                                $('#' + key + 'Error').text(errors[key][0]);
                            }
                        } else {
                            console.error(xhr.responseText);
                            alert('Error: ' + xhr.responseText);
                        }
                    }
                });
            });
        });

        $(document).on('click', '.edit-unit-btn', function () {
            $('#editSpecialModal').modal('show');
            var unitId = $(this).data('id');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // Perform AJAX request to get unit details
            $.ajax({
                type: 'GET',
                url: '/special_lists/' + unitId, // Adjust the URL according to your route
                success: function (data) {
                    console.log(data);
                    // Populate the modal fields with unit details
                    $('#editUnitId').val(data.id);
                    $('#editUnitType').empty();
                    if (data.special_type === "nurse specialist") {
                        $('#editUnitType').append($('<option></option>').attr('value', data.special_type).text(data.special_type));
                        $('#editUnitType').append($('<option></option>').attr('value', 'nonnurse specialist').text('Non-Nurse'));
                    } else {
                        $('#editUnitType').append($('<option></option>').attr('value', data.special_type).text(data.special_type));
                        $('#editUnitType').append($('<option></option>').attr('value', 'nurse specialist').text('Nurse'));
                    }
                    $('#editUnitName').val(data.name);

                    // Show the modal
                    $('#editUnitModal').modal('show');
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });
        $(document).on('submit', '#editUnitForm', function (e) {
            e.preventDefault(); // Prevent the default form submission
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // Get the form data
            var formData = $(this).serialize();

            // Perform the Ajax request to update the unit
            $.ajax({
                type: 'POST', // Change the method to POST if you are updating data
                url: '/special_lists/update', // Adjust the URL according to your route
                data: formData,
                success: function (response) {

                    // Handle success response, if needed

                    // Close the modal after successful update
                    $('#editSpecialModal').modal('hide');
                    $('#unit_table_1').DataTable().ajax.reload();

                },
                error: function (xhr, status, error) {
                    if (xhr.status === 422) {
                        // If validation errors, display them in the modal
                        var errors = xhr.responseJSON.errors;
                        for (var key in errors) {
                            $('#' + key + 'Error').text(errors[key][0]);
                        }
                    } else {
                        console.error(xhr.responseText);
                        alert('Error: ' + xhr.responseText);
                    }

                }
            });
        });

        // If your server expects a POST request for updating data, make sure to adjust the method in the form to POST:
        // <form id="editUnitForm" method="POST">


    </script>
    <script>
        $(document).on('click', '.delete-record', function (e) {
            e.preventDefault();

            var recordId = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: 'You won\'t be able to revert this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Perform the Ajax request to delete the record
                    $.ajax({
                        type: 'DELETE',
                        url: '/special_lists/' + recordId, // Adjust the URL according to your route
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            // Handle success response, if needed
                            Swal.fire('Deleted!', 'Your record has been deleted.', 'success');
                            $('#unit_table_1').DataTable().ajax.reload();

                            // You might want to update the UI, remove the deleted record from the list, etc.
                        },
                        error: function (xhr, status, error) {
                            console.error(xhr.responseText);
                            Swal.fire('Error!', 'An error occurred while deleting the record.', 'error');
                        }
                    });
                }
            });
        });
    </script>



@endsection
