<?php

namespace App\Http\Resources\Chat;

use App\Enums\GenderEnum;
use App\Enums\MatchStatusEnum;
use App\Enums\MatchTypeEnum;
use App\Models\Country;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Ladders\LadderCollection;
use App\Http\Resources\Match\TeamMember\GetMatchTeamMembersResource;
use App\Http\Resources\Match\TeamMember\GetSecondTeamMemberResource;
use App\Http\Resources\Match\TeamMember\GetTeamMemberResource;
use App\Models\Ladder;
use App\Models\Team;
use App\Models\TeamMatch;
use App\Models\User;
use App\Services\FirestoreService;
use App\Traits\FirestoreTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    use FirestoreTrait;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $firestoreService = new FirestoreService();
        if ($this->type == 'ADMIN_CHAT' || $this->type == 'ADMIN_CHAT_ISSUE') {
            $adminInfo = User::with('getUserDetail')->where('uid', $this->adminUId)->withTrashed()->first();
            $playerInfo = User::with('getUserDetail')->where('uid', $this->userUId)->withTrashed()->first();
            $lastChats = $firestoreService->getLastMessageInFirestore($this);
            $unreadMessagesCount = $firestoreService->countUnreadMessagesInFirestoreChats($this, $this->userUId);
            $lastChatInfo = null;
            foreach ($lastChats as $lastChat) {
                $lastChatInfo = $lastChat->data();
            }
            return [
                "lastMessage" =>  $lastChatInfo != null ? $lastChatInfo['lastMessage'] : null,
                "lastMessageTime" =>  $lastChatInfo != null ? $lastChatInfo['lastMessageTime'] : null,
                "chatType" => ($this->type == 'ADMIN_CHAT' || $this->type == 'ADMIN_CHAT_ISSUE') ? "ADMIN" : $this->type,
                "unReadMessageCount" => $unreadMessagesCount,
                "adminAvatar" => !is_null($adminInfo->avatar) ? Storage::disk('s3')->url($adminInfo->avatar) : ($adminInfo->getUserDetail->gender == 'MALE' ? asset('img/avatar/maleAvatar.png') : asset('img/avatar/femaleAvatar.png')),
                "adminFullName" =>  $adminInfo->fullName,
                "adminUid" =>  $adminInfo->uid,
                "playerAvatar" => !is_null($playerInfo->avatar) ? Storage::disk('s3')->url($playerInfo->avatar) : ($playerInfo->getUserDetail->gender == 'MALE' ? asset('img/avatar/maleAvatar.png') : asset('img/avatar/femaleAvatar.png')),
                "playerFullName" =>  $playerInfo->fullName,
                "playerUid" =>  $playerInfo->uid,
                "issueId" =>  $this->issueId
            ];
        } elseif ($this->type == 'SUPPORT' || $this->type == 'SUPPORT_CHAT') {
            $isPlayOff = false;
            $adminInfo = User::with('getUserDetail')->where('uid', $this->adminUId)->withTrashed()->first();
            $playerInfo = User::with('getUserDetail')->where('uid', $this->userUId)->withTrashed()->first();
            $lastChats = $firestoreService->getLastMessageInFirestore($this);
            $unreadMessagesCount = $firestoreService->countUnreadMessagesInFirestoreChats($this, $this->userUId);
            $lastChatInfo = null;
            foreach ($lastChats as $lastChat) {
                $lastChatInfo = $lastChat->data();
            }
            $matchResult = null;
            $match = TeamMatch::find($this->matchId);
            if ($match->matchableType == MatchTypeEnum::PLAYOFF) {
                $isPlayOff = true;
            }
            $matchDetail = $match->matchDetail;
            if ($match->wonTeamId != null) {
                $matchResult = 'WON';
            }

            $matchType = 'DOUBLE';
            if ($match->category->name == 'Single') {
                $matchType = 'SINGLE';
            }


            return [
                "lastMessage" =>  $lastChatInfo != null ? $lastChatInfo['lastMessage'] : null,
                "lastMessageTime" =>  $lastChatInfo != null ? $lastChatInfo['lastMessageTime'] : null,
                "chatType" => ($this->type == 'ADMIN_CHAT' || $this->type == 'ADMIN_CHAT_ISSUE') ? "ADMIN" : $this->type,
                "unReadMessageCount" => 0,
                "adminAvatar" => !is_null($adminInfo->avatar) ? Storage::disk('s3')->url($adminInfo->avatar) : ($adminInfo->getUserDetail->gender == 'MALE' ? asset('img/avatar/maleAvatar.png') : asset('img/avatar/femaleAvatar.png')),
                "adminFullName" =>  $adminInfo->fullName,
                "adminUid" =>  $adminInfo->uid,
                "playerAvatar" => !is_null($playerInfo->avatar) ? Storage::disk('s3')->url($playerInfo->avatar) : ($playerInfo->getUserDetail->gender == 'MALE' ? asset('img/avatar/maleAvatar.png') : asset('img/avatar/femaleAvatar.png')),
                "playerFullName" =>  $playerInfo->fullName,
                "playerUid" =>  $playerInfo->uid,
                "matchId" =>  $match->id,
                "type" => $match->matchableType,
                "isPlayOff" => $isPlayOff,
                "matchType" =>  $matchType,
                "matchStatus" => $match->status ?: null,
                "timeOfMatch" => $match->matchTime ? (string)$match->matchTime : null,
                "matchResult" => !is_null($matchResult) ? $matchResult : null,
                "firstTeam" => new GetTeamMemberResource($match->firstTeam, $match->matchDetail, $match->wonTeamId, $match),
                "secondTeam" => $match->secondTeam ? new GetSecondTeamMemberResource($match->secondTeam, $match->matchDetail, $match->wonTeamId, $match) : null
            ];
        } elseif ($this->type == 'CHAT' || $this->type == 'USER_CHAT') {
            $isPlayOff = false;
            $lastChats = $firestoreService->getLastMessageInFirestore($this);
            $authUid = auth()->user() ? auth()->user()->uid : null;
            $unreadMessagesCount = $firestoreService->countUnreadMessagesInFirestoreChats($this, $authUid);
            $lastChatInfo = null;
            foreach ($lastChats as $lastChat) {
                $lastChatInfo = $lastChat->data();
            }
            $matchResult = null;
            $match = TeamMatch::find($this->matchId);
            if ($match->matchableType == MatchTypeEnum::PLAYOFF) {
                $isPlayOff = true;
            }
            if ($match->wonTeamId != null) {
                $matchResult = 'WON';
            }
            $matchType = 'DOUBLE';
            if ($match->category->name == 'Single') {
                $matchType = 'SINGLE';
            }
            $teamOneIds = Team::whereIn('id', [$match->teamOneId, $match->teamTwoId])->pluck('firstMemberId')->toArray();
            $teamTwoIds = Team::whereIn('id', [$match->teamOneId, $match->teamTwoId])->pluck('secondMemberId')->toArray();
            $teamIds = array_merge($teamOneIds, $teamTwoIds);
            $teamUsers = User::whereIn('id', $teamIds)->get();

            return   [
                "lastMessage" =>  $lastChatInfo != null ? $lastChatInfo['lastMessage'] : null,
                "lastMessageTime" =>  $lastChatInfo != null ? $lastChatInfo['lastMessageTime'] : null,
                "chatType" => $this->type == 'CHAT' ? "USER_CHAT" : $this->type,
                "unReadMessageCount" => $unreadMessagesCount,
                "messageType" => $lastChatInfo != null ? $lastChatInfo['messageType'] : null,
                "matchId" =>  $match->id,
                "isPlayOff" => $isPlayOff,
                "timeOfMatch" => $match->matchTime ?  (string)$match->matchTime : null,
                "matchStatus" => $match->status ?: null,
                "type" => $match->matchableType,
                "matchType" =>  $matchType,
                "users" => GetMatchTeamMembersResource::collection($teamUsers)
            ];
        }
    }
}
