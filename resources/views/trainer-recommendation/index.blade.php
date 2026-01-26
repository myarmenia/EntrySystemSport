@extends('layouts.app')
@section("page-script")
    <script src="{{ asset('assets/js/delete-item.js') }}"></script>
    <script src="{{ asset('assets/js/trainer-person.js') }}"></script>
@endsection


@section('content')

    <main id="main" class="main">
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <div class="d-flex justify-content-between">
                        <h5 class="card-title">
                            <nav>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active">Մարզչի խորհուրդներ </li>
                                    <li class="breadcrumb-item active">Ցանկ</li>
                                </ol>
                            </nav>
                        </h5>
                        <div class="pull-right d-flex justify-content-end m-3">
                            <a class="btn btn-primary  mb-2" href="{{ route('recommendation.create') }}"><i
                                    class="fa fa-plus"></i> Ստեղծել</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        @session('success')
            <div class="alert alert-success" role="alert">
                {{ $value }}
            </div>
        @endsession
        @session('error')
            <div class="alert alert-danger" role="alert">
                {{ $value }}
            </div>
        @endsession

        <table class="table table-bordered">
            <tr>
                <th>Հ/Հ</th>
                <th>Անուն</th>
                <th>Նկարագրություն</th>
                <th width="280px">Գործողություն</th>
            </tr>
            @foreach ($data as $key => $recommendation)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $recommendation->name }}</td>
                    <td>{{ $recommendation->description }}</td>

                    <td>
                        <div class="dropdown action" data-id="{{ $recommendation->id }}" data-tb-name="recommendations">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>

                            <div class="dropdown-menu">

                                {{-- @if ($person != null)
                                    <a class="dropdown-item" href="{{route('calendar', $person->id)}}">
                                        <i class="bi bi-calendar-event"></i>Ժամանակացույց
                                    </a>

                                @endif
                                <a class="dropdown-item" href="{{route('absence.list', $person->id)}}"><i
                                        class="bi bi-person-x me-1"></i>Հարգելի Բացակա</a> --}}

                                <button type="button" class="dropdown-item click_attach_person" data-bs-toggle="modal"
                                    data-user="{{ $booking }}"
                                    data-bs-target="#trainerPerson">
                                    <i class="bi bi-person-plus me-1"></i>
                                    Կցել</button>

                                <a class="dropdown-item" href="{{route('recommendation.edit', $recommendation->id)}}"><i
                                        class="bx bx-edit-alt me-1"></i>Խմբագրել</a>
                                <button type="button" class="dropdown-item click_delete_item" data-bs-toggle="modal"
                                    data-bs-target="#smallModal">
                                    <i class="bx bx-trash me-1"></i>
                                    Ջնջել</button>
                            </div>
                        </div>

                    </td>
                </tr>
            @endforeach
        </table>
    </main>
@endsection
<x-modal-delete></x-modal-delete>
<x-trainer-person :booking="$booking"/>
