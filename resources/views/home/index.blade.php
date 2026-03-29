@extends('layouts.app')

@section('content')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Stats Cards -->
    <div id="home-stats-cards">
        @include('home.stats_cards', ['stats' => $stats])
    </div>

    <!-- Loading Spinner -->
    <div id="loading-spinner">
        <div class="spinner"></div>
    </div>

    <!-- Quick Actions -->
    <div id="home-quick-action">
        @include('home.quick_actions')
    </div>

    <!-- Dashboard Overview -->
    <div id="home-dashboard-overview">
        @include('home.dashboard_overview', ['overview' => $overview])
    </div>

    <!-- Recent Activity Timeline -->
    <div id="activity-container-wrapper">
        @include('home.recent_activity', ['recent_activities' => $recent_activities])
    </div>
@endsection