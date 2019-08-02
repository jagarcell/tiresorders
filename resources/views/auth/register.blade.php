@extends('layouts.app')
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">


	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
    <link href="{{ asset('public/css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="public/css/register.css">


	<script src="//code.jquery.com/jquery-1.12.4.js"></script>
	<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

	<script type="text/javascript" src="public/js/register.js"></script>

	<title>REGISTER USER</title>
</head>
<body>
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-80">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="type" class="col-md-4 col-form-label text-md-right">{{ __('User Type') }}</label>

                            <div class="col-md-6">
                                <select id="type" class="form-control{{ $errors->has('type') ? ' is-invalid' : '' }}" name="type" onchange="typeChange(this)">
                                    <option value="admin">Admin</option>
                                    <option value="user">User</option>
                                </select>

                                @if ($errors->has('type'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('type') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div id="nameDiv" class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}">

                                @if ($errors->has('name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div id="qbNameDiv" class="form-group row" style="display: none;">
                            <label for="qb_customer_id" class="col-md-4 col-form-label text-md-right">{{ __('QB Name') }}</label>

                            <div class="col-md-6">
                                <select id="qb_customer_id" type="text" class="form-control{{ $errors->has('qb_customer_id') ? ' is-invalid' : '' }}" name="qb_customer_id" value="{{ old('qb_customer_id') }}" onchange="qbCustomerSelectChange(this)">
                                </select>

                                @if ($errors->has('qb_customer_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('qb_customer_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="address" class="col-md-4 col-form-label text-md-right">{{ __('Address') }}</label>

                            <div class="col-md-6">
                                <input id="address" placeholder="Enter A Shipping Address Here" type="text" class="form-control{{ $errors->has('address') ? ' is-invalid' : '' }}" name="address" value="{{ old('address') }}">

                                @if ($errors->has('address'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('address') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="phone" class="col-md-4 col-form-label text-md-right">{{ __('Phone') }}</label>

                            <div class="col-md-6">
                                <input id="phone" type="text" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone" value="{{ old('phone') }}">

                                @if ($errors->has('phone'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div id="pricelevelsDiv" class="form-group row">
                            <label for="pricelevels_id" class="col-md-4 col-form-label text-md-right">{{ __('Price Levels') }}</label>

                            <div class="col-md-6">
                                <select id="pricelevels_id" type="text" class="form-control{{ $errors->has('pricelevels_id') ? ' is-invalid' : '' }}" name="pricelevels_id" value="{{ old('pricelevels_id') }}">
                                </select>
                                @if ($errors->has('pricelevels_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('pricelevels_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div id="pricelistsDiv" class="form-group row">
                            <label for="pricelists_id" class="col-md-4 col-form-label text-md-right">{{ __('Price Lists') }}</label>

                            <div class="col-md-6">
                                <select id="pricelists_id" type="text" class="form-control{{ $errors->has('pricelist_id') ? ' is-invalid' : '' }}" name="pricelist_id" value="{{ old('pricelist_id') }}">
                                </select>

                                @if ($errors->has('pricelevels_id'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('pricelist_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
</body>
</html>