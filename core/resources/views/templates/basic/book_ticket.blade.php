@extends($activeTemplate.$layout)
@section('content')
<div class="padding-top padding-bottom">
    <div class="container">
        <div class="row gx-xl-5 gy-4 gy-sm-5 justify-content-center">
            <div class="col-lg-4 col-md-6">
                <div class="seat-overview-wrapper">
                    <form action="{{ route('ticket.book', $trip->id) }}" method="POST" id="bookingForm" class="row gy-2">
                        @csrf
                        <input type="text" name="price" value="{{ $ticketPrice->price }}" hidden>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="date_of_journey" class="form-label">@lang('Journey Date')</label>
                                <input type="text" id="date_of_journey" class="form--control datepicker" value="{{ Session::get('date_of_journey') ? Session::get('date_of_journey') : date('d/m/Y') }}" name="date_of_journey" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="pickup_point" class="form-label">@lang('Pickup Point')</label>
                                <select name="pickup_point" id="pickup_point" class="form--control select2" required>
                                    <option value="">@lang('Select One')</option>
                                    @foreach($stoppages as $item)
                                    <option value="{{ $item->id }}" @if (Session::get('pickup')==$item->id)
                                        selected
                                        @endif>
                                        {{ __($item->nom) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="dropping_point" class="form-label">@lang('Dropping Point')</label>
                                <select name="dropping_point" id="dropping_point" class="form--control select2" required>
                                    <option value="">@lang('Select One')</option>
                                    @foreach($stoppages as $item)
                                    <option value="{{ $item->id }}" @if (Session::get('destination')==$item->id)
                                        selected
                                        @endif>
                                        {{ __($item->nom) }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="booked-seat-details my-3 d-none">
                            <label>@lang('Selected Seats')</label>
                            <div class="list-group seat-details-animate">
                                <span class="list-group-item d-flex bg--base text-white justify-content-between">@lang('Seat Details')<span>@lang('Price')</span></span>
                                <div class="selected-seat-details">
                                </div>
                            </div>
                        </div>
                        <input type="text" name="seats" hidden>
                        <div class="col-12">
                            <button type="submit" class="book-bus-btn">@lang('Continue')</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <h6 class="title">@lang('Click on Seat to select or deselect')</h6>
                @if ($trip->day_off)
                <span class="fs--14px">
                    @lang('Off Days') :
                    @foreach ($trip->day_off as $item)
                    <span class="badge badge--success">
                        {{ __(showDayOff($item)) }}
                        @if (!$loop->last)
                        ,
                        @endif
                    </span>
                    @endforeach
                </span>
                @endif
                @foreach ($trip->fleetType->places_pont as $seat)
                <div class="seat-plan-inner">
                    <div class="single">

                        @php
                        echo $busLayout->getDeckHeader($loop->index);
                        @endphp

                        @php
                        $totalRow = $busLayout->getTotalRow($seat);
                        $lastRowSeat = $busLayout->getLastRowSit($seat);
                        $chr = 'A';
                        $deckIndex = $loop->index + 1;
                        $seatlayout = $busLayout->sitLayouts();
                        $rowItem = $seatlayout->left + $seatlayout->right;
                        @endphp
                        @for($i = 1; $i <= $totalRow; $i++) @php if($lastRowSeat==1 && $i==$totalRow -1) break; $seatNumber=$chr; $chr++; $seats=$busLayout->getSeats($deckIndex,$seatNumber);
                            @endphp
                            <div class="seat-wrapper">
                                @php echo $seats->left; @endphp
                                @php echo $seats->right; @endphp
                            </div>
                            @endfor
                            @if($lastRowSeat == 1)
                            @php $seatNumber++ @endphp
                            <div class="seat-wrapper justify-content-center">
                                @for ($lsr=1; $lsr <= $rowItem+1; $lsr++) @php echo $busLayout->generateSeats($lsr,$deckIndex,$seatNumber); @endphp
                                    @endfor
                            </div><!-- single-row end -->
                            @endif

                            @if($lastRowSeat > 1)
                            @php $seatNumber++ @endphp
                            <div class="seat-wrapper justify-content-center">
                                @for($l = 1; $l <= $lastRowSeat; $l++) @php echo $busLayout->generateSeats($l,$deckIndex,$seatNumber); @endphp
                                    @endfor
                            </div><!-- single-row end -->
                            @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- confirmation modal --}}
<div class="modal fade" id="bookConfirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> @lang('Confirm Booking')</h5>
                <button type="button" class="w-auto btn--close" data-bs-dismiss="modal"><i class="las la-times"></i></button>
            </div>
            <div class="modal-body">
                <strong class="text-dark">@lang('Are you sure to book these seats?')</strong>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn--danger w-auto btn--sm px-3" data-bs-dismiss="modal">
                    @lang('Close')
                </button>
                <button type="submit" class="btn btn--success btn--sm w-auto" id="btnBookConfirm">@lang("Confirm")
                </button>
            </div>
        </div>
    </div>
</div>

{{-- alert modal --}}
<div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> @lang('Alert Message')</h5>
                <button type="button" class="w-auto btn--close" data-bs-dismiss="modal"><i class="las la-times"></i></button>
            </div>
            <div class="modal-body">
                <strong>
                    <p class="error-message text-danger"></p>
                </strong>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn--danger w-auto btn--sm px-3" data-bs-dismiss="modal">
                    @lang('Continue')
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    (function($) {
        "use strict";

        var date_of_journey = '{{ Session::get('
        date_of_journey ') }}';
        var pickup = '{{ Session::get('
        pickup ') }}';
        var destination = '{{ Session::get('
        destination ') }}';

        if (date_of_journey != null && pickup != null && destination != null) {
            showBookedSeat();
        }

        //reset all seats
        function reset() {
            $('.seat-wrapper .seat').removeClass('selected');
            $('.seat-wrapper .seat').parent().removeClass('seat-condition selected-by-ladies selected-by-gents selected-by-others disabled');
            $('.selected-seat-details').html('');
        }

        //click on seat
        $('.seat-wrapper .seat').on('click', function() {
            var date_journay = $('input[name="date_of_journey"]').val();
            var droppingPoing = $('select[name="dropping_point"]').val();
            var pickupPoint = $('select[name="pickup_point"]').val();

            if (date_journay && pickupPoint && droppingPoing) {
                selectSeat();
            } else {
                $(this).removeClass('selected');
                notify('error', "@lang('Please select pickup point and dropping point before select any seat')")
            }
        });

        //select and booked seat
        function selectSeat() {
            let selectedSeats = $('.seat.selected');
            let seatDetails = ``;
            let price = $('input[name=price]').val();
            let subtotal = 0;
            let currency = '{{ __($general->cur_text) }}';
            let seats = '';
            if (selectedSeats.length > 0) {
                $('.booked-seat-details').removeClass('d-none');
                $.each(selectedSeats, function(i, value) {
                    seats += $(value).data('seat') + ',';
                    showBookedSeat();
                    seatDetails += `<span class="list-group-item d-flex justify-content-between">${$(value).data('seat')} <span>${price} ${currency}</span></span>`;
                    subtotal = subtotal + parseFloat(price);
                });

                $('input[name=seats]').val(seats);
                $('.selected-seat-details').html(seatDetails);
                $('.selected-seat-details').append(`<span class="list-group-item d-flex justify-content-between">@lang('Sub total')<span>${subtotal} ${currency}</span></span>`);
                
            } else {
                $('.selected-seat-details').html('');
                $('.booked-seat-details').addClass('d-none');
            }
        }

        //on change date, pickup point and destination point show available seats
        $(document).on('change', 'select[name="pickup_point"], select[name="dropping_point"], input[name="date_of_journey"]', function(e) {
            showBookedSeat();
        });

        //booked seat
        function showBookedSeat() {
            //reset();
            var date = $('input[name="date_of_journey"]').val();
            var sourceId = $('select[name="pickup_point"]').val();
            var destinationId = $('select[name="dropping_point"]').val();

            if (sourceId == destinationId && destinationId != '') {
                notify('error',"@lang('Source Point and Destination Point Must Not Be Same')");
               $('select[name="dropping_point"]').val('').select2();
                return false;
            } else if (sourceId != destinationId) {

                var routeId = '{{ $trip->route->id }}';
                var fleetTypeId = '{{ $trip->fleetType->id }}';

                if (date && sourceId && destinationId) {
                    getprice(routeId, fleetTypeId, sourceId, destinationId, date)
                }
            }
        }

        // check price, booked seat etc
        function getprice(routeId, fleetTypeId, sourceId, destinationId, date) {
            var data = {
                "trip_id": '{{ $trip->id }}',
                "vehicle_route_id": routeId,
                "fleet_type_id": fleetTypeId,
                "source_id": sourceId,
                "destination_id": destinationId,
                "date": date,
            }
            $.ajax({
                type: "get",
                url: "{{ route('ticket.get-price') }}",
                data: data,
                success: function(response) {

                    if (response.error) {
                        var modal = $('#alertModal');
                        modal.find('.error-message').text(response.error);
                        modal.modal('show');
                        $('select[name="pickup_point"]').val('');
                        $('select[name="dropping_point"]').val('');
                    } else {
                        var stoppages = response.stoppages;

                        var reqSource = response.reqSource;
                        var reqDestination = response.reqDestination;

                        reqSource = stoppages.indexOf(reqSource.toString());
                        reqDestination = stoppages.indexOf(reqDestination.toString());

                        if (response.reverse == true) {
                            $.each(response.bookedSeats, function(i, v) {
                                var bookedSource = v.pickup_point; //Booked
                                var bookedDestination = v.dropping_point; //Booked

                                bookedSource = stoppages.indexOf(bookedSource.toString());
                                bookedDestination = stoppages.indexOf(bookedDestination.toString());

                                if (reqDestination >= bookedSource || reqSource <= bookedDestination) {
                                    $.each(v.seats, function(index, val) {
                                        if(v.status == 1){
                                            $(`.seat-wrapper .seat[data-seat="${val}"]`).parent().removeClass('seat-condition selected-by-gents disabled');
                                        }
                                      
                                    });
                                } else {
                                    $.each(v.seats, function(index, val) {
                                        if(v.status == 1){
                                            $(`.seat-wrapper .seat[data-seat="${val}"]`).parent().addClass('seat-condition selected-by-gents disabled');
                                        }
                                      
                                    });
                                }
                            });
                        } else {
                            $.each(response.bookedSeats, function(i, v) {

                                var bookedSource = v.pickup_point; //Booked
                                var bookedDestination = v.dropping_point; //Booked

                                bookedSource = stoppages.indexOf(bookedSource.toString());
                                bookedDestination = stoppages.indexOf(bookedDestination.toString());

                                if (reqDestination <= bookedSource || reqSource >= bookedDestination) {
                                    $.each(v.seats, function(index, val) {
                                       if(v.status == 1 || v.status == "1"){
                                            $(`.seat-wrapper .seat[data-seat="${val}"]`).parent().removeClass('seat-condition selected-by-gents disabled');
                                        }
                                       
                                    });
                                } else {
                                    $.each(v.seats, function(index, val) {
                                        if(v.status == 1 || v.status == "1"){
                                            $(`.seat-wrapper .seat[data-seat="${val}"]`).parent().addClass('seat-condition selected-by-gents disabled');
                                        }
                                        
                                    });
                                }
                            });
                        }

                        if (response.price.error) {
                            var modal = $('#alertModal');
                            modal.find('.error-message').text(response.price.error);
                            modal.modal('show');
                        } else {
                            $('input[name=price]').val(response.price);
                        }
                    }
                }
            });
        }

        //booking form submit
        $('#bookingForm').on('submit', function(e) {
            e.preventDefault();
            let selectedSeats = $('.seat.selected');
            if (selectedSeats.length > 0) {
                var modal = $('#bookConfirm');
                modal.modal('show');
            } else {
                notify('error', 'Sélectionnez au moins une place.');
            }
        });

        //confirmation modal
        $(document).on('click', '#btnBookConfirm', function(e) {
            var modal = $('#bookConfirm');
            modal.modal('hide');
            document.getElementById("bookingForm").submit();
        });

    })(jQuery);
</script>
@endpush
