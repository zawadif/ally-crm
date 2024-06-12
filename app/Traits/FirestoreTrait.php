<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Models\Chat;
use App\Models\User;
use App\Models\Issue;
use App\Models\TeamMatch;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Storage;
use Google\Cloud\Firestore\FirestoreClient;

trait FirestoreTrait
{
    public function getLastMessageInFirestore($chat)
    {
        if (env("APP_ENV") == 'production') {
            $db = new FirestoreClient([
                'projectId' => 'tennisfights-production',
            ]);
        } elseif (env('APP_ENV') == 'acceptance') {
            $db = new FirestoreClient([
                'projectId' => 'tennisfights-acceptance',
            ]);
        } elseif (env('APP_ENV') == 'staging' || env('APP_ENV') == 'local') {
            $db = new FirestoreClient([
                'projectId' => 'tennisfights-staging',
            ]);
        }
        if ($chat->type == 'ADMIN_CHAT' || $chat->type == 'ADMIN_CHAT_ISSUE') {
            return $db->collection('admin_chat')->document($chat->issueId)->collection($chat->adminUId)->document($chat->userUId)->collection('issue_chat')->orderBy('lastMessageTime', 'desc')->limit(1)->documents();
        } elseif ($chat->type == 'CHAT') {
            return $db->collection('Chat')->document($chat->matchId)->collection('match_chat')->orderBy('lastMessageTime', 'desc')->limit(1)->documents();
        } elseif ($chat->type == 'SUPPORT') {
            return $db->collection('support')->document($chat->matchId)->collection($chat->adminUId)->document($chat->userUId)->collection('issue_chat')->orderBy('lastMessageTime', 'desc')->limit(1)->documents();
        }
    }
    public function getSaveLastSeenMessageInFirestore($chat, $adminUID)
    {
        if (env("APP_ENV") == 'production') {
            $db = new FirestoreClient([
                'projectId' => 'tennisfights-production',
            ]);
        } elseif (env('APP_ENV') == 'acceptance') {
            $db = new FirestoreClient([
                'projectId' => 'tennisfights-acceptance',
            ]);
        } elseif (env('APP_ENV') == 'staging' || env('APP_ENV') == 'local') {
            $db = new FirestoreClient([
                'projectId' => 'tennisfights-staging',
            ]);
        }
        $data = null;
        if ($chat->type == 'ADMIN_CHAT' || $chat->type == 'ADMIN_CHAT_ISSUE') {
            $chatLastDoc = $db->collection('admin_chat')->document($chat->issueId)->collection($chat->adminUId)->document($chat->userUId)->collection('issue_chat')->orderBy('lastMessageTime', 'desc')->limit(1)->documents();
            foreach ($chatLastDoc as $chatDoc) {
                $data = $chatDoc->data();
            }
            if ($data) {
                $db->collection('admin_chat')->document($chat->adminUId)->collection('last_seen_message')->document($chat->userUId)->set($data);
            }
        } elseif ($chat->type == 'CHAT') {
            $chatLastDoc = $db->collection('Chat')->document($chat->matchId)->collection('match_chat')->orderBy('lastMessageTime', 'desc')->limit(1)->documents();
            foreach ($chatLastDoc as $chatDoc) {
                $data = $chatDoc->data();
            }
            if ($data) {
                $db->collection('Chat')->document($chat->matchId)->collection('last_seen_message')->document($adminUID)->set($data);
            }
        } elseif ($chat->type == 'SUPPORT') {
            $chatLastDoc = $db->collection('support')->document($chat->matchId)->collection($chat->adminUId)->document($chat->userUId)->collection('issue_chat')->orderBy('lastMessageTime', 'desc')->limit(1)->documents();
            foreach ($chatLastDoc as $chatDoc) {
                $data = $chatDoc->data();
            }
            if ($data) {
                $db->collection('support')->document($chat->matchId)->collection($chat->adminUId)->document($chat->userUId)->collection('last_seen_message')->document($chat->adminUId)->set($data);
            }
        }
    }
    public function createChatInFirestore($type, $adminUID, $userId, $matchId, $issueId)
    {
        if (env("APP_ENV") == 'production') {
            $db = new FirestoreClient([
                'projectId' => 'tennisfights-production',
            ]);
        } elseif (env('APP_ENV') == 'acceptance') {
            $db = new FirestoreClient([
                'projectId' => 'tennisfights-acceptance',
            ]);
        } elseif (env('APP_ENV') == 'staging' || env('APP_ENV') == 'local') {
            $db = new FirestoreClient([
                'projectId' => 'tennisfights-staging',
            ]);
        }
        if ($type == 'ADMIN_CHAT' || $type == 'ADMIN_CHAT_ISSUE') {

            $userDetail = UserDetail::where('userId', $userId)->first();
            if ($type == 'ADMIN_CHAT_ISSUE') {
                $issueTitle = Issue::find($issueId);
                $admin = User::where('uid', $adminUID)->first();
                $currentUser = User::where('id', $userId)->first();
                $message = $db->collection('admin_chat')->document($issueId)->collection($adminUID)->document($currentUser->uid)->collection('issue_chat')->newDocument();

                $message->set([
                    'address' => null,
                    'adminAvatar' => !is_null($admin->avatar) ? Storage::disk('s3')->url($admin->avatar) : asset('img/avatar/maleAvatar.png'),
                    'adminFullName' =>  $admin->fullName,
                    'adminUid' =>  $admin->uid,
                    'chatType' => 'ADMIN_CHAT',
                    'currentUserId' =>  $currentUser->uid,
                    'lastMessage' =>  $issueTitle ? $issueTitle->issue . " : " . $issueTitle->description : null,
                    'lastMessageId' => $message->id(),
                    'lastMessageTime' => time(),
                    'lastSeenUserId' => null,
                    'latitude' => null,
                    'longitude' => null,
                    'matchId' => null,
                    'issueId' => $issueId,
                    'matchStatus' => null,
                    'matchType' => null,
                    'messageType' => "TEXT_MESSAGE",
                    'updatedMatchDate' => time(),
                    'updatedMatchTime' => time(),
                ]);
            } else {
                $message = $db->collection('admin_chat')->document($issueId)->collection($adminUID)->document($userId)->collection('issue_chat')->newDocument();
                $message->set([]);
            }
            $chat = Chat::updateOrCreate([
                'type' => $type,
                'userId' => $userId,
                'adminUId' => $adminUID,
                'issueId' => $issueId
            ], [
                'userUId' => $userDetail->user->uid,
                'regionId' => $userDetail->regionId,
                'inboxId' => null,
                'matchId' => null,
                'issueId' => $issueId,
                'chatId' => null
            ]);
        } elseif ($type == 'CHAT') {
            $message = $db->collection('Chat')->document($matchId)->collection('match_chat')->newDocument([]);
            $message->set([]);
            $userDetail = UserDetail::where('userId', $userId)->first();
            $chat = Chat::updateOrCreate([
                'type' => 'CHAT',
                'matchId' => $matchId,
            ], [
                'userId' => $userId,
                'userUId' => $userDetail->user->uid,
                'adminUId' => null,
                'regionId' => $userDetail->regionId,
                'inboxId' => null,
                'issueId' => null,
                'chatId' => null
            ]);
        } elseif ($type == 'SUPPORT') {

            $issueTitle = Issue::find($issueId);
            $admin = User::where('uid', $adminUID)->first();
            $teamMatch = TeamMatch::find($matchId);
            $userDetail = UserDetail::where('userId', $userId)->first();
            $userUId = $userDetail->user->uid;

            $message = $db->collection('support')->document($matchId)->collection($userUId)->document($adminUID)->collection('issue_chat')->newDocument([]);
            $message->set([]);
            $message = $db->collection('support')->document($matchId)->collection($adminUID)->document($userUId)->collection('issue_chat')->newDocument([]);
            $message->set([
                'address' => $teamMatch->address,
                'adminAvatar' => !is_null($admin->avatar) ? Storage::disk('s3')->url(auth()->user()->avatar) : asset('img/avatar/maleAvatar.png'),
                'adminFullName' =>  $admin->fullName,
                'adminUid' =>  $admin->uid,
                'chatType' => 'SUPPORT',
                'currentUserId' => $userUId,
                'lastMessage' =>  $issueTitle ? $issueTitle->issue . " : " . $issueTitle->description : null,
                'lastMessageId' => $message->id(),
                'lastMessageTime' => time(),
                'lastSeenUserId' => null,
                'latitude' => null,
                'longitude' => null,
                'matchId' => (int)$matchId,
                'matchStatus' => $teamMatch->status,
                'matchType' => $teamMatch->matchableType,
                'messageType' => "TEXT_MESSAGE",
                'updatedMatchDate' => time(),
                'updatedMatchTime' => time(),
            ]);

            $chat = Chat::updateOrCreate([
                'type' => $type,
                'userUId' => $userDetail->user->uid,
                'adminUId' => $adminUID,
                'matchId' => $matchId
            ], [
                'userId' => $userId,
                'regionId' => $userDetail->regionId,
                'inboxId' => null,
                'issueId' => $issueId,
                'chatId' => null
            ]);
        }
    }
    public function deleteChatInFirestore($type)
    {
        if ($type->type == 'ADMIN_CHAT' || $type->type == 'ADMIN_CHAT_ISSUE') {
        } elseif ($type->type == 'CHAT') {
        } elseif ($type->type == 'SUPPORT') {
        }
    }

