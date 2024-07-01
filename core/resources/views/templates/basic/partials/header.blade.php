@php
$content = getContent('contact.content', true);
@endphp
<!-- Header Section Starts Here -->

<div class="header-top">
    <div class="container">
        <div class="header-top-area">
            <ul class="left-content">
                <li>
                    <i class="las la-phone"></i>
                    <a href="tel:{{ __(@$content->data_values->contact_number) }}">
                        {{ __(@$content->data_values->contact_number) }}
                    </a>
                </li>
                <li>
                    <i class="las la-envelope-open"></i>
                    <a href="mailto:{{ __(@$content->data_values->email) }}">
                        {{ __(@$content->data_values->email) }}
                    </a>
                </li>
            </ul>
            <div class="right-content d-flex flex-wrap" style="gap:10px">
                @guest
                <ul class="header-login">
                    <li><a class="sign-in" href="{{ route('user.login') }}"><i class="fas fa-sign-in-alt"></i>@lang('Sign In')</a></li>
                    <li>/</li>
                    <li><a class="sign-up" href="{{ route('user.register') }}"><i class="fas fa-user-plus"></i>@lang('Sign Up')</a></li>
                </ul>
                @endguest
                @auth
                <ul class="header-login">
                    <li>
                        <a href="{{ route('user.home') }}">@lang('Dashboard')</a>
                    </li>
                </ul>
                @endauth
            </div>
        </div>
    </div>
</div>
<div class="header-bottom">
    <div class="container">
        <div class="header-bottom-area">
            <div class="logo">
                <a href="{{ route('home') }}">
                    <img src="{{ getImage(imagePath()['logoIcon']['path'].'/default.png') }}" alt="@lang('Logo')">
                </a>
            </div> <!-- Logo End -->
            <ul class="menu">
                <li>
                    <a href="{{ route('home') }}">@lang('Home')</a>
                </li>
                @foreach($pages as $k => $data)
                <li>
                    <a href="{{route('pages',[$data->slug])}}">{{__($data->nom)}}</a>
                </li>
                @endforeach
                <li>
                    <a href="{{ route('contact') }}">@lang('Contact')</a>
                </li>
            </ul>
            <div class="d-flex flex-wrap algin-items-center">
                <div class="header-trigger-wrapper d-flex d-lg-none ms-4">
                    <div class="header-trigger d-block d-lg-none">
                        <span></span>
                    </div>
                    <div class="top-bar-trigger">
                        <i class="las la-ellipsis-v"></i>
                    </div>
                </div><!-- Trigger End-->
            </div>
        </div>
    </div>
</div>

<!-- Header Section Ends Here -->

@push('script')
<script>
    $(document).ready(function() {
        "use strict";
        $(".langSel").on("change", function() {
            window.location.href = "{{route('home')}}/change/" + $(this).val();
        });
    });
</script>
@endpush