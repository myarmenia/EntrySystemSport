@extends('layouts.app')

@section('content')

<main id="main" class="main">
    <section class="section">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">

                    <div class="card-body">

                        <h5 class="card-title">
                            <nav>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('package.list') }}">Փաթեթների ցանկ</a>
                                    </li>
                                    <li class="breadcrumb-item active">Խմբագրել</li>
                                </ol>
                            </nav>
                        </h5>

                        @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        {{-- սպասվում է որ controller-ից view-ին փոխանցում ես $package --}}
                        <form action="{{ route('package.update', $package->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            {{-- MONTHS --}}
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Ամիսներ</label>
                                <div class="col-sm-9">
                                    <input type="number"
                                        min="1"
                                        class="form-control"
                                        name="months"
                                        placeholder="Օր. 1, 3, 6, 12"
                                        value="{{ old('months', $package->months) }}"
                                        required>

                                    @error('months')
                                    <div class="mb-3 row">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- NAME --}}
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Անվանում</label>
                                <div class="col-sm-9">
                                    <input type="text"
                                        class="form-control"
                                        name="name"
                                        placeholder="Օր․ Gold / Silver / Basic"
                                        value="{{ old('name', $package->name) }}"
                                        required>

                                    @error('name')
                                    <div class="mb-3 row">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- PRICE AMD --}}
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Գին (դրամ)</label>
                                <div class="col-sm-9">
                                    <input type="number"
                                        min="0"
                                        class="form-control"
                                        name="price_amd"
                                        placeholder="Օր. 10000"
                                        value="{{ old('price_amd', $package->price_amd) }}"
                                        required>

                                    @error('price_amd')
                                    <div class="mb-3 row">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <input type="hidden" name="is_active" value="1">


                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label"></label>
                                <div class="col-sm-9">
                                    <button type="submit" class="btn btn-primary">Պահպանել</button>
                                    <a href="{{ route('package.list') }}" class="btn btn-light">Չեղարկել</a>
                                </div>
                            </div>

                        </form>

                    </div>

                </div>
            </div>
        </div>
    </section>
</main>

@endsection