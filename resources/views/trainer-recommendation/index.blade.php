@extends('layouts.app')


@section('content')

<main id="main" class="main">
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
              <div class = "d-flex justify-content-between">
                    <h5 class="card-title">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active">Մարզչի խորհուրդներ </li>
                            <li class="breadcrumb-item active">Ցանկ</li>
                        </ol>
                    </nav>
                    </h5>
                    <div class="pull-right d-flex justify-content-end m-3" >
                        <a class="btn btn-primary  mb-2" href="{{ route('recommendation.create') }}"><i class="fa fa-plus"></i> Ստեղծել</a>
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
                    {{-- <a class="btn btn-info btn-sm" href="{{ route('users.show',$user->id) }}"><i class="fa-solid fa-list"></i> Show</a> --}}
                    <a class="btn btn-primary btn-sm" href="{{ route('users.edit',$recommendation->id) }}"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                    {{-- <form method="POST" action="{{ route('users.destroy', $recommendation->id) }}" style="display:inline">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i> Delete</button>
                    </form> --}}
                </td>
            </tr>
        @endforeach
    </table>




</main>
@endsection
