<?php

namespace App\Traits;

use App\Models\Team;
use App\Models\User;
use App\Models\Ladder;
use App\Models\Category;
use App\Models\FcmToken;
use App\Models\Challenge;
use App\Models\TeamMatch;
use App\Enums\MatchTypeEnum;
use App\Models\Notification;
use App\Services\FirebaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\Chat\ChatResource;
use App\Http\Resources\Chat\ChatCollection;
use App\Http\Resources\Match\MatchResource;
use App\Http\Resources\Notification\NotificationMatchResource;
use App\Http\Resources\Match\ScheduledMatch\GetScheduledMatchResource;
use App\Http\Resources\Notification\NotificationScheduleMatchResource;
use App\Services\FirestoreService;

trait NotificationTrait
{
    use FirestoreTrait;
    public function CreateNotification($match, $title, $message, $status, $type, $users)
    {
        $tableName = $match->getTable();
        $category = Category::find($match->categoryId);
        $ladder = Ladder::find($match->ladderId);
        foreach ($users as $user) {
            if ($user->id != auth()->id()) {
                $fcmToken = FcmToken::where('userId', $user->id)->latest()->get();

                if ($tableName == "challenges") {
                    $data
                        = new NotificationMatchResource($match, $user->id);
                    $matchType = MatchTypeEnum::CHALLENGE;
                } else if ($tableName == "proposals") {

                    $data
                        = new NotificationMatchResource($match, $user->id);
                    $matchType = MatchTypeEnum::PROPOSAL;
                } else {
                    $data
                        = new NotificationScheduleMatchResource(TeamMatch::find($match->id), $user->id);
                    $matchType = 'MATCH';
                }
                $fcm['title'] = $title;
                $fcm['body'] = $message;
                $fcm['data'] = [
                    'title' => $title,
                    'body' => $message,
                    'type' => $type,
                    'time' => time(),
                    'match' => json_encode($data)
                ];
                $fcmDevice = null;
                if ($fcmToken) {
                    $firebase = new FirebaseService();
                    foreach ($fcmToken as $firebaseToken) {
                        $token =  $firebaseToken->fcmToken;
                        $fcm['FCMRegistrationToken'] = $token;
                        $fcmDevice = $firebaseToken->deviceIdentifier;
                        $firebase->cloudMessageToSingleDevice($fcm);
                    }
                }
                if (!is_null($match)) {
                    if ($match->matchableType == 'PLAYOFF') {
                        $notification = Notification::insert([
                            'userId' => $user->id,
                            'title' => $fcm['title'],
                            'message' => $message,
                            'status' => $status,
                            'notificationType' => $type,
                            'ladderId' => $match->ladderId,
                            'matchId' => $match->id,
                            'matchType' => $matchType,
                            'data' => json_encode($data),
                            'createdBy' => 1,
                            'createdAt' => time()
                        ]);
                    } else {
                        $notification = Notification::insert([
                            'userId' => $user->id,
                            'title' => $fcm['title'],
                            'message' => $message,
                            'status' => $status,
                            'notificationType' => $type,
                            'ladderId' => $match->ladderId,
                            'matchId' => $match->id,
                            'matchType' => $matchType,
                            'data' => json_encode($data),
                            'notificationDevice' =>  $fcmDevice,
                            'createdBy' => Auth()->id(),
                            'createdAt' => time()
                        ]);
                    }
                } else {
                    $notification = Notification::insert([
                        'userId' => $user->id,
                        'title' => $fcm['title'],
                        'message' => $message,
                        'status' => $status,
                        'notificationType' => $type,
                        'notificationDevice' =>  $fcmDevice,
                        'createdBy' => Auth()->id(),
                        'createdAt' => time()
                    ]);
                }
            }
        }
    }
    public function CreatePlayOffsNotification($teamMatch)
    {
        $firestoreService = new FirestoreService();
        if (!is_null($teamMatch)) {
            foreach ($teamMatch as $match) {

                $teamMember = Team::select('firstMemberId', 'secondMemberId')->where('id',  $match->teamOneId)->first();
                $firestoreService->createChatInFirestore('CHAT', null, $teamMember->firstMemberId, $match->id, null);
                $category = Category::find($match->categoryId);
                $ladder = Ladder::find($match->ladderId);
                $notifyUsers = Team::select('firstMemberId', 'secondMemberId')->whereIn('id', [$match->teamTwoId, $match->teamOneId])->get();
                $users = $notifyUsers->pluck('firstMemberId');
                if ($category->name != 'Single') {
                    $users = $users->concat($notifyUsers->pluck('secondMemberId'));
                }
                $users = User::whereIn('id', $users)->get();

                $body = 'Congradulation! You have qualified for ' . ucwords(strtolower(str_replace('_', ' ', $match->playOfType))) . ' in ' . $ladder->name;
                $this->CreateNotification($match, $match->playOfType, $body, 'UNSEEN', 'PLAY_OFF', $users);
            }
        } else {
            return false;
        }
    }

