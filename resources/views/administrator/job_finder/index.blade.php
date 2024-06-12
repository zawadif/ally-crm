@extends('layouts.master')

@section('content')
    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Page header -->
        {{--        <div class="page-header page-header-dark has-cover" style="border: 1px solid #ddd; border-bottom: 0;">--}}

        <!-- /page header -->


        <!-- Content area -->
        <div class="content">
            <!-- Inner container -->
            <div class="d-md-flex align-items-md-start">

                <!-- Left sidebar component -->
                <div
                    class="sidebar sidebar-light bg-transparent sidebar-component sidebar-component-left border-0 shadow-0 sidebar-expand-md">

                    <!-- Sidebar content -->
                    <div class="sidebar-content">

                        <!-- Filter -->
                        <div class="card">
                            <div class="card-header bg-transparent header-elements-inline">
                                <span class="text-uppercase font-size-sm font-weight-semibold">Find Postcode</span>
                            </div>

                            <div class="card-body">
                                <form action="{{ route('postcodeFinderResults') }}" method="post">
                                    @csrf()
                                    <div class="form-group form-group-feedback form-group-feedback-left">
                                        <input type="text" class="form-control rounded-pill" placeholder="Enter Postcode" name="postcode" value="" required>
                                        <span> <small class="text-danger">{{ $errors->first('postcode') }}</small> </span>
                                        <div class="form-control-feedback">
                                            <i class="icon-pin-alt text-muted"></i>
                                        </div>
                                    </div>
                                    <div class="form-group form-group-feedback form-group-feedback-left">
                                        <select class="form-control rounded-pill select-search" name="radius" required>
                                            <option value="">Select Radius</option>
                                            <option value="10">10 KMs</option>
                                            <option value="15">15 KMs</option>
                                            <option value="20">20 KMs</option>
                                            <option value="25">25 KMs</option>
                                            <option value="30">30 KMs</option>
                                            <option value="40">40 KMs</option>
                                            <option value="50">50 KMs</option>
                                        </select>
                                        <div class="form-control-feedback">
                                            <i class="icon-reading text-muted"></i>
                                        </div>
                                        <span> <small class="text-danger">{{ $errors->first('radius') }}</small> </span>
                                    </div>
                                    <button type="submit" class="btn bg-teal greenButton btn-block rounded-pill">
                                        <i class="icon-search4 font-size-base mr-2"></i>
                                        Find Postcode
                                    </button>
                                </form>
                            </div>


                        </div>
                        <!-- /filter -->
                    </div>
                    <!-- /sidebar content -->

                </div>
                <!-- /left sidebar component -->
                <!-- Right content -->
                <div class="accordion" id="jobAccordion">
                    @if(isset($data['cordinate_results']))
                        @forelse($data['cordinate_results'] as $result)
                            <!-- Card -->
                            <div class="card mb-3">
                                <div class="card-header" id="heading{{ $loop->index }}">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse{{ $loop->index }}" aria-expanded="false" aria-controls="collapse{{ $loop->index }}">
                                            <i class="fas fa-plus"></i>
                                            {{ $result->job_title_prof_res && $result->job_title_prof_res != '' ? $result->job_title . ' (' . $result->job_title_prof_res . ')' : $result->job_title }}/{{ $result->job_category }}
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapse{{ $loop->index }}" class="collapse" aria-labelledby="heading{{ $loop->index }}" data-parent="#jobAccordion">
                                    <div class="card-body">
                                        <!-- Job Title and Category -->
                                        <h6 class="media-title font-weight-semibold">
                                            <a href="#">

                                                    <?php echo $result->job_title_prof_res && $result->job_title_prof_res != '' ? $result->job_title . ' (' . $result->job_title_prof_res . ')' : $result->job_title; ?> / {{ $result->job_category }}
                                            </a>
                                        </h6>
                                        <!-- CV Limit Badge -->
                                        @if($result->cv_limit == $result->send_cv_limit)
                                            <span class="badge badge-danger" style="font-size: 90%">Limit Reached</span>
                                        @else
                                            <span class="badge badge-success" style="font-size: 90%">{{ $result->send_cv_limit - $result->cv_limit }} Cv's limit remaining</span>
                                        @endif
                                        <ul class="list-inline list-inline-dotted text-muted mb-2">
                                            <li class="list-inline-item"><a href="{{ route('range',['id'=>$result->id,'radius'=>$radius]) }}" class="text-muted">{{ $result->postcode }}</a></li>
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-inline">
                                            <li class="list-inline-item">Name: <span class="font-weight-bold">{{ $result->office_name }}</span></li>
                                            <li class="list-inline-item">Postcode: <span class="font-weight-bold">{{ $result->postcode }}</span></li>
                                            <li class="list-inline-item">Category: <span class="font-weight-bold">{{ $result->job_category }}</span></li>
                                        </ul>
                                        <p><b>Benefits:</b> {{ $result->benefits }}</p>
                                        <ul class="list-inline">
                                            <li class="list-inline-item"><b>Office:</b> {{ $result->office_name }}</li>
                                            <li class="list-inline-item"><b>Unit:</b> {{ $result->unit_name }}</li>
                                            <li class="list-inline-item"><b>Salary:</b> {{ $result->salary }}</li>
                                            <li class="list-inline-item"><b>Qualification:</b> {{ $result->qualification }}</li>
                                            <li class="list-inline-item"><b>Type:</b> {{ $result->job_type }}</li>
                                            <li class="list-inline-item"><b>Time:</b> {{ $result->time }}</li>
                                            <li class="list-inline-item"><b>Experience:</b> {{ $result->experience }}</li>
                                        </ul>
                                        <p><b>Postcode:</b> {{ $result->postcode }}</p>
                                        <p><b>Details:</b>  {{$result->sale_notes}}</p>
                                        <span class="badge bg-teal">Distance: {{ round(floatval(substr(strval($result->distance), 0, 6)), 1) }}</span>
                                        <span class="badge {{ $result->days_diff == 'true' ? 'test_demo' : 'bg-teal' }}">{{ $result->posted_date }}</span>
                                    </div>
                                </div>
                            </div>
                            <!-- /Card -->
                        @empty
                            <div class="card">
                                <div class="card-body">
                                    <p>No job found.</p>
                                </div>
                            </div>
                        @endforelse
                    @endif
                </div>

                <!-- /right content -->

            </div>
            <!-- /inner container -->
        </div>
        <!-- /content area -->

@endsection
