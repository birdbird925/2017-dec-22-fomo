@extends('layouts.admin')

@section('page-direction')
    Dashboard
@endsection

@section('dashboard-sidebar')
    active
@endsection

@section('content')
    <div class="col-sm-3">
        <div class="card data-card">
            <div class="content">
                <i class="pe-7s-note2"></i>
                <div class="data">
                    {{$orders->count()}}
                </div>
                <p class="description">
                    Total Orders
                </p>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card data-card">
            <div class="content">
                <i class="pe-7s-wristwatch"></i>
                <div class="data">
                    {{$products->count()}}
                </div>
                <p class="description">
                    Total Products
                </p>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card data-card">
            <div class="content">
                <i class="pe-7s-users"></i>
                <div class="data">
                    {{$customers->count()}}
                </div>
                <p class="description">
                    Total Customers
                </p>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card data-card">
            <div class="content">
                <i class="pe-7s-cash"></i>
                <div class="data">
                    ${{$amount}}
                </div>
                <p class="description">
                    Total Sales
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="header">
                <h4 class="title">Last 7 days traffic source</h4>
            </div>
            <div class="content">
                <canvas id="myChart" width="100%" height="60px"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="header">
                <h4 class="title">Notifications</h4>
            </div>
            <div class="content">

            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script src="/js/admin/Chart.js"></script>
    <script>
    var ctx = document.getElementById("myChart");
    var data = {
        labels: ["28 Apr", "29 Apr", "30 Apr", "1 May", "2 May", "3 May", "4 May"],
        datasets: [
            {
                label: "Page View",
                fill: false,
                lineTension: 0,
                backgroundColor: 'rgba(255,99,132,1)',
                borderColor: 'rgba(255,99,132,1)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(255,99,132,1)',
                pointBorderColor: 'rgba(255,99,132,1)',
                pointHoverRadius: 5,
                pointHoverBorderWidth: 1,
                pointRadius: 3,
                pointHitRadius: 10,
                data: [20, 30, 12, 21, 56, 33, 40],
            },
            {
                label: "Visit",
                fill: false,
                lineTension: 0,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                pointBorderColor: 'rgba(54, 162, 235, 1)',
                pointHoverRadius: 5,
                pointHoverBorderWidth: 1,
                pointRadius: 3,
                pointHitRadius: 10,
                data: [12, 22, 18, 41, 35, 17, 35],
            },
            {
                label: "Visitor",
                fill: false,
                lineTension: 0,
                borderColor: 'rgba(255, 206, 86, 1)',
                backgroundColor: 'rgba(255, 206, 86, 1)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(255, 206, 86, 1)',
                pointBorderColor: 'rgba(255, 206, 86, 1)',
                pointHoverRadius: 5,
                pointHoverBorderWidth: 1,
                pointRadius: 3,
                pointHitRadius: 10,
                data: [5, 6, 3, 1, 10, 8, 2],
            }
        ]
    };
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: data,
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        maxTicksLimit: 5
                    }
                }]
            }
        }
    });
    </script>
@endpush
