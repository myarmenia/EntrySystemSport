@extends('layouts.app')

@section("page-script")
<script src="{{ asset('assets/js/change-status.js') }}"></script>
<script src="{{ asset('assets/js/delete-item.js') }}"></script>
@endsection

@section('content')

<main id="main" class="main">

    <section class="section">
        <div class="row">

            <div class="col-lg-12">
                <div class="card">

                    <div class="card-body table-responsive">
                        <div class="d-flex justify-content-between">
                            <h5 class="card-title">
                                <nav>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item active">Զեղչերի ցանկ</li>
                                    </ol>
                                </nav>
                            </h5>

                            <div class="pull-right d-flex justify-content-end m-3">
                                <a class="btn btn-primary mb-2" href="{{ route('discounts.create') }}">
                                    <i class="fa fa-plus"></i> Ստեղծել նոր զեղչ
                                </a>
                            </div>
                        </div>

                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <table class="table table-bordered table-sm align-middle text-nowrap">
                            <thead>
                                <tr>
                                    <th scope="col">Հ/Հ</th>
                                    <th scope="col">ID</th>
                                    <th scope="col">Անուն</th>
                                    <th scope="col">Տեսակ</th>
                                    <th scope="col">Արժեք</th>
                                    <th scope="col">Սկիզբ</th>
                                    <th scope="col">Վերջ</th>
                                    <th scope="col">Կարգավիճակ</th>
                                    <th scope="col">Փաթեթներ</th>
                                    <th scope="col">Գործողություն</th>
                                </tr>
                            </thead>

                            <tbody>
                                @if($data != null && count($data) > 0)
                                    @foreach ($data as $discount)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <th scope="row">{{ $discount->id }}</th>

                                            <td>{{ $discount->name ?? '—' }}</td>

                                            <td>
                                                @if(($discount->type ?? null) === 'percent')
                                                    %
                                                @elseif(($discount->type ?? null) === 'fixed')
                                                    AMD
                                                @else
                                                    —
                                                @endif
                                            </td>

                                            <td>{{ $discount->value ?? '—' }}</td>

                                            <td>
                                                {{ $discount->starts_at ? \Carbon\Carbon::parse($discount->starts_at)->format('Y-m-d H:i') : '—' }}
                                            </td>

                                            <td>
                                                {{ $discount->ends_at ? \Carbon\Carbon::parse($discount->ends_at)->format('Y-m-d H:i') : '—' }}
                                            </td>

                                            <td>
                                                <span class="badge {{ ($discount->status ?? 0) ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ ($discount->status ?? 0) ? 'Ակտիվ' : 'Պասիվ' }}
                                                </span>
                                            </td>

                                            <td style="min-width: 220px;">
                                                @php
                                                    $pkgs = $discount->packages ?? collect();
                                                @endphp

                                                @if($pkgs->count() > 0)
                                                    {{ $pkgs->pluck('name')->filter()->unique()->implode(', ') }}
                                                @else
                                                    —
                                                @endif
                                            </td>

                                            <td>
                                                <div class="dropdown action" data-id="{{ $discount->id }}" data-tb-name="discounts">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>

                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="{{ route('discounts.edit', $discount->id) }}">
                                                            <i class="bx bx-edit-alt me-1"></i>Խմբագրել
                                                        </a>

                                                        <button type="button" class="dropdown-item click_delete_item"
                                                            data-bs-toggle="modal" data-bs-target="#smallModal">
                                                            <i class="bx bx-trash me-1"></i>Ջնջել
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="10" class="text-center">Տվյալներ չկան</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>

                        @if($data != null && count($data) > 0)
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

<x-modal-delete></x-modal-delete>
