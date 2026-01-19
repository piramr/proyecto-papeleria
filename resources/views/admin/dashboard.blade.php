@extends('layouts.app')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Tablero Principal</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box shadow-none border">
                <span class="info-box-icon bg-success-gradient"><i class="fas fa-dollar-sign"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted">Total Revenue</span>
                    <span class="info-box-number text-lg">$45,231.89</span>
                    <span class="text-success text-sm"><i class="fas fa-arrow-up"></i> 20.1%</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box shadow-none border">
                <span class="info-box-icon bg-info-gradient"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted">Active Users</span>
                    <span class="info-box-number text-lg">2,350</span>
                    <span class="text-success text-sm"><i class="fas fa-arrow-up"></i> 15.3%</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box shadow-none border">
                <span class="info-box-icon bg-danger-gradient"><i class="fas fa-shopping-cart"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted">Total Orders</span>
                    <span class="info-box-number text-lg">1,234</span>
                    <span class="text-danger text-sm"><i class="fas fa-arrow-down"></i> 4.2%</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box shadow-none border">
                <span class="info-box-icon bg-warning-gradient"><i class="fas fa-chart-line"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted">Conversion Rate</span>
                    <span class="info-box-number text-lg">3.42%</span>
                    <span class="text-success text-sm"><i class="fas fa-arrow-up"></i> 8.7%</span>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        {{-- Revenue Overview --}}
        <div class="col-md-6">
            <div class="card shadow-none border">
                <div class="card-header border-0">
                    <h3 class="card-title text-bold">Revenue Overview</h3>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart"
                        style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
        {{-- Profit vs Expenses --}}
        <div class="col-md-6">
            <div class="card shadow-none border">
                <div class="card-header border-0">
                    <h3 class="card-title text-bold">Profit vs Expenses</h3>
                </div>
                <div class="card-body">
                    <canvas id="profitChart"
                        style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-none border">
                <div class="card-header border-0">
                    <h3 class="card-title text-bold">Recent Orders</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-valign-middle">
                        <tbody>
                            <tr>
                                <td>John Doe <br> <small class="text-muted">Wireless Headphones</small></td>
                                <td class="text-right"><strong>$129.99</strong> <br> <span
                                        class="badge badge-success">completed</span></td>
                            </tr>
                            <tr>
                                <td>Jane Smith <br> <small class="text-muted">Smart Watch</small></td>
                                <td class="text-right"><strong>$299.99</strong> <br> <span
                                        class="badge badge-warning">pending</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-none border">
                <div class="card-header border-0">
                    <h3 class="card-title text-bold">Top Products</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>Wireless Headphones <br> <small class="text-muted">1,234 sales</small></div>
                            <div class="text-right"><strong>$160,410.00</strong> <br> <small class="text-success">↑
                                    12.5%</small></div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>Smart Watch <br> <small class="text-muted">987 sales</small></div>
                            <div class="text-right"><strong>$296,003.00</strong> <br> <small class="text-success">↑
                                    8.3%</small></div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Configuración básica para el ejemplo
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Revenue',
                    data: [4000, 3000, 2000, 2800, 2500, 3500],
                    borderColor: '#4e73df',
                    fill: true,
                    backgroundColor: 'rgba(78, 115, 223, 0.05)'
                }]
            }
        });
    </script>
@stop
