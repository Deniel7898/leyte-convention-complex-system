@extends('layouts.app')

@section('content')
<style>
    :root{
        --lcc-navy:#1f2156;
        --lcc-navy-2:#2b2f73;
        --lcc-bg:#eef2f7;
        --lcc-card:#ffffff;
        --lcc-line:#e6ebf2;
        --lcc-text:#1f2937;
        --lcc-muted:#7a8599;
        --lcc-success:#1f9d63;
        --lcc-warning:#d68c00;
        --lcc-danger:#d64545;
        --lcc-info:#3b82f6;
    }

    .reports-wrapper{
        padding: 22px;
        background: var(--lcc-bg);
        min-height: calc(100vh - 70px);
    }

    .reports-header{
        margin-bottom: 18px;
    }

    .reports-title{
        font-size: 32px;
        font-weight: 800;
        color: var(--lcc-text);
        margin-bottom: 4px;
    }

    .reports-subtitle{
        font-size: 14px;
        color: var(--lcc-muted);
    }

    .reports-toolbar{
        background: #fff;
        border: 1px solid var(--lcc-line);
        border-radius: 18px;
        padding: 16px;
        margin-bottom: 18px;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: end;
    }

    .reports-field{
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 180px;
    }

    .reports-field label{
        font-size: 13px;
        font-weight: 700;
        color: var(--lcc-muted);
    }

    .reports-input{
        height: 44px;
        border: 1px solid var(--lcc-line);
        border-radius: 12px;
        padding: 0 14px;
        outline: none;
        background: #fff;
    }

    .reports-btn{
        height: 44px;
        border: none;
        border-radius: 12px;
        padding: 0 18px;
        cursor: pointer;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: .2s ease;
    }

    .reports-btn-primary{
        background: var(--lcc-navy);
        color: #fff;
    }

    .reports-btn-primary:hover{
        background: var(--lcc-navy-2);
        color: #fff;
    }

    .reports-btn-light{
        background: #f6f8fc;
        border: 1px solid var(--lcc-line);
        color: var(--lcc-text);
    }

    .reports-grid{
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 18px;
    }

    .summary-card{
        grid-column: span 3;
        background: #fff;
        border: 1px solid var(--lcc-line);
        border-radius: 20px;
        padding: 18px;
        box-shadow: 0 2px 8px rgba(18, 23, 38, .04);
    }

    .summary-label{
        font-size: 12px;
        color: var(--lcc-muted);
        text-transform: uppercase;
        letter-spacing: .05em;
        font-weight: 800;
        margin-bottom: 10px;
    }

    .summary-value{
        font-size: 30px;
        font-weight: 800;
        color: var(--lcc-text);
        line-height: 1;
        margin-bottom: 8px;
    }

    .summary-note{
        font-size: 13px;
        color: var(--lcc-muted);
    }

    .report-panel{
        background: #fff;
        border: 1px solid var(--lcc-line);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(18, 23, 38, .04);
    }

    .report-panel.span-12{ grid-column: span 12; }
    .report-panel.span-6{ grid-column: span 6; }

    .report-panel-header{
        padding: 18px 20px;
        border-bottom: 1px solid var(--lcc-line);
    }

    .report-panel-title{
        margin: 0;
        font-size: 18px;
        font-weight: 800;
        color: var(--lcc-text);
    }

    .report-panel-subtitle{
        margin-top: 4px;
        font-size: 13px;
        color: var(--lcc-muted);
    }

    .report-panel-body{
        padding: 18px;
    }

    .chart-wrap{
        position: relative;
        height: 320px;
    }

    .report-table{
        width: 100%;
        border-collapse: collapse;
    }

    .report-table th,
    .report-table td{
        padding: 14px 18px;
        border-bottom: 1px solid var(--lcc-line);
        text-align: left;
        vertical-align: top;
    }

    .report-table th{
        background: #fbfcfe;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #5d6780;
        font-weight: 800;
    }

    .report-table td{
        font-size: 14px;
        color: var(--lcc-text);
    }

    .report-table tr:last-child td{
        border-bottom: none;
    }

    .status-badge{
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        text-transform: capitalize;
    }

    .status-pending,
    .status-borrowed,
    .status-under-repair,
    .status-scheduled{
        background: rgba(214, 140, 0, .12);
        color: var(--lcc-warning);
    }

    .status-approved,
    .status-received,
    .status-completed,
    .status-available,
    .status-returned{
        background: rgba(31, 157, 99, .12);
        color: var(--lcc-success);
    }

    .status-rejected,
    .status-cancelled{
        background: rgba(214, 69, 69, .12);
        color: var(--lcc-danger);
    }

    .status-issued,
    .status-ordered,
    .status-distributed,
    .status-maintenance,
    .status-installation,
    .status-inspection{
        background: rgba(59, 130, 246, .12);
        color: var(--lcc-info);
    }

    .mini-pill-wrap{
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .mini-pill{
        display: inline-flex;
        gap: 6px;
        padding: 7px 10px;
        background: #f6f8fc;
        border: 1px solid var(--lcc-line);
        border-radius: 999px;
        font-size: 12px;
    }

    .text-danger{
        color: var(--lcc-danger);
        font-weight: 700;
    }

    .empty-state{
        padding: 24px 18px;
        color: var(--lcc-muted);
        font-size: 14px;
    }

    .no-print{
        display: block;
    }

    @media (max-width: 1200px){
        .summary-card{ grid-column: span 6; }
        .report-panel.span-6{ grid-column: span 12; }
    }

    @media (max-width: 768px){
        .reports-wrapper{ padding: 16px; }
        .summary-card{ grid-column: span 12; }
        .reports-title{ font-size: 24px; }
        .chart-wrap{ height: 260px; }
    }

    @media print{
        .reports-toolbar,
        .no-print{
            display: none !important;
        }

        .reports-wrapper{
            background: #fff;
            padding: 0;
        }

        .report-panel,
        .summary-card{
            box-shadow: none;
        }
    }
</style>

<div class="reports-wrapper">
    <div class="reports-header">
        <div class="reports-title">Reports</div>
        <div class="reports-subtitle">Inventory, purchase requests, stock status, distribution, and service summaries</div>
    </div>

    <form action="{{ route('reports.index') }}" method="GET" class="reports-toolbar">
        <div class="reports-field">
            <label for="date_from">Date From</label>
            <input type="date" name="date_from" id="date_from" class="reports-input" value="{{ $dateFrom }}">
        </div>

        <div class="reports-field">
            <label for="date_to">Date To</label>
            <input type="date" name="date_to" id="date_to" class="reports-input" value="{{ $dateTo }}">
        </div>

        <button type="submit" class="reports-btn reports-btn-primary">Apply Filter</button>
        <a href="{{ route('reports.index') }}" class="reports-btn reports-btn-light">Reset</a>
        <button type="button" onclick="window.print()" class="reports-btn reports-btn-light">Print</button>
    </form>

    @include('reports.cards')
    @include('reports.table')
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const palette = [
        '#1f2156',
        '#3b82f6',
        '#1f9d63',
        '#d68c00',
        '#d64545',
        '#7c3aed',
        '#0ea5e9',
        '#14b8a6',
        '#f97316',
        '#64748b'
    ];

    function buildChart(id, type, labels, data, options = {}) {
        const canvas = document.getElementById(id);
        if (!canvas) return;

        new Chart(canvas, {
            type: type,
            data: {
                labels: labels,
                datasets: [{
                    label: options.label ?? 'Total',
                    data: data,
                    backgroundColor: type === 'line'
                        ? 'rgba(31, 33, 86, 0.15)'
                        : labels.map((_, i) => palette[i % palette.length]),
                    borderColor: type === 'line'
                        ? '#1f2156'
                        : labels.map((_, i) => palette[i % palette.length]),
                    borderWidth: 2,
                    fill: type === 'line',
                    tension: 0.35,
                    borderRadius: type === 'bar' ? 8 : 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: type === 'doughnut' || type === 'pie'
                    }
                },
                scales: type === 'doughnut' || type === 'pie'
                    ? {}
                    : {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    },
                ...options.extra
            }
        });
    }

    const prStatusLabels = @json($purchaseRequestStatusSummary->pluck('status')->values());
    const prStatusData = @json($purchaseRequestStatusSummary->pluck('total')->values());

    const inventoryStatusLabels = @json($inventoryStatusSummary->pluck('status')->values());
    const inventoryStatusData = @json($inventoryStatusSummary->pluck('total')->values());

    const distributionLabels = @json($distributionSummary->pluck('type')->values());
    const distributionData = @json($distributionSummary->pluck('total_quantity')->values());

    const serviceLabels = @json($serviceStatusSummary->pluck('status')->values());
    const serviceData = @json($serviceStatusSummary->pluck('total')->values());

    const topRequestedLabels = @json(collect($topRequestedItems)->pluck('name')->values());
    const topRequestedData = @json(collect($topRequestedItems)->pluck('quantity')->values());

    buildChart('prStatusChart', 'doughnut', prStatusLabels, prStatusData, {
        label: 'PR Status'
    });

    buildChart('inventoryStatusChart', 'bar', inventoryStatusLabels, inventoryStatusData, {
        label: 'Inventory Count'
    });

    buildChart('distributionChart', 'bar', distributionLabels, distributionData, {
        label: 'Distributed Quantity',
        extra: {
            indexAxis: 'y'
        }
    });

    buildChart('serviceStatusChart', 'doughnut', serviceLabels, serviceData, {
        label: 'Service Status'
    });

    buildChart('topRequestedChart', 'bar', topRequestedLabels, topRequestedData, {
        label: 'Requested Quantity',
        extra: {
            indexAxis: 'y'
        }
    });
});
</script>
@endsection