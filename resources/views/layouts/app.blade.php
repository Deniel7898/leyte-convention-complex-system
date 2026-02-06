<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- ========== All CSS files linkup ========= -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/lineicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/fullcalendar.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/materialdesignicons.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/main.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/custom/categories.css') }}" />

    <!-- <link rel="stylesheet" href="{{ asset('css/custom/'.request()->segment(1).'.css') }}" /> -->
</head>

<body>
    <!-- ======== sidebar-nav start =========== -->
    <aside class="sidebar-nav-wrapper">
        <nav class="sidebar-nav-wrapper bg-dark vh-100 d-flex flex-column">
            <div class="sidebar-header d-flex align-items-center ps-3 pe-3 justify-content-between"
                id="sidebar-header">

                <img src="{{ asset('images/logo/leyte_province_logo.jpg') }}"
                    class="rounded-circle"
                    style="margin-right: -18px;"
                    width="40"
                    height="40"
                    alt="Logo">

                <!-- Brand -->
                <a href="{{ route('home') }}"
                    class="brand text-white fw-bold fs-5 text-decoration-none">
                    LCC System
                </a>

                <!-- Hamburger -->
                <button id="menu-toggle"
                    class="bg-transparent border-0 d-flex align-items-center ps-2"
                    type="button"
                    aria-label="Toggle sidebar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28"
                        fill="currentColor" class="bi bi-list text-white" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z" />
                        <path fill-rule="evenodd" d="M2.5 8a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z" />
                        <path fill-rule="evenodd" d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z" />
                    </svg>
                </button>
            </div>

            <!-- Divider -->
            <hr class="my-3 border-light opacity-50">

            <!-- Sidebar menu items -->
            <div class="sidebar-nav flex-column">
                @include('layouts.navigation')
            </div>
        </nav>

    </aside>
    <div class="overlay"></div>
    <!-- ======== sidebar-nav end =========== -->

    <!-- ======== main-wrapper start =========== -->
    <main class="main-wrapper">
        <!-- ========== header start ========== -->
        <header class="header py-2">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-12 d-flex justify-content-between align-items-center flex-wrap">
                        <div class="header-left d-flex flex-column">
                            <!-- Page Title -->
                            <span class="fw-bold fs-4 text-dark mb-0">
                                @yield('page_title', 'Dashboard')
                            </span>

                            <!-- Subtitle -->
                            <p class="mb-0 text-muted small fs-7 d-none d-md-block">
                                Leyte Convention Complex – Local System Only
                            </p>
                        </div>

                        <!-- Right: Last Synced + Profile -->
                        <div class="header-right d-flex align-items-center gap-4 flex-shrink-0 mt-2 mt-md-0">

                            <!-- Last Synced -->
                            <div class="d-flex align-items-center gap-3 flex-shrink-0">
                                <div class="text-end">
                                    <div class="text-muted" style="font-size:11px;">
                                        Last Synced
                                    </div>
                                    <div class="fw-semibold text-dark" style="font-size:13px;">
                                        12:24:22 PM
                                    </div>
                                </div>
                            </div>

                            <!-- Profile -->
                            <div class="profile-box">
                                <button class="dropdown-toggle bg-transparent border-0 d-flex align-items-center"
                                    type="button"
                                    id="profile"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <div class="profile-info me-2">
                                        <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                                    </div>
                                    <i class="lni lni-chevron-down"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profile">
                                    <li>
                                        <a href="{{ route('profile.show') }}">
                                            <i class="lni lni-user"></i> {{ __('My profile') }}
                                        </a>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <a href="{{ route('logout') }}"
                                                onclick="event.preventDefault(); this.closest('form').submit();">
                                                <i class="lni lni-exit"></i> {{ __('Logout') }}
                                            </a>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div> <!-- End Right -->
                    </div>
                </div> <!-- End row -->
            </div>
        </header>
        <!-- ========== header end ========== -->

        <!-- ========== section start ========== -->
        <section class="section">
            <div class="container-fluid">
                @yield('content')
            </div>
            <!-- end container -->
        </section>
        <!-- ========== section end ========== -->

        <!-- ========== footer start =========== -->
        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6 order-last order-md-first">
                        <div class="copyright text-md-start">
                            <p class="text-sm">
                                Maintained by: The Code Crew. {{ now()->year }}
                            </p>
                        </div>
                    </div>
                    <!-- end col-->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </footer>
        <!-- ========== footer end =========== -->
    </main>
    <!-- ======== main-wrapper end =========== -->

    <!-- ========= All Javascript files linkup ======== -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/Chart.min.js') }}"></script>
    <script src="{{ asset('js/dynamic-pie-chart.js') }}"></script>
    <script src="{{ asset('js/moment.min.js') }}"></script>
    <script src="{{ asset('js/fullcalendar.js') }}"></script>
    <script src="{{ asset('js/jvectormap.min.js') }}"></script>
    <script src="{{ asset('js/world-merc.js') }}"></script>
    <script src="{{ asset('js/polyfill.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>

    <script src="{{ asset('js/custom/'.request()->segment(1).'.js') }}"></script>
</body>

</html>