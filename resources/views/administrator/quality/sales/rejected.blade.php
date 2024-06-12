@extends('layouts.master')
@section('title','Team Management')
@section('style')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet"/>


    <style>
        sale_rejected_sample_1_paginate.page-item.active .page-link {
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
                        {{--                        <h1>Pending Sales</h1>--}}
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
                                <h3 class="card-title" style="color: white">Rejected Sales</h3>


                            </div>

                            <!-- /.box-header -->
                            <div class="card-body">
                                <table class="table table-striped" id="sale_rejected_sample_1">
                                    <thead>
                                    <tr>
                                        <th>Created Date</th>
                                        <th>Updated Date</th>
                                        <th>Category</th>
                                        <th>Job Title</th>
                                        <th>Head Office</th>
                                        <th>Unit</th>
                                        <th>Postcode</th>
                                        <th>Type</th>
                                        <th>Experience</th>
                                        <th>Qualification</th>
                                        <th>Salary</th>
                                        <th>Status</th>
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



@endsection

@section('script')
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script>
        var table;
        $(document).ready(function() {
            $.fn.dataTable.ext.errMode = 'none';
            table = $('#sale_rejected_sample_1').DataTable({
                "aoColumnDefs": [{"bSortable": false, "aTargets": [0,10]}],
                "bProcessing": true,
                "bServerSide": true,
                "aaSorting": [[0, "desc"]],
                // "sPaginationType": "full_numbers",
                "sAjaxSource": "{{ url('get-rejected-sales') }}",
                "aLengthMenu": [[10, 50, 100, 500], [10, 50, 100, 500]],
                "drawCallback": function( settings, json){
                    $('[data-popup="tooltip"]').tooltip();
                }
            });

            // table.destroy();

        });
    </script>




@endsection
