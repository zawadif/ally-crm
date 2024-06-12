<?php // Code within app\Helpers\Helper.php

namespace App\Helpers;


use App\Models\User;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Helper
{
//    public function getCoordinates($postcode)
//    {
////         = 'L1 8JQ';
//        $apiKey='AIzaSyCA1maGn_da4Y35faHXfCxa4sau-bYlSKk';
//        $postcode1=urlencode($postcode);
//
//        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$postcode1}&key={$apiKey}";
//        $response = file_get_contents($url);
////
//        try {
//
//            $data = json_decode($response, true);
////            dd($data);
//            if ($data['status'] === 'OK') {
//                $latitude = $data['results'][0]['geometry']['location']['lat'];
//                $longitude = $data['results'][0]['geometry']['location']['lng'];
////                dd($latitude,$longitude);
//                return [
//                    'latitude' => $latitude,
//                    'longitude' => $longitude,
//                ];
//            } else {
////                dd('error');
//                return null;
//            }
//        } catch (\Exception $e) {
//            dd($e->getMessage());
//            // Handle exceptions if the request fails
//            return null;
//        }
//
//
//
//
////        return response()->json(['error' => 'Location not found for this postcode.'], 404);
//    }
    public function getCoordinates($postcode)
    {

        // Check if postcode is empty or null
        if (empty($postcode)) {
            return null;
        }
//        dd($postcode);

        // Initialize Google Maps API key
//        $apiKey = 'AIzaSyCA1maGn_da4Y35faHXfCxa4sau-bYlSKk'; // Replace with your actual API key
//        $apiKey = ; // Replace with your actual API key
        $apiKey = env('GOOGLE_API_KEY');

        // URL encode the postcode
        $postcodeEncoded = urlencode($postcode);

        // Construct the API request URL
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$postcodeEncoded}&key={$apiKey}";

        // Perform the HTTP request
        $response = file_get_contents($url);

        // Check if the request was successful
        if ($response === false) {
            return null; // Failed to fetch data, return null
        }

        // Decode the JSON response
        $data = json_decode($response, true);
//dd($data);
        // Check if the response status is OK
        if ($data['status'] === 'OK') {
            // Extract latitude and longitude from the response
            $latitude = $data['results'][0]['geometry']['location']['lat'];
            $longitude = $data['results'][0]['geometry']['location']['lng'];

            // Return latitude and longitude as an array
            return [
                'latitude' => $latitude,
                'longitude' => $longitude,
            ];
        } else {
            // Handle error response (e.g., location not found)
            return null;
        }
    }







}
