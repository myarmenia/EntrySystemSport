@extends('layouts.app')

@section("page-script")
{{-- եթե պետք լինի JS, բայց հիմա պետք չէ --}}
@endsection

@section('content')
<main id="main" class="main">
    <section class="section">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">

                    <div class="card-body">
                        @if (session('message'))
                        <div class="alert alert-success" role="alert">
                            {{ session('message') }}
                        </div>
                        @endif

                        @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                        @endif
                    </div>

                    <div class="card-body">
                        <h5 class="card-title">
                            <nav>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('discounts.index') }}">Զեղչերի ցանկ</a>
                                    </li>
                                    <li class="breadcrumb-item active">Խմբագրել</li>
                                </ol>
                            </nav>
                        </h5>

                        <form action="{{ route('discounts.update', $discount->id) }}" method="post">
                            @csrf
                            @method('PUT')


                            {{-- Անուն --}}
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Անուն</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="name"
                                        placeholder="Զեղչի անունը"
                                        value="{{ old('name', $discount->name) }}" required>

                                    @error('name')
                                    <div class="mb-3 row">
                                        <p class="col-sm-10 text-danger fs-6">{{ $message }}</p>
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Տեսակ --}}
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Տեսակ</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="type" required>
                                        <option value="percent" {{ old('type', $discount->type) === 'percent' ? 'selected' : '' }}>
                                            Տոկոս (%)
                                        </option>
                                        <option value="fixed" {{ old('type', $discount->type) === 'fixed' ? 'selected' : '' }}>
                                            Գումար (AMD)
                                        </option>
                                    </select>

                                    @error('type')
                                    <div class="mb-3 row justify-content-end">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Արժեք --}}
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Արժեք</label>
                                <div class="col-sm-9">
                                    <input type="number" step="0.01" class="form-control" name="value"
                                        placeholder="Օր․ 10 կամ 5000"
                                        value="{{ old('value', $discount->value) }}" required>

                                    @error('value')
                                    <div class="mb-3 row justify-content-end">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Սկիզբ --}}
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Սկիզբ (optional)</label>
                                <div class="col-sm-9">
                                    <input type="datetime-local" class="form-control" name="starts_at"
                                        value="{{ old('starts_at', optional($discount->starts_at)->format('Y-m-d\TH:i')) }}">

                                    @error('starts_at')
                                    <div class="mb-3 row justify-content-end">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Վերջ --}}
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Վերջ (optional)</label>
                                <div class="col-sm-9">
                                    <input type="datetime-local" class="form-control" name="ends_at"
                                        value="{{ old('ends_at', optional($discount->ends_at)->format('Y-m-d\TH:i')) }}">

                                    @error('ends_at')
                                    <div class="mb-3 row justify-content-end">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Status --}}
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Կարգավիճակ</label>
                                <div class="col-sm-9">
                                    @php
                                    $oldStatus = old('status', $discount->status ? '1' : '0');
                                    @endphp
                                    <select class="form-select" name="status" required>
                                        <option value="1" {{ $oldStatus == '1' ? 'selected' : '' }}>Ակտիվ</option>
                                        <option value="0" {{ $oldStatus == '0' ? 'selected' : '' }}>Պասիվ</option>
                                    </select>

                                    @error('status')
                                    <div class="mb-3 row justify-content-end">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Packages (multi-select) --}}
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Փաթեթներ</label>
                                <div class="col-sm-9">
                                    @php
                                    // նախընտրում ենք old(package_ids) եթե կա, հակառակ դեպքում discount-ի կապած package-ները
                                    $selected = old('package_ids', $discount->packages->pluck('id')->toArray());
                                    if (!is_array($selected)) $selected = [];
                                    $selected = array_map('intval', $selected);
                                    @endphp

                                    <select class="form-select" name="package_ids[]" multiple required>
                                        @foreach($packages as $p)
                                        <option value="{{ $p->id }}" {{ in_array($p->id, $selected) ? 'selected' : '' }}>
                                            {{ $p->name }} — {{ number_format($p->price_amd) }} դրամ
                                        </option>
                                        @endforeach
                                    </select>

                                    <small class="text-muted">Ctrl (Windows) / Cmd (Mac)՝ մի քանի ընտրելու համար</small>

                                    @error('package_ids')
                                    <div class="mb-3 row justify-content-end">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                    @error('package_ids.*')
                                    <div class="mb-3 row justify-content-end">
                                        <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Submit --}}
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label"></label>
                                <div class="col-sm-9">
                                    <button type="submit" class="btn btn-primary">Պահպանել</button>
                                    <a href="{{ route('discounts.index') }}" class="btn btn-secondary">Վերադառնալ</a>
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