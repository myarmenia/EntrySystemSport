@extends('layouts.app')

@section('content')
@php
    $user = Auth::user();
@endphp
    <main id="main" class="main">

        <section class="section dashboard">
            <div class="row">
                <!-- Left side columns -->
                <div class="col-lg-12">
                    <div class="col-8">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white d-flex align-items-center justify-content-center
                                                rounded me-2 px-2 py-1">
                                        <i class="bi bi-clock"></i>
                                    </div>

                                    <h4 class="mb-0">
                                        Աշխատանքային ժամանակի ղեկավարման վահանակ
                                    </h4>

                                    <a href="{{ route('schedule.work-time-create') }}"
                                            class="btn btn-primary btn-sm ms-auto">
                                        <i class="fa fa-plus me-1"></i> Ստեղծել
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Reports -->
                        <div class="col-8">
                            <div class="card">

                                <div class="card-body">


                                    <table class="table align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th scope="col" class="py-3 text-start">Անվանում</th>
                                                <th scope="col" class="py-3 text-center">Աշխատանքային ժամ</th>
                                                <th scope="col" class="py-3 text-center">Գործողություն</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($data as $item)
                                                <tr class="align-middle">
                                                    <!-- Անվանում -->
                                                    <td class="text-start">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="bg-primary text-white rounded
                                                                        d-flex align-items-center justify-content-center"
                                                                style="width:32px; height:32px;opacity: 0.7;">
                                                                <i class="bi bi-clock"></i>
                                                            </div>
                                                            <span class="fw-medium">{{ $item->name }}</span>
                                                        </div>
                                                    </td>


                                                    <!-- Ժամեր -->
                                                    <td class="text-center">
                                                        <span class="badge text-success me-1" style="opacity: 0.7">{{ $item->schedule_details[0]->day_start_time ?? null }}</span>
                                                        <span class="badge text-danger" style="opacity: 0.7">{{ $item->schedule_details[0]->day_end_time ?? null }}</span>
                                                    </td>

                                                    <!-- Գործողություններ -->
                                                    <td class="text-center">
                                                        <div class="dropdown action"data-id="{{ $item['id'] }}" data-tb-name="schedule_names" >
                                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                            data-bs-toggle="dropdown">
                                                            <i class="bx bx-dots-vertical-rounded"></i>
                                                        </button>

                                                        <div class="dropdown-menu">
                                                            @if(auth()->user()->hasRole(['super_admin','client_admin', 'client_admin_rfID','manager']))
                                                                <a class="dropdown-item d-flex" href="javascript:void(0);">
                                                                    <div class="form-check form-switch">
                                                                        <input class="form-check-input change_status" type="checkbox"
                                                                            role="switch" data-field-name="status"
                                                                            {{ $item['status'] ? 'checked' : null }}>
                                                                    </div>Կարգավիճակ
                                                                </a>
                                                            @endif
                                                            @if ($user->hasAnyRole([ 'trainer']))
                                                                <a class="dropdown-item" href="{{route('schedule-calendar',$item['id'] )}}"><i
                                                                class="bi bi-person me-1"></i>Գրանցված այցելուներ</a>

                                                            @endif
                                                            @if (!auth()->user()->hasRole('client_admin_rfID'))

                                                                <a class="dropdown-item" href="{{route('schedule.work-time-edit',$item['id'])}}"><i
                                                                        class="bx bx-edit-alt me-1"></i>Խմբագրել</a>
                                                            @endif
                                                            <button type="button" class="dropdown-item click_delete_item"
                                                                data-bs-toggle="modal" data-bs-target="#smallModal"><i
                                                                    class="bx bx-trash me-1"></i>
                                                                Ջնջել</button>
                                                        </div>
                                                    </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>


                                </div>
                            </div><!-- End Reports -->

                            <!-- Recent Sales -->
                            <div class="col-12">
                                <div class="card recent-sales overflow-auto">

                                    <div class="filter">
                                        <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                                class="bi bi-three-dots"></i></a>
                                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                            <li class="dropdown-header text-start">
                                                <h6>Filter</h6>
                                            </li>

                                            <li><a class="dropdown-item" href="#">Today</a></li>
                                            <li><a class="dropdown-item" href="#">This Month</a></li>
                                            <li><a class="dropdown-item" href="#">This Year</a></li>
                                        </ul>
                                    </div>

                                    <div class="card-body">
                                        <h5 class="card-title">Recent Sales <span>| Today</span></h5>

                                        <div
                                            class="datatable-wrapper datatable-loading no-footer sortable searchable fixed-columns">
                                            <div class="datatable-top">
                                                <div class="datatable-dropdown">
                                                    <label>
                                                        <select class="datatable-selector" name="per-page">
                                                            <option value="5">5</option>
                                                            <option value="10" selected="">10</option>
                                                            <option value="15">15</option>
                                                            <option value="-1">All</option>
                                                        </select> entries per page
                                                    </label>
                                                </div>
                                                <div class="datatable-search">
                                                    <input class="datatable-input" placeholder="Search..." type="search"
                                                        name="search" title="Search within table">
                                                </div>
                                            </div>
                                            <div class="datatable-container">
                                                <table class="table table-borderless datatable datatable-table">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col" data-sortable="true"
                                                                style="width: 14.698795180722893%;"><button
                                                                    class="datatable-sorter">#</button></th>
                                                            <th scope="col" data-sortable="true"
                                                                style="width: 24.096385542168676%;"><button
                                                                    class="datatable-sorter">Customer</button></th>
                                                            <th scope="col" data-sortable="true"
                                                                style="width: 24.819277108433734%;"><button
                                                                    class="datatable-sorter">Product</button></th>
                                                            <th scope="col" data-sortable="true"
                                                                style="width: 16.14457831325301%;"><button
                                                                    class="datatable-sorter">Price</button></th>
                                                            <th scope="col" data-sortable="true" class="red"
                                                                style="width: 20.240963855421686%;"><button
                                                                    class="datatable-sorter">Status</button></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr data-index="0">
                                                            <td scope="row"><a href="#">#2457</a></td>
                                                            <td>Brandon Jacob</td>
                                                            <td><a href="#" class="text-primary">At praesentium minu</a>
                                                            </td>
                                                            <td>$64</td>
                                                            <td class="green"><span class="badge bg-success">Approved</span>
                                                            </td>
                                                        </tr>
                                                        <tr data-index="1">
                                                            <td scope="row"><a href="#">#2147</a></td>
                                                            <td>Bridie Kessler</td>
                                                            <td><a href="#" class="text-primary">Blanditiis dolor omnis
                                                                    similique</a></td>
                                                            <td>$47</td>
                                                            <td class="green"><span class="badge bg-warning">Pending</span>
                                                            </td>
                                                        </tr>
                                                        <tr data-index="2">
                                                            <td scope="row"><a href="#">#2049</a></td>
                                                            <td>Ashleigh Langosh</td>
                                                            <td><a href="#" class="text-primary">At recusandae
                                                                    consectetur</a></td>
                                                            <td>$147</td>
                                                            <td class="green"><span class="badge bg-success">Approved</span>
                                                            </td>
                                                        </tr>
                                                        <tr data-index="3">
                                                            <td scope="row"><a href="#">#2644</a></td>
                                                            <td>Angus Grady</td>
                                                            <td><a href="#" class="text-primar">Ut voluptatem id earum
                                                                    et</a></td>
                                                            <td>$67</td>
                                                            <td class="green"><span class="badge bg-danger">Rejected</span>
                                                            </td>
                                                        </tr>
                                                        <tr data-index="4">
                                                            <td scope="row"><a href="#">#2644</a></td>
                                                            <td>Raheem Lehner</td>
                                                            <td><a href="#" class="text-primary">Sunt similique
                                                                    distinctio</a></td>
                                                            <td>$165</td>
                                                            <td class="green"><span class="badge bg-success">Approved</span>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="datatable-bottom">
                                                <div class="datatable-info">Showing 1 to 5 of 5 entries</div>
                                                <nav class="datatable-pagination">
                                                    <ul class="datatable-pagination-list"></ul>
                                                </nav>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div><!-- End Recent Sales -->

                            <!-- Top Selling -->
                            <div class="col-12">
                                <div class="card top-selling overflow-auto">

                                    <div class="filter">
                                        <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                                class="bi bi-three-dots"></i></a>
                                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                            <li class="dropdown-header text-start">
                                                <h6>Filter</h6>
                                            </li>

                                            <li><a class="dropdown-item" href="#">Today</a></li>
                                            <li><a class="dropdown-item" href="#">This Month</a></li>
                                            <li><a class="dropdown-item" href="#">This Year</a></li>
                                        </ul>
                                    </div>

                                    <div class="card-body pb-0">
                                        <h5 class="card-title">Top Selling <span>| Today</span></h5>

                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Preview</th>
                                                    <th scope="col">Product</th>
                                                    <th scope="col">Price</th>
                                                    <th scope="col">Sold</th>
                                                    <th scope="col">Revenue</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th scope="row"><a href="#"><img src="assets/img/product-1.jpg"
                                                                alt=""></a></th>
                                                    <td><a href="#" class="text-primary fw-bold">Ut inventore ipsa voluptas
                                                            nulla</a></td>
                                                    <td>$64</td>
                                                    <td class="fw-bold">124</td>
                                                    <td>$5,828</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><a href="#"><img src="assets/img/product-2.jpg"
                                                                alt=""></a></th>
                                                    <td><a href="#" class="text-primary fw-bold">Exercitationem similique
                                                            doloremque</a></td>
                                                    <td>$46</td>
                                                    <td class="fw-bold">98</td>
                                                    <td>$4,508</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><a href="#"><img src="assets/img/product-3.jpg"
                                                                alt=""></a></th>
                                                    <td><a href="#" class="text-primary fw-bold">Doloribus nisi
                                                            exercitationem</a></td>
                                                    <td>$59</td>
                                                    <td class="fw-bold">74</td>
                                                    <td>$4,366</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><a href="#"><img src="assets/img/product-4.jpg"
                                                                alt=""></a></th>
                                                    <td><a href="#" class="text-primary fw-bold">Officiis quaerat sint rerum
                                                            error</a></td>
                                                    <td>$32</td>
                                                    <td class="fw-bold">63</td>
                                                    <td>$2,016</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><a href="#"><img src="assets/img/product-5.jpg"
                                                                alt=""></a></th>
                                                    <td><a href="#" class="text-primary fw-bold">Sit unde debitis delectus
                                                            repellendus</a></td>
                                                    <td>$79</td>
                                                    <td class="fw-bold">41</td>
                                                    <td>$3,239</td>
                                                </tr>
                                            </tbody>
                                        </table>

                                    </div>

                                </div>
                            </div><!-- End Top Selling -->

                        </div>
                    </div><!-- End Left side columns -->



                </div>
        </section>
    </main>
@endsection
