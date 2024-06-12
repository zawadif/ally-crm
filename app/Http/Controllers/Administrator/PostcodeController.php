<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\CvNote;
use App\Models\Office;
use App\Models\Sale;
use App\Models\Specialist_job_titles;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PostcodeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
//        $this->middleware('permission:postcode-finder_search', ['only' => ['index','getPostcodeResults']]);
    }
    public function index(){
        return view('administrator.job_finder.index');
    }
    public function getPostcodeResults(Request $request)
    {
        $today =Carbon::now()->format("Y-m-d");
//        dd($today);
        $validator = Validator::make($request->all(), [
            'postcode' => 'required',
            'radius' => 'required'
        ])->validate();

        $postcode = $request->Input('postcode');
        $radius = $request->Input('radius');

        $postcode_para = urlencode($postcode).',UK';


//        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$postcode_para}&key=AIzaSyBPx06p1VPBhS_qz-dw7t0rYkoMbKeoNBM";
        $postcode_api = env('GOOGLE_API_KEY');

        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$postcode_para}&key={$postcode_api}";
        $resp_json = file_get_contents($url);

        $resp = json_decode($resp_json, true);
//        dd($resp);
      
        if ($resp['status'] == 'OK') {

            // get the important data
            $lati = isset($resp['results'][0]['geometry']['location']['lat']) ? $resp['results'][0]['geometry']['location']['lat'] : "";
            $longi = isset($resp['results'][0]['geometry']['location']['lng']) ? $resp['results'][0]['geometry']['location']['lng'] : "";

            $data['cordinate_results'] = $this->distance($lati,$longi, $radius);
//            $new_result =  $this->distance($lati,$longi, $radius);

            if ($data['cordinate_results']->isNotEmpty()) {
//                dd('sad');
                foreach($data['cordinate_results'] as &$job){
                    $cv_limit = CvNote::where(['sale_id' => $job->id, 'status' => 'active'])
                        ->count();
                    $job['cv_limit'] = $cv_limit;

//                    $newDate = Carbon::parse($job->posted_date);
//                    dd($job->posted_date);
//                    $different_days = $today->diffInDays($newDate);
//                    dd($different_days,$today);
                    $office_id = $job['head_office'];
                    $unit_id =$job['head_office_unit'];
                    $office = Office::select("name")->where(["id" => $office_id,"status" => "active"])->first();
                    $office = $office->name;
                    $unit = Unit::select("unit_name")->where(["id" => $unit_id,"status" => "active"])->first();
                    $unit = $unit->unit_name;
                    $job['office_name'] = $office;
                    $job['unit_name'] = $unit;
//                    if($different_days <= 7)
//                    {
////                        dd('sd7');
//                        $job['days_diff'] = 'true';
//                    }
//                    else
//                    {
//
//                        $job['days_diff'] = 'false';
//                    }
                    $title_prof =$job['job_title_prof'];
                    if($title_prof)
                    {
                        $job_title_prof = Specialist_job_titles::select("name")->where("id", $title_prof)->first();
                        $job['job_title_prof_res']=$job_title_prof->name;
                    }

                }
            } else {
                $data['cordinate_results'] = [];
            }

        }
//        dd($data);
        return view('administrator.job_finder.index',compact('data','radius'));
    }

    function distance($lat, $lon, $radius)
    {
        $location_distance = Sale::select(DB::raw("*, ((ACOS(SIN($lat * PI() / 180) * SIN(lat * PI() / 180) +
                COS($lat * PI() / 180) * COS(lat * PI() / 180) * COS(($lon - lng) * PI() / 180)) * 180 / PI()) * 60 * 1.1515)
                AS distance"))->having("distance", "<", $radius)->orderBy("distance")->where("status", "active")->where("is_on_hold", "0")->get();
        /**
         * gives more accurate distance but it also shows more distances for 2 out of 10
         * (ROUND( 6353 * 2 * ASIN(SQRT( POWER(SIN(($lat - lat) * pi()/180 / 2),2) + COS($lat * pi()/180 ) * COS( lat *  pi()/180) * POWER(SIN(($lon - lng) * pi()/180 / 2), 2) )), 2)) AS distance_cur
         */
//dd($location_distance->count());
        //  $distance = Sale::select(DB::raw("*, ((ACOS(SIN($lat * PI() / 180) * SIN(lat * PI() / 180) +
        //  COS($lat * PI() / 180) * COS(lat * PI() / 180) * COS(($lon - lng) * PI() / 180)) * 180 / PI()) * 60 * 1.1515)
        //  AS distance"))->having("distance", "<", $radius)->get();
        //  print_r($distance);exit();
        return $location_distance;
    }
}
