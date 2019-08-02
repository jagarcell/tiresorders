<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta id="csrf_token" name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
    <link href="{{ asset('public/css/app.css') }}" rel="stylesheet">
    
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 34px;
            font-style: italic;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .menuOption50{
            display: inline;
            margin-right: 5%;
            margin-left: 5%; 
        }

        .menuOption33{
            display: inline;
            margin-right: 3%;
            margin-left: 3%;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
    </style>    
    @yield('styles')

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
    @yield('fonts')

    <!-- Scripts -->
    <script src="//code.jquery.com/jquery-1.12.4.js"></script>
    <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="{{ asset('public/js/app.js') }}" defer></script>
    @yield('scripts')
</head>
<body>
    @yield('header')
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
            <div class="container">
                <div id="companyName" class="companyNameDiv">
                    <a class="navbar-brand">
                        @isset($companyName)
                        {{$companyName}}
                        @endisset
                    </a>
                </div>
                @auth
                @if(Auth::user()->type == 'admin')
                <!--form action="/disconnect"-->
                    <div id="qbDisconnectDiv" class="qbDisconnectDiv">
                        <input id="qbDisconnect" type="button" value="Disconnect From Quick Books" class="qbDisconnectButton">
                    </div>
                <!--/form-->
                @endif
                @endauth
                
                @auth
                @if(Auth::user()->type == 'admin')
                <div id="qbConnectDiv">
                    <input id="qbConnect" type="button" value="Quick Books Connect" class="qbConnectButton">
                </div>
                @else
                <div id="qbDisconnectedLabel" class="qbDisconnectedLabel">
                    <label class="qbDisconnected">Disconnected</label>
                </div>
                @endif
                @endauth

                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <div id="loginDiv" style="display: inline;">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            </div>
                            <div id="registerDiv" style="display: inline;">
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                            </div>
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                    @can('adminonly')
                                    @if (Route::has('register'))
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                        </li>
                                    @endif
                                    @endcan
                                    <!--li class="nav-item"><a id="orderLink" class="nav-link">Order</a></li-->
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
                @auth
                @if (Auth::user()->type == 'user')
                <div style="display: inline; width: 48px; height: 48px; margin-top: 0px;">
                    <a href="/viewtheorder">
                        <img src="/public/img/logos/cart.jpg" style="width: 48px;height: 48px;">
                    </a>
                </div>
                @endif
                @endauth

            </div>
        </nav>

        <div class="content">
            <!--div class="title m-b-md">
                {{env('APP_NAME')}}
            </div-->

            <div class="links">
                @can('adminonly')
                <div class="menuOption33"><a href="/listusers">USERS</a></div>
                <div class="menuOption33"><a href="/listpricelevels">PRICE LEVELS</a></div>
                <div class="menuOption33"><a href="/pricelists">PRICE LISTS</a></div>
                <div class="menuOption33"><a href="/listopenorders">ORDERS</a></div>
                <div class="menuOption33"><a href="/inventory">INVENTORY</a></div>
                @endcan
                @auth
                @if(Auth::user()->type != 'admin')
                <!--div class="menuOption50"><a href="/placeanorder">Place An Order</a></div>
                <div class="menuOption50"><a href="/viewtheorder">View The Order</a></div-->
                @endif
                @endauth
            </div>
        </div>

        <main>
            <div id="contentDiv" class="contentDiv">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
