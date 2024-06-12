<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Specialist_job_titles;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class SpecialistTitleController extends Controller
{
    public function index(){
        $all_titles = Specialist_job_titles::orderBy('updated_at', 'desc')->get();
        return view('administrator.specialist_titles.index',compact('all_titles'));
    }
    public function getSpecialist(Request $request){
        if ($request->ajax()) {
            $units = Specialist_job_titles::orderBy('updated_at', 'desc');
//            $auth_user=Auth::user();
            return DataTables::of($units)
                ->addIndexColumn()
                ->addColumn('time', function($row){
                    $time=Carbon::parse($row->created_at)->format('h:i A');
                    return $time;
                })
                ->addColumn('date', function($row){
                    $date=Carbon::parse($row->created_at)->format('jS F Y');
                    return $date;
                })
                ->addColumn('name', function($row){
                    $name=ucfirst($row->name);
                    return $name;
                })  ->addColumn('type', function($row){
                    $type=ucfirst($row->special_type);
                    return $type;
                })

                ->addColumn('action', function ($oRow) {
                    $auth_user = Auth::user();
                    $action = "<div class=\"btn-group\">
        <div class=\"dropdown\">
            <a href=\"#\" class=\"list-icons-item\" data-bs-toggle=\"dropdown\">
                <i class=\"bi bi-list\"></i>
            </a>
            <div class=\"dropdown-menu dropdown-menu-right\">";
                    $action .= "<a href=\"#\" class=\"dropdown-item edit-unit-btn\" data-id=\"{$oRow->id}\"> Edit</a>";
                    $action .= "<a href=\"#\" class=\"dropdown-item delete-record\" data-id=\"" . $oRow->id . "\"> Delete </a>";
                       $action .= "</div>
        </div>
    </div>";

                    return $action;
                })

                ->rawColumns(['action','date','name','type','time'])
                ->make(true);

        }
    }

    public function store(Request $request)
    {
        try {
//            dd($request->all());
            // Validate the incoming request data
            $validatedData = $request->validate([
                'unitType' => 'required',
                'name' => 'required',
                // Add more validation rules as needed
            ]);

            // Create a new Unit instance
            $unit = new Specialist_job_titles($validatedData);
           $unit->special_type=$request->input('unitType');
           $unit->name=$request->input('name');
            // Save the unit to the database
            $unit->save();

            // You can return a response or redirect as needed
            return response()->json(['message' => 'Unit created successfully']);
        } catch (\ValidationException $e) {
            // If validation fails, return the errors to the front end
            return response()->json(['errors' => $e->errors()], 422);
        }
    }
    public function show($id)
    {
        $unit = Specialist_job_titles::findOrFail($id);
        return response()->json($unit);
    }

    public function update(Request $request, $id)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'editUnitType' => 'required|in:nurse specialist,nonnurse specialist', // Adjust the validation rules
                'editUnitName' => 'required|string',
                // Add more validation rules for other fields as needed
            ]);
            // Find the special list record by ID
            $specialList = Specialist_job_titles::findOrFail($request->id);
            $specialList->special_type=$request->input('editUnitType');
            $specialList->name=$request->input('editUnitName');
            $specialList->update($validatedData);

            // Return a response (you can customize this based on your needs)
            return response()->json(['message' => 'Record updated successfully']);
        } catch (\ValidationException $e) {
            // If validation fails, return the errors to the front end
            return response()->json(['errors' => $e->errors()], 422);
        }catch (\Exception $e) {
            // Handle the exception (log it, return an error response, etc.)
            return response()->json(['error' => 'Error updating record: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Find the unit by ID
            $special_title = Specialist_job_titles::findOrFail($id);

            // Delete the unit
            $special_title->delete();

            // Return a success response (you can customize this based on your needs)
            return response()->json(['message' => 'Record deleted successfully']);
        } catch (\Exception $e) {
            // Handle the exception (log it, return an error response, etc.)
            return response()->json(['error' => 'Error deleting record: ' . $e->getMessage()], 500);
        }
    }

    public function getSpecialTitles($category)
    {
        $titles = Specialist_job_titles::where('special_type', $category)->pluck('name', 'id');

        return response()->json(['titles' => $titles]);
    }



}
