@extends('layouts.webportalMaster')
@section('title','Web Portal My Subscription')

@section('style')
<link href="{{ asset('css/manageAdministrator.css') }}" rel="stylesheet">
@endsection
@php
 use App\Models\Team;
@endphp

@section('content')
    <div class="content-wrapper" style="background-color: #f8f9fa;">
        <section class="content pt-4 px-lg-5 px-md-2 px-sm-2 px-xs-2">
            <b style="font-size:larger">My Seasons </b>
            <div class="row">
                @if($activeSeason)
                <div class="col-lg-4 position-relative" style="box-sizing: border-box;">
                    <div class="card shadow-none" style="background-color:white; height:10rem;">
                        <div class="row mt-2">
                            <div class="col-8">
                                <b class="ml-2">{{ucfirst($activeSeason->title)}}</b>
                            </div>
                            <div class="col-4"style="
                            padding-left: 50px;
                        ">
                                <span class="badge position-absolute top-0 end-100" style="background-color: #70c7b4">Active</span>
                            </div>
                        </div>
                        <div class="card-body" style="padding-bottom: 0px;">
                            <div class="row">
                                <div class="col-6 col-md-6">
                                </div>
                                @php
                                    $firstLadder = $ladders->first();
                                    $urlLadder = "ladderId=".$firstLadder->id."&referredUserId=".Auth::user()->id."&ladderList=true&requestType=USER_ID";
                                @endphp
                                <div class="col-6 col-md-6 pull-right float-right" style="padding-top: 4.2rem; padding-right: 0px;">
                                    <a class="btn btn-success btn-sm  mb-1" href="{{url('/webportal/payment?').$urlLadder}}"><small>Buy Now</small></a>
                                    <button class="btn btn-light btn-sm  mb-1"data-toggle="modal" data-target="#exampleModal"><small>View Details</small></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @else 
                    <div class="ml-1 mt-3 mb-3 col-12" style="color: blue;"> No active season found in your region. </div> <br/>
                @endif
                @if($purchases->count() > 0)
                @foreach($purchases as $purchase)
                    <div class="col-lg-4  position-relative" style="box-sizing: border-box;">
                        <div class="card shadow-none" style="background-color:white; height:10rem">
                            <div class="row mt-2">
                                <div class="col-8">
                                    <b class="ml-2">{{ucfirst($purchase->getPurchaseSeason->title)}} <small>{{$purchase->getPurchaseLadder->name}}</small></b>
                                </div>
                                <div class="col-4" style="
                                padding-left: 50px;
                            ">
                                    <span class="badge position-absolute top-0 end-100" style="background-color: #70c7b4">Active</span>
                                </div>
                            </div>
                            <div class="card-body" style="padding-bottom: 0px;">
                                <div class="row" style="font-size: x-small; padding-top: 2.1rem;">

                                    <div class="col-4" style="padding-left: 10px;">
                                        @php
                                            $playerOne=Team::where('ladderId',$purchase->ladderId)->where('firstMemberId','!=',null)->get();

                                            $playerTwo=Team::where('ladderId',$purchase->ladderId)->where('secondMemberId','!=',null)->get();
                                            $totalPlayer=$playerOne->concat($playerTwo);
                                            $totalCount=$totalPlayer->unique();
                                        @endphp
                                        <b>{{count($totalCount)}}</b> <br>
                                        <b>Players</b>
                                    </div>|<br>|
                                    <div class="col-4" style="padding-left: 10px;">
                                        <b>{{$purchase->getPurchaseSeason->noOfWeeks}}</b> <br>
                                        <b>Weeks</b>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                @else 
                    <br/><br/>
                    <div class="ml-3 col-12" style="color: blue;"> You have not purchased any ladder yet. </div>
                @endif

            </div>
        </section>
    </div>

    @include('webportal.models.ladderModel')
@endsection

@section('script')
<script>
$('input[type="checkbox"]').on('change', function() {
    $('input[type="checkbox"]').not(this).prop('checked', false);
    $('.ladderModelButton').attr("href", "");
    var hrefLadder = '{{url("/webportal/payment")}}';
    $('.ladderModelButton').attr("href", hrefLadder+"?ladderId="+this['value']+"&referredUserId="+'{{Auth::user()->id}}'+"&requestType=USER_ID");
});
</script>
@endsection
