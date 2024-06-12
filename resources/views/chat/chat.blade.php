@extends('layouts.master')
@section('title', 'Chat')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style class="cp-pen-styles">
        li {
            list-style-type: none;

        }

        .score {
            font-size: 10px;
            margin-top: 30px;
            margin-left: 49px;
            position: absolute;
        }

        .active {
            border-bottom: 1px solid #51337a;
        }

        .messagecounter {
            padding: 2px;
            border-radius: 15px;
            font-size: 10px;
            padding-right: 6px;
            padding-bottom: 4px;
            padding-top: 4px;
            padding-left: 6px;
        }
        .nav-link a{
            color: black !important;
        }
        a{
            color: #000 !important;
        }
        .nav-link > a a:active {
            color: #51337a !important;
        }
        #frame {
            width: 95%;
            min-width: 360px;
            max-width: 1000px;
            height: 92vh;
            min-height: 300px;
            max-height: 720px;
            background: #E6EAEA;
        }

        @media screen and (max-width: 360px) {
            #frame {
                width: 100%;
                height: 100vh;
            }
        }

        #frame #sidepanel {
            float: left;
            min-width: 280px;
            max-width: 340px;
            width: 40%;
            height: 100%;
            background: white;

            overflow: hidden;
            position: relative;
        }

        @media screen and (max-width: 735px) {
            #frame #sidepanel {
                width: 58px;
                min-width: 58px;
            }
        }

        #frame #sidepanel #profile {
            width: 80%;
            margin: 25px auto;
        }

        @media screen and (max-width: 735px) {
            #frame #sidepanel #profile {
                width: 100%;
                margin: 0 auto;
                padding: 5px 0 0 0;
                background: #32465a;
            }
        }

        #frame #sidepanel #profile.expanded .wrap {
            height: 210px;
            line-height: initial;
        }

        #frame #sidepanel #profile.expanded .wrap p {
            margin-top: 20px;
        }

        #frame #sidepanel #profile.expanded .wrap i.expand-button {
            -moz-transform: scaleY(-1);
            -o-transform: scaleY(-1);
            -webkit-transform: scaleY(-1);
            transform: scaleY(-1);
            filter: FlipH;
            -ms-filter: "FlipH";
        }

        #frame #sidepanel #profile .wrap {
            height: 60px;
            line-height: 60px;
            overflow: hidden;
            -moz-transition: 0.3s height ease;
            -o-transition: 0.3s height ease;
            -webkit-transition: 0.3s height ease;
            transition: 0.3s height ease;
        }

        @media screen and (max-width: 735px) {
            #frame #sidepanel #profile .wrap {
                height: 55px;
            }
        }

        #frame #sidepanel #profile .wrap img {
            width: 50px;
            border-radius: 50%;
            padding: 3px;
            border: 2px solid #e74c3c;
            height: auto;
            float: left;
            cursor: pointer;
            -moz-transition: 0.3s border ease;
            -o-transition: 0.3s border ease;
            -webkit-transition: 0.3s border ease;
            transition: 0.3s border ease;
        }

        @media screen and (max-width: 735px) {
            #frame #sidepanel #profile .wrap img {
                width: 40px;
                margin-left: 4px;
            }
        }

        #frame #sidepanel #profile .wrap img.online {
            border: 2px solid #2ecc71;
        }

        #frame #sidepanel #profile .wrap img.away {
            border: 2px solid #f1c40f;
        }

        #frame #sidepanel #profile .wrap img.busy {
            border: 2px solid #e74c3c;
        }

        #frame #sidepanel #profile .wrap img.offline {
            border: 2px solid #95a5a6;
        }

        #frame #sidepanel #profile .wrap p {
            float: left;
            margin-left: 15px;
        }

        @media screen and (max-width: 735px) {
            #frame #sidepanel #profile .wrap p {
                display: none;
            }
        }

        #frame #sidepanel #profile .wrap i.expand-button {
            float: right;
            margin-top: 23px;
            font-size: 0.8em;
            cursor: pointer;
            color: #435f7a;
        }

        @media screen and (max-width: 735px) {
            #frame #sidepanel #profile .wrap i.expand-button {
                display: none;
            }
        }

        #frame #sidepanel #profile .wrap #status-options {
            position: absolute;
            opacity: 0;
            visibility: hidden;
            width: 150px;
            margin: 70px 0 0 0;
            border-radius: 6px;
            z-index: 99;
            line-height: initial;
            background: #435f7a;
            -moz-transition: 0.3s all ease;
            -o-transition: 0.3s all ease;
            -webkit-transition: 0.3s all ease;
            transition: 0.3s all ease;
        }

        @media screen and (max-width: 735px) {
            #frame #sidepanel #profile .wrap #status-options {
                width: 58px;
                margin-top: 57px;
            }
        }

        #frame #sidepanel #profile .wrap #status-options.active {
            opacity: 1;
            visibility: visible;
            margin: 75px 0 0 0;
        }

        @media screen and (max-width: 735px) {
            #frame #sidepanel #profile .wrap #status-options.active {
                margin-top: 62px;
            }
        }

        #frame #sidepanel #profile .wrap #status-options:before {
            content: '';
            position: absolute;
            width: 0;
            height: 0;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-bottom: 8px solid #435f7a;
            margin: -8px 0 0 24px;
        }

        @media screen and (max-width: 735px) {
            #frame #sidepanel #profile .wrap #status-options:before {
                margin-left: 23px;
            }
        }

        #frame #sidepanel #profile .wrap #status-options ul {
            overflow: hidden;
            border-radius: 6px;
        }

        #frame #sidepanel #profile .wrap #status-options ul li {
            padding: 15px 0 30px 18px;
            display: block;
            cursor: pointer;
        }

        @media screen and (max-width: 735px) {
            #frame #sidepanel #profile .wrap #status-options ul li {
                padding: 15px 0 35px 22px;
            }
        }

        #frame #sidepanel #profile .wrap #status-options ul li:hover {
            background: #496886;
        }

        #frame #sidepanel #profile .wrap #status-options ul li span.status-circle {
            position: absolute;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin: 5px 0 0 0;
        }

        @media screen and (max-width: 735px) {
            #frame #sidepanel #profile .wrap #status-options ul li span.status-circle {
                width: 14px;
                height: 14px;
            }
        }

        #frame #sidepanel #profile .wrap #status-options ul li span.status-circle:before {
            content: '';
            position: absolute;
            width: 14px;
            height: 14px;
            margin: -3px 0 0 -3px;
            background: transparent;
            border-radius: 50%;
            z-index: 0;
        }

        @media screen and (max-width: 735px) {
            #frame #sidepanel #profile .wrap #status-options ul li span.status-circle:before {
                height: 18px;
                width: 18px;
            }
        }

        #frame #sidepanel #profile .wrap #status-options ul li p {
            padding-left: 12px;
        }

        @media screen and (max-width: 735px) {
            #frame #sidepanel #profile .wrap #status-options ul li p {
                display: none;
            }
        }

        #frame #sidepanel #profile .wrap #status-options ul li#status-online span.status-circle {
            background: #2ecc71;
        }

        #frame #sidepanel #profile .wrap #status-options ul li#status-online.active span.status-circle:before {
            border: 1px solid #2ecc71;
        }

        #frame #sidepanel #profile .wrap #status-options ul li#status-away span.status-circle {
            background: #f1c40f;
        }

        #frame #sidepanel #profile .wrap #status-options ul li#status-away.active span.status-circle:before {
            border: 1px solid #f1c40f;
        }

        #frame #sidepanel #profile .wrap #status-options ul li#status-busy span.status-circle {
            background: #e74c3c;
        }

        #frame #sidepanel #profile .wrap #status-options ul li#status-busy.active span.status-circle:before {
            border: 1px solid #e74c3c;
        }

        #frame #sidepanel #profile .wrap #status-options ul li#status-offline span.status-circle {
            background: #95a5a6;
        }

        #frame #sidepanel #profile .wrap #status-options ul li#status-offline.active span.status-circle:before {
            border: 1px solid #95a5a6;
        }

        #frame #sidepanel #profile .wrap #expanded {
            padding: 100px 0 0 0;
            display: block;
            line-height: initial !important;
        }

        #frame #sidepanel #profile .wrap #expanded label {
            float: left;
            clear: both;
            margin: 0 8px 5px 0;
            padding: 5px 0;
        }

        #frame #sidepanel #profile .wrap #expanded input {
            border: none;
            margin-bottom: 6px;
            background: #32465a;
            border-radius: 3px;
            color: #f5f5f5;
            padding: 7px;
            width: calc(100% - 43px);
        }

        #frame #sidepanel #profile .wrap #expanded input:focus {
            outline: none;
            background: #435f7a;
        }

        #frame #sidepanel #search {
            border-top: 1px solid #32465a;
            border-bottom: 1px solid #32465a;
            font-weight: 300;
        }

        @media screen and (max-width: 735px) {
            #frame #sidepanel #search {
                display: none;
            }
        }

        #frame #sidepanel #search label {
            position: absolute;
            margin: 10px 0 0 20px;
        }

        #frame #sidepanel #search input {
            font-family: "proxima-nova", "Source Sans Pro", sans-serif;
            padding: 10px 0 10px 46px;
            width: calc(100% - 25px);
            border: none;
            background: #32465a;
            color: #f5f5f5;
        }

        #frame #sidepanel #search input:focus {
            outline: none;
            background: #435f7a;
        }

        #frame #sidepanel #search input::-webkit-input-placeholder {
            color: #f5f5f5;
        }

        #frame #sidepanel #search input::-moz-placeholder {
            color: #f5f5f5;
        }

        #frame #sidepanel #search input:-ms-input-placeholder {
            color: #f5f5f5;
        }

        #frame #sidepanel #search input:-moz-placeholder {
            color: #f5f5f5;
        }

        #frame #sidepanel #contacts {
            height: calc(100% - 80px);
            overflow-y: scroll;
            overflow-x: hidden;
        }

        @media screen and (max-width: 735px) {
            #frame #sidepanel #contacts {
                height: calc(100% - 60px);
                overflow-y: scroll;
                overflow-x: hidden;
            }

            #frame #sidepanel #contacts::-webkit-scrollbar {
                display: none;
            }
        }

        #frame #sidepanel #contacts.expanded {
            height: calc(100% - 334px);
        }

        #frame #sidepanel #contacts::-webkit-scrollbar {
            width: 8px;
            background: #2c3e50;
        }

        #frame #sidepanel #contacts::-webkit-scrollbar-thumb {
            background-color: #243140;
        }

        #frame #sidepanel #contacts ul li.contact {
            position: relative;
            padding: 10px 0 15px 0;
            font-size: 0.9em;
            cursor: pointer;
        }

        @media screen and (max-width: 735px) {
            #frame #sidepanel #contacts ul li.contact {
                padding: 6px 0 46px 8px;
            }
        }

        #frame #sidepanel #contacts ul li.contact:hover {
            background: #51337a;
            color: #f5f5f5;
        }

        #frame #sidepanel #contacts ul li.contact.active {
            background: #51337a;
            color: #f5f5f5;

        }

        #frame #sidepanel #contacts ul li.contact.active span.contact-status {
            border: 2px solid #32465a !important;
        }

        #frame #sidepanel #contacts ul li.contact .wrap {
            width: 88%;
            margin: 0 auto;
            position: relative;
        }

        @media screen and (max-width: 735px) {
            #frame #sidepanel #contacts ul li.contact .wrap {
                width: 100%;
            }
        }

        #frame #sidepanel #contacts ul li.contact .wrap span {
            position: absolute;
            left: 0;
            margin: -2px 0 0 -2px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            border: 2px solid #2c3e50;
            background: #95a5a6;
        }

        #frame #sidepanel #contacts ul li.contact .wrap span.online {
            background: #2ecc71;
        }

        #frame #sidepanel #contacts ul li.contact .wrap span.away {
            background: #f1c40f;
        }

        #frame #sidepanel #contacts ul li.contact .wrap span.busy {
            background: #e74c3c;
        }

        #frame #sidepanel #contacts ul li.contact .wrap img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            float: left;
            margin-right: 10px;
        }

        @media screen and (max-width: 735px) {
            #frame #sidepanel #contacts ul li.contact .wrap img {
                margin-right: 0px;
            }
        }

        #frame #sidepanel #contacts ul li.contact .wrap .meta {
            padding: 5px 0 0 0;
        }

        @media screen and (max-width: 735px) {
            #frame #sidepanel #contacts ul li.contact .wrap .meta {
                display: none;
            }
        }

        #frame #sidepanel #contacts ul li.contact .wrap .meta .name {
            font-weight: 600;
        }

        #frame #sidepanel #contacts ul li.contact .wrap .meta .preview {
            margin: 5px 0 0 0;
            padding: 0 0 1px;
            font-weight: 400;
            white-space: nowrap;

            text-overflow: ellipsis;
            -moz-transition: 1s all ease;
            -o-transition: 1s all ease;
            -webkit-transition: 1s all ease;
            transition: 1s all ease;
        }

        #frame #sidepanel #contacts ul li.contact .wrap .meta .preview span {
            position: initial;
            border-radius: initial;
            background: none;
            border: none;
            padding: 0 2px 0 0;
            margin: 0 0 0 1px;
            opacity: .5;
        }

        #frame #sidepanel #bottom-bar {
            position: absolute;
            width: 100%;
            bottom: 0;
        }

        #frame #sidepanel #bottom-bar button {
            float: left;
            border: none;
            width: 50%;
            padding: 10px 0;
            background: #32465a;
            color: #f5f5f5;
            cursor: pointer;
            font-size: 0.85em;
            font-family: "proxima-nova", "Source Sans Pro", sans-serif;
        }

        @media screen and (max-width: 735px) {
            #frame #sidepanel #bottom-bar button {
                float: none;
                width: 100%;
                padding: 15px 0;
            }
        }

        #frame #sidepanel #bottom-bar button:focus {
            outline: none;
        }

        #frame #sidepanel #bottom-bar button:nth-child(1) {
            border-right: 1px solid #2c3e50;
        }

        @media screen and (max-width: 735px) {
            #frame #sidepanel #bottom-bar button:nth-child(1) {
                border-right: none;
                border-bottom: 1px solid #2c3e50;
            }
        }

        #frame #sidepanel #bottom-bar button:hover {
            background: #435f7a;
        }

        #frame #sidepanel #bottom-bar button i {
            margin-right: 3px;
            font-size: 1em;
        }

        @media screen and (max-width: 735px) {
            #frame #sidepanel #bottom-bar button i {
                font-size: 1.3em;
            }
        }

        @media screen and (max-width: 735px) {
            #frame #sidepanel #bottom-bar button span {
                display: none;
            }
        }

        #frame .content {
            float: right;
            width: 60%;
            height: 100%;
            overflow: hidden;
            position: relative;
        }

        @media screen and (max-width: 735px) {
            #frame .content {
                width: calc(100% - 58px);
                min-width: 300px !important;
            }
        }

        @media screen and (min-width: 900px) {
            #frame .content {
                width: calc(100% - 340px);
            }
        }

        #frame .content .contact-profile {
            width: 100%;
            height: 60px;
            line-height: 40px;
            background: #51337a;
        }

        #frame .content .contact-profile img {
            width: 40px;
            border-radius: 50%;
            float: left;
            margin: 9px 12px 0 9px;
        }

        #frame .content .contact-profile p {
            float: left;
        }

        #frame .content .contact-profile .social-media {
            float: right;
        }

        #frame .content .contact-profile .social-media i {
            margin-left: 14px;
            cursor: pointer;
        }

        #frame .content .contact-profile .social-media i:nth-last-child(1) {
            margin-right: 20px;
        }

        #frame .content .contact-profile .social-media i:hover {
            color: #435f7a;
        }

        #frame .content .messages {
            height: auto;
            min-height: calc(100% - 93px);
            max-height: calc(100% - 93px);
            overflow-y: scroll;
            overflow-x: hidden;
            width: 100%;
            background-color: white;
        }

        @media screen and (max-width: 735px) {
            #frame .content .messages {
                max-height: calc(100% - 105px);
            }
        }

        #frame .content .messages::-webkit-scrollbar {
            width: 8px;
            background: transparent;
        }

        #frame .content .messages::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.3);
        }

        #frame .content .messages ul li {
            display: inline-block;
            clear: both;
            float: left;
            margin: 15px 15px 5px 0px;
            width: calc(100% - 25px);
            font-size: 0.9em;
        }

        #frame .content .messages ul li:nth-last-child(1) {
            margin-bottom: 20px;
        }

        #frame .content .messages ul li.sent img {
            float: right;
            margin: 6px 8px 0 0;
        }

        #frame .content .messages ul li.sent p {
            background: #F3F7F8;
            color: black;
            float: right;
        }

        #frame .content .messages ul li.replies img {
            float: left;
            margin: 6px 0 0 8px;
        }

        #frame .content .messages ul li.replies p {
            background: #f5f5f5;
            float: left;
        }

        #frame .content .messages ul li img {
            width: 22px;
            border-radius: 50%;
            float: left;
        }

        #frame .content .messages ul li p {
            display: inline-block;
            padding: 10px 15px;
            border-radius: 5px;
            max-width: 205px;
            line-height: 130%;
        }

        @media screen and (min-width: 735px) {
            #frame .content .messages ul li p {
                max-width: 300px;
            }
        }

        #frame .content .message-input {
            position: absolute;
            bottom: 8px;
            width: 100%;
            z-index: 99;
            background-color: white;

        }

        #frame .content .message-input .wrap {
            position: relative;
        }

        #frame .content .message-input .wrap input {
            font-family: "proxima-nova", "Source Sans Pro", sans-serif;
            float: left;
            border-radius: 5px;
            width: calc(100% - 90px);
            padding: 11px 32px 10px 8px;
            margin-left: 10px;
            font-size: 0.8em;
            color: #32465a;
        }

        @media screen and (max-width: 735px) {
            #frame .content .message-input .wrap input {
                padding: 15px 32px 16px 8px;
            }
        }

        #frame .content .message-input .wrap input:focus {
            outline: none;
        }

        #frame .content .message-input .wrap .attachment {
            position: absolute;
            right: 60px;
            z-index: 4;
            margin-top: 10px;
            font-size: 1.1em;
            color: #435f7a;
            opacity: .5;
            cursor: pointer;
        }

        @media screen and (max-width: 735px) {
            #frame .content .message-input .wrap .attachment {
                margin-top: 17px;
                right: 65px;
            }
        }

        #frame .content .message-input .wrap .attachment:hover {
            opacity: 1;
        }

        #frame .content .message-input .wrap button {

            border: none;
            width: 50px;
            padding: 0px 0;
            cursor: pointer;
            background: #32465a;
            color: #f5f5f5;
        }

        @media screen and (max-width: 735px) {
            #frame .content .message-input .wrap button {
                padding: 16px 0;
            }
        }

        #frame .content .message-input .wrap button:hover {
            background: #435f7a;
        }

        #frame .content .message-input .wrap button:focus {
            outline: none;
        }
    </style>
    @php
        use App\Helpers\Helper;
        $customHelper =  new Helper;
    @endphp

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">

            <!-- Main content -->
            <section class="content px-4">

                <!-- Default box -->



                <div class="border">

                <br>
                <!-- Nav tabs -->
                <ul class="nav mt-n2" role="tablist">
                    <li class="nav-item ">
                        <a class="nav-link active userChat" data-toggle="tab" href=".home" >Chats @if(auth()->user()->id != 1) <sup class="text-white bg-danger messagecounter totalChatUnreadCount rounded-circle">{{$adminChatUnreadCount+$chatUnreadCount}}</sup> @endif</a>
                    </li>
                    <li class="nav-item matchDisputes" data-disputedChatType="Single">
                        <a class="nav-link" data-toggle="tab" href=".home">Match Disputes @if(auth()->user()->id != 1) <sup class="text-white bg-danger messagecounter totalDisputeUnreadCount  rounded-circle">{{$supportSingleUnreadCount + $supportDoubleUnreadCount}}</sup> @endif</a>
                    </li>
                    <li class="nav-item support">
                        <a class="nav-link " data-toggle="tab" href=".home">Customer Support @if(auth()->user()->id != 1) <sup class="text-white bg-danger messagecounter totalSupportUnreadCount rounded-circle">{{$adminChatIssueUnreadCount}}</sup> @endif</a>
                    </li>
                </ul>
                <hr class="" style="margin-top: -2px;">
                <!-- Tab panes -->
                <div class="tab-content">

                        <div class=" tab-pane active home"><br>
                            <h4 class="heading"></h4>
                            <div class="ml-2 mb-4 button-div d-flex">
                                <h4 class="pt-1">Chats</h4>
                                @if(auth()->user()->id != 1)
                                <button type="button" class="btn btn-outline-secondary bg-success ml-3 homeNewChat mb-1"
                                    data-toggle="modal" data-target="#myModal">+ New chats</button>
                                @endif
                            </div>
                            <div id="frame">
                                <div id="sidepanel" class="border-top border-right">

                                <div id="contacts">
                                    <div class=" bg-white ml-2  pt-3 pb-2 chats" >
                                        <button type="button" class="btn btn-outline-secondary bg-success rounded-pill userChat userChatsButton">User chats</button>@if(auth()->user()->id != 1)<sup class="text-white bg-danger messagecounter mt-4 userChatUnreadCount rounded-circle">{{$chatUnreadCount}}</sup>@endif
                                        <button type="button" class="btn btn-outline-secondary rounded-pill ml-2 adminChat adminChatsButton">Admin chats</button>@if(auth()->user()->id != 1)<sup class="text-white bg-danger messagecounter mt-4 adminChatUnreadCount rounded-circle">{{$adminChatUnreadCount}}</sup>@endif
                                    </div>
                                    <ul style="padding-inline-start: 0px;" class="chatSidebar" id="sidebar">
                                        @foreach($userChat as $chatted_users)


                                        <li class="contact active userChatMessages" data-chatNumber="{{$loop->iteration}}" data-chatid="{{$chatted_users->id}}" data-id="{{$chatted_users->matchId}}">
                                            <div class="d-flex ml-1">
                                            <div class="wrap d-flex">
                                                <input type="hidden" name="userChatLaddder" id="userChatLaddder" value="{{$chatted_users->match->ladder->name}}">
                                                @php
                                                $firstMemberProfileURL=$customHelper->getUserImage($chatted_users->match->firstTeam->getFirstMember->id);
                                                @endphp
                                                <img class="img{{$loop->iteration}}" src="{{$firstMemberProfileURL}}" height="30px" width="30px" alt="" />

                                                <div class="meta">
                                                    <p class="name1{{$loop->iteration}}">{{$chatted_users->match->firstTeam->getFirstMember->fullName}}</p>

                                                            </div>

                                                        </div>
                                                        <div class="meta mt-2 pr-sm-5 pr-md-4 pr-lg-4 pr-xl-5">
                                                            <p class="name">VS</p>
                                                        </div>

                                            <div class="wrap mr-1">
                                                @php
                                                    $secondMemberProfileURL =$customHelper->getUserImage($chatted_users->match->secondTeam->getFirstMember->id);
                                                @endphp

                                                <img class="img1{{$loop->iteration}}" src="{{$secondMemberProfileURL}}" height="30px" width="30px" alt="" />
                                                <div class="meta">
                                                    <p class="name2{{$loop->iteration}}">{{$chatted_users->match->secondTeam->getFirstMember->fullName}}</p>

                                                </div>
                                            </div>
                                            </div>
                                            @if(isset($chatted_users->match->firstTeam->getSecondMember))
                                            <div class="d-flex ml-1">
                                                <div class="wrap d-flex">
                                                    @php
                                                        $thirdMemberProfileURL = $customHelper->getUserImage($chatted_users->match->firstTeam->getSecondMember->id);

                                                    @endphp

                                                    <img class="img2{{$loop->iteration}}" src="{{$thirdMemberProfileURL}}" alt="" height="30px" width="30px" />

                                                                <div class="meta">
                                                                    <p class="name3{{ $loop->iteration }}">
                                                                        {{ $chatted_users->match->firstTeam->getSecondMember->fullName }}
                                                                    </p>

                                                                </div>

                                                            </div>
                                                            <div class="meta mt-2 pr-sm-5 pr-md-4 pr-lg-4 pr-xl-5">
                                                            </div>

                                                <div class="wrap mr-1">
                                                    @php
                                                        $fourthMemberProfileURL =$customHelper->getUserImage($chatted_users->match->secondTeam->getSecondMember->id);

                                                   @endphp

                                                    <img class="img3{{$loop->iteration}}" src="{{$fourthMemberProfileURL}}" alt="" height="30px" width="30px" />
                                                    <div class="meta">
                                                        <p class="name4{{$loop->iteration}}">{{$chatted_users->match->secondTeam->getSecondMember->fullName}}</p>

                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                           <div> <p class="preview ml-1 d-none">{{$chatted_users->lastMessage}}</p></div>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>

                            </div>
                            <div class="content bg-white border-right">
                                <input type="hidden"   name="sendid" class="senderId" value="">
                                <input type="hidden"   name="issueId" class="adminissueId" value="">
                                <input type="hidden"   name="supportUserId" class="supportUserId" value="">
                                <input type="hidden"   name="supportUserUId" class="supportUserUId" value="">
                                <input type="hidden"   name="sendMessageChatType" class="sendMessageChatType" value="">
                                <input type="hidden"   name="matchType" class="matchType" value="">
                                <div class="contact-profile text-white">
                                    <div class="setProfile d-none">
                                    </div>
                                </div>
                                <div class="messages  border-right" >
                                    <ul class="chatMessages">

                                        </ul>
                                    </div>

                                <input type="hidden" name="disputeUserId" id="disputeUserId" class="disputeUserId" value="">
                                @if(auth()->user()->id != 1)
                                <div class="message-input d-none" >

                                        <div class="wrap">
                                            <input type="text" placeholder="Write your message..." />
                                            <button class="submit bg-white"><i class="fa fa-paper-plane"
                                                    style="color: #51337a;" aria-hidden="true"></i></button>
                                        </div>
                                    </div>
                                @endif
                                </div>
                            </div>

                        </div>

                    </div>
                </div>



                <!-- The Modal -->
                <div class="modal" id="myModal" data-backdrop="false">
                    <div class="modal-dialog ">
                        <div class="modal-content container">

                            <!-- Modal Header -->
                            <div class="modal-header">
                                <h4 class="modal-title">New Chat</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>

                            <!-- Modal body -->
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">Select User</label>
                                    <select class="form-control" id="userId">
                                        <option>Select user to chat with</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->fullName }}</option>
                                        @endforeach

                                    </select>
                                    <span id="errorUser" class="text-danger d-none">Please select user to chat</span>
                                </div>
                                <div class="form-group">
                                    <label for="exampleFormControlInput1">Write you message</label>
                                    <textarea type="text" class="form-control" id="startMessage" style="height: 100px;"
                                        placeholder="Write the message"></textarea>
                                    <span id="errorMessage" class="text-danger d-none">Please enter message</span>
                                </div>
                            </div>

                            <!-- Modal footer -->
                            <div class="">
                                <button type="button" id="startChat" class="btn btn-success float-right mb-2">Start
                                    Chat</button>
                                <button type="button" id="closeModal" class="btn btn-white border float-right mb-2 mr-2"
                                    data-dismiss="modal">Cancel</button>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- /.card -->
                <!-- /view score modal -->

            <div class="modal " id="scoreModal" data-backdrop="false" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content container">

                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h4 class="modal-title">Match Score</h4>
                        </div>

                            <!-- Modal body -->
                            <div class="modal-body" id="matchScoreDetail">

                        </div>
                        <!-- Modal footer -->
                        <div class="">
                            <button type="button"  value="submit" class="btn btn-success float-right mb-2" id="editScore">Edit Score</button>
                            <button type="button"  onclick="closeDialog()" class="btn btn-white border float-right mb-2 mr-2" data-dismiss="modal">Go Back</button>
                        </div>

                        </div>
                    </div>
                </div>
                <!-- /end view score modal -->

            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

    <script src='//production-assets.codepen.io/assets/common/stopExecutionOnTimeout-b2a7b3fe212eaa732349046d8416e00a9dec26eb7fd347590fbced3ab38af52e.js'></script>
    <script src='https://code.jquery.com/jquery-2.2.4.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js"></script>
    {{-- <script type="module" src="{{asset('/js/firestore.js')}}"></script> --}}
    <script type="text/javascript">

     function closeDialog(){
     $('#scoreModal').hide();
     }


    </script>
    <script type="module" >
        // Import the functions you need from the SDKs you need
        import { initializeApp } from "https://www.gstatic.com/firebasejs/9.1.1/firebase-app.js";
        import { getFirestore } from "https://www.gstatic.com/firebasejs/9.1.1/firebase-firestore.js"
        import { collection, getDocs,doc, addDoc, Timestamp } from "https://www.gstatic.com/firebasejs/9.1.1/firebase-firestore.js"
        import { query, orderBy, limit, where, onSnapshot, startAfter,startAt } from "https://www.gstatic.com/firebasejs/9.1.1/firebase-firestore.js"
        // import {getCountFromServer} from "https://www.gstatic.com/firebasejs/9.1.1/firebase-firestore.js";
        // TODO: Add SDKs for Firebase products that you want to use
        // https://firebase.google.com/docs/web/setup#available-libraries

        // TODO: Replace the following with your app's Firebase project configuration
        let userChatGlobalCount=0;
        let adminChatGlobalCount=0;
        let unsubscribe = null;
        let unsubscribeCount =  null;
        var singleMatchDisputeCount = 0;
        var doubleMatchDisputeCount = 0;

        var firebaseConfig = null;
        if("{{env('APP_ENV')}}"  == "local" || "{{env('APP_ENV')}}"  == 'staging'){
            firebaseConfig = {
                apiKey: "AIzaSyALQDXfgBdo4uExuY3mt6Ewe9q19RxBnlo",
                authDomain: "tennisfights-staging.firebaseapp.com",
                projectId: "tennisfights-staging",
                storageBucket: "tennisfights-staging.appspot.com",
                messagingSenderId: "484053911200",
                appId: "1:484053911200:web:62a8d129798c1938b7ebc5",
                measurementId: "G-HLF2R606N7"
            };
        }if("{{env('APP_ENV')}}"  == 'acceptance'){
            firebaseConfig = {
                apiKey: "AIzaSyApDuPfldGwb-99hv8VYobhvHovixCSbX0",
                authDomain: "tennisfights-acceptance.firebaseapp.com",
                projectId: "tennisfights-acceptance",
                storageBucket: "tennisfights-acceptance.appspot.com",
                messagingSenderId: "612006749160",
                appId: "1:612006749160:web:bdd1576e7f159c25b2ba5d",
                measurementId: "G-X30BRB2S57"
            };
        }if("{{env('APP_ENV')}}"  == 'production'){
            firebaseConfig = {
                apiKey: "AIzaSyAvNFJpmJv31u81dFvgeo60UMARhl4hozs",
                authDomain: "tennisfights-production.firebaseapp.com",
                projectId: "tennisfights-production",
                storageBucket: "tennisfights-production.appspot.com",
                messagingSenderId: "925561948846",
                appId: "1:925561948846:web:cc63d3a1087c61d07f7b3f",
                measurementId: "G-10LGTQL9QJ"
            };
        }

        const app = initializeApp(firebaseConfig);
        const db = getFirestore(app);
         let adminChatIssueChats={!!json_encode($adminChatIssueChats)!!};
         let userChat={!!json_encode($userChat)!!};
         let adminChats={!!json_encode($adminChats)!!};
         let supportSingleChats={!!json_encode($supportSingleChats)!!};
         let supportDoubleChats={!!json_encode($supportDoubleChats)!!};


        $('.messages').scrollTop($('.messages')[0].scrollHeight);

        $("#profile-img").click(function() {
            $("#status-options").toggleClass("active");
        });

        $(".expand-button").click(function() {
            $("#profile").toggleClass("expanded");
            $("#contacts").toggleClass("expanded");
        });

        $("#status-options ul li").click(function() {
            $("#profile-img").removeClass();
            $("#status-online").removeClass("active");
            $("#status-away").removeClass("active");
            $("#status-busy").removeClass("active");
            $("#status-offline").removeClass("active");
            $(this).addClass("active");

            if($("#status-online").hasClass("active")) {
                $("#profile-img").addClass("online");
            } else if ($("#status-away").hasClass("active")) {
                $("#profile-img").addClass("away");
            } else if ($("#status-busy").hasClass("active")) {
                $("#profile-img").addClass("busy");
            } else if ($("#status-offline").hasClass("active")) {
                $("#profile-img").addClass("offline");
            } else {
                $("#profile-img").removeClass();
            };
            $("#status-options").removeClass("active");
        });
        function newMessage() {
           var message = $(".message-input input").val();
           var senderId = $(".senderId").val();
           var sendMessageChatType = $(".sendMessageChatType").val();
            if($.trim(message) == '') {
                return false;
            }
            if(sendMessageChatType=='matchChat'){
                var disputeuserid = $("#disputeUserId").val();
                var matchType = $(".matchType").val();
                sendDisputedChatMessage(disputeuserid, matchType,senderId,message);
            }else if(sendMessageChatType=='support'){
                var userid = $(".supportUserId").val();
                var useruid = $(".supportUserUId").val();
                sendSupportMessage(senderId,userid,message)
            }else{
                var issueId=$(".adminissueId").val();
                createChat(senderId,message,issueId);
            }
            $('.message-input input').val(null);
            $('.contact.active .preview').html('<span>You: </span>' + message);
            $(".messages").animate({ scrollTop: $(document).height() }, "fast");
        };
        $('.submit').click(function() {
            newMessage();
        });

        $(window).on('keydown', function(e) {
            if (e.which == 13) {
                newMessage();
                return false;
            }
        });
        $(document).ready(function(){ /*code here*/
            $(".chatSidebar .userChatMessages:first").click();
         });
        ///get user chat
        $(document).on('click','.userChat',function () {
            $('.heading').html("");
            $('.userChat').css('color','#51337a')
            $('.heading').removeClass("mb-5");
            $('.button-div').removeClass('d-none');
            $('.button-div').addClass('d-flex');
          $('.homeChat').addClass('bg-success');
            $('.homeNewChat').removeClass('d-none');
            let  appnedHTml = "";
            let adminuserid={!!json_encode(auth()->user()->id)!!};
            if(adminuserid == 1){
                appnedHTml += '<button type="button" class="btn btn-outline-secondary bg-success rounded-pill userChat userChatsButton">User chats</button>'
            appnedHTml += '<button type="button" class="btn btn-outline-secondary rounded-pill ml-2 adminChat adminChatsButton">Admin chats</button>'
            }else{
                appnedHTml += '<button type="button" class="btn btn-outline-secondary bg-success rounded-pill userChat userChatsButton">User chats</button><sup class="text-white bg-danger messagecounter mt-4 userChatUnreadCount rounded-circle">'+userChatGlobalCount+'</sup>'
            appnedHTml += '<button type="button" class="btn btn-outline-secondary rounded-pill ml-2 adminChat adminChatsButton">Admin chats</button><sup class="text-white bg-danger messagecounter mt-4 adminChatUnreadCount rounded-circle" style="border-radius:50%">'+adminChatGlobalCount+'</sup>'
            }
            $('.chats').empty();
            $('.setProfile').empty();
            $('.chats').append(appnedHTml);

          var defaultImage ='{{ url('/img/avatar/maleAvatar.png')}}';
            $.ajax({
                type: 'GET',
                url: '{{ url('/chat/chatted-users') }}',
                    beforeSend: function () {
                    $.LoadingOverlay('show');
                },
                data: {
                    'chatType': 'userchat'
                },
                success: function(response) {
                 $.LoadingOverlay('hide');
                 if(response.length !=0){
                    let  html = "";
                    var num=0;
                    var lastMessageVar = "";
                    $.each(response,function(index,value){
                        var firstPlayerImage=value.match.first_team.get_first_member.avatar?value.match.first_team.get_first_member.ImageFullUrl:defaultImage;
                       var secondPlayerImage=value.match.second_team.get_first_member.avatar?value.match.second_team.get_first_member.ImageFullUrl:defaultImage;

                        num++;
                        html +='<li class="contact userChatMessages chat'+num +'" data-chatNumber="'+num +'" data-chatId="'+value.id+'" data-id="'+value.matchId+'" data-ladder="'+value.match.ladder.name+'"> <div class="d-flex ml-1"> <div class="wrap d-flex">  <img class="img'+num+' '+value.match.first_team.get_first_member.uid+'" src="'+firstPlayerImage+'" alt="" height="30px" width="30px" /> <div class="meta"> <p class="name1'+num+'">'+value.match.first_team.get_first_member.fullName+'</p> </div> </div> <div class="meta mt-2 pr-sm-5 pr-md-4 pr-lg-4 pr-xl-5"> <p class="name">VS</p> </div> <div class="wrap mr-1">  <img class="img1'+num+' '+value.match.second_team.get_first_member.uid+'" src="'+secondPlayerImage+'" alt="" height="30px" width="30px" /> <div class="meta"> <p class="name2'+num+'">'+value.match.second_team.get_first_member.fullName+'</p> </div> </div> </div>';
                        if(value.match.first_team.get_second_member !=undefined) {

                       var thirdPlayerImage=value.match.first_team.get_second_member.avatar?value.match.first_team.get_second_member.ImageFullUrl:defaultImage;
                       var fourthPlayerImage=value.match.second_team.get_second_member.avatar?value.match.second_team.get_second_member.ImageFullUrl:defaultImage;
                            html += '<div class="d-flex ml-1"> <div class="wrap d-flex">  <img class="img2'+num+' '+value.match.first_team.get_second_member.uid+'" src="'+thirdPlayerImage+'" alt="" height="30px" width="30px" /> <div class="meta"> <p class="name3'+num+'">'+value.match.first_team.get_second_member.fullName+'</p> </div> </div> <div class="meta mt-2 pr-sm-5 pr-md-4 pr-lg-4 pr-xl-5"> </div> <div class="wrap mr-1">  <img class="img3'+num+' '+value.match.second_team.get_second_member.uid+'" src="'+fourthPlayerImage+'" alt="" height="30px" width="30px"/> <div class="meta"> <p class="name4'+num+'">'+value.match.second_team.get_second_member.fullName+'</p> </div> </div> </div>';
                        }

                        html +='<div> <div class="preview ml-3">'+value.lastMessage+'</div></div> </li>';
                    });
                    $('.chatSidebar').empty();
                    $('.chatSidebar').append(html);
                    $(".chatSidebar .userChatMessages:first").click();
                    $('.chatMessages').empty();
                    $(".message-input").addClass("d-none")
                  }else{

                    $('.chatMessages').html(`<li class="mt-5 text-center"><span class="bold"></span>No Message Available</span></li>`);
                    $('.chatSidebar').html(`<li class="p-5">No Data Available</li>`);
                 }
            }

        });});

        //////////////////////saveUnreadMessageCountTab//////////////////////////////////////////
        function saveUnreadMessageCountTab(unreadCountMatchType , chatId) {
             $.ajax({
                type: 'GET',
                url: '{{ url('/chat/save-unread-message') }}',
                data: {
                'unreadCountMatchType': unreadCountMatchType,'chatId':chatId
                },
                success: function(response) {

                }
            });
        }
        //////////////////////saveUnreadMessageCountTab//////////////////////////////////////////
        // Count unread messages for selected tab and oarrent tab
        function getUnreadMessageCountTab(unreadCountMatchType , chatId) {
             $.ajax({
                type: 'GET',
                url: '{{ url('/chat/unread-message-count') }}',
                data: {
                'unreadCountMatchType': unreadCountMatchType,'chatId':chatId
                },
                success: function(response) {
                    var sum = 0;
                    if(response['unreadCountMatchType'] == 'USER_CHAT')
                    {
                        $('.userChatUnreadCount').html(response['userChatUnreadCount']);
                        sum = (parseInt(response['userChatUnreadCount']) + parseInt(response['adminChatUnreadCount']));
                        if(sum == null || sum == ''){
                            sum = 0;
                        }
                        $('.totalChatUnreadCount').html(sum);
                    }
                    if(response['unreadCountMatchType'] == 'ADMIN_CHAT')
                    {
                        $('.adminChatUnreadCount').html(response['adminChatUnreadCount']);
                        sum = (parseInt(response['userChatUnreadCount']) + parseInt(response['adminChatUnreadCount']));
                        if(sum == null || sum == ''){
                            sum = 0;
                        }
                        $('.totalChatUnreadCount').html(sum);
                    }
                    if(response['unreadCountMatchType'] == 'SUPPORT_CHAT')
                    {
                        $('.totalSupportUnreadCount').html(response['adminChatIssueUnreadCount']);
                    }
                    if(response['unreadCountMatchType'] == 'SINGLE_DISPUTE_CHAT')
                    {
                        $('.disputeSingleUnreadCount').html(response['supportSingleUnreadCount']);
                        sum = (parseInt(response['supportSingleUnreadCount']) + parseInt(response['supportDoubleUnreadCount']));
                        if(sum == null || sum == ''){
                            sum = 0;
                        }
                        $('.totalDisputeUnreadCount').html(sum);
                    }
                    if(response['unreadCountMatchType'] == 'DOUBLE_DISPUTE_CHAT')
                    {
                        $('.disputeDoubleUnreadCount').html(response['supportDoubleUnreadCount']);
                        sum = (parseInt(response['supportSingleUnreadCount']) + parseInt(response['supportDoubleUnreadCount']));
                        if(sum == null || sum == ''){
                            sum = 0;
                        }
                        $('.totalDisputeUnreadCount').html(sum);
                    }
                }
            });
        }
        // Count unread messages for selected tab and oarrent tab
        //user chat to get messages
        $(document).on('click','.userChatMessages',function () {
            $('.contact.active').removeClass("active");
          var defaultImage ='{{ url('/img/avatar/maleAvatar.png')}}';
            var img=$('.img'+$(this).attr("data-chatNumber")).prop('src');
            var img1=$('.img1'+$(this).attr("data-chatNumber")).prop('src');
              $(this).addClass("active")
            var ladder =  $(this).data("ladder");
           let appendHtml='';
           appendHtml +='<img src="'+img+'" height="40px"  width="30px" alt="" />'
           appendHtml +='<p id="currentUserId">'+ $('.name1'+$(this).attr("data-chatNumber")).text() +'</p>'
            if ($("p").hasClass("name3"+$(this).attr("data-chatNumber"))){
                var img2=$('.img2'+$(this).attr("data-chatNumber")).prop('src');
                appendHtml +='<img src="'+img2+'" height="40px"  width="30px" alt="" />'
                appendHtml +='<p id="currentUserId">'+ $('.name3'+$(this).attr("data-chatNumber")).text() +'</p>'
            }
           appendHtml +='<p class="ml-2">VS</p>'
           appendHtml +='<img src="'+img1+'" height="40px" width="30px" alt="" />'
           appendHtml +='<p id="currentUserId">'+ $('.name2'+$(this).attr("data-chatNumber")).text() +'</p>'
            if ($("p").hasClass("name4"+$(this).attr("data-chatNumber"))){
                var img3=$('.img3'+$(this).attr("data-chatNumber")).prop('src');
                appendHtml +='<img src="'+img3+'" alt=""  height="40px" width="30px"/>'
                appendHtml +='<p id="currentUserId">'+ $('.name4'+$(this).attr("data-chatNumber")).text() +'</p>'
            }
            appendHtml +='<p class="score">Ladder: '+ ladder +'</p>'
           appendHtml +='<div class="social-media">'
            let adminuserid={!!json_encode(auth()->user()->id)!!};
            if(adminuserid != 1){
                appendHtml +='<li class="nav-link deleteChat" data-deleteChat="" data-currentChatNumber="" >'
                appendHtml +='<i class="far fa-trash-alt mt-1"  style="font-size: 27px; color: white;"></i>'
                appendHtml +='</li>'
            }
           appendHtml +='</div>'
            $('.setProfile').removeClass('d-none');
            $('.setProfile').empty();
            $('.setProfile').append(appendHtml);
            $('.deleteChat').attr("data-currentChatNumber",$(this).attr("data-chatNumber"));
            $('.deleteChat').attr("data-deleteChat",$(this).attr("data-chatId"));
            var matchID = $(this).attr("data-id");
            var unreadCountMatchType = "USER_CHAT";
            var chatID = $(this).attr("data-chatid");

            if(adminuserid != 1){
                // getUnreadMessageCountTab(unreadCountMatchType , chatID)
            }
            async function getCities(db) {
                if(unsubscribe != null){
                    unsubscribe();
                }
                userChatListenerDecrement("Chat",matchID,"match_chat")
                .then(() => {
                    console.log('Overall execution completed.');
                })
                .catch((error) => {
                    console.log('Error during execution:', error);
                });
                //////  Decrement chat count /////////////////
                setTimeout(()=>{
                    saveUnreadMessageCountTab('CHAT' , chatID);
                }, 1000);
                const q = query(collection(db, "Chat",matchID,"match_chat"),orderBy("lastMessageTime"));
                unsubscribe = onSnapshot(q, (querySnapshot) => {
                    const messages = [];
                    querySnapshot.forEach((doc) => {
                        messages.push(doc.data());
                    });
                    let adminUid={!!json_encode(auth()->user()->uid)!!}
                    let adminid={!!json_encode(auth()->user()->id)!!};
                    let adminAvatar={!!json_encode(auth()->user()->avatar)!!};
                    var path="{{Storage::disk('s3')->url('')}}";
                    let html='';
                    if(messages.length!=0){
                    for (let i = 0; i < messages.length; i++) {
                        if(messages[i].lastMessage != ''){
                            if(messages[i].currentUserId==adminUid) {
                                var dateFormat= new Date(messages[i].lastMessageTime * 1000);
                                var hours = dateFormat.getHours();
                                var minutes = dateFormat.getMinutes();
                                var AMPM = 'AM';
                                if(hours > 13){
                                    AMPM = 'PM';
                                    hours = hours-12;
                                }
                                if(hours < 10){
                                    hours = "0"+hours;
                                }
                                if(minutes < 10){
                                    minutes = "0"+minutes;
                                }

                                var firstImage=adminAvatar?path+adminAvatar:defaultImage;
                                html += '<li class=" sent"> <img class="rounded-circle" src="'+firstImage+'" alt="" height="20px" width="30px"/><p>'+messages[i].lastMessage+'<br><small class="pt-1 text-right" style="font-size: 8px;">'+hours+":"+minutes+" "+AMPM+'</small></p></li>'
                            }else{
                                var dateFormat= new Date(messages[i].lastMessageTime * 1000);
                                var hours = dateFormat.getHours();
                                var minutes = dateFormat.getMinutes();
                                var AMPM = 'AM';
                                if(hours > 13){
                                    AMPM = 'PM';
                                    hours = hours-12;
                                }

                                if(hours < 10){
                                    hours = "0"+hours;
                                }
                                if(minutes < 10){
                                    minutes = "0"+minutes;
                                }
                                var img;
                                if(messages[i].currentUserId){
                                img= $('.'+messages[i].currentUserId).attr('src');
                                if(img===undefined){
                                    img=defaultImage;
                                }
                                }else{
                                    img=defaultImage;
                                }
                                html += '<li class="replies"> <img class="rounded-circle" src="'+img+'" height="22px" width="30px" alt="" /><p>'+messages[i].lastMessage+'<br><small class="pt-1 text-right" style="font-size: 8px; float:right">'+hours+":"+minutes+" "+AMPM+'</small></p></li>';


                            }
                        }
                    }
                    $('.chatMessages').empty();
                    $('.chatMessages').append(html);
                    $('.messages').scrollTop($('.messages')[0].scrollHeight);
                    $('.senderId').val(matchID);
                    $('.sendMessageChatType').val('matchChat');
                }else{
                    $('.chatMessages').html(`<li class="mt-5 text-center"><span class="bold"></span>No Message Available</span></li>`);
                }
                });
            }

            getCities(db);

        });
        //dispute chat to get messages
        $(document).on('click','.disputeChatMessages',function () {
            $('.contact.active').removeClass("active");
            $('#disputeUserId').val($(this).attr("data-useruid"));
            var img=$('.img'+$(this).attr("data-chatNumber")).prop('src');
            var img1=$('.img1'+$(this).attr("data-chatNumber")).prop('src');
            $(this).addClass("active");
            var ladder=$(this).data("ladder");
            let appendHtml='';
           var  matchChatType = 'SINGLE_DISPUTE_CHAT';
            appendHtml +='<img src="'+img+'" height="40px"  width="30px" alt="" />'
            appendHtml +='<p id="currentUserId">'+ $('.name1'+$(this).attr("data-chatNumber")).text() +'</p>'
            if ($("p").hasClass("name3"+$(this).attr("data-chatNumber"))){
                var img2=$('.img2'+$(this).attr("data-chatNumber")).prop('src');
                appendHtml +='<img src="'+img2+'" height="40px"  width="30px" alt="" />'
                appendHtml +='<p id="currentUserId">'+ $('.name3'+$(this).attr("data-chatNumber")).text() +'</p>'
            }
            appendHtml +='<p class="ml-2">VS</p>'
            appendHtml +='<img src="'+img1+'" height="40px"  width="30px" alt="" />'
            appendHtml +='<p id="currentUserId">'+ $('.name2'+$(this).attr("data-chatNumber")).text() +'</p>'
            if ($("p").hasClass("name4"+$(this).attr("data-chatNumber"))){
                matchChatType = 'DOUBLE_DISPUTE_CHAT';
                var img3=$('.img3'+$(this).attr("data-chatNumber")).prop('src');
                appendHtml +='<img src="'+img3+'" height="40px"  width="30px" alt="" />'
                appendHtml +='<p id="currentUserId">'+ $('.name4'+$(this).attr("data-chatNumber")).text() +'</p>'
            }
            appendHtml +='<p class="score">Ladder: '+ ladder +'</p>'
            appendHtml +='<p class="" id="viewScore">(View Score)</p>'
            appendHtml +='<div class="social-media">'
            let adminuserid={!!json_encode(auth()->user()->id)!!};
            if(adminuserid != 1){
                appendHtml +='<li class="nav-link deleteChat" data-deleteChat="" data-currentChatNumber="" >'
                appendHtml +='<i class="far fa-trash-alt mt-1"  style="font-size: 27px; color: white;"></i>'
                appendHtml +='</li>'
            }
            appendHtml +='</div>'
            $('.setProfile').removeClass('d-none');
            $('.setProfile').empty();
            $('.setProfile').append(appendHtml);
            $('.deleteChat').attr("data-currentChatNumber",$(this).attr("data-chatNumber"));
            $('.deleteChat').attr("data-deleteChat",$(this).attr("data-chatId"));
            $('.message-input').removeClass('d-none');
            var matchID = $(this).attr("data-id");
            var useruid = $(this).attr("data-useruid");
            var adminuid = $(this).attr("data-adminuid");
            var adminid = $(this).attr("data-adminid");
            var adminAvatar = $(this).attr("data-adminavatar");
            var unreadCountMatchType = matchChatType;
            var chatID = $(this).attr("data-chatId");
            if(adminuserid != 1){
                // getUnreadMessageCountTab(unreadCountMatchType , chatID)
            }

            async function getCities(db,adminuid,adminid,adminAvatar) {
                if(unsubscribe != null){
                    unsubscribe();
                }
                if(matchChatType == 'SINGLE_DISPUTE_CHAT')
                {
                    //////  Decrement chat count /////////////////
                    singleDisputeChatListenerDecrement("support",matchID,adminuid,useruid,"issue_chat")
                    .then(() => {
                        console.log('Overall execution completed.');
                    })
                    .catch((error) => {
                        console.log('Error during execution:', error);
                    });
                    //////  Decrement chat count /////////////////
                }
                if(matchChatType == 'DOUBLE_DISPUTE_CHAT')
                {
                    //////  Decrement chat count /////////////////
                    doubleSupportChatListenerDecrement("support",matchID,adminuid,useruid,"issue_chat")
                    .then(() => {
                        console.log('Overall execution completed.');
                    })
                    .catch((error) => {
                        console.log('Error during execution:', error);
                    });
                    //////  Decrement chat count /////////////////
                }
                setTimeout(()=>{
                    saveUnreadMessageCountTab(null , chatID);
                }, 1000);

                var defaultImage ='{{ url('/img/avatar/default-avatar.png')}}';
                const q = query(collection(db, "support",matchID,adminuid,useruid,"issue_chat"),orderBy("lastMessageTime"));
                unsubscribe = onSnapshot(q, (querySnapshot) => {
                    const messages = [];
                    querySnapshot.forEach((doc) => {
                        messages.push(doc.data());
                    });
                    let adminUid=adminuid;
                    var path="{{Storage::disk('s3')->url('')}}";
                    let html='';
                    for (let i = 0; i < messages.length; i++) {
                        if(messages[i].lastMessage != ''){
                            if(messages[i].currentUserId==adminUid) {
                                var dateFormat= new Date(messages[i].lastMessageTime * 1000);
                                var hours = dateFormat.getHours();
                                var minutes = dateFormat.getMinutes();
                                var AMPM = 'AM';
                                if(hours > 13){
                                    AMPM = 'PM';
                                    hours = hours-12;
                                }

                                if(hours < 10){
                                    hours = "0"+hours;
                                }
                                if(minutes < 10){
                                    minutes = "0"+minutes;
                                }

                                var firstImage=adminAvatar?path+adminAvatar:defaultImage;
                                html += '<li class=" sent"> <img class="rounded-circle" src="'+firstImage+'" height="20px" width="30px" alt="" /><p>'+messages[i].lastMessage+'<br><small class="pt-1 text-right" style="font-size: 8px;">'+hours+":"+minutes+" "+AMPM+'</small></p> </li>';
                            }else{
                                var dateFormat= new Date(messages[i].lastMessageTime * 1000);
                                var hours = dateFormat.getHours();
                                var minutes = dateFormat.getMinutes();
                                var AMPM = 'AM';
                                if(hours > 13){
                                    AMPM = 'PM';
                                    hours = hours-12;
                                }

                                if(hours < 10){
                                    hours = "0"+hours;
                                }
                                if(minutes < 10){
                                    minutes = "0"+minutes;
                                }
                                var img;
                                if(messages[i].currentUserId){
                                img= $('.'+messages[i].currentUserId).attr('src');
                                if(img===undefined){
                                    img=defaultImage;
                                }
                                }else{
                                    img=defaultImage;
                                }
                                var message=messages[i].lastMessage;
                                var  newMessage=message.replace(':',':<br>');
                                html += '<li class="replies"> <img class="rounded-circle" src="'+img+'" height="22px" width="30px" alt="" /> <p>'+newMessage+'<br><small class="pt-1 text-right" style="font-size: 8px; float:right">'+hours+":"+minutes+" "+AMPM+'</small></p> </li>';
                            }
                        }
                    }
                    $('.chatMessages').empty();
                    $('.chatMessages').append(html);

                    $('.messages').scrollTop($('.messages')[0].scrollHeight);
                    $('.senderId').val(matchID);
                    $('.sendMessageChatType').val('matchChat');

                });
            }

            getCities(db,adminuid);

        });
        //admin start chat
        $(document).on('click','#startChat',function () {
            var userId = $('#userId').find(":selected").val();
            var adminMessage = $('#startMessage').val();
            if(adminMessage==""){
                $('#errorMessage').removeClass('d-none');
                $('#myModal').modal('show');
            }else if(userId=="Select user to chat with"){
                $('#errorUser').removeClass('d-none');
                $('#myModal').modal('show');
            }else{
                createChat(userId,adminMessage);
            }
        });
        function createChat(userId,adminMessage,issueId){
            $("#closeModal").click()
            $.ajaxSetup({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
            });

            $.ajax({
                type: 'POST',
                url: '{{ url('/chat/create-chat') }}',
                beforeSend: function () {
                    $.LoadingOverlay('show');
                },
                data: {
                    'userId': userId,'adminMessage':adminMessage,'issueId':issueId
                },
                success: function(response) {
                $.LoadingOverlay('hide');
                var userId = $('#userId').val("");
                var adminMessage = $('#startMessage').val("");
                    getAdminChttedUsers();
                }
            });
        }
         $(document).on('click','#closeModal',function () {
            var userId = $('#userId').val("");
            var adminMessage = $('#startMessage').val("");

        });
        //get admin chatted user

        $(document).on('click','.adminChat',function () {
            getAdminChttedUsers();
        });

        $(document).on('click','.homeChat',function () {
            $('.homeNewChat').removeClass('bg-success');
            $('.homeChat').addClass('bg-success');
        });

        $(document).on('click','.homeNewChat',function () {
            $('.homeChat').removeClass('bg-success');
            $('.homeNewChat').addClass('bg-success');
        });
        function getAdminChttedUsers(){
            var defaultImage ="{{ url('/img/avatar/default-avatar.png')}}";
            $.ajax({
                type: 'GET',
                url: '{{ url('/chat/admin-chatted-users') }}',
                beforeSend: function () {
                    $.LoadingOverlay('show');
                },
                data: {
                    'chatType': 'ADMIN_CHAT'
                },
                success: function(response) {
                $.LoadingOverlay('hide');
                $('.userChatsButton').removeClass('bg-success');
                $('.adminChatsButton').addClass('bg-success');
                if(response.data.length !=0){
                    let  html = "";
                    var num=0;
                    $.each(response.data,function(index,value){
                        var firstImage=value.user.avatar?value.user.ImageFullUrl:defaultImage;
                        num++;
                        html += '  <li class="contact adminChatMessages active chat'+num +'" data-chatNumber="'+num +'" data-supportIssueId="'+value.issueId+'" data-adminUId="'+value.adminUId+'"  data-chatId="'+value.id+'" data-id="'+value.user.id+'" data-userUid="'+value.user.uid+'"> <div class="wrap">  <img class="img'+num+' '+value.user.uid+'" src="'+firstImage+'" height="30px" width="30px" alt="" /> <div class="meta"> <p class="name">'+value.user.fullName+'</p></div> </div> <div class="preview ml-3">'+value.lastMessage+'</div> ';

                        });
                    $('.chatSidebar').empty();
                    $('.chatSidebar').append(html);
                    $('.chatMessages').empty();
                    $('.setProfile').empty();
                    $(".chatSidebar .adminChatMessages:first").click();

                }else{

                    $('.chatMessages').html(`<li class="mt-5 text-center"><span class="bold"></span>No Message Available</span></li>`);
                    $('.chatSidebar').html(`<li class="p-5">No Data Available</li>`);
                 }
                }
            });
        }
        $(document).on('click','.adminChatMessages',function () {
              $('.contact.active').removeClass("active");
            var img=$('.img'+$(this).attr("data-chatNumber")).prop('src');
            $(this).addClass("active");
            let profileHtml='';
            profileHtml +='<img src="'+img+'" height="40px" width="30px" alt="" />';
            profileHtml +='<p id="currentUserId">'+$(this).closest("li").find(".name1").text()+'</p>';
            profileHtml +='</div>';
            profileHtml +='<div class="social-media">'
            let adminuserid={!!json_encode(auth()->user()->id)!!};
            if(adminuserid != 1){
                profileHtml +='<li class="nav-link deleteChat" data-deleteChat="" data-currentChatNumber="" >'
                profileHtml +='<i class="far fa-trash-alt mt-1"  style="font-size: 27px; color: white;"></i>'
                profileHtml +='</li>'
            }
            profileHtml +='</div>'
            $('.setProfile').removeClass('d-none');
            $('.setProfile').empty();
            $('.setProfile').append(profileHtml);
            $('.deleteChat').attr("data-deleteChat",$(this).attr("data-chatId"));
            $('.deleteChat').attr("data-currentChatNumber",$(this).attr("data-chatNumber"));
            $(".message-input").removeClass("d-none");
            $('#currentUserId').text($(this).closest("li").find("p").text());

            var issueId = $(this).attr("data-supportIssueId");
            var userId = $(this).attr("data-id");
            var userUId = $(this).attr("data-userUid");
            let adminUid=$(this).attr("data-adminUId");
            let adminid={!!json_encode(auth()->user()->id)!!};
            var unreadCountMatchType = "ADMIN_CHAT";
            var chatID = $(this).attr("data-chatid");
            getAdminChatMessages(db,adminUid,adminid,userId,userUId,issueId,chatID);
            if(adminid != 1){
                // getUnreadMessageCountTab(unreadCountMatchType , chatID)
            }
        });
        //function to get admin chat messages
        async function getAdminChatMessages(db,adminUid,adminid,userId,userUId,issueId,chatID) {
            if(unsubscribe != null){
                unsubscribe();
            }
            // adminChatListenerDecrement
              adminChatListenerDecrement("admin_chat",issueId,adminUid,userUId,"issue_chat")
            .then(() => {

                console.log('Overall execution completed.');
            })
            .catch((error) => {
                console.log('Error during execution:', error);
            });
            //////  Decrement chat count /////////////////
            setTimeout(()=>{
                saveUnreadMessageCountTab(null , chatID);
            }, 1000);
                let adminAvatar={!!json_encode(auth()->user()->avatar)!!};
                    var path="{{Storage::disk('s3')->url('')}}";
            var defaultImage ='{{ url('/img/avatar/default-avatar.png')}}';
            const q =query(collection(db, "admin_chat",issueId,adminUid,userUId,"issue_chat"),orderBy("lastMessageTime"));
            unsubscribe = onSnapshot(q, (querySnapshot) => {
                let html='';
                querySnapshot.forEach((doc) => {
                    if(doc.data().lastMessage != ''){
                        if(doc.data().currentUserId==adminUid){
                            var dateFormat= new Date(doc.data().lastMessageTime * 1000);
                            var hours = dateFormat.getHours();
                            var minutes = dateFormat.getMinutes();
                            var AMPM = 'AM';
                            if(hours > 13){
                                AMPM = 'PM';
                                hours = hours-12;
                            }
                            if(hours < 10){
                                hours = "0"+hours;
                            }
                            if(minutes < 10){
                                minutes = "0"+minutes;
                            }

                            var firstImage=adminAvatar?path+adminAvatar:defaultImage;
                            html += '<li class="sent"> <img class="rounded-circle" src="'+firstImage+'" height="20px" width="30px" alt="" /> <p>'+doc.data().lastMessage+'<br><small class="pt-1 text-right" style="font-size: 8px;">'+hours+":"+minutes+" "+AMPM+'</small></p></li>'
                        }else{
                            var dateFormat= new Date(doc.data().lastMessageTime * 1000);
                            var hours = dateFormat.getHours();
                            var minutes = dateFormat.getMinutes();
                            var AMPM = 'AM';
                            if(hours > 13){
                                AMPM = 'PM';
                                hours = hours-12;
                            }
                            if(hours < 10){
                                hours = "0"+hours;
                            }
                            if(minutes < 10){
                                minutes = "0"+minutes;
                            }
                            var img;
                                if(doc.data().currentUserId){
                                img= $('.'+doc.data().currentUserId).attr('src');
                                if(img===undefined){
                                    img=defaultImage;
                                }
                                }else{
                                    img=defaultImage;
                                }
                            html += '<li class="replies"> <img class="rounded-circle" src="'+img+'"  height="22px" width="30px" alt="" /><p>'+doc.data().lastMessage+'<br><small class="pt-1 text-right" style="font-size: 8px; float:right">'+hours+":"+minutes+" "+AMPM+'</small></p></li>';
                        }
                    }
                });
                $('.chatMessages').empty();
                $('.chatMessages').append(html);
                    $('.messages').scrollTop($('.messages')[0].scrollHeight);
                $('.senderId').val(userId);
                 $('.adminissueId').val(issueId);
                $('.sendMessageChatType').val('adminChat');
            });
        }
        //////Match disputes////////

        $(document).on('click','.matchDisputes',function () {
           var path="{{Storage::disk('s3')->url('')}}";
           var defaultImage ='{{ url('/img/avatar/maleAvatar.png')}}';
           $('.button-div').removeClass('d-flex');
            $('.button-div').addClass('d-none');
            $('.heading').html(` <span class="ml-3"> Match Disputes</span>`);
            $('.heading').addClass("mb-5");
            $('.setProfile').addClass('d-none');
            $('.chatMessages').empty();
            let  appnedHTml = "";
            let adminuserid={!!json_encode(auth()->user()->id)!!};
            if(adminuserid == 1){
                appnedHTml += '<button type="button" data-disputedChatType="Single"  class="btn btn-outline-secondary bg-success rounded-pill  matchDisputes singleMatchDisputes">Single</button> '
              appnedHTml += '<button type="button" data-disputedChatType="Double" class="btn btn-outline-secondary rounded-pill ml-2  matchDisputes doubleMatchDisputes">Double</button>'
            }else{
                appnedHTml += '<button type="button" data-disputedChatType="Single"  class="btn btn-outline-secondary bg-success rounded-pill  matchDisputes singleMatchDisputes">Single</button> <sup class="text-white bg-danger messagecounter mt-4 disputeSingleUnreadCount">'+singleMatchDisputeCount+'</sup>'
              appnedHTml += '<button type="button" data-disputedChatType="Double" class="btn btn-outline-secondary rounded-pill ml-2  matchDisputes doubleMatchDisputes">Double</button><sup class="text-white bg-danger messagecounter mt-4 disputeDoubleUnreadCount">'+doubleMatchDisputeCount+'</sup>'
            }
            $('.chats').empty();
            $('.chats').append(appnedHTml);
            $('.matchType').val($(this).attr('data-disputedChatType'));
            $.ajax({
                type: 'GET',
                url: '{{ url('/chat/disputed-chat') }}',
                 beforeSend: function () {
                    $.LoadingOverlay('show');
                },
                data: {'chatType': $(this).attr('data-disputedChatType')},
                success: function(response) {
                    $.LoadingOverlay('hide');
                    if(response.length!=0){
                    let  html = "";
                    var num=0;

                    $.each(response,function(index,value){
                    var firstMImage= value.match.first_team.get_first_member.avatar? value.match.first_team.get_first_member.avatar:null;
                    var firstImgCheck=(firstMImage)?(path+firstMImage):defaultImage;
                    var secondMImg= value.match.second_team.get_first_member.avatar? value.match.second_team.get_first_member.avatar:null;
                    var secondImgCheck=(secondMImg)?(path+secondMImg):defaultImage;
                     num++;
                      html +='<li class="contact active disputeChatMessages  chat'+num +'"  data-ladder="'+value.match.ladder.name+'" data-disuserId="'+value.userId+'" data-chatNumber="'+num +'" data-chatId="'+value.id+'" data-id="'+value.matchId+'"  data-useruid="'+value.userUId+'" data-adminuid="'+value.adminUId+'" data-adminid="'+value.regionalAdmin.id+'" data-adminavatar="'+value.regionalAdmin.avatar+'"> <div class="d-flex ml-1"> <div class="wrap d-flex">  <img class="img'+num+' '+value.match.first_team.get_first_member.uid+'" src="'+firstImgCheck+'" height="30px" width="30px" alt="" /> <div class="meta"> <p class="name1'+num +'">'+value.match.first_team.get_first_member.fullName+'</p> </div> </div> <div class="meta mt-2 pr-sm-5 pr-md-4 pr-lg-4 pr-xl-5"> <p class="name">VS</p> </div> <div class="wrap mr-1"> <img class="img1'+num+' '+value.match.second_team.get_first_member.uid+'" src="'+secondImgCheck+'" height="30px" width="30px" alt="" /> <div class="meta"> <p class="name2'+num +'">'+value.match.second_team.get_first_member.fullName+'</p> </div> </div> </div>';
                        if(value.match.first_team.get_second_member !=undefined) {
                            var thirdMImg= value.match.first_team.get_second_member.avatar? value.match.first_team.get_second_member.avatar:null;
                            var thirdImgCheck=(thirdMImg)?(path+thirdMImg):defaultImage;
                            var fourMImg=value.match.second_team.get_second_member.avatar? value.match.second_team.get_second_member.avatar:null;
                            var fourImgCheck=(fourMImg)?(path+fourMImg):defaultImage;
                            html += '<div class="d-flex ml-1"> <div class="wrap d-flex">  <img class="img2'+num+' '+value.match.first_team.get_second_member.uid+'"  src="'+thirdImgCheck+'" alt="" height="30px" width="30px" /> <div class="meta"> <p class="name3'+num +'">'+value.match.first_team.get_second_member.fullName+'</p> </div> </div> <div class="meta mt-2 pr-sm-5 pr-md-4 pr-lg-4 pr-xl-5"> </div> <div class="wrap mr-1">  <img class="img3'+num+' '+value.match.second_team.get_second_member.uid+'" src="'+fourImgCheck+'" alt="" height="30px" width="30px" /> <div class="meta"> <p class="name4'+num +'">'+value.match.second_team.get_second_member.fullName+'</p> </div> </div> </div>';
                        }
                        html +='<div> <div class="preview ml-3">'+value.lastMessage+'</div></div> </li>';
                    });
                    $('.chatSidebar').empty();
                    $('.chatSidebar').append(html);
                    $(".chatSidebar .disputeChatMessages:first").click();

                    }else{

                    $('.chatMessages').html(`<li class="mt-5 text-center"><span class="bold"></span>No Message Available</span></li>`);
                    $('.chatSidebar').html(`<li class="p-5">No Data Available</li>`);
                    }
                }
        });});

         $(document).on('click','.singleMatchDisputes',function () {
            $('.doubleMatchDisputes').removeClass('bg-success');
            $('.singleMatchDisputes').addClass('bg-success');
        });

        $(document).on('click','.doubleMatchDisputes',function () {
            $('.singleMatchDisputes').removeClass('bg-success');
            $('.doubleMatchDisputes').addClass('bg-success');
        });

         function sendDisputedChatMessage(userUid,matchType,matchId,Message) {
             $.ajax({
                 type: 'GET',
                 url: '{{ url('/chat/send-disputed-chat-message') }}',
                 data: {
                    'userUId' : userUid, 'matchType': matchType,'matchId':matchId,'message':Message
                 },
                 success: function(response) {
                 }
             })
        }
        //////// Customer Support Chat


        $(document).on('click','.support',function () {
          var defaultImage ="{{ url('/img/avatar/maleAvatar.png')}}";
            $('.button-div').removeClass('d-flex');
          $('.button-div').addClass('d-none');

            $('.heading').html(` <span class="ml-3"> Customer Support</span>`);
            $('.heading').addClass("mb-5");
            $('.chats').empty();
            $('.setProfile').empty();

            $.ajax({
                type: 'GET',
                url: '{{ url('/chat/support-chat-users') }}',
                beforeSend: function () {
                    $.LoadingOverlay('show');
                },
                data: {'chatType': $(this).attr('data-disputedChatType')},
                success: function(response) {
                    if(response.length!=0){
                      let  html = "";
                      var num=0;

                      var issue="";
                      var issueTime="";
                        $.each(response,function(index,value){
                            if(value.issue){
                              var dateFormat= new Date(value.issue.createdAt);
                             var hours = dateFormat.getHours();
                             var minutes = dateFormat.getMinutes();
                             var AMPM='AM'
                             if(hours > 13){
                                hours = hours-12;
                                AMPM='PM'
                             }
                             if(hours < 10){
                                hours = "0"+hours;
                             }
                             if(minutes < 10){
                                 minutes = "0"+minutes;
                             }
                             issue=value.issue.issue;
                             issueTime='"<time>'+hours+":"+minutes+" "+AMPM+'</time>"';
                              }else{
                             issue="null";
                             issueTime="null";
                            }
                        var firstImage=value.user.avatar?value.user.ImageFullUrl:defaultImage;
                            num++;
                            html += '  <li class="contact  supportChatMessages  chat'+num +'" data-chatNumber="'+num +'" data-chatId="'+value.id+'" data-supportMatchId="'+value.matchId+'" data-supportIssueId="'+value.issueId+'" data-id="'+value.user.id+'" data-userUid="'+value.user.uid+'" data-issueTitle='+issue+' data-issueTime='+issueTime+'> <div class="wrap">  <img class="img'+num+' '+value.user.uid+'" src="'+firstImage+'" height="30px" width="30px" alt="" /> <div class="meta"> <p class="name1">'+value.user.fullName+'</p></div> </div>';
                            html +=  '<div class="preview ml-3">'+value.lastMessage+'</div></li>';
                        });

                      $.LoadingOverlay('hide');
                     $('.chatSidebar').empty();
                     $('.chatSidebar').append(html);
                     $('.chatMessages').empty();

                     $(".chatSidebar .supportChatMessages:first").click();
                }else{

                      $.LoadingOverlay('hide');

                    $('.chatMessages').html(`<li class="mt-5 text-center"><span class="bold"></span>No Message Available</span></li>`);
                    $('.chatSidebar').html(`<li class="p-5">No Data Available</li>`);
                }
            }
          });
       });
        $(document).on('click','.supportChatMessages',function () {
            $('.contact.active').removeClass("active");
            var issueTitle=$(this).attr("data-issueTitle");
            var issueTime=$(this).attr("data-issueTime");
            $(this).addClass("active")
            var img=$('.img'+$(this).attr("data-chatNumber")).prop('src');
            let profileHtml='';
            profileHtml +='<img src="'+img+'" height="40px" width="30px" alt="" />';
            profileHtml +='<p id="currentUserId">'+$(this).closest("li").find(".name1").text()+'</p>';
            profileHtml +='</div>';
            profileHtml +='<div class="social-media">'
            let adminuserid={!!json_encode(auth()->user()->id)!!};
            if(adminuserid != 1){
                profileHtml +='<li class="nav-link deleteChat" data-deleteChat="" data-currentChatNumber="" >'
                profileHtml +='<i class="far fa-trash-alt mt-1"  style="font-size: 27px; color: white;"></i>'
                profileHtml +='</li>'
            }
            profileHtml +='</div>'
            $('.setProfile').removeClass('d-none');
            $('.setProfile').empty();
            $('.setProfile').append(profileHtml);
            $('.deleteChat').attr("data-deleteChat",$(this).attr("data-chatId"));
            $('.deleteChat').attr("data-currentChatNumber",$(this).attr("data-chatNumber"));
            $(".message-input").removeClass("d-none");
            $('.setProfile').removeClass('d-none');
            $('#currentUserId').text($(this).closest("li").find("p").text());
            var userId = $(this).attr("data-id");
            var userUid = $(this).attr("data-userUid");
            var matchId = $(this).attr("data-supportIssueId");
            let adminUid={!!json_encode(auth()->user()->uid)!!};
            let adminid={!!json_encode(auth()->user()->id)!!};
            let adminavatar={!!json_encode(auth()->user()->avatar)!!};
            var unreadCountMatchType = "SUPPORT_CHAT";
            var chatID = $(this).attr("data-chatId");
            if(adminid != 1){
                // getUnreadMessageCountTab(unreadCountMatchType , chatID);
            }
            getsupportChatMessages(db,matchId,adminUid,adminavatar,adminid,userId,userUid,img,issueTitle,issueTime,chatID);
        });
        //function to get support chat messages
        async function getsupportChatMessages(db,issueId,adminUid,adminavatar,adminid,userId,userUid,image,issue,issueTime,chatID) {

            if(unsubscribe != null){
                unsubscribe();
            }

            //////  Decrement chat count /////////////////
            customerSupportChatListenerDecrement("admin_chat",issueId,adminUid,userUid,"issue_chat")
            .then(() => {

                console.log('Overall execution completed.');
            })
            .catch((error) => {
                console.log('Error during execution:', error);
            });
            //////  Decrement chat count /////////////////
            setTimeout(()=>{
                saveUnreadMessageCountTab(null , chatID);
            }, 1000);

             let adminAvatar={!!json_encode(auth()->user()->avatar)!!};
                    var path="{{Storage::disk('s3')->url('')}}";

          var defaultImage ='{{ url('/img/avatar/maleAvatar.png')}}';
            const q =query(collection(db, "admin_chat",issueId,adminUid,userUid,"issue_chat"),orderBy("lastMessageTime"));
            unsubscribe = onSnapshot(q, (querySnapshot) => {
                   var html='';
                querySnapshot.forEach((doc) => {
                    const messages = [];
                    if(doc.data().lastMessage != '')
                    {
                        if(doc.data().currentUserId==adminUid){
                            var dateFormat= new Date(doc.data().lastMessageTime * 1000);
                            var hours = dateFormat.getHours();
                            var minutes = dateFormat.getMinutes();
                            var AMPM = 'AM';
                            if(hours > 13){
                                AMPM = 'PM';
                                hours = hours-12;
                            }
                            if(hours < 10){
                                hours = "0"+hours;
                            }
                            if(minutes < 10){
                                minutes = "0"+minutes;
                            }

                            var firstImage=adminAvatar?path+adminAvatar:defaultImage;
                            html += '<li class=" sent"> <img class="rounded-circle" src="'+firstImage+'" height="20px" width="30px" alt="" /> <p>'+doc.data().lastMessage+'<br><small class="pt-1 text-right" style="font-size: 8px;">'+hours+":"+minutes+" "+AMPM+'</small></p> </li>';

                        }else{
                            var dateFormat= new Date(doc.data().lastMessageTime * 1000);
                            var hours = dateFormat.getHours();
                            var minutes = dateFormat.getMinutes();
                            var AMPM = 'AM';
                            if(hours > 13){
                                AMPM = 'PM';
                                hours = hours-12;
                            }
                            if(hours < 10){
                                hours = "0"+hours;
                            }
                            if(minutes < 10){
                                minutes = "0"+minutes;
                            }
                            var img;
                                if(doc.data().currentUserId){
                                 img= $('.'+doc.data().currentUserId).attr('src');
                                  if(img===undefined){
                                    img=defaultImage;
                                 }
                                }else{
                                    img=defaultImage;
                                }
                             var message=doc.data().lastMessage;
                            var  newMessage=message.replace(':',':<br>');
                            html += '<li class="replies"> <img class="rounded-circle" src="'+img+'" height="22px" width="30px" alt="" /> <p>'+newMessage+'<br><small class="pt-1 text-right" style="font-size: 8px; float:right">'+hours+":"+minutes+" "+AMPM+'</small></p></li>';
                        }
                    }
                });

                $('.chatMessages').empty();
                $('.chatMessages').append(html);
                $('.senderId').val(issueId);
                    $('.messages').scrollTop($('.messages')[0].scrollHeight);
                $('.supportUserId').val(userId);
                $('.supportUserUId').val(userUid);
                $('.sendMessageChatType').val('support');
            });
        }

        function sendSupportMessage(issueID,userId,Message) {
            $.ajax({
                type: 'GET',
                url: '{{ url('/chat/send-support-chat-message') }}',
                data: {
                    'issueId': issueID,'userId':userId,'message':Message
                },
                success: function(response) {

                }
            })
        }
        $(document).on('click','.deleteChat',function () {
             var id=$(this).attr('data-deletechat');
             var chatNumber=$(this).attr('data-currentchatnumber');
            $.ajaxSetup({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
            });
            $.ajax({
                type: 'POST',
                url: '{{ url('/chat/delete-chat') }}',
                beforeSend: function () {
                    $.LoadingOverlay('show');
                },
                data: {
                    'chatId':id,
                },
                success: function(response) {
                    $.LoadingOverlay('hide');
                    $(".chat"+chatNumber).remove();
                    $(".setProfile").empty();
                    $(".chatMessages").empty();
                }
            });
        });

        $(document).on('click','#viewScore',function () {
            var matchId=$('.senderId').val()
            var defaultImage ='{{ url('/img/avatar/default-avatar.png')}}';
            $.ajax({
                type: 'GET',
                url: '{{ url('/chat/view-score') }}',
                beforeSend: function () {
                    $.LoadingOverlay('show');
                },
                data: {
                    'matchId':matchId,
                },
                success: function(response) {
                    var firstMemberImg=response.first_team.get_first_member.avatar?response.first_team.get_first_member.ImageFullUrl:defaultImage;
                    var secondMemberImg=response.second_team.get_first_member.avatar?response.second_team.get_first_member.ImageFullUrl:defaultImage;
                  if(response.first_team.get_second_member !=undefined) {
                     var secondTeamFirstMemberImg=response.first_team.get_second_member.avatar?response.first_team.get_second_member.ImageFullUrl:defaultImage;
                    var secondTeamSecondMemberImg=response.second_team.get_second_member.avatar?response.second_team.get_second_member.ImageFullUrl:defaultImage;
                   }
                    var html='';
                    html +='<ul style="list-style-type: none; ">';
                    html +='<div class="row">'
                    html +='<div class="col-6">'
                    html +='<li class="contact"> <div class="wrap">  <img style="height: 30px;width: 30px;border-radius: 50%;float: left;"  src="'+firstMemberImg+'" alt=""><small class="name-1 p-2">'+response.first_team.get_first_member.fullName+'</small>   </div> </li>'
                    html +='</div>'
                    html += '<input type="hidden" value="'+matchId+'" id="editMatchId">'
                    html +='<div class="col-6">'
                    html +='<li class="contact mt-2"> <div class="wrap">  <img style="height: 30px;width: 30px;border-radius: 50%;float: left";  src="'+ secondMemberImg +'" alt=""><small class="name-1 p-2">'+response.second_team.get_first_member.fullName+'</small></div> </li>'
                    html +='</div>'
                    html +='</div>'
                    if(response.first_team.get_second_member !=undefined) {
                        html += '<div class="row">'
                        html += '<div class="col-6">'
                        html += '<li class="contact mt-2"> <div class="wrap">  <img style="height: 30px;width: 30px;border-radius: 50%;float: left"; src="'+secondTeamFirstMemberImg+'" alt=""><small class="name1 p-2">'+response.first_team.get_second_member.fullName+'</small>  </div> </li>'
                        html += '</div>'
                        html += '<div class="col-6">'
                        html += '<li class="contact mt-2"> <div class="wrap">  <img style="height: 30px;width: 30px;border-radius: 50%;float: left";  src="'+secondTeamSecondMemberImg+'" alt=""> <small class="name1 p-2">'+response.second_team.get_second_member.fullName+'</small>  </div> </li>'
                        html += '</div>'
                        html += '</div>'
                    }
                    html +='</ul>'
                    html +='<div class="row">'
                    html +='<div class="col-6">'
                    html +='<div class="form-group">'
                    html +='<label for="exampleFormControlInput1">Set 1<sup>*</sup></label>'
                    html +='<input type="text" value="'+response.match_detail.teamOneSetOneScore+'" class="form-control" id="teamOneSetOneScore"  placeholder="Set1 Score">'
                    html +='</div>'
                    html +='</div>'
                    html +='<div class="col-6">'
                    html +='<div class="form-group">'
                    html +='<label for="exampleFormControlInput1">Set 1<sup>*</sup></label>'
                    html +='<input type="text" value="'+response.match_detail.teamTwoSetOneScore+'" class="form-control" id="teamTwoSetOneScore"  placeholder="Set2 Score">'
                    html +='</div>'
                    html +='</div>'
                    html +='</div>'
                    html +='<div class="row">'
                    html +='<div class="col-6">'
                    html +='<div class="form-group">'
                    html +='<label for="exampleFormControlInput1">Set 2<sup>*</sup></label>'
                    html +='<input type="text" value="'+response.match_detail.teamOneSetTwoScore+'" class="form-control" id="teamOneSetTwoScore"  placeholder="Set2 Score">'
                    html +='</div>'
                    html +='</div>'
                    html +='<div class="col-6">'
                    html +='<div class="form-group">'
                    html +='<label for="exampleFormControlInput1">Set 2<sup>*</sup></label>'
                    html +='<input type="text" value="'+response.match_detail.teamTwoSetTwoScore+'" class="form-control" id="teamTwoSetTwoScore"   placeholder="Set2 Score">'
                    html +='</div>'
                    html +='</div>'
                    html +='</div>'
                    html +='<div class="row">'
                    html +='<div class="col-6">'
                    html +='<div class="form-group">'
                    html +='<label for="exampleFormControlInput1">Set 3<sup>*</sup></label>'
                    html +='<input type="text" value="'+response.match_detail.teamOneSetThreeScore+'" class="form-control" id="teamOneSetThreeScore"  placeholder="Set3 Score">'
                    html +='</div>'
                    html +='</div>'
                    html +='<div class="col-6">'
                    html +='<div class="form-group">'
                    html +='<label for="exampleFormControlInput1">Set 3<sup>*</sup></label>'
                    html +='<input type="text" value="'+response.match_detail.teamTwoSetThreeScore+'" class="form-control" id="teamTwoSetThreeScore"  placeholder="Set3 score">'
                    html +='</div>'
                    html +='</div>'
                    html +='</div>'

                    $.LoadingOverlay('hide');
                    $("#matchScoreDetail").empty();
                    $("#matchScoreDetail").append(html);
                    $("#scoreModal").show();
                }
            });
        });

          $(document).on('click','#editScore',function () {
             var id=$('#editMatchId').val()
             var teamOneSetOneScore=$('#teamOneSetOneScore').val()
             var teamOneSetTwoScore=$('#teamOneSetTwoScore').val()
             var teamOneSetThreeScore=$('#teamOneSetThreeScore').val()
             var teamTwoSetOneScore=$('#teamTwoSetOneScore').val()
             var teamTwoSetTwoScore=$('#teamTwoSetTwoScore').val()
             var teamTwoSetThreeScore=$('#teamTwoSetThreeScore').val()
            $.ajaxSetup({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
            });
            $.ajax({
                type: 'POST',
                url: '{{ url('/chat/update-score') }}',
                data: {
                    'matchId':id,'teamOneSetOneScore':teamOneSetOneScore,'teamOneSetTwoScore':teamOneSetTwoScore,'teamOneSetThreeScore':teamOneSetThreeScore,'teamTwoSetOneScore':teamTwoSetOneScore,
                    'teamTwoSetTwoScore':teamTwoSetTwoScore,'teamTwoSetTwoScore':teamTwoSetTwoScore,'teamTwoSetThreeScore':teamTwoSetThreeScore
                },
                  beforeSend: function () {
                $.LoadingOverlay('show');
            },
            success: function(response) {
            if (response.status == 400) {

                     toastr.error("Only Integers value now");
                    $.LoadingOverlay('hide');
                    $("#scoreModal").show();
            }
            if (response.status == 200) {
                  $.LoadingOverlay('hide');
                 $("#scoreModal").hide();
                 toastr.success("'Match score updated successfully");

                 }
                },


            });
        });


        function changeTime(time) {
            let res;
            $.ajax({
                type: 'GET',
                url: '{{ url('/chat/change-time') }}',
                data: {
                    'time':time,
                },
                success: function(response) {
                    res= response;
                }
            });
             return res;
        }
        $(document).ready(function(){
            $('.userChat').click();
        });
        let currentTimestamp=Date.now();


         //  customer support count

        var adminChatIssueChatCount=0;
         if(adminChatIssueChats.length>0){
           adminChatIssueChats.forEach(async (adminChatIssueChat) => {
                var issueId=adminChatIssueChat.issueId.toString();
                customerSupportChatListener("admin_chat",issueId,adminChatIssueChat.adminUId,adminChatIssueChat.userUId,"issue_chat",adminChatIssueChat.id)
                .then(() => {
                    console.log('Overall execution completed.');
                })
                .catch((error) => {
                    console.log('Error during execution:', error);
                });
            });
         }
         async function customerSupportChatListener(parrentCollection, issueId, adminUid, userUid, childCollection,chatId) {
            var lastMessageTime = 0;
            const lastSeenQuery = query(
                collection(db, parrentCollection, issueId, adminUid, userUid, 'last_seen_message')
            );
            const lastSeenSnapshot = await getDocs(lastSeenQuery);
            lastSeenSnapshot.forEach((lastSeendoc) => {
                if (lastSeendoc.id === adminUid) {
                    lastMessageTime = lastSeendoc.data().lastMessageTime;
                }
            });

            const q = query(
                collection(db, parrentCollection, issueId, adminUid, userUid, childCollection),
                orderBy("lastMessageTime"),
                startAfter(lastMessageTime)
            );
            onSnapshot(q, (snapshot) => {
                snapshot.docChanges().forEach((change) => {
                    if (change.type === 'added') {
                        if(document.querySelector("li.supportChatMessages[data-supportIssueId='" + issueId + "'].active")){
                            // console.log("Inside if has class.");
                            setTimeout(()=>{
                                saveUnreadMessageCountTab(null , chatId);
                            }, 1000);
                        }else{
                            // console.log("Inside else case.");
                            adminChatIssueChatCount++;
                        }
                        $('.totalSupportUnreadCount').html(adminChatIssueChatCount);
                    }
                });
            });
        }
        async function customerSupportChatListenerDecrement(parrentCollection, issueId, adminUid, userUid, childCollection) {
            var lastMessageTime = 0;
            const lastSeenQuery = query(
                collection(db, parrentCollection, issueId, adminUid, userUid, 'last_seen_message')
            );
            const lastSeenSnapshot = await getDocs(lastSeenQuery);
            lastSeenSnapshot.forEach((lastSeendoc) => {
                if (lastSeendoc.id === adminUid) {
                    lastMessageTime = lastSeendoc.data().lastMessageTime;
                }
            });
            console.log(parrentCollection+" -- "+issueId+" -- "+adminUid+" -- "+userUid+" -- "+childCollection);
            const q = query(
                collection(db, parrentCollection, issueId, adminUid, userUid, childCollection),
                orderBy("lastMessageTime"),
                startAfter(lastMessageTime)
            );
            try {
                const getLastSeenDocs = await getDocs(q);
                getLastSeenDocs.forEach((lastSeendoc) => {
                        adminChatIssueChatCount--;
                        $('.totalSupportUnreadCount').html(adminChatIssueChatCount);
                    });
            } catch (error) {
                console.log("Error fetching documents:", error);
            }
        }



        // user chat count

    var userChatCount=0;
    if(userChat.length>0){
        userChat.forEach(async function(UserChat){
        var matchId=UserChat.matchId.toString();
        userChatListener("Chat",matchId,"match_chat",UserChat.id).then(() => {
                // console.log('Overall user chat execution completed.');
            })
            .catch((error) => {
                console.log('Error user chat during execution:', error);
            });
        });
    }


   async function userChatListener(parrentCollection,matchId,childCollection,chatId) {

            let adminUid={!!json_encode(auth()->user()->uid)!!};
        var userChatLastMessageTime = 0;
        const userChatLastSeenQuery = query(
            collection(db, parrentCollection, matchId, 'last_seen_message')
        );
        const userChatLastSeenSnapshot = await getDocs(userChatLastSeenQuery);
        userChatLastSeenSnapshot.forEach((userChatLastSeendoc) => {
            if (userChatLastSeendoc.id === adminUid) {
                userChatLastMessageTime = userChatLastSeendoc.data().lastMessageTime;
            }
        });
        const q= query(collection(db, parrentCollection,matchId,childCollection),
        orderBy("lastMessageTime"),
        startAfter(userChatLastMessageTime));
        onSnapshot(q,(snapshot) => {
        snapshot.docChanges().forEach((change) => {
            if (change.type === 'added') {
            if(document.querySelector("li.userChatMessages[data-id='" + matchId + "'].active")){
                    setTimeout(()=>{
                        saveUnreadMessageCountTab(null , chatId);
                    }, 1000);
                }else{
                    userChatCount++;
                }
                $('.userChatUnreadCount').html(userChatCount);
                userChatGlobalCount=userChatCount;
                $('.totalChatUnreadCount').html(adminChatsCount+userChatCount);
            }
            });
        });
    }


    async function userChatListenerDecrement(parrentCollection,matchId,childCollection){

        let adminUid={!!json_encode(auth()->user()->uid)!!};
        var userChatLastMessageTime = 0;
        const userChatLastSeenQuery = query(
            collection(db, parrentCollection, matchId, 'last_seen_message')
        );
        const userChatLastSeenSnapshot = await getDocs(userChatLastSeenQuery);
        userChatLastSeenSnapshot.forEach((userChatLastSeendoc) => {
            if (userChatLastSeendoc.id === adminUid) {
                userChatLastMessageTime = userChatLastSeendoc.data().lastMessageTime;
            }
        });
        const q = query(
            collection(db, parrentCollection, matchId, childCollection),
            orderBy("lastMessageTime"),
            startAfter(userChatLastMessageTime)
        );
        try {
            const getUserChatLastSeenDocs = await getDocs(q);
            getUserChatLastSeenDocs.forEach((lastSeendoc) => {
                    userChatCount--;
                $('.userChatUnreadCount').html(userChatCount);
                 userChatGlobalCount=userChatCount;
                $('.totalChatUnreadCount').html(adminChatsCount+userChatCount);
                });
        } catch (error) {
            console.log("Error fetching documents:", error);
        }
    }

        // admin chat count

    var adminChatsCount=0;
    if(adminChats.length>0){
        adminChats.forEach(function(adminChat){
        var adminChatIssueId=adminChat.issueId.toString();
        adminChatListener("admin_chat",adminChatIssueId,adminChat.adminUId,adminChat.userUId,"issue_chat",adminChat.id).then(() => {
                console.log('Overall execution completed.');
            })
            .catch((error) => {
                console.log('Error during execution:', error);
            });
        });
    }




    async function adminChatListener(parrentCollection,adminChatIssueId,adminUid,userUid,childCollection,chatId) {
            var adminChatLastMessageTime = 0;
        const adminChatLastSeenQuery = query(
            collection(db, parrentCollection, adminChatIssueId, adminUid, userUid, 'last_seen_message')
        );
        const adminChatLastSeenSnapshot = await getDocs(adminChatLastSeenQuery);
        adminChatLastSeenSnapshot.forEach((adminChatLastSeendoc) => {
            if (adminChatLastSeendoc.id === adminUid) {
                adminChatLastMessageTime = adminChatLastSeendoc.data().lastMessageTime;
            }
        });

        const q = query(
            collection(db, parrentCollection, adminChatIssueId, adminUid, userUid, childCollection),
            orderBy("lastMessageTime"),
            startAfter(adminChatLastMessageTime)
        );
        onSnapshot(q, (snapshot) => {
            snapshot.docChanges().forEach((change) => {
            if (change.type === 'added') {
                if(document.querySelector("li.adminChatMessages[data-supportissueid='" + adminChatIssueId + "'].active")){
                    setTimeout(()=>{
                        saveUnreadMessageCountTab(null , chatId);
                    }, 1000);
                }else{
                    adminChatsCount++;
                }
                $('.adminChatUnreadCount').html(adminChatsCount);
                adminChatGlobalCount=adminChatsCount;
                $('.totalChatUnreadCount').html(adminChatsCount+userChatCount);
            }
            });
        });
    }
    async function adminChatListenerDecrement(parrentCollection, issueId, adminUid, userUid, childCollection) {
        var adminChatLastMessageTime = 0;
        const adminChatLastSeenQuery = query(
            collection(db, parrentCollection, issueId, adminUid, userUid, 'last_seen_message')
        );
        const adminChatLastSeenSnapshot = await getDocs(adminChatLastSeenQuery);
        adminChatLastSeenSnapshot.forEach((adminChatLastSeendoc) => {
            if (adminChatLastSeendoc.id === adminUid) {
                adminChatLastMessageTime = adminChatLastSeendoc.data().lastMessageTime;
            }
        });
        console.log("Admin");
        const q = query(
            collection(db, parrentCollection, issueId, adminUid, userUid, childCollection),
            orderBy("lastMessageTime"),
            startAfter(adminChatLastMessageTime)
        );
        try {
            const getLastSeenDocs = await getDocs(q);
            getLastSeenDocs.forEach((lastSeendoc) => {
                    adminChatsCount--;
                    $('.adminChatUnreadCount').html(adminChatsCount);
                      adminChatGlobalCount=adminChatsCount;
                $('.totalChatUnreadCount').html(adminChatsCount+userChatCount);
                });
        } catch (error) {
            console.log("Error fetching documents:", error);
        }
    }

      // dispute Chats

        // single dispute chat count

        var supportSingleChatsCount=0;
        if(supportSingleChats.length>0){
            supportSingleChats.forEach(async (supportSingleChat) => {
                var supportSingleChatMatchId=supportSingleChat.matchId.toString();
                singleDisputeChatListener("support",supportSingleChatMatchId,supportSingleChat.adminUId,supportSingleChat.userUId,"issue_chat",supportSingleChat.id)
                .then(() => {
                        console.log('Overall execution completed.');
                    })
                    .catch((error) => {
                        console.log('Error during execution:', error);
                    });
            });
        }



        async function singleDisputeChatListener(parrentCollection,matchId,adminUid,userUid,childCollection,chatId) {
            var lastMessageTime = 0;
            const lastSeenQuery = query(
                collection(db, parrentCollection, matchId, adminUid, userUid, 'last_seen_message')
            );
            const lastSeenSnapshot = await getDocs(lastSeenQuery);
            lastSeenSnapshot.forEach((lastSeendoc) => {
                if (lastSeendoc.id === adminUid) {
                    lastMessageTime = lastSeendoc.data().lastMessageTime;
                }
            });

            const q = query(
                collection(db, parrentCollection, matchId, adminUid, userUid, childCollection),
                orderBy("lastMessageTime"),
                startAfter(lastMessageTime)
            );
            onSnapshot(q,(snapshot) => {
                snapshot.docChanges().forEach((change) => {
                    if (change.type === 'added') {
                        if(document.querySelector("li.disputeChatMessages[data-chatid='" + chatId + "'].active")){
                            // console.log("Inside if has class.");
                            setTimeout(()=>{
                                saveUnreadMessageCountTab(null , chatId);
                            }, 1000);
                        }else{
                            // console.log("Inside else case.");
                            supportSingleChatsCount++;
                        }
                        singleMatchDisputeCount = supportSingleChatsCount;
                        $('.disputeSingleUnreadCount').html(supportSingleChatsCount);
                        $('.totalDisputeUnreadCount').html(supportSingleChatsCount+supportDoubleChatsCount);
                    }
                });
            });
        }
        async function singleDisputeChatListenerDecrement(parrentCollection, matchId, adminUid, userUid, childCollection) {
            var lastMessageTime = 0;
            const lastSeenQuery = query(
                collection(db, parrentCollection, matchId, adminUid, userUid, 'last_seen_message')
            );
            const lastSeenSnapshot = await getDocs(lastSeenQuery);
            lastSeenSnapshot.forEach((lastSeendoc) => {
                if (lastSeendoc.id === adminUid) {
                    lastMessageTime = lastSeendoc.data().lastMessageTime;
                }
            });
            const q = query(
                collection(db, parrentCollection, matchId, adminUid, userUid, childCollection),
                orderBy("lastMessageTime"),
                startAfter(lastMessageTime)
            );
            try {
                const getLastSeenDocs = await getDocs(q);
                getLastSeenDocs.forEach((lastSeendoc) => {
                        supportSingleChatsCount--;
                        singleMatchDisputeCount = supportSingleChatsCount;
                        $('.disputeSingleUnreadCount').html(supportSingleChatsCount);
                        $('.totalDisputeUnreadCount').html(supportSingleChatsCount+supportDoubleChatsCount);
                    });
            } catch (error) {
                console.log("Error fetching documents:", error);
            }
        }

        // double dispute chat count
        var supportDoubleChatsCount=0;
        if(supportDoubleChats.length>0){
            supportDoubleChats.forEach(async (supportDoubleChat) => {
                var supportDoubleChatMatchId=supportDoubleChat.matchId.toString();
                doubleSupportChatListener("support",supportDoubleChatMatchId,supportDoubleChat.adminUId,supportDoubleChat.userUId,"issue_chat",supportDoubleChat.id)
                .then(() => {
                        console.log('Overall execution completed.');
                    })
                    .catch((error) => {
                        console.log('Error during execution:', error);
                    });
            });
        }



        async function doubleSupportChatListener(parrentCollection,matchId,adminUid,userUid,childCollection,chatId) {
        var lastMessageTime = 0;
        const lastSeenQuery = query(
            collection(db, parrentCollection, matchId, adminUid, userUid, 'last_seen_message')
        );
        const lastSeenSnapshot = await getDocs(lastSeenQuery);
        lastSeenSnapshot.forEach((lastSeendoc) => {
            if (lastSeendoc.id === adminUid) {
                lastMessageTime = lastSeendoc.data().lastMessageTime;
            }
        });

        const q= query(
            collection(db,  parrentCollection,matchId,adminUid,userUid,childCollection),
            orderBy("lastMessageTime"),
            startAfter(lastMessageTime)
        );
        onSnapshot(q,(snapshot) => {
           snapshot.docChanges().forEach((change) => {
                if (change.type === 'added') {
                    if(document.querySelector("li.disputeChatMessages[data-chatid='" + chatId + "'].active")){
                        // console.log("Inside if has class.");
                        setTimeout(()=>{
                            saveUnreadMessageCountTab(null , chatId);
                        }, 1000);
                    }else{
                        // console.log("Inside else case.");
                        supportDoubleChatsCount++;
                    }
                    $('.totalDisputeUnreadCount').html(supportSingleChatsCount+supportDoubleChatsCount);
                    doubleMatchDisputeCount = supportDoubleChatsCount;
                    $('.disputeDoubleUnreadCount').html(supportDoubleChatsCount);
                }
            });
        });
      }
      async function doubleSupportChatListenerDecrement(parrentCollection, matchId, adminUid, userUid, childCollection) {
            var lastMessageTime = 0;
            const lastSeenQuery = query(
                collection(db, parrentCollection, matchId, adminUid, userUid, 'last_seen_message')
            );
            const lastSeenSnapshot = await getDocs(lastSeenQuery);
            lastSeenSnapshot.forEach((lastSeendoc) => {
                if (lastSeendoc.id === adminUid) {
                    lastMessageTime = lastSeendoc.data().lastMessageTime;
                }
            });
            console.log(parrentCollection+" -- "+matchId+" -- "+adminUid+" -- "+userUid+" -- "+childCollection+" -- "+lastMessageTime);
            const q = query(
                collection(db, parrentCollection, matchId, adminUid, userUid, childCollection),
                orderBy("lastMessageTime"),
                startAfter(lastMessageTime)
            );
            try {
                const getLastSeenDocs = await getDocs(q);
                getLastSeenDocs.forEach((lastSeendoc) => {
                        supportDoubleChatsCount--;
                        doubleMatchDisputeCount = supportDoubleChatsCount;
                        $('.disputeDoubleUnreadCount').html(supportDoubleChatsCount);
                        $('.totalDisputeUnreadCount').html(supportSingleChatsCount+supportDoubleChatsCount);
                    });
            } catch (error) {
                console.log("Error fetching documents:", error);
            }
        }




    </script>
    @endsection
