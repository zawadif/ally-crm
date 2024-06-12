<?php

namespace App\Traits;
use Illuminate\Support\Facades\Log;

trait loggerExceptionTrait
{
    /**
     * This trait was built to easily exception handling log error file
     */
    public function saveExceptionLog($e,$title='exception'){
        $data = [
            'Time' => gmdate("F j, Y, g:i a"),
            'Status Code' => $e->getCode(),
            'Message' => $e->getMessage(),
        ];
        Log::error($title,$data);
    }
}
