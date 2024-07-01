@extends('admin.layouts.app')
@section('panel')

    <div class="row mb-none-30">
        <div class="col-xl-6 col-lg-6 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--primary b-radius--10 box-shadow">
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{$widget['total_users']}}</span>
                    </div>
                    <div class="desciption">
                        <span class="text--small">@lang('Total Users')</span>
                    </div>
                    <a href="{{route('admin.users.all')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
        <!--<div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--cyan b-radius--10 box-shadow">
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{$widget['verified_users']}}</span>
                    </div>
                    <div class="desciption">
                        <span class="text--small">@lang('Total Verified Users')</span>
                    </div>
                    <a href="{{route('admin.users.active')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
                </div>
            </div>
        </div>-->
        <!--<div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--orange b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="la la-envelope"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{$widget['email_unverified_users']}}</span>
                    </div>
                    <div class="desciption">
                        <span class="text--small">@lang('Total Email Unverified Users')</span>
                    </div>

                    <a href="{{route('admin.users.email.unverified')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
    </div><!-- row end-->


    <div class="row mb-none-30 mt-30">
        <div class="col-xl-6 col-lg-6 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--success b-radius--10 box-shadow">
                <div class="icon">
                    <i class="fa fa-dollar-sign"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{ showAmount($widget['successful_payment']) }} {{ __($general->cur_text) }}</span>
                    </div>
                    <div class="desciption">
                        <span class="text--small">@lang('Successful Payment')</span>
                    </div>
                    <a href="{{route('admin.deposit.successful')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
       <!-- <div class="col-xl-4 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--warning b-radius--10 box-shadow">
                <div class="icon">
                    <i class="fa fa-dollar-sign"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{ showAmount($widget['pending_payment']) }} {{ __($general->cur_text) }}</span>
                    </div>
                    <div class="desciption">
                        <span class="text--small">@lang('Pending Payment')</span>
                    </div>
                    <a href="{{route('admin.deposit.pending')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
                </div>
            </div>
        </div>-->
        <div class="col-xl-6 col-lg-6 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--danger b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="fa fa-dollar-sign"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{ showAmount($widget['rejected_payment']) }} {{ __($general->cur_text) }}</span>
                    </div>
                    <div class="desciption">
                        <span class="text--small">@lang('Rejected Payment')</span>
                    </div>

                    <a href="{{route('admin.deposit.rejected')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
                </div>
            </div>
        </div><!-- dashboard-w1 end -->


    </div><!-- row end-->

    <div class="row mb-none-30 mt-30">
        <div class="col-xl-6 col-lg-6 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--1 b-radius--10 box-shadow">
                <div class="icon">
                    <i class="fa fa-car"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{ $widget['vehicle_with_ac'] }}</span>
                    </div>
                    <div class="desciption">
                        <span class="text--small">Total Vehicles</span>
                    </div>
                    <a href="{{ route('admin.fleet.vehicles') }}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
 <!--<div class="col-xl-4 col-lg-4 col-sm-6 mb-30">
           <div class="dashboard-w1 bg--2 b-radius--10 box-shadow">
                <div class="icon">
                    <i class="fa fa-car"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{ $widget['vehicle_without_ac'] }}</span>
                    </div>
                    <div class="desciption">
                        <span class="text--small">@lang('Non-AC Vehicle')</span>
                    </div>
                    <a href="{{ route('admin.fleet.vehicles') }}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
                </div>
            </div>
        </div>-->
        <div class="col-xl-6 col-lg-6 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--4 b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="fas fa-car-building"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{ $widget['total_counter'] }}</span>
                    </div>
                    <div class="desciption">
                        <span class="text--small">@lang('Total Counter')</span>
                    </div>

                    <a href="{{route('admin.manage.counter')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
                </div>
            </div>
        </div><!-- dashboard-w1 end -->


    </div><!-- row end-->

    <div class="row mb-none-30 mt-5">
        <div class="col-xl-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Latest Booking History')</h5>
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                            <tr>
                                <th>@lang('User')</th>
                                <th>@lang('PNR Number')</th>
                                <th>@lang('Ticket Count')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($soldTickets as $item)
                                <tr>
                                    <td data-label="@lang('User')">
                                    <span class="font-weight-bold">{{ __($item->user->fullname) }}</span>
                                        <br>
                                        <span class="small">
                                        <a href="{{ route('admin.users.detail', $item->user_id) }}"><span>@</span>{{ $item->user->username }}</a>
                                        </span>
                                    </td>
                                    <td data-label="@lang('PNR Number')">
                                        <strong>{{ __($item->pnr_number) }}</strong>
                                    </td>
                                    <td data-label="@lang('Ticket Count')">
                                        <strong>{{ $item->ticket_count }}</strong>
                                    </td>
                                    <td data-label="@lang('Amount')">
                                       {{ showAmount($item->sub_total ) }} {{ __($general->cur_text) }}
                                    </td>
                                    <td data-label="@lang('Action')">
                                        <a href="{{ route('admin.vehicle.ticket.booked') }}"
                                           class="icon-btn ml-1 " data-toggle="tooltip" title="" data-original-title="@lang('Detail')">
                                            <i class="la la-desktop"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">@lang('No booked ticket found')</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Last 30 days Payment History')</h5>
                    <div id="deposit-line"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')

    <script src="{{asset('assets/admin/js/vendor/apexcharts.min.js')}}"></script>
    <script src="{{asset('assets/admin/js/vendor/chart.js.2.8.0.js')}}"></script>
@endpush
