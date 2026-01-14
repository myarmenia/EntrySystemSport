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
                            <li class="breadcrumb-item active">Օգտատերեր </li>
                            <li class="breadcrumb-item active">Ցանկ</li>
                        </ol>
                    </nav>
                    </h5>
                    <div class="pull-right d-flex justify-content-end m-3" >
                        <a class="btn btn-primary  mb-2" href="{{ route('users.create') }}"><i class="fa fa-plus"></i> Ստեղծել նոր Օգտատեր</a>
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
            <th>Էլ․հասցե</th>
            <th>Դերեր</th>
            <th width="280px">Գործողություն</th>
        </tr>
        @foreach ($data as $key => $user)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                @if(!empty($user->getRoleNames()))
                    @foreach($user->getRoleNames() as $v)
                    <label class="badge bg-primary">{{ $v }}</label>
                    @endforeach
                @endif
                </td>
                <td>
                    {{-- <a class="btn btn-info btn-sm" href="{{ route('users.show',$user->id) }}"><i class="fa-solid fa-list"></i> Show</a> --}}
                    <a class="btn btn-primary btn-sm" href="{{ route('users.edit',$user->id) }}"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                    <form method="POST" action="{{ route('users.destroy', $user->id) }}" style="display:inline">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i> Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>

    {!! $data->links('pagination::bootstrap-5') !!}


</main>
@endsection
