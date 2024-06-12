@extends('layouts.master')
@section('style')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
{{--    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">--}}
{{--    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">--}}

@endsection
@section('content')

    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Page header -->
        <div class="page-header page-header-light">
            <div class="page-header-content header-elements-inline">
                <div class="page-title">
                    <h5>
                        <a href=""><i class="icon-arrow-left52 mr-2" style="color: white;"></i></a>
                        <span class="font-weight-semibold">Role</span> - Create
                    </h5>
                </div>
            </div>

        </div>
        <!-- /page header -->


        <!-- Content area -->
        <div class="content">
            <!-- Centered forms -->
            <div class="row">

                <div class="col-md-12">
                    <div class="card border-top-teal-400 border-top-3">
                        <div class="card-header">

                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-10 offset-md-1">

                                    @if (count($errors) > 0)
                                        <div class="alert alert-danger">
                                            <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                        <?php
                                        $module_permission = [];
                                        $permission_ids = [];

                                        foreach ($permission as $v) {
                                            if (strpos($v->name, 'dashboard_', 0) !== false) {
                                                $permission_ids['Dashboard'][] = $module_permission['Dashboard'][$v->id]['id'] = $v->id;
                                                $module_permission['Dashboard'][$v->id]['name'] = str_replace('dashboard_', "", $v->name);
                                                $module_permission['Dashboard'][$v->id]['original_name'] = $v->name;
                                            }
                                            elseif (strpos($v->name, 'role_', 0) !== false) {
                                                $permission_ids['Roles'][] = $module_permission['Roles'][$v->id]['id'] = $v->id;
                                                $module_permission['Roles'][$v->id]['name'] = str_replace('role_', "", $v->name);
                                                $module_permission['Roles'][$v->id]['original_name'] = $v->name;
                                            } elseif (strpos($v->name, 'applicant_', 0) !== false) {
                                                $permission_ids['Applicants'][] = $module_permission['Applicants'][$v->id]['id'] = $v->id;
                                                $module_permission['Applicants'][$v->id]['name'] = str_replace('applicant_', "", $v->name);
                                                $module_permission['Applicants'][$v->id]['original_name'] = $v->name;
                                            } elseif (strpos($v->name, 'user_', 0) !== false) {
                                                $permission_ids['Users'][] = $module_permission['Users'][$v->id]['id'] = $v->id;
                                                $module_permission['Users'][$v->id]['name'] = str_replace('user_', "", $v->name);
                                                $module_permission['Users'][$v->id]['original_name'] = $v->name;
                                            } elseif (strpos($v->name, 'office_', 0) !== false) {
                                                $permission_ids['Head-Offices'][] = $module_permission['Head Offices'][$v->id]['id'] = $v->id;
                                                $module_permission['Head Offices'][$v->id]['name'] = str_replace('office_', "", $v->name);
                                                $module_permission['Head Offices'][$v->id]['original_name'] = $v->name;
                                            } elseif (strpos($v->name, 'unit_', 0) !== false) {
                                                $permission_ids['Units'][] = $module_permission['Units'][$v->id]['id'] = $v->id;
                                                $module_permission['Units'][$v->id]['name'] = str_replace('unit_', "", $v->name);
                                                $module_permission['Units'][$v->id]['original_name'] = $v->name;
                                            } elseif (strpos($v->name, 'sale_', 0) !== false) {
                                                $permission_ids['Sales'][] = $module_permission['Sales'][$v->id]['id'] = $v->id;
                                                $module_permission['Sales'][$v->id]['name'] = str_replace('sale_', "", $v->name);
                                                $module_permission['Sales'][$v->id]['original_name'] = $v->name;
                                            }  elseif (strpos($v->name, 'postcode-finder_', 0) !== false) {
                                                $permission_ids['Postcode-Finder'][] = $module_permission['Postcode Finder'][$v->id]['id'] = $v->id;
                                                $module_permission['Postcode Finder'][$v->id]['name'] = str_replace('postcode-finder_', "", $v->name);
                                                $module_permission['Postcode Finder'][$v->id]['original_name'] = $v->name;
                                            } elseif (strpos($v->name, 'resource_', 0) !== false) {
                                                $sub_module = explode('_', $v->name);
                                                $permission_ids['Resource'][] = $v->id;
                                                if (count($sub_module) == 3) {
                                                    $module_permission['Resource'][$sub_module[1]][] = [
                                                        'id' => $v->id,
                                                        'name' => str_replace('resource_'.$sub_module[1].'_', "", $v->name),
                                                        'original_name' => $v->name
                                                    ];
                                                } else {
                                                    $module_permission['Resource']['Sub-Links'][] = [
                                                        'id' => $v->id,
                                                        'name' => str_replace('resource_', "", $v->name),
                                                        'original_name' => $v->name
                                                    ];
                                                }
                                            } elseif (strpos($v->name, 'quality_', 0) !== false) {
                                                $sub_module = explode('_', $v->name);
                                                $permission_ids['Quality'][] = $v->id;
                                                $module_permission['Quality'][$sub_module[1]][] = [
                                                    'id' => $v->id,
                                                    'name' => str_replace('quality_'.$sub_module[1].'_', "", $v->name),
                                                    'original_name' => $v->name
                                                ];
                                            } elseif (strpos($v->name, 'CRM_', 0) !== false) {
                                                $permission_ids['CRM'][] = $v->id;
                                                $sub_module = explode('_', $v->name);
                                                $module_permission['CRM'][$sub_module[1]][] = [
                                                    'id' => $v->id,
                                                    'name' => str_replace('CRM_'.$sub_module[1].'_', "", $v->name),
                                                    'original_name' => $v->name
                                                ];
                                            }
                                            /*** common links
                                            elseif (strpos($v->name, 'common-links_', 0) !== false) {
                                                $sub_module = explode('_', $v->name);
                                                $module_permission['Common-Actions'][] = [
                                                    'id' => $v->id,
                                                    'name' => str_replace('common-links_', "", $v->name),
                                                    'original_name' => $v->name
                                                ];
                                            }
                                            */
                                        }
                                        ?>

                                    {!! Form::open(array('route' => 'roles.store','method'=>'POST')) !!}
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12" style="margin-bottom: 15px;">
                                            <div class="form-group">
                                                <strong>Name:</strong>
                                                {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control', 'required')) !!}
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                            <div class="form-group">
                                                <div class="card-group-control card-group-control-right" id="accordion-control-right-create">
                                                @foreach($module_permission as $key1 => $value1)

                                                    @php($key1 = str_replace(' ','-',$key1))
                                                    <div class="card border-top-dark-alpha border-top-1" style="margin-bottom: 0;">
                                                        <div class="card-header">
                                                            <h6 class="card-title">
                                                                <a href="#" style="font-size: 12px;"><input type="checkbox" class="form-check-input check-all" data-fouc data-permissions="{{ implode('-' , $permission_ids[$key1]) }}" data-permission-name="{{ $key1 }}"></a>
                                                                <a data-toggle="collapse" class="collapsed text-default" href="#accordion-control-right-group-{{ $key1 }}" aria-expanded="false"><h6 style="margin-bottom: 0;">{{ $key1 }} Permissions ({{ count($value1) }})</h6></a>
                                                            </h6>
                                                        </div>

                                                        <div id="accordion-control-right-group-{{ $key1 }}" class="collapse" data-parent="#accordion-control-right-create">
                                                            <div class="card-body">

                                                            @if($key1 === 'Quality' || $key1 === 'Resource' || $key1 === 'CRM')

                                                                @foreach($value1 as $key2 => $value2)
                                                                    <br>
                                                                    <h6 style="margin-bottom: 0;"><span class="badge badge-mark border-danger mr-2"></span>{{ $key2 }} ({{ count($value2) }})</h6>
                                                                    <br>


                                                                        <div class="row">
                                                                            @foreach($value2 as $value3)
                                                                                <div class="col-md-3">
                                                                                    <div class="form-check form-switch form-switch-md form-switch-inline">
                                                                                        <input class="form-check-input" type="checkbox" id="{{ $key1.'-'.$value3['id'] }}" name="permission[]" value="{{ $value3['id'] }}">
                                                                                        <label class="form-check-label" for="permission">{{ $value3['name'] }}</label>
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    <br/>
                                                                @endforeach

                                                            @else
{{--                                                                    <div class="col-md-3 list-inline-item">--}}
{{--                                                                        <label class="switch" style="font-size: 14px;">--}}
{{--                                                                            <input type="checkbox" name="permission[]" value="{{ $val['id'] }}" class="name" id="{{ $key1.'-'.$val['id'] }}">--}}
{{--                                                                            <span class="slider round"></span>--}}
{{--                                                                            {{ $val['name'] }}--}}

{{--                                                                        </label>--}}
{{--                                                                    </div>--}}
                                                                    <div class="row">
                                                                        @foreach($value1 as $permission)
                                                                            <div class="col-md-3">
                                                                                <div class="form-check form-switch form-switch-md form-switch-inline">
                                                                                    <input class="form-check-input" type="checkbox" id="{{ $key1.'-'.$permission['id'] }}" name="permission[]" value="{{ $permission['id'] }}">
                                                                                    <label class="form-check-label" for="permission">{{ $permission['name'] }}</label>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
{{--                                                                @foreach($value1 as $val)--}}
{{--                                                                    <div class="col-md-3 list-inline-item">--}}
{{--                                                                        <label style="font-size: 14px;">{{ Form::checkbox('permission[]', $val['id'], false, array('class' => 'name', 'id' => $key1.'-'.$val['id'])) }}--}}
{{--                                                                                    {{ $val['name'] }}</label>--}}
{{--                                                                    </div>--}}
{{--                                                                @endforeach--}}

                                                            @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                @endforeach




                                                </div>
                                            </div>
                                            <div class="text-right">
                                                {{ Form::button('Save <i class="icon-paperplane ml-2"></i>',['type'=>'submit','class'=>'btn bg-teal legitRipple']) }}
                                            </div>
                                        </div>
                                    </div>
                                    {!! Form::close() !!}

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!-- /form centered -->
        </div>
        <!-- /content area -->
@endsection

@section('script')
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
{{--            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>--}}
            <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>

{{--            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>--}}
            <script>
                $(document).on('click', '.check-all', function(event) {
                    var $element = $(this);
                    var permissionName = $element.data('permission-name');
                    var permissions = $element.data('permissions');

                    if (typeof permissions === 'string') {
                        var permissionArray = permissions.split('-');
                        var isChecked = $element.prop("checked");

                        permissionArray.forEach(function(item) {
                            $("#" + permissionName + "-" + item).prop("checked", isChecked).change();
                        });
                    } else {
                        console.error("Invalid permissions format");
                    }
                });

                // Toggle Bootstrap 5 switches
                $(document).ready(function() {
                    $('.form-check-input').each(function() {
                        $(this).bootstrapToggle({
                            on: 'Enabled',
                            off: 'Disabled'
                        });
                    });
                });
            </script>

            <script>


                // $(document).on('click', '.check-all', function (event) {
    //     alert(1);
    //     $element = $(this);
    //     var permission_name = $element.data('permission-name');
    //     var permissions = $element.data('permissions');
    //     var single_perm = false;
    //     if (typeof permissions === 'number') {
    //         if($element.prop("checked") === true) {
    //             single_perm = true;
    //         }
    //         $( "#"+permission_name+"-"+permissions ).prop( "checked", single_perm );
    //     } else {
    //         permissions = permissions.split('-');
    //         if($element.prop("checked") === true) {
    //             permissions.forEach(function (item, i) {
    //                 $( "#"+permission_name+"-"+item ).prop( "checked", true );
    //             });
    //         } else {
    //             permissions.forEach(function (item, i) {
    //                 $( "#"+permission_name+"-"+item ).prop( "checked", false );
    //             });
    //         }
    //     }
    // });
</script>
@endsection
