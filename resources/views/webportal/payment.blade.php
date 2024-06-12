@extends('layouts.webportalMaster')
@section('title', 'Web Portal Payment')

@section('style')
    <link href="{{ asset('css/payment.css') }}" rel="stylesheet">

@endsection


@section('content')

    <div class="content-wrapper" style="background-color: #f8f9fa;">
        <section class="content pt-4 px-lg-5 px-md-2 px-sm-2 px-xs-2">
            <div class="row mt-3">
                <div class="col-lg-4"></div>
                <div class="col-lg-4 ml-3">
                    <div class="row">
                        <a href="{{ url('webportal/my-subscription') }}" class="btn btn-sm"> <i class="fa fa-arrow-left"
                                aria-hidden="true"></i>
                        </a>
                        <span><b style="font-size:larger">{{  ucfirst($activeSeason->title) }} </b><span class="badge badge-success"
                                style="
                        margin-left: 50px;
                    ">Active</span></span>
                    </div>
                </div>
                <div class="col-lg-4"></div>
            </div>
            <div class="row">
                <div class="col-lg-4"></div>
                <div class="col-lg-4" style="background-color:white;">
                    <div class="row">
                        <div class="col-lg-12 p-4">
                            @if ($creditView == false)
                                <form action="{{ route('stripe.post') }}" method="POST" id="subscribe-form">
                                @else
                                    <form action="{{ route('stripe.post') }}" method="POST">
                            @endif
                            <div class="form-row formRowMarginTop">
                                @if ($viewLadderList)
                                    <label class="labelFont" for="ladderId">Select Ladder</label>
                                    <select class="form-control" name="ladderId" id="ladderId">
                                        <option value="" data-price="0.00">Select Ladder</option>
                                        @foreach ($ladders as $ladder)
                                            <option value="{{ $ladder->id }}" data-price="{{ $ladder->price }}"
                                                {{ $ladder->id == $ladderId ? 'selected' : '' }}>{{ $ladder->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <label class="labelFont" for="ladderId">Ladder</label>
                                    <input type="text" class="form-control" name="ladderName" id="ladderName"
                                        value="{{ $selectedLadder->name }}" readonly>
                                    <input type="hidden" class="form-control" name="ladderId" id="ladderId"
                                        value="{{ $selectedLadder->id }}">
                                @endif
                            </div>
                            @csrf
                            <input type="hidden" class="form-control" id="seasonId" name="seasonId"
                                value="{{ $activeSeason->id }}">
                            <input type="hidden" class="form-control" id="otherPlayerId" name="otherPlayerId"
                                value="{{ $otherPlayerId }}">
                            <input type="hidden" class="form-control" id="otherPlayerEmail" name="otherPlayerEmail"
                                value="{{ $otherPlayerEmail }}">
                            <input type="hidden" class="form-control" id="requestType" name="requestType"
                                value="{{ $requestType }}">
                            <input type="hidden" class="form-control" id="amount" name="amount" value="">
                            <input type="hidden" class="form-control" id="availableCredits" name="availableCredits"
                                value="{{ $availableCredits }}">
                            <input type="hidden" class="form-control" id="creditView" name="creditView"
                                value="{{ $creditView }}">

                            @if ($creditView == false)
                                <div class="form-row formRowMarginTop">
                                    <label class="labelFont" for="card-holder-name">Card Holder Name</label>
                                    <input class="form-control" id="card-holder-name" name="card-holder-name"
                                        type="text">
                                </div>
                                <div class="form-row formRowMarginTop">
                                    <label class="labelFont" for="card-element">Credit/Debit Card Information</label>
                                    <div id="card-element" class="form-control">
                                    </div>
                                    <!-- Used to display form errors. -->
                                    <div id="card-errors" role="alert" class="text-danger"></div>
                                </div>
                                @if ($availableCredits > 0)
                                    <div class="form-row formRowMarginTop">
                                        Credits available ${{ $availableCredits }}
                                    </div>
                                    <div class="form-row formRowMarginTop">
                                        Amount to be deducted from card $<b id="deductionFromCard"></b>
                                    </div>
                                @endif
                                <hr style="margin-top: 10rem;">
                                <div class="stripe-errors"></div>

                                @if (count($errors) > 0)
                                    <div class="alert alert-danger">
                                        @foreach ($errors->all() as $error)
                                            {{ $error }}<br>
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                <div class="form-row formRowMarginTop">
                                    Credits available ${{ $availableCredits }}
                                </div>
                                <div class="form-row formRowMarginTop">
                                </div>
                                <hr style="margin-top: 10rem;">
                            @endif

                            <div class="row">
                                <div class="col-lg-8">
                                    <h4><small style="font-size: initial;"> Price </small><b id="ladderPrice"></b>
                                    </h4>
                                </div>
                                <div class="col-lg-4">
                                    <button id="card-button" data-secret="{{ $intent->client_secret }}"
                                        class="btn btn-sm btn-success btn-block orm-control">Pay Now</button>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>

                </div>
                <div class="col-lg-4"></div>
            </div>
        </section>
    </div>
    @if ($creditView == false)
        <script src="https://js.stripe.com/v3/"></script>
        <script type="text/javascript">
            var stripe = Stripe('{{ env('STRIPE_KEY') }}');
            var elements = stripe.elements();
            var style = {
                base: {
                    color: '#32325d',
                    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                    fontSmoothing: 'antialiased',
                    fontSize: '16px',
                    '::placeholder': {
                        color: '#aab7c4'
                    }
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a'
                }
            };
            var card = elements.create('card', {
                hidePostalCode: true,
                style: style
            });
            card.mount('#card-element');
            card.addEventListener('change', function(event) {
                var displayError = document.getElementById('card-errors');
                if (event.error) {
                    displayError.textContent = event.error.message;
                } else {
                    displayError.textContent = '';
                }
            });
            const cardHolderName = document.getElementById('card-holder-name');
            const cardButton = document.getElementById('card-button');
            const clientSecret = cardButton.dataset.secret;
            cardButton.addEventListener('click', async (e) => {
                e.preventDefault();
                $('#card-button').prop('disabled', true);
                console.log("attempting");
                const {
                    setupIntent,
                    error
                } = await stripe.confirmCardSetup(
                    clientSecret, {
                        payment_method: {
                            card: card,
                            billing_details: {
                                name: cardHolderName.value
                            }
                        }
                    }
                );
                if ($('#ladderId').val() == "") {
                    var errorElement = document.getElementById('card-errors');
                    errorElement.textContent = "Select ladder.";
                    $('#card-button').prop('disabled', false);
                } else if ($('#card-holder-name').val() == "") {
                    var errorElement = document.getElementById('card-errors');
                    errorElement.textContent = "Enter card holder name.";
                    $('#card-button').prop('disabled', false);
                } else {
                    if (error) {
                        var errorElement = document.getElementById('card-errors');
                        errorElement.textContent = error.message;
                        $('#card-button').prop('disabled', false);
                    } else {
                        paymentMethodHandler(setupIntent.payment_method);
                    }
                }

            });

            function paymentMethodHandler(payment_method) {
                var form = document.getElementById('subscribe-form');
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'payment_method');
                hiddenInput.setAttribute('value', payment_method);
                form.appendChild(hiddenInput);
                form.submit();
            }
        </script>
    @endif
@endsection
@section('script')

    <script>
        $('#card-button').prop('disabled', false);
        $('#amount').val({{ $selectedLadder->price }});
        let price={{$selectedLadder->price}};
        let creditAvailable = {{$availableCredits}};
        var availablePrice = price - creditAvailable;
        $('#deductionFromCard').html(availablePrice);
        $('#ladderPrice').html("$" +  parseFloat(price).toFixed(2));
        $('#ladderId').on('change', function() {
            $('#ladderPrice').html("$" + ($(this).find(':selected').attr('data-price')));
            $('#amount').val($(this).find(':selected').attr('data-price'));
        });
    </script>
@endsection