    public function countUnreadMessagesInFirestoreChats($chat, $adminUId = null)
    {
        $lastSeenMessageId = null;
        $lastSeenMessageCount = 0;
        if (env("APP_ENV") == 'production') {
            $db = new FirestoreClient([
                'projectId' => 'tennisfights-production',
            ]);
        } elseif (env('APP_ENV') == 'acceptance') {
            $db = new FirestoreClient([
                'projectId' => 'tennisfights-acceptance',
            ]);
        } elseif (env('APP_ENV') == 'staging' || env('APP_ENV') == 'local') {
            $db = new FirestoreClient([
                'projectId' => 'tennisfights-staging',
            ]);
        }

        if ($chat->type == 'ADMIN_CHAT' || $chat->type == 'ADMIN_CHAT_ISSUE') {
            $allDocuments = $db->collection('admin_chat')->document($chat->issueId)->collection($chat->adminUId)->document($chat->userUId)->collection('issue_chat')->orderBy('lastMessageTime')->documents();
            $lastSeenDocument = $db->collection('admin_chat')->document($chat->issueId)->collection($chat->adminUId)->document($chat->userUId)->collection('last_seen_message')->documents();
            foreach ($lastSeenDocument as $lastSeenDoc) {
                if ($lastSeenDoc->id() == $adminUId) {
                    $lastSeenMessageId = $lastSeenDoc->data()["lastMessageId"];
                }
            }
        } elseif ($chat->type == 'CHAT') {

            $allDocuments = $db->collection('Chat')->document($chat->matchId)->collection('match_chat')->orderBy('lastMessageTime')->documents();

            $lastSeenDocument = $db->collection('Chat')->document($chat->matchId)->collection('last_seen_message')->documents();
            foreach ($lastSeenDocument as $lastSeenDoc) {
                if ($lastSeenDoc->id() == $adminUId) {
                    $lastSeenMessageId = $lastSeenDoc->data()["lastMessageId"];
                }
            }
        } elseif ($chat->type == 'SUPPORT') {
            $allDocuments = $db->collection('support')->document($chat->matchId)->collection($chat->adminUId)->document($chat->userUId)->collection('issue_chat')->orderBy('lastMessageTime')->documents();

            $lastSeenDocument = $db->collection('support')->document($chat->matchId)->collection($chat->adminUId)->document($chat->userUId)->collection('last_seen_message')->documents();
            foreach ($lastSeenDocument as $lastSeenDoc) {
                if ($lastSeenDoc->id() == $adminUId) {
                    $lastSeenMessageId = $lastSeenDoc->data()["lastMessageId"];
                }
            }
        }
        $startCount = false;
        foreach ($allDocuments as $document) {
            if ($startCount) {
                $lastSeenMessageCount = $lastSeenMessageCount + 1;
            }
            if ($document['lastMessageId'] == $lastSeenMessageId) {
                $startCount = true;
            }
        }
        return  $lastSeenMessageCount;
    }
}
