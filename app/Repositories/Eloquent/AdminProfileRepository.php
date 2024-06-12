<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Support\Facades\DB;
use App\Traits\PhoneNumberFormattingTraits;
use App\Traits\FileUploadTrait;

class AdminProfileRepository
{
    use PhoneNumberFormattingTraits, FileUploadTrait;

    protected $imageDirectory = "uploads/users";

    public function profile()
    {
        $user = User::with('getUserDetail')->where('id', auth()->user()->id)->first();
        $country = Country::where('id', $user->getUserDetail->countryId)->first();
        $countrys = Country::all();
        return view('adminProfile', ['user' => $user, 'country' => $country, 'countrys' => $countrys]);
    }

    public function updateProfile($request)
    {
        try {
            DB::beginTransaction();
            $contactNumber = $this->formattingStorePhoneNumber($request->contactNumber, $request->contactNumberValue);
            $id = $request->userId;
            $user = User::where('id', $id)->first();
            if ($request->imageChange == 'yes') {
                $avatar = $user->avatar;
            } else {
                $avatar = null;
            }
            if (request()->hasFile('avatar')) {
                $file = $request->file('avatar');
                $avatar = $this->uploadFile($file, $this->imageDirectory);
            }
            $user = User::where('id', $id)->update([
                'fullName' => $request->firstName . " " . $request->lastName,
                'email' => $request->email,
                'avatar' => $avatar,
            ]);

            UserDetail::where('userId', $id)->update([
                'gender' => $request->gender,
                'state' => $request->state,
                'postalCode' => $request->postalCode,
                'country' => $request->country,
                'completeAddress' => $request->address,
            ]);
            DB::commit();
            return $user;
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new \ErrorException('Fail to update profile' . $exception->getMessage());
        }
    }

    public function getProfile($id)
    {
        try {
            $user = User::with('getUserDetail')->where('id', $id)->first();
            return $user;
        } catch (\Exception $exception) {
            throw new \ErrorException('Fail to get user profile ' . $exception->getMessage());
        }
    }
}
