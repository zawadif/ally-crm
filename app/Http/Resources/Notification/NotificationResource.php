<?php

namespace App\Http\Resources\Notification;

use App\Models\Chat;
use App\Models\User;
use App\Models\Country;
use Illuminate\Http\JsonResponse;
use App\Enums\NotificationTypeEnum;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Chat\ChatResource;
use App\Http\Resources\Ladders\LadderCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->notificationType == NotificationTypeEnum::PAYMENT_REQUEST) {
            return [
                "title" => $this->title ?: null,
                "type" => $this->notificationType ?: null,
                "message" => $this->message ?: null,
                "time" => $this->createdAt ? strtotime($this->createdAt) : null,
                "ladderId" => $this->ladderId ?: null,
                "userId" => $this->createdBy ?: null  // user id of created notification
            ];
        } else if (
            $this->notificationType == NotificationTypeEnum::CHAT || $this->notificationType == NotificationTypeEnum::ADMIN_CHAT ||
            $this->notificationType == NotificationTypeEnum::USER_CHAT || $this->notificationType == NotificationTypeEnum::SUPPORT_CHAT ||
            $this->notificationType == NotificationTypeEnum::SUPPORT
        ) {
            if (
                $this->notificationType == NotificationTypeEnum::CHAT  ||
                $this->notificationType == NotificationTypeEnum::USER_CHAT
            ) {
                $type = "CHAT";
                $getInboxChat = Chat::where('type', 'CHAT')->where('matchId', $this->matchId)->first();
            } else if ($this->notificationType == NotificationTypeEnum::ADMIN_CHAT) {

                $type = "CHAT";
                $userDetailRegionId = Auth()->user()->getUserDetail->regionId;
                $userId = Auth()->user()->id;
                $adminusers = User::with('roles')->whereHas('roles', function ($query) {
                    $query->where('name', '!=', 'admin');
                    $query->where('name', '!=', 'user');
                })->whereHas('regions', function ($query) use ($userDetailRegionId) {
                    $query->where('regionId', $userDetailRegionId);
                })->first();
                if (!$adminusers) {
                    return response()->json(['response' => ['status' => false, 'message' => 'Regional admin not found.']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                }

                $getInboxChat = Chat::whereIn('type', ['ADMIN_CHAT_ISSUE', 'ADMIN_CHAT'])->where('userId', $userId)->where('adminUId', $adminusers->uid)->first();
            } else {
                $type = "SUPPORT";
                $getInboxChat = Chat::where('type', 'SUPPORT')->where('matchId', $this->matchId)->where('userUId', Auth()->user()->uid)->first();
            }
            return [
                "title" => $this->title ?: null,
                "type" => $type ?: null,
                "message" => $this->message ?: null,
                "time" => $this->createdAt ? strtotime($this->createdAt) : null,
                "inboxModel" => $getInboxChat ? new ChatResource($getInboxChat) : null
            ];
        } else {
            return [
                "title" => $this->title ?: null,
                "type" => $this->notificationType ?: null,
                "message" => $this->message ?: null,
                "time" => $this->createdAt ? strtotime($this->createdAt) : null
            ];
        }
    }
}
