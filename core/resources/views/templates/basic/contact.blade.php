@extends($activeTemplate.'layouts.frontend')

@section('content')

<section class="contact-section padding-top padding-bottom overflow-hidden">
    <div class="container">
        <div class="text-center">
            <h3 class="title mb-2">{{ __(@$content->data_values->title) }}</h3>
            <p class="mb-5">{{ __(@$content->data_values->short_details) }}</p>
        </div>
        <div class="row pb-80 gy-4 justify-content-center">
            <div class="col-sm-6 col-lg-4">
                <div class="info-item">
                    <div class="icon">
                        <i class="flaticon-location"></i>
                    </div>
                    <div class="content">
                        <h5 class="title">@lang('Our Address')</h5>
                        @lang('Address') : {{ __(@$content->data_values->address )}}
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="info-item active">
                    <div class="icon">
                        <i class="flaticon-call"></i>
                    </div>
                    <div class="content">
                        <h5 class="title">@lang('Call Us')</h5>
                        <a href="tel:{{ @$content->data_values->contact_number }}">{{ __(@$content->data_values->contact_number) }}</a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="info-item">
                    <div class="icon">
                        <i class="flaticon-envelope"></i>
                    </div>
                    <div class="content">
                        <h5 class="title">@lang('Email Us')</h5>
                        <a href="mailto:{{ @$content->data_values->email }}">{{ __(@$content->data_values->email) }}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row gy-5">
            <div class="col-lg-12">
                <div class="map-wrapper">
                    <iframe class="map" style="border:0;" src="https://maps.google.com/maps?q={{ @$content->data_values->latitude }},{{ @$content->data_values->longitude }}&hl=es;z=14&amp;output=embed"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>



@endsection
