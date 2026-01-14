@extends('layouts.app')

@section("page-script")
    <script src="{{ asset('assets/js/change-status.js') }}"></script>
    <script src="{{ asset('assets/js/delete-item.js') }}"></script>
@endsection

@section('content')


{{-- commentel em heto karox e petq gal  --}}
   {{-- @include("navbar") --}}




   <main id="main" class="main">



    <section class="section">
      <div class="row">

        <div class="col-lg-12">
          <div class="card">

            <div class="card-body">
                <div class = "d-flex justify-content-between">
                    <h5 class="card-title">

                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active">
                                <a href="{{ route('visitors.list') }}">
                                Աշխատակիցների/Այցելուների ցանկ</a></li>

                        </ol>
                    </nav>

                    </h5>
                    <div class="pull-right d-flex justify-content-end m-3" >
                        <a class="btn btn-primary  mb-2" href="{{ route('create.person.absence',$person->id) }}"><i class="fa fa-plus"></i> Ստեղծել</a>
                    </div>

                </div>
              <!-- Bordered Table -->


              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th scope="col">Հ/Հ</th>
                    <th scope="col">Տեսակը</th>
                    <th scope="col">Սկիզբ</th>
                    <th scope="col">Ավարտ</th>
                    <th scope="col">Գործողություն</th>
                  </tr>
                </thead>
                <tbody>

                    @if( $data !=null && count($data)>0)
                        @foreach ($data as $absence)

                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $absence->type }}</td>
                                <td>{{ $absence->start_date }}</td>
                                <td>{{ $absence->end_date }}</td>
                                <td>

                                    <div class="dropdown action"data-id="{{ $absence->id }}" data-tb-name="absences">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>

                                        <div class="dropdown-menu">

                                            <a class="dropdown-item" href="{{route('absence.edit',$absence->id)}}"><i
                                                    class="bx bx-edit-alt me-1"></i>Խմբագրել</a>
                                            <button type="button" class="dropdown-item click_delete_item"
                                                data-bs-toggle="modal" data-bs-target="#smallModal"><i
                                                    class="bx bx-trash me-1"></i>
                                                Ջնջել</button>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                        @endforeach
                    @endif


                </tbody>
              </table>

              <!-- End Bordered Table -->

              @if( $data !=null && count($data)>0)
                <div class="demo-inline-spacing">
                    {{-- {{ $data->links() }} --}}
                </div>
                @endif
            </div>


          </div>





        </div>

      </div>

    </section>

  </main><!-- End #main -->
<script>

    $('.supervised').on('change', function() {

        // alert(7777)
        let isChecked = $(this).prop("checked") ? 1 : 0;
        console.log(isChecked)
        let people_id=$(this).val()
        let client_id = $(this).attr('data-client')
        if(isChecked){

            $.ajax({
            type: "POST",
            url: '/supervised',
            data: {
                people_id: people_id,
                client_id: client_id
            },
            cache: false,
            success: function (data) {
                if (data.success) {

                }
                else {
                message = data.message

                }
            }
            });

        }else{
            console.log(people_id,client_id)

            $.ajax({
            type: "POST",
            url: '/delete-superviced',
            data: {
                people_id: people_id,
                client_id: client_id
            },
            cache: false,
            success: function (data) {
                if (data.success) {

                }
                else {
                message = data.message

                }
            }
            });


        }
    })


</script>

@endsection
<x-modal-delete></x-modal-delete>

