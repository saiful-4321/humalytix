@extends('Main::layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Financial Dashboard</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Finance</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Assets</p>
                    </div>
                    <div class="flex-shrink-0">
                        <h5 class="text-success fs-14 mb-0">
                            <i class="ri-arrow-right-up-line fs-13 align-middle"></i>
                        </h5>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{ number_format($totalAssets, 2) }}</h4>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-success-subtle rounded fs-3">
                            <i class="bx bx-dollar-circle text-success"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Liabilities</p>
                    </div>
                    <div class="flex-shrink-0">
                        <h5 class="text-danger fs-14 mb-0">
                            <i class="ri-arrow-right-down-line fs-13 align-middle"></i>
                        </h5>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{ number_format($totalLiabilities, 2) }}</h4>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-info-subtle rounded fs-3">
                            <i class="bx bx-briefcase-alt-2 text-info"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Revenue (YTD)</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{ number_format($totalRevenue, 2) }}</h4>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-warning-subtle rounded fs-3">
                            <i class="bx bx-bar-chart-alt-2 text-warning"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Net Income</p>
                    </div>
                    <div class="flex-shrink-0">
                        <h5 class="{{ $netIncome >= 0 ? 'text-success' : 'text-danger' }} fs-14 mb-0">
                            {{ $netIncome >= 0 ? '+' : '' }} {{ number_format($netIncome, 2) }}
                        </h5>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{ number_format($netIncome, 2) }}</h4>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-primary-subtle rounded fs-3">
                            <i class="bx bx-wallet text-primary"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Revenue vs Expenses (Last 12 Months)</h4>
            </div>
            <div class="card-body">
                <div id="revenue-expense-chart" style="height: 350px;"></div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                 <h4 class="card-title mb-0">Expense Breakdown</h4>
            </div>
            <div class="card-body">
                 <div id="expense-donut-chart" style="height: 350px;"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Recent Transactions</h4>
                <div class="flex-shrink-0">
                    <a href="{{ route('finance.journals.index') }}" class="btn btn-soft-info btn-sm">View All</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive table-card">
                    <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                        <tbody>
                            @foreach($recentTransactions as $entry)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm flex-shrink-0 me-2">
                                            <span class="avatar-title rounded-circle fs-4 {{ $entry->debit > 0 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}">
                                                <i class="{{ $entry->debit > 0 ? 'ri-arrow-up-line' : 'ri-arrow-down-line' }}"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="fs-14 mb-1">{{ $entry->description ?: 'Journal Entry' }}</h5>
                                            <p class="text-muted mb-0">{{ $entry->journal->date->format('d M, Y') }} • {{ $entry->account->name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <h5 class="fs-14 mb-0 {{ $entry->debit > 0 ? 'text-danger' : 'text-success' }}">
                                        {{ $entry->debit > 0 ? '-' : '+' }} {{ number_format($entry->debit > 0 ? $entry->debit : $entry->credit, 2) }}
                                    </h5>
                                    <span class="text-muted">{{ $entry->journal->journal_number }}</span>
                                </td>
                            </tr>
                            @endforeach
                            @if($recentTransactions->isEmpty())
                            <tr>
                                <td colspan="2" class="text-center text-muted">No recent transactions</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Revenue vs Expense Chart
        var optionsRevExp = {
            series: [{
                name: 'Revenue',
                data: {!! json_encode($monthlyData['revenue']) !!}
            }, {
                name: 'Expenses',
                data: {!! json_encode($monthlyData['expense']) !!}
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: { show: false }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: { enabled: false },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: {!! json_encode($monthlyData['months']) !!},
            },
            yaxis: {
                title: { text: 'Amount' }
            },
            fill: { opacity: 1 },
            colors: ['#0ab39c', '#f06548'],
            tooltip: {
                y: {
                    formatter: function (val) {
                        return "$ " + val.toFixed(2)
                    }
                }
            }
        };

        var chartRevExp = new ApexCharts(document.querySelector("#revenue-expense-chart"), optionsRevExp);
        chartRevExp.render();
        
        // Expense Breakdown Chart
        var optionsDonut = {
            series: {!! json_encode($expenseData['series']) !!},
            labels: {!! json_encode($expenseData['labels']) !!},
            chart: {
                type: 'donut',
                height: 350,
            },
            legend: {
                position: 'bottom'
            },
            colors: ['#405189', '#0ab39c', '#f7b84b', '#f06548', '#299cdb'],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: { width: 200 },
                    legend: { position: 'bottom' }
                }
            }]
        };

        var chartDonut = new ApexCharts(document.querySelector("#expense-donut-chart"), optionsDonut);
        chartDonut.render();
    });
</script>
@endsection