    public function CreateTeamNotification($team, $type)
    {
        if (!is_null($team)) {
            $ladderName = Ladder::find($team->ladderId);

            $users = User::whereIn('id', [$team->firstMemberId]);
            if ($team->secondMemberId != null) {
                $users = User::whereIn('id', [$team->firstMemberId, $team->secondMemberId]);
            }
            $users = $users->get();

            foreach ($users as $user) {
                $fcmToken = FcmToken::where('userId', $user->id)->latest()->get();

                if ($type == 'SUBSCRIPTION') {
                    $notificationTitle = ucwords(strtolower($type));
                    if ($user->id == Auth()->id()) {
                        $msg = "You have successfully purchased " . $ladderName->name;
                    } else {
                        $msg = "You and " . Auth()->user()->fullName . " successfully purchased " . $ladderName->name;
                    }
                } else if ($type == 'PAYMENT_REQUEST') {
                    $notificationTitle = ucwords(strtolower(str_replace('_', ' ', $type)), " ");
                    if ($user->id == $team->firstMemberId) {
                        $msg =  $user->fullName . " successfully purchase " . $ladderName->name;
                    } else {
                        $msg =  $user->fullName . " invites you to pay " . $ladderName->name;
                    }
                }
                $fcm['title'] = $notificationTitle;
                $fcm['body'] = $msg;
                $data = array("title" => $fcm['title'], "type" => $type, 'ladderId' => $team->ladderId, 'userId' => $user->id, 'time' => time());
                $fcm['data'] = [
                    'title' => $notificationTitle,
                    'body' => $msg,
                    'type' => $type,
                    'time' => time(),
                    'data' => json_encode($data)
                ];
                $fcmDevice = null;
                if ($fcmToken) {
                    $firebase = new FirebaseService();
                    foreach ($fcmToken as $firebaseToken) {
                        $token =   $firebaseToken->fcmToken;
                        $fcm['FCMRegistrationToken'] = $token;
                        $fcmDevice = $firebaseToken->deviceIdentifier;
                        $firebase->cloudMessageToSingleDevice($fcm);
                    }
                }
                $notification = Notification::insert([
                    'userId' => $user->id,
                    'title' => $fcm['title'],
                    'message' => $msg,
                    'status' => 'UNSEEN',
                    'notificationType' => $type,
                    'ladderId' => $team->ladderId,
                    'data' => json_encode($data),
                    'notificationDevice' =>  $fcmDevice,
                    'createdBy' => Auth()->id(),
                    'createdAt' => time()
                ]);
            }
        } else {
            return false;
        }
    }
    public function CreateChatNotification($title, $message, $status, $type, $users, $match = null, $issueId = null)
    {
        if ($type == 'ADMIN_CHAT') {
            $chatType = 'ADMIN';
        } elseif ($type == 'SUPPORT_CHAT') {
            $chatType = 'SUPPORT';
        } else {
            $chatType = "CHAT";
        }
        $data = null;
        foreach ($users as $user) {
            if (!is_null($match)) {
                $data = new NotificationScheduleMatchResource(TeamMatch::find($match->id), $user->id);
                $matchType = 'MATCH';
            }
            $fcmToken = FcmToken::where('userId', $user->id)->latest()->get();
            $fcm['title'] = $title;
            $fcm['body'] = $message;
            $fcm['data'] = [
                'title' => $title,
                'body' => $message,
                'type' => $chatType,
                'time' => time(),
                'match' => json_encode($data)
            ];
            $fcmDevice = null;
            if ($fcmToken) {
                $firebase = new FirebaseService();
                foreach ($fcmToken as $firebaseToken) {
                    $token = $firebaseToken->fcmToken;
                    $fcmDevice = $firebaseToken->deviceIdentifier;
                    $fcm['FCMRegistrationToken'] = $token;
                    $firebase->cloudMessageToSingleDevice($fcm);
                }
            }
            if (!is_null($match)) {
                if ($match->matchableType == 'PLAYOFF') {
                    $notification = Notification::insert([
                        'userId' => $user->id,
                        'title' => $fcm['title'],
                        'message' => $message,
                        'status' => $status,
                        'notificationType' => $type,
                        'ladderId' => $match->ladderId,
                        'matchId' => $match->id,
                        'matchType' => $matchType,
                        'createdBy' => 1,
                        'createdAt' => time()
                    ]);
                } else {
                    $notification = Notification::insert([
                        'userId' => $user->id,
                        'title' => $fcm['title'],
                        'message' => $message,
                        'status' => $status,
                        'notificationType' => $type,
                        'ladderId' => $match->ladderId,
                        'matchId' => $match->id,
                        'matchType' => $matchType,
                        'data' => json_encode($data),
                        'notificationDevice' => $fcmDevice,
                        'createdBy' => Auth()->id(),
                        'createdAt' => time()
                    ]);
                }
            } else {
                $notification = Notification::insert([
                    'userId' => $user->id,
                    'title' => $fcm['title'],
                    'message' => $message,
                    'status' => $status,
                    'notificationType' => $type,
                    'notificationDevice' => $fcmDevice,
                    'createdBy' => Auth()->id(),
                    'createdAt' => time()
                ]);
            }
        }
    }
    public function CreateKnockOutNotification($teamMatch, $message)
    {
        $firestoreService = new FirestoreService();
        $teamMember = Team::select('firstMemberId', 'secondMemberId')->where('ladderId', $teamMatch->ladderId)->where('id',  $teamMatch->teamOneId)->first();
        $firestoreService->createChatInFirestore('CHAT', null, $teamMember->firstMemberId, $teamMatch->id, null);
        $category = Category::find($teamMatch->categoryId);
        $notifyUsers = Team::select('firstMemberId', 'secondMemberId')->where('ladderId', $teamMatch->ladderId)->whereIn('id', [$teamMatch->teamTwoId, $teamMatch->teamOneId])->get();
        $users = $notifyUsers->pluck('firstMemberId');
        if ($category->name != 'Single') {
            $users = $users->concat($notifyUsers->pluck('secondMemberId'));
        }
        $users = User::whereIn('id', $users)->get();
        $this->CreateNotification($teamMatch, $teamMatch->playOfType, $message, 'UNSEEN', 'PLAY_OFF', $users);
    }
    public function KnockOutNotification($teamMatch, $message)
    {
        $firestoreService = new FirestoreService();
        $teamMember = Team::select('firstMemberId', 'secondMemberId')->where('ladderId', $teamMatch->ladderId)->where('id',  $teamMatch->teamOneId)->first();
        $firestoreService->createChatInFirestore('CHAT', null, $teamMember->firstMemberId, $teamMatch->id, null);
        $category = Category::find($teamMatch->categoryId);
        $notifyUsers = Team::select('firstMemberId', 'secondMemberId')->where('ladderId', $teamMatch->ladderId)->where('id', $teamMatch->teamOneId)->get();
        $users = $notifyUsers->pluck('firstMemberId');
        if ($category->name != 'Single') {
            $users = $users->concat($notifyUsers->pluck('secondMemberId'));
        }
        $users = User::whereIn('id', $users)->get();
        $this->CreateNotification($teamMatch, $teamMatch->playOfType, $message, 'UNSEEN', 'PLAY_OFF', $users);
    }

