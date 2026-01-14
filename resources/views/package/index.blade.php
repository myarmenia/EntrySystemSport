@extends('layouts.app')

@section("page-script")
<script src="{{ asset('assets/js/delete-item.js') }}"></script>
@endsection

@section('content')

<main id="main" class="main">
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">

                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5 class="card-title">
                                <nav>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item active">Փաթեթների ցանկ</li>
                                    </ol>
                                </nav>
                            </h5>

                            <div class="pull-right d-flex justify-content-end m-3">
                                <a class="btn btn-primary mb-2" href="{{ route('package.create') }}">
                                    <i class="fa fa-plus"></i> Ստեղծել նոր փաթեթ
                                </a>
                            </div>
                        </div>

                        @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">Հ/Հ</th>
                                    <th scope="col">ID</th>
                                    <th scope="col">Ամիսների քանակ</th>
                                    <th scope="col">Անվանում</th>
                                    <th scope="col">Զեղչ</th>

                                    <th scope="col">Գին</th>
                                    <th scope="col">Գործողություն</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($data as $package)
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $package->id }}</td>
                                    <td>{{ $package->months ?? '—' }}</td>
                                    <td>{{ $package->name ?? '—' }}</td>
                                    <td>
                                        @php
                                        $activeDiscount = $package->discounts->first(); // repository-ում արդեն ֆիլտրած ակտիվներն են գալիս
                                        @endphp

                                        @if($activeDiscount)
                                        <span class="badge bg-success">Զեղչված</span>
                                        @else
                                        <span class="badge bg-secondary">Չզեղչված</span>
                                        @endif
                                    </td>
                                    <td>{{ $package->price_amd ?? '—' }}</td>

                                    <td>
                                        <div class="dropdown action" data-id="{{ $package->id }}" data-tb-name="packages">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>

                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('package.edit', $package->id) }}">
                                                    <i class="bx bx-edit-alt me-1"></i> Խմբագրել
                                                </a>

                                                <form action="{{ route('package.destroy', $package->id) }}" method="POST"
                                                    onsubmit="return confirm('Վստա՞հ եք, որ ուզում եք ջնջել')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="bx bx-trash me-1"></i> Ջնջել
                                                    </button>
                                                </form>

                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Փաթեթներ չկան</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        @if($data->total() > 0)
                        <div class="demo-inline-spacing">
                            {{ $data->links() }}
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

@endsection