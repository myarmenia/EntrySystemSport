@extends('layouts.app')

@section('content')
<div class="container">
    {{-- <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Reset Password') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Send Password Reset Link') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div> --}}
    <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

              <div class="d-flex justify-content-center py-4">
                <a href="index.html" class="logo d-flex align-items-center w-auto">
                  <img src="{{ asset('assets/img/logo.png') }}" alt="">
                  <span class="d-none d-lg-block">NiceAdmin</span>
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                  <div class="pt-4 pb-2">
                    <h6 class="card-title text-center pb-0 fs-5">Գաղտնաբառի վերականգնում </h6>
                    {{-- <p class="text-center small">Գաղտնաբառի վերականգնում</p> --}}
                  </div>
                  <form  class="row g-3 needs-validation" method="POST" action="{{ route('password.email') }}" novalidate>
                    @csrf
                    <div class="col-12">
                      {{-- <label for="yourUsername" class="form-label">Էլ․Հասցե</label> --}}
                      <div class="input-group has-validation">
                        <span class="input-group-text" id="inputGroupPrepend">@</span>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                         placeholder="Մուտքագրեք Ձեր էլ․ հասցեն"
                         name="email"
                          value="{{ old('email') }}" required autocomplete="email" >
                        <div class="invalid-feedback">Մուտքագրեք Ձեր Էլ․հասցեն</div>
                      </div>
                    </div>
                    <div class="col-12 d-flex justify-content-center">

                                <button type="submit" class="btn btn-primary">
                                    Ուղարկեք վերականգնման հղումը
                                </button>

                    </div>
                  </form>

                </div>
              </div>
            </div>
          </div>
        </div>
    </section>

</div>
@endsection
