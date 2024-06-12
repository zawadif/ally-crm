<?php

namespace App\DataTables;

use Spatie\Permission\Models\Role;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class AdminRolesDataTable extends DataTable
{
    protected $dataTableVariable = 'roleDataTable';
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
            ->addColumn('#No',function ($row){
                static $x = 0;
                return ++$x;
            })
            ->addColumn('Role_Title',function ($row){
                return $row->name;
            })
            ->addColumn('Actions',function ($row){
                $button = '<div class="d-flex justify-content-start float-right">
                <a href="javascrit:void(0)" class="transhColor mr-2" onclick="deleteRole('. $row->id .')"><i class="bi bi-trash-fill"></i></a>
                <a class="btn btn-default btn-sm whiteButton mr-2" onclick="editAdminRole('. $row->id .','.TRUE.')">View Permission</a>
                <a class="btn btn-default btn-sm whiteButton" onclick="editAdminRole('. $row->id .','.FALSE.')">Edit Role</a>
                </div>';
                return $button;
            })->rawColumns(['#No','Role_Title','Actions']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\AdminRolesDataTable $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(AdminRolesDataTable $model)
    {
        $roles = Role::where('name','!=','user');
        return $this->applyScopes($roles);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
        ->setTableId('adminRoleTable')
        ->columns($this->getColumns())
        ->minifiedAjax('/manage/administrators')
        ->lengthMenu([10, 50, 100, 250, 500, 1000, "All"])
        ->dom(  'rt<"bottom"p><"clear">')
        ->parameters([
            'responsive'=>
                [
                    'details'=> [
                        'display'=> '$.fn.dataTable.Responsive.display.childRowImmediate',
                        'type'=> 'none',
                        'target'=> ''
                    ]
                ],
            'autoWidth' => false,
            'searching'=>false,
            'display'=>true,
            "processing"=> '<i class="fa fa-spinner fa-spin" style="font-size:24px;color:rgb(75, 183, 245);"></i>'
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
            Column::make('#No')
                  ->exportable(false)
                  ->printable(false)
                  ->width(60)
                  ->addClass('text-center'),
            Column::make('Role_Title'),
            Column::make('Actions')
                ->addClass('text-right'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'AdminRoles_' . date('YmdHis');
    }
}
