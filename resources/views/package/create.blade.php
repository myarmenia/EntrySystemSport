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
                                    <li class="breadcrumb-item active">Ստեղծել</li>
                                </ol>
                            </nav>
                        </h5>

                        @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form action="{{ route('package.store') }}" method="POST">
                            @csrf

                            {{-- MONTHS --}}
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Ամիսներ</label>
                                <div class="col-sm-9">
                                    <input type="number"
                                        min="1"
                                        class="form-control"
                                        name="months"
                                        placeholder="Օր. 1, 3, 6, 12"
                                        value="{{ old('months') }}"
                                        required>

                                    @error('months')
                                    <div class="mb-3 row">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Անվանում</label>
                                <div class="col-sm-9">
                                    <input type="text"
                                        class="form-control"
                                        name="name"
                                        placeholder="Օր․ Gold / Silver / Basic"
                                        value="{{ old('name') }}"
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
                                        value="{{ old('price_amd') }}"
                                        required>

                                    @error('price_amd')
                                    <div class="mb-3 row">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                </div>
                            </div>


                            {{-- OPTIONAL: status --}}
                            {{-- Եթե քո table-ում կա status դաշտ, կարող ես բացել սա --}}
                            {{--
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Կարգավիճակ</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="status">
                                        <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Ակտիվ</option>
                            <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Պասիվ</option>
                            </select>
                            @error('status')
                            <div class="mb-3 row">
                                <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                            </div>
                            @enderror
                    </div>
                </div>
                --}}

                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label"></label>
                    <div class="col-sm-9">
                        <button type="submit" class="btn btn-primary">Ստեղծել</button>
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