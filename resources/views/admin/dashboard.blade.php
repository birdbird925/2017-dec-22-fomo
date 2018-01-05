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
                    {{ round($amount, 2) }}
                </div>
                <p class="description">
                    Total Sales
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <h4 class="title">Sales Statistics</h4>
                <br>
                <ul class="nav navbar-nav">
                  <li><a href="#" class="statistics-toggle" data-target=7>This Week</a></li>
                  <li><a href="#" class="statistics-toggle" data-target=30>Last Month</a></li>
                  <li><a href="#" class="statistics-toggle" data-target=60>Last 2 Months</a></li>
                  <li><a href="#" class="statistics-toggle" data-target=90>Last 3 Months</a></li>
                </ul>
                <br>
            </div>
            <div class="content">
                <canvas id="myChart" width="100%" height="60px"></canvas>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script src="/js/admin/Chart.js"></script>
    <script>
    myLineChart = new Chart($('#myChart'));
    function statistics(range) {
      $.ajax({
        url: "/admin/statistics/"+range,
        type: 'post',
        data: {_token: $('meta[name="csrf-token"]').attr('content')},
        dataType: 'json',
        success: function(res){
          var data = {
            labels: res.label,
            datasets: [
              // {
              //   label: "Orders",
              //   fill: false,
              //   lineTension: 0,
              //   borderColor: 'rgba(255, 206, 86, 1)',
              //   backgroundColor: 'rgba(255, 206, 86, 1)',
              //   borderWidth: 2,
              //   pointBackgroundColor: 'rgba(255, 206, 86, 1)',
              //   pointBorderColor: 'rgba(255, 206, 86, 1)',
              //   pointHoverRadius: 5,
              //   pointHoverBorderWidth: 1,
              //   pointRadius: 3,
              //   pointHitRadius: 10,
              //   data: res.order,
              // },
              {
                  label: "Sales",
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
                  data: res.sales,
              },
            ]
          };
          $('#myChart').html('');
          myLineChart.destroy();
          myLineChart = new Chart($('#myChart'), {
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
        }
      });
    }
    var chart = $('#myChart');
    chart.one('load',statistics(7));
    // statistic toggle
    $('.statistics-toggle').on('click', function(e){
      e.preventDefault();
      statistics($(this).attr('data-target'));
    })
    </script>
@endpush