    public function notificationForAdmin($title, $message, $status, $type, $user)
    {
        $notification = Notification::insert([
            'userId' => $user->id,
            'title' => $title,
            'message' => $message,
            'status' => $status,
            'notificationType' => $type,
            'createdBy' => auth()->id(),
            'createdAt' => time()
        ]);
    }

    public function CreateAdminChatNotification($title, $message, $status, $type, $user, $issueId = null, $chat)
    {

        if ($type == 'ADMIN_CHAT') {
            $chatType = 'ADMIN';
        } elseif ($type == 'SUPPORT_CHAT') {
            $chatType = 'SUPPORT';
        } else {
            $chatType = "CHAT";
        }
        $data = new ChatResource($chat);
        $fcmToken = FcmToken::where('userId', $user->id)->latest()->get();
        $fcm['title'] = $title;
        $fcm['body'] = $message;
        $fcm['data'] = [
            'title' => $title,
            'body' => $message,
            'type' => $chatType,
            'time' => time(),
            'inboxModel' => json_encode($data)
        ];
        $fcmDevice = null;
        if ($fcmToken) {
            $firebase = new FirebaseService();
            foreach ($fcmToken as $firebaseToken) {
                $token = $firebaseToken->fcmToken;
                $fcm['FCMRegistrationToken'] = $token;
                $fcmDevice = $firebaseToken->deviceIdentifier;
                $firebase->cloudMessageToSingleDevice($fcm);
            }
        }
        $notification = Notification::insert([
            'userId' => $user->id,
            'title' => $fcm['title'],
            'message' => $message,
            'status' => $status,
            'issueId' => $issueId,
            'data' => json_encode($data),
            'notificationType' => $type,
            'notificationDevice' => $fcmDevice,
            'createdBy' => Auth()->id(),
            'createdAt' => time()
        ]);
    }
}
