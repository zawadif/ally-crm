<?php

namespace App\DataTables;

use Carbon\Carbon;
use App\Models\User;
use App\Models\UserDetail;
use App\Traits\FileUploadTrait;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class AdminUsersDataTable extends DataTable
{
    use FileUploadTrait;
    protected $dataTableVariable = 'userDataTable';
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('#No', function ($row) {
                static $x = 0;
                return ++$x;
            })->addColumn('Full_Name', function ($row) {
                if (!is_null($row->avatar)) {
                    $img = '<img src=' . Storage::disk('s3')->url($row->avatar) . ' alt="User Image" class="img-size-50" style="height: 35px !important;width: 35px !important;border-radius: 50%;"> ' . $row->fullName;
                } else {
                    $img = '<img src=' . asset('img/avatar/default-avatar.png') . ' alt="User Image" class="img-size-50" style="height: 35px !important;width: 35px !important;border-radius: 50%;"> ' . $row->fullName;
                }
                return $img;
            })->addColumn('Role', function ($row) {
                $user = User::find($row->id);
                if ($user->getRoleNames()->first() == 'user') {
                    return $user->getRoleNames()->skip(1)->first();
                } else {
                    return $user->getRoleNames()->first();
                }
            })->addColumn('Email', function ($row) {
                return $row->email;
            })->addColumn('Contact#', function ($row) {
                if ($row->getUserDetail->phoneNumber) {
                    return $row->getUserDetail->phoneNumber;
                } else {
                    return ' ';
                }
            })->addColumn('Member_Since', function ($row) {
                $dateFormat = Carbon::parse($row->createdAt)->setTimezone(Session::get('timeZone'))->format('d/m/Y');
                return $dateFormat;
            })->addColumn('Actions', function ($row) {
                if (auth()->user()->hasRole('admin')) {
                    $button = '<div class="d-flex justify-content-start">
                <a href="javascript:void(0)" class="transhColor mr-2" onclick="deleteUser(' . $row->id . ')"><i class="bi bi-trash-fill"></i></a>
                <a class="btn btn-block btn-default btn-sm whiteButton" onclick="editUserProfile(' . $row->id . ')" >Edit Profile</a>
                </div>';
                } else {
                    $button = "";
                }
                return $button;
            })->rawColumns(['#No', 'Full_Name', 'Role', 'Contact#', 'Email', 'Member_Since', 'Actions']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
        $users = User::with('getUserDetail')->whereHas('roles', function ($query) {
            $query->where('name', '!=', 'admin');
            $query->where('name', '!=', 'user');
        });
        return $this->applyScopes($users);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('adminUserTable')
            ->columns($this->getColumns())
            ->minifiedAjax('/manage/administrators')
            ->lengthMenu([10, 50, 100, 250, 500, 1000, "All"])
            ->dom('rt<"bottom"p><"clear">')
            ->parameters([
                'responsive' =>
                [
                    'details' => [
                        'display' => '$.fn.dataTable.Responsive.display.childRowImmediate',
                        'type' => 'none',
                        'target' => ''
                    ]
                ],
                'autoWidth' => false,
                'searching' => true,
                'display' => true,
                "processing" => '<i class="fa fa-spinner fa-spin" style="font-size:24px;color:rgb(75, 183, 245);"></i>'
            ])
            ->orderBy(1)
            ->serverSide(true)
            ->buttons(
                Button::make('create'),
                Button::make('export'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::computed('#No')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
            Column::make('Full_Name'),
            Column::make('Role'),
            Column::make('Contact#'),
            Column::make('Email'),
            Column::make('Member_Since'),
            Column::make('Actions'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'AdminUsers_' . date('YmdHis');
    }
}
