@extends('layouts.app')

@section('content')
    <main id="main" class="main">
        <div class="pagetitle d-flex justify-content-between">
            <div>
                <h1>Աշխատանքային ժամանակի վահանակ</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </nav>

            </div>

            <div class="pull-right d-flex justify-content-end m-3">
                <a class="btn btn-primary  mb-2" href="{{ route('schedule.work-time-create') }}"><i class="fa fa-plus"></i> Ստեղծել</a>

            </div>

        </div><!-- End Page Title -->


        <section class="section dashboard">
            <div class="row">

                <!-- Left side columns -->
                <div class="col-lg-12">
                    <div class="row">

                        <!-- Sales Card -->
                        <div class="col-xxl-3 col-md-6">
                            <div class="card info-card sales-card">

                                <div class="filter">
                                    <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
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
                                    <h5 class="card-title">Sales <span>| Today</span></h5>

                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-cart"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>145</h6>
                                            <span class="text-success small pt-1 fw-bold">12%</span> <span
                                                class="text-muted small pt-2 ps-1">increase</span>

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div><!-- End Sales Card -->

                        <!-- Revenue Card -->
                        <div class="col-xxl-3 col-md-6">
                            <div class="card info-card revenue-card">

                                <div class="filter">
                                    <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
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
                                    <h5 class="card-title">Revenue <span>| This Month</span></h5>

                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-currency-dollar"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>$3,264</h6>
                                            <span class="text-success small pt-1 fw-bold">8%</span> <span
                                                class="text-muted small pt-2 ps-1">increase</span>

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div><!-- End Revenue Card -->

                        <!-- Customers Card -->
                        <div class="col-xxl-3 col-md-6">

                            <div class="card info-card customers-card">

                                <div class="filter">
                                    <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
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
                                    <h5 class="card-title">Customers <span>| This Year</span></h5>

                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-people"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>1244</h6>
                                            <span class="text-danger small pt-1 fw-bold">12%</span> <span
                                                class="text-muted small pt-2 ps-1">decrease</span>

                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div><!-- End Customers Card -->
                        <!-- Customers Card -->
                        <div class="col-xxl-3 col-md-6">

                            <div class="card info-card customers-card">

                                <div class="filter">
                                    <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
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
                                    <h5 class="card-title">Customers <span>| This Year</span></h5>

                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-people"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>1244</h6>
                                            <span class="text-danger small pt-1 fw-bold">12%</span> <span
                                                class="text-muted small pt-2 ps-1">decrease</span>

                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div><!-- End Customers Card -->

                        <!-- Reports -->
                        <div class="col-12">
                            <div class="card">


                                <div class="rounded-top" style="background-color:#4154f1;">
                                    <h6 class="text-white py-3 ms-3">Շաբաթական գրանցումներ</h6>
                                </div>

                                <div class="card-body">


                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Շաբաթվա օրեր</th>
                                                <th scope="col">Աշխատանքային ժամի սկիզբ</th>
                                                <th scope="col">Աշխատանքային ժամի ավարտ</th>
                                                <th scope="col">Ընդմիջում</th>
                                                <th scope="col">Աշխատանքային ժամ</th>
                                                <th scope="col">Աշխատողների քանակ</th>
                                                <th scope="col">Գործողություն</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="text-center align-middle">
                                                <th scope="row">1</th>
                                                <td>Brandon Jacob</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm"
                                                        style="background: rgba(220, 252, 231, 1);color: #28a745;font-weight: 600">
                                                        <i class="bi bi-clock"></i> 14:00
                                                    </button>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm"
                                                        style="background: rgba(255, 226, 226, 1); color: #c82333; font-weight: 600">
                                                        <i class="bi bi-clock"></i> 14:00
                                                    </button>
                                                </td>
                                                <td class="d-flex flex-column">
                                                    <button type="button" class="btn btn-sm mb-2"
                                                        style="background: rgba(254, 243, 198, 1);color: #ffc107;font-weight: 600">
                                                        <i class="fa-solid fa-smoking"></i> 13:00
                                                    </button>

                                                    <button type="button" class="btn btn-sm"
                                                        style="background: rgba(220, 252, 231, 1);color: #28a745;font-weight: 600">

                                                        <i class="fa-solid fa-utensils"></i> 14:00
                                                    </button>
                                                </td>
                                                <td>2016-05-25</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm"
                                                        style="background: rgba(219, 234, 254, 1); color: #0d6efd;font-weight: 600">
                                                        <i class="bi bi-people"></i>45
                                                    </button>

                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm"
                                                        style="background: rgba(219, 234, 254, 1); color: #0d6efd;font-weight: 600">
                                                        <i class="bx bx-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm"
                                                        style="background: rgba(254, 243, 198, 1);color: #ffc107;font-weight: 600">
                                                        <i class="bi bi-gear"></i>
                                                    </button>
                                                </td>
                                            </tr>
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
