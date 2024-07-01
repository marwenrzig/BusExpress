@extends('admin.layouts.app')

@section('panel')
<div class="row mb-none-30">
    <div class="col-xl-12 col-lg-12 col-md-12 mb-30">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title border-bottom pb-2">@lang('Information of Route') </h5>

                <form action="{{ route('admin.trip.route.update', $route->id)}}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-control-label font-weight-bold"> @lang('Name')</label>
                                <input type="text" class="form-control" placeholder="@lang('Enter Name')" value="{{ $route->name }}" name="name" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-label font-weight-bold"> @lang('Start From')</label>
                                <select name="start_from" class="select2-basic" required>
                                    <option value="">@lang('Select an option')</option>
                                    @foreach ($allStoppages as $item)
                                        <option value="{{ $item->id }}" @if ($route->start_from == $item->id) selected
                                            @endif>{{ __($item->nom) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-label font-weight-bold"> @lang('End To')</label>
                                <select name="end_to" class="select2-basic" required>
                                    <option value="">@lang('Select an option')</option>
                                    @foreach ($allStoppages as $item)
                                        <option value="{{ $item->id }}" @if ($route->end_to == $item->id) selected
                                        @endif>{{ __($item->nom) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="stoppages-wrapper col-md-12">
                            <div class="row stoppages-row">
                                @foreach ($stoppages as $item)
                                    <div class=" col-md-3">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">{{ $loop->iteration }}</span>
                                            </div>
                                            <select class="select2-basic form-control w-auto" name="stoppages[{{ $loop->iteration }}]" required >
                                                <option value="" selected>@lang('Select Stoppage')</option>
                                                @foreach ($allStoppages as $stoppage)
                                                <option value="{{$stoppage->id}}" {{ $item->id == $stoppage->id?'selected' : '' }}>{{$stoppage->nom}}</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <button type="button" class="input-group-text bg-danger border--danger remove-stoppage"><i class="la la-times"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-control-label font-weight-bold"> @lang('Distance')</label>
                                <input type="text" class="form-control" placeholder="@lang('Enter Distance')" name="distance" value="{{ $route->distance }}" required>
                                <small class="text-danger">@lang('Keep space between value & unit')</small>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn--primary btn-block btn-lg">@lang('Save Changes')
                                </button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.trip.route') }}" class="btn btn-sm btn--primary box--shadow1 text--small addBtn"><i class="la la-fw la-backward"></i>@lang('Go Back')</a>
@endpush
@push('style')
    <style>
        .input-group > .select2-container--default {
            width: auto !important;
            flex: 1 1 auto !important;
        }

        .input-group > .select2-container--default .select2-selection--single {
            height: 100% !important;
            line-height: inherit !important;
        }
    </style>
@endpush
@push('script')
<script>
     "use strict";

     (function($){
        $('.select2-basic').select2({
            dropdownParent: $('.card-body')
        });

        var itr = 2;
        $(document).on('click', '.add-stoppage-btn', function(){
            var elements = $('.stoppages-row .col-md-3');
            $(elements).each(function (index, element) {
                $(element).find('.select2-basic').attr('name',`stoppages[${index+1}]`);

            });

            var itr = elements.length;


            $('.select2-basic').select2({
                dropdownParent: $('.card-body')
            });

            $($('.stoppages-row .col-md-3')).each(function (index, element) {
                $(element).find('.input-group-prepend > .input-group-text').text(index+1);
            });
        });
     })(jQuery)
</script>
@endpush
