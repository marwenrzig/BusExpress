
@extends($activeTemplate.$layout)
@section('content')
@php
$counters = App\Models\Counter::get();
@endphp
<div class="ticket-search-bar bg_img padding-top" style="background: url({{ getImage('assets/templates/basic/images/bg/inner.jpg') }}) left center;">
    <div class="container">
        <div class="bus-search-header">
            <form action="{{ route('search') }}" class="ticket-form ticket-form-two row g-3 justify-content-center">
                <div class="col-md-4 col-lg-3">
                    <div class="form--group">
                        <i class="las la-location-arrow"></i>
                        <select name="pickup" class="form--control select2" required>
                            <option value="">@lang('Pickup Point')</option>
                            @foreach ($counters as $counter)
                            <option value="{{ $counter->id }}" @if(request()->pickup == $counter->id) selected @endif>{{ __($counter->nom) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4 col-lg-3">
                    <div class="form--group">
                        <i class="las la-map-marker"></i>
                        <select name="destination" class="form--control select2" required>
                            <option value="">@lang('Dropping Point')</option>
                            @foreach ($counters as $counter)
                            <option value="{{ $counter->id }}" @if(request()->destination == $counter->id) selected @endif>{{ __($counter->nom) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4 col-lg-3">
                    <div class="form--group">
                        <i class="las la-calendar-check"></i>
                        <input type="text" name="date_of_journey" class="form--control datepicker" placeholder="@lang('Date of Journey')" autocomplete="off" value="{{ request()->date_of_journey }}" required>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="form--group">
                        <button>@lang('Find Tickets')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Ticket Search Starts -->


<!-- Ticket Section Starts Here -->
<section class="ticket-section padding-bottom section-bg">
    <div class="container">
        <div class="row gy-5">
            <div class="col-lg-3">
                <form action="{{ route('search') }}" id="filterForm">
                    <div class="ticket-filter">
                        <div class="filter-header filter-item">
                            <h4 class="title mb-0">@lang('Filter')</h4>
                            <button type="reset" class="reset-button h-auto">@lang('Reset All')</button>
                        </div>
                        @if($fleetType)
                        <div class="filter-item">
                            <h5 class="title">@lang('Vehicle Type')</h5>
                            <ul class="bus-type">
                                @foreach ($fleetType as $fleet)
                                <li class="custom--checkbox">
                                    <input name="fleetType[]" class="search" value="{{ $fleet->id }}" id="{{ $fleet->nom }}" type="checkbox" @if (request()->fleetType)
                                    @foreach (request()->fleetType as $item)
                                    @if ($item == $fleet->id)
                                    checked
                                    @endif
                                    @endforeach
                                    @endif >
                                    <label for="{{ $fleet->nom }}"><span><i class="las la-bus"></i>{{ __($fleet->nom) }}</span></label>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        @if ($routes)
                        <div class="filter-item">
                            <h5 class="title">@lang('Routes')</h5>
                            <ul class="bus-type">
                                @foreach ($routes as $route)
                                <li class="custom--checkbox">
                                    <input name="routes[]" class="search" value="{{ $route->id }}" id="route.{{ $route->id }}" type="checkbox" @if (request()->routes)
                                    @foreach (request()->routes as $item)
                                    @if ($item == $route->id)
                                    checked
                                    @endif
                                    @endforeach
                                    @endif >
                                    <label for="route.{{ $route->id }}"><span><span><i class="las la-road"></i> {{ __($route->name) }} </span></label>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        @if ($schedules)
                        <div class="filter-item">
                            <h5 class="title">@lang('Schedules')</h5>
                            <ul class="bus-type">
                                @foreach ($schedules as $schedule)
                                <li class="custom--checkbox">
                                    <input name="schedules[]" class="search" value="{{ $schedule->id }}" id="schedule.{{ $schedule->id }}" type="checkbox" @if (request()->schedules)
                                    @foreach (request()->schedules as $item)
                                    @if ($item == $schedule->id)
                                    checked
                                    @endif
                                    @endforeach
                                    @endif>
                                    <label for="schedule.{{ $schedule->id }}"><span><span><i class="las la-clock"></i> {{ showDateTime($schedule->depart, 'h:i a').' - '. showDateTime($schedule->arrive, 'h:i a') }} </span></label>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                </form>
            </div>
            <div class="col-lg-9">
                <div class="ticket-wrapper">
                    @forelse ($trips as $trip)
                    @php
                    $start = Carbon\Carbon::parse($trip->schedule->depart);
                    $end = Carbon\Carbon::parse($trip->schedule->arrive);
                    $diff = $start->diff($end);
                    $ticket = App\Models\TicketPrice::where('fleet_type_id', $trip->fleetType->id)->where('vehicle_route_id', $trip->route->id)->first();
                    @endphp


                    <div class="ticket-item">
                        <div class="ticket-item-inner">
                            <h5 class="bus-name">{{ __($trip->title) }}</h5>
                            <span class="bus-info">@lang('Seat Layout') {{ __($trip->fleetType->place_layout) }}</span>
                            <span class="ratting"><i class="las la-bus"></i>{{ __($trip->fleetType->nom) }}</span>
                        </div>
                        <div class="ticket-item-inner travel-time">
                            <div class="bus-time">
                                <p class="time">{{ showDateTime($trip->schedule->depart, 'h:i A') }}</p>
                                <p class="place">{{ __($trip->startFrom->nom) }}</p>
                            </div>
                            <div class=" bus-time">
                                <i class="las la-arrow-right"></i>
                                <p>{{ $diff->format('%H:%I min') }}</p>
                            </div>
                            <div class=" bus-time">
                                <p class="time">{{ showDateTime($trip->schedule->arrive, 'h:i A') }}</p>
                                <p class="place">{{ __($trip->endTo->nom) }}</p>
                            </div>
                        </div>
                        <div class="ticket-item-inner book-ticket">
                            <p class="rent mb-0">{{ __($general->cur_sym) }}{{ showAmount($ticket->price) }}</p>
                            @if($trip->day_off)
                            <div class="seats-left mt-2 mb-3 fs--14px">
                                @lang('Off Days'): <div class="d-inline-flex flex-wrap" style="gap:5px">
                                    @foreach ($trip->day_off as $item)
                                    <span class="badge badge--primary">{{ __(showDayOff($item)) }}</span>
                                    @endforeach
                                </div>
                                @else
                                @lang('Every day available')
                                @endif</div>
                            <a class="btn btn--base" href="{{ route('ticket.seats', [$trip->id, slug($trip->title)]) }}">@lang('Select Seat')</a>
                        </div>
                    </div>
                    @empty
                    <div class="ticket-item">
                        <h5>{{ __($emptyMessage) }}</h5>
                    </div>
                    @endforelse
                    @if ($trips->hasPages())
                    {{ paginateLinks($trips) }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('script')
<script>
    (function($) {
        "use strict";
        $('.search').on('change', function() {
            $('#filterForm').submit();
        });

        $('.reset-button').on('click', function() {
            $('.search').attr('checked', false);
            $('#filterForm').submit();
        })
    })(jQuery)
</script>
@endpush