@extends('layouts.app')

@section("page-script")
<script src="{{ asset('assets/js/change-status.js') }}"></script>
<script src="{{ asset('assets/js/delete-item.js') }}"></script>
@endsection

@section('content')




<style>
    /* ✅ table-responsive թող աշխատի, որ mobile-ում scroll լինի */
    .card-body.table-responsive {
        overflow-x: auto !important;
        overflow-y: hidden !important;
    }

    .col-pay {
        width: 170px;
        white-space: normal;
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .pay-green {
        background: #d1fae5 !important;
    }

    /* paid */
    .pay-red {
        background: #fee2e2 !important;
    }

    /* not paid */
    .pay-gray {
        background: #f3f4f6 !important;
    }

    /* no active package (optional) */


    .idx-green {
        background: #d1fae5 !important;
    }

    /* կանաչ */
    .idx-red {
        background: #fee2e2 !important;
    }

    /* կարմիր */


    /* fallback: եթե template-ը կտրում է dropdown-ը, սա կփրկի */
    .visitors-table .dropdown-menu {
        position: fixed !important;
    }


    .dropdown-menu {
        z-index: 9999;
    }


    /* ✅ stable layout */
    .visitors-table {
        width: 100%;
        table-layout: fixed;
        /* stable column widths */
        min-width: 1100px;
        /* որ շատ սեղմ չլինի, mobile-ում scroll կանի */
    }

    .visitors-table th,
    .visitors-table td {
        padding: 6px 8px;
        vertical-align: middle;
    }

    /* ✅ column width helpers */
    .col-idx {
        width: 52px;
        white-space: nowrap;
        text-align: center;
    }

    .col-code {
        width: 90px;
        white-space: nowrap;
    }

    .col-img {
        width: 90px;
        white-space: nowrap;
    }

    .col-phone {
        width: 130px;
        white-space: normal;
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .col-super {
        width: 90px;
        white-space: nowrap;
        text-align: center;
    }

    .col-abs {
        width: 110px;
        white-space: nowrap;
    }

    .col-act {
        width: 72px;
        white-space: nowrap;
        text-align: center;
    }

    /* ✅ wrap columns (long text) */
    .col-wrap {
        white-space: normal;
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    /* optional: make images neat */
    .visitor-img {
        width: 70px;
        height: auto;
        display: block;
    }

    /* smaller screens: allow even more wrapping */
    @media (max-width: 768px) {
        .visitors-table {
            min-width: 980px;
        }
    }
</style>

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
                                        <li class="breadcrumb-item active">Այցելուների ցանկ</li>
                                    </ol>
                                </nav>
                            </h5>

                            @if (!auth()->user()->hasRole('trainer'))
                            <div class="pull-right d-flex justify-content-end m-3">
                                <a class="btn btn-primary mb-2" href="{{ route('visitors.create') }}">
                                    <i class="fa fa-plus"></i> Ստեղծել նոր այցելու
                                </a>
                            </div>
                            @endif
                        </div>
                        <form method="GET" class="row g-2 align-items-end mb-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label mb-1">Անուն / Ազգանուն</label>
                                <input
                                    type="text"
                                    name="q"
                                    value="{{ request('q') }}"
                                    class="form-control"
                                    placeholder="օր․ Արամ / Պետրոսյան">
                            </div>

                            <div class="col-12 col-md-3">
                                <label class="form-label mb-1">Բացակայություն</label>
                                <select name="absence" class="form-select">
                                    <option value="" {{ request('absence')=='' ? 'selected' : '' }}>Բոլորը</option>
                                    <option value="has" {{ request('absence')=='has' ? 'selected' : '' }}>Ունի</option>
                                    <option value="none" {{ request('absence')=='none' ? 'selected' : '' }}>Չունի</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-3">
                                <label class="form-label mb-1">Վճարման կարգավիճակ</label>
                                <select name="payment" class="form-select">
                                    <option value="" {{ request('payment')=='' ? 'selected' : '' }}>Բոլորը</option>
                                    <option value="paid" {{ request('payment')=='paid' ? 'selected' : '' }}>Վճարված </option>
                                    <option value="pending" {{ request('payment')=='pending' ? 'selected' : '' }}>Չվճարված </option>
                                </select>
                            </div>

                            <div class="col-12 col-md-2 d-flex gap-2">
                                <button class="btn btn-primary w-100" type="submit">Ֆիլտրել</button>
                                <a class="btn btn-light w-100" href="{{ route('visitors.list') }}">Մաքրել</a>
                            </div>
                        </form>


                        @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                        @endif

                        @if ($data->total() === 0)
                        <div class="alert alert-danger">
                            Դուք չունեք գրանցված այցելուներ
                        </div>
                        @else

                        <table class="table table-bordered table-sm align-middle visitors-table">
                            <thead>
                                <tr>
                                    <th scope="col" class="col-idx">Հ/Հ</th>
                                    <th scope="col" class="col-wrap">Անուն</th>
                                    <th scope="col" class="col-wrap">Ազգանուն</th>
                                    <th scope="col" class="col-phone">Հեռ․</th>
                                    <th scope="col" class="col-pay">Վճար․</th>
                                    <th scope="col" class="col-act">Կ/Վ</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($data as $person)

                                <tr>
                                    <td class="col-idx {{ $person->activeAbsences->count() > 0 ? 'idx-red' : '' }}">
                                        {{ ++$i }}
                                    </td>

                                    <td class="col-wrap {{ $person->activeAbsences->count() > 0 ? 'idx-red' : '' }}">{{ $person->name ?? '—' }}</td>
                                    <td class="col-wrap {{ $person->activeAbsences->count() > 0 ? 'idx-red' : '' }}">{{ $person->surname ?? '—' }}</td>

                                    <td class="col-phone {{ $person->activeAbsences->count() > 0 ? 'idx-red' : '' }}">{{ $person->phone ?? '—' }}</td>
                                    @php
                                    $hasActivePackage = $person->activeBookings?->count() > 0;

                                    $pay = $person->latestPayment; // կարող է null լինել
                                    $paid = $pay && $pay->status === 'paid';

                                    $payClass = $hasActivePackage
                                    ? ($paid ? 'pay-green' : 'pay-red')
                                    : 'pay-red';

                                    $methodMap = [
                                    'cash' => 'Կանխիկ',
                                    'cashless' => 'Անկանխիկ',
                                    'credit' => 'Կրեդիտ',
                                    ];

                                    $methodText = $pay ? ($methodMap[$pay->payment_method] ?? $pay->payment_method) : '—';
                                    $bankText = ($pay && $pay->payment_bank) ? ' / ' . $pay->payment_bank : '';
                                    @endphp

                                    <td class="col-pay {{ $payClass }}">
                                        @if(!$hasActivePackage)
                                        {{ $methodText }}{!! $bankText ? "<span class='text-muted'>{$bankText}</span>" : "" !!}
                                        @else
                                        {{ $methodText }}{!! $bankText ? "<span class='text-muted'>{$bankText}</span>" : "" !!}
                                        @endif
                                    </td>


                                    <!-- <td class="col-super">
                                        @if($person->activated_code_connected_person)
                                        <input
                                            type="checkbox"
                                            class="supervised"
                                            {{ $person->superviced != null ? 'checked' : '' }}
                                            value="{{ $person->id }}"
                                            data-client="{{ $person->client->id }}" />
                                        @else
                                        —
                                        @endif
                                    </td> -->


                                    <td class="col-act">
                                        <div class="dropdown">
                                            <button
                                                class="btn p-0 dropdown-toggle hide-arrow"
                                                type="button"
                                                data-bs-toggle="dropdown"
                                                data-bs-container="body"
                                                data-bs-boundary="viewport"
                                                aria-expanded="false">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>

                                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('calendar', $person->id) }}">
                                                        Ժամանակացույց
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('absence.list', $person->id) }}">
                                                        Հարգելի բացակա
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('visitors.edit', $person->id) }}">
                                                        Խմբագրել
                                                    </a>
                                                </li>
                                                <li>
                                                    <button
                                                        type="button"
                                                        class="dropdown-item click_delete_item"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#smallModal">
                                                        Ջնջել
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>


                                </tr>
                                @endforeach
                            </tbody>
                        </table>

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

<script>
    $('.supervised').on('change', function() {
        let isChecked = $(this).prop("checked") ? 1 : 0;
        let people_id = $(this).val();
        let client_id = $(this).attr('data-client');

        if (isChecked) {
            $.ajax({
                type: "POST",
                url: '/supervised',
                data: {
                    people_id,
                    client_id
                },
                cache: false
            });
        } else {
            $.ajax({
                type: "POST",
                url: '/delete-superviced',
                data: {
                    people_id,
                    client_id
                },
                cache: false
            });
        }
    });
</script>

@endsection

<x-modal-delete></x-modal-delete>