<?php

namespace App\Traits;

use Propaganistas\LaravelPhone\PhoneNumber;

trait  PhoneNumberFormattingTraits{

    public function formattingPhone($phoneNumber){
        if(strlen($phoneNumber)== 13 && str_contains($phoneNumber,'+11')){
            $phoneNumber = str_replace('+11','+1',$phoneNumber);
        }
        $phoneNumber =  preg_replace('/[^0-9+]/', '', $phoneNumber);
        $phoneNumber=(string) PhoneNumber::make($phoneNumber);
        return $phoneNumber;
    }

    public function formattingStorePhoneNumber($phoneNumber,$code)
    {
        if(str_contains($phoneNumber, '+')){
            return $phoneNumber;
        }
        $phoneNumberResult = (int)str_replace(" ","",$phoneNumber);
        $phoneNumberResult = $this->formattingPhone('+'.$code.$phoneNumberResult);
        return $phoneNumberResult;
    }
}
