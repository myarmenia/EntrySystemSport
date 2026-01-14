@extends('layouts.app')

@section('content')
@php
// ցանկության դեպքում կարող ես default-ներ դնել
@endphp

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
                                        <a href="{{ route('discounts.index') }}">Զեղչերի ցանկ</a>
                                    </li>
                                    <li class="breadcrumb-item active">Ստեղծել</li>
                                </ol>
                            </nav>
                        </h5>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="m-0">
                                    @foreach ($errors->all() as $e)
                                        <li>{{ $e }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('discounts.store') }}" method="post">
                            @csrf

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Անուն</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="name"
                                        placeholder="Օր․ Նոր տարվա զեղչ"
                                        value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="mb-3 row">
                                            <p class="col-sm-10 text-danger fs-6">{{ $message }}</p>
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Տեսակ</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="type" required>
                                        <option value="percent" {{ old('type','percent') === 'percent' ? 'selected' : '' }}>
                                            Տոկոս (%)
                                        </option>
                                    </select>

                                    @error('type')
                                        <div class="mb-3 row">
                                            <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Արժեք</label>
                                <div class="col-sm-9">
                                    <input type="number" step="0.01" class="form-control" name="value"
                                        placeholder="Օր․ 10 (տոկոս)"
                                        value="{{ old('value') }}" required>

                                    @error('value')
                                        <div class="mb-3 row">
                                            <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Սկիզբ (optional)</label>
                                <div class="col-sm-9">
                                    <input type="datetime-local" class="form-control" name="starts_at"
                                        value="{{ old('starts_at') }}">

                                    @error('starts_at')
                                        <div class="mb-3 row">
                                            <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Վերջ (optional)</label>
                                <div class="col-sm-9">
                                    <input type="datetime-local" class="form-control" name="ends_at"
                                        value="{{ old('ends_at') }}">

                                    @error('ends_at')
                                        <div class="mb-3 row">
                                            <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Կարգավիճակ</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="status" required>
                                        <option value="1" {{ old('status','1') == '1' ? 'selected' : '' }}>Ակտիվ</option>
                                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Պասիվ</option>
                                    </select>

                                    @error('status')
                                        <div class="mb-3 row">
                                            <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- ✅ Packages multi-select --}}
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Փաթեթներ</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="package_ids[]" multiple required>
                                        @php
                                            $oldSelected = collect(old('package_ids', []))->map(fn($v) => (int)$v)->toArray();
                                        @endphp

                                        @foreach($packages as $p)
                                            <option value="{{ $p->id }}" {{ in_array($p->id, $oldSelected) ? 'selected' : '' }}>
                                                {{ $p->name ?? ($p->months . ' ամիս — ' . number_format($p->price_amd) . ' դրամ') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Ctrl (Windows) / Cmd (Mac)՝ մի քանի ընտրելու համար</small>

                                    @error('package_ids')
                                        <div class="mb-3 row">
                                            <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                        </div>
                                    @enderror
                                    @error('package_ids.*')
                                        <div class="mb-3 row">
                                            <div class="col-sm-10 text-danger fts-14">{{ $message }}</div>
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label"></label>
                                <div class="col-sm-9">
                                    <button type="submit" class="btn btn-primary">Ստեղծել</button>
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
