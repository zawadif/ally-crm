@extends('layouts.webportalMaster')
@section('title','Web Portal Congratulations')

@section('style')
<link href="{{ asset('css/manageAdministrator.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="content-wrapper" style="background-color: #f8f9fa; height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;">
        <section class="content pt-4 px-lg-5 px-md-2 px-sm-2 px-xs-2" style="">
            <b style="font-size:larger;">Congratulations </b>
          <p>
            Now, you are part of purchased ladder to play with other members and start scheduling match with other participants.
          </p>
          <a  class="btn btn-success" href="{{url('')."/interceptUrl?status=true"}}">Open App Now</a>
        </section>
    </div>
@endsection
