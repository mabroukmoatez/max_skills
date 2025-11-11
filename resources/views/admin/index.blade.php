@extends('layout.master')
@section('title', 'Tableau de bord')
@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>
    :root {
        --primary-orange: #ff8c00;
        --dark-bg: #1a1a1a;
        --card-shadow: 0 2px 10px rgba(0,0,0,0.1);
        --light-bg: #f8f9fa;
    }

    body {
        background-color: var(--light-bg);
    }

    .main-content {
        margin-left: 250px;
        padding: 20px;
    }

    /* Welcome Card */
    .welcome-card {
        background: linear-gradient(135deg, #ff8c00 0%, #ffb347 100%);
        border-radius: 20px;
        padding: 40px;
        color: white;
        position: relative;
        overflow: hidden;
        min-height: 220px;
        box-shadow: var(--card-shadow);
    }

    .welcome-card h2 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .welcome-card p {
        max-width: 60%;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 20px;
    }

    .welcome-card .btn-connexion {
        background: white;
        color: #ff8c00;
        border: none;
        padding: 10px 30px;
        border-radius: 8px;
        font-weight: 600;
        transition: transform 0.2s;
    }

    .welcome-card .btn-connexion:hover {
        transform: translateY(-2px);
    }

    .welcome-illustration {
        position: absolute;
        right: 40px;
        top: 50%;
        transform: translateY(-50%);
        width: 200px;
        height: auto;
    }

    /* Stats Cards */
    .stats-container {
        display: flex;
        gap: 15px;
        margin-top: 20px;
    }

    .stat-card {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border-radius: 15px;
        padding: 20px 25px;
        flex: 1;
        box-shadow: var(--card-shadow);
    }

    .stat-card h5 {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 10px;
        font-weight: 500;
    }

    .stat-card h3 {
        font-size: 2rem;
        font-weight: 700;
        color: #333;
        margin: 0;
    }

    .stat-card .percentage {
        font-size: 0.85rem;
        margin-left: 10px;
    }

    .stat-card .percentage.positive {
        color: #28a745;
    }

    .stat-card .percentage.negative {
        color: #dc3545;
    }

    /* Balance Card */
    .balance-card {
        background: var(--dark-bg);
        border-radius: 20px;
        padding: 30px;
        color: white;
        text-align: center;
        box-shadow: var(--card-shadow);
        height: 100%;
    }

    .balance-card .logo {
        width: 150px;
        margin-bottom: 20px;
    }

    .balance-card h3 {
        font-size: 1.1rem;
        font-weight: 500;
        margin-bottom: 15px;
        color: #aaa;
    }

    .balance-card h1 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .balance-card .growth {
        color: #28a745;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: rgba(40, 167, 69, 0.1);
        padding: 5px 15px;
        border-radius: 20px;
    }

    /* Chart Cards */
    .chart-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: var(--card-shadow);
        height: 100%;
    }

    .chart-card h4 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 10px;
        color: #333;
    }

    .chart-card p {
        font-size: 0.85rem;
        color: #666;
        margin-bottom: 20px;
    }

    .chart-card canvas {
        max-height: 250px;
    }

    .chart-info {
        display: flex;
        justify-content: space-between;
        margin-top: 15px;
        font-size: 0.85rem;
        color: #666;
    }

    .chart-info span {
        font-weight: 600;
        color: #333;
    }

    .badge-vente {
        background: #007bff;
        color: white;
        padding: 8px 20px;
        border-radius: 20px;
        display: inline-block;
        margin-top: 10px;
        font-size: 0.85rem;
    }

    /* Pie Chart Card */
    .pie-chart-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: var(--card-shadow);
        text-align: center;
        height: 100%;
    }

    .pie-legend {
        display: flex;
        justify-content: space-around;
        margin-top: 20px;
    }

    .pie-legend-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 5px;
    }

    .pie-legend-item .percentage {
        font-size: 1.5rem;
        font-weight: 700;
    }

    .pie-legend-item .label {
        font-size: 0.85rem;
        color: #666;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .pie-legend-item .color-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }

    .pie-legend-item .color-dot.active {
        background: #007bff;
    }

    .pie-legend-item .color-dot.inactive {
        background: #ffc107;
    }

    /* Projects Card */
    .projects-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: var(--card-shadow);
        height: 100%;
    }

    .projects-card h4 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 20px;
        color: #333;
    }

    .project-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 10px;
        margin-bottom: 15px;
    }

    .project-item .number {
        background: #e3f2fd;
        width: 35px;
        height: 35px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #007bff;
        font-size: 0.9rem;
    }

    .project-item .text {
        flex: 1;
        font-size: 0.9rem;
        color: #333;
    }

    .projects-filter {
        display: flex;
        gap: 15px;
        margin-top: 20px;
        font-size: 0.85rem;
        color: #666;
    }

    .projects-filter span {
        cursor: pointer;
        padding: 5px 10px;
        border-radius: 5px;
        transition: background 0.2s;
    }

    .projects-filter span:hover,
    .projects-filter span.active {
        background: #e3f2fd;
        color: #007bff;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .welcome-card p {
            max-width: 100%;
        }
        
        .welcome-illustration {
            width: 150px;
        }
    }

    @media (max-width: 992px) {
        .stats-container {
            flex-wrap: wrap;
        }
        
        .stat-card {
            flex: 0 0 calc(33.333% - 10px);
            min-width: 150px;
        }
    }

    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
            padding: 15px;
        }

        .welcome-card {
            padding: 30px 20px;
            min-height: 180px;
        }

        .welcome-card h2 {
            font-size: 1.5rem;
        }

        .welcome-illustration {
            display: none;
        }

        .stats-container {
            overflow-x: auto;
            flex-wrap: nowrap;
            gap: 10px;
        }

        .stat-card {
            flex: 0 0 auto;
            min-width: 140px;
        }

        .balance-card h1 {
            font-size: 1.5rem;
        }
    }

    @media (max-width: 576px) {
        .chart-card canvas {
            max-height: 200px;
        }
    }
</style>
@endsection

@section('main-content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="row">
                <!-- Left Section: Welcome + Stats -->
                <div class="col-lg-8 mb-4">
                    <!-- Welcome Card -->
                    <div class="welcome-card mb-3">
                        <h2>Bienvenu à Maxskills</h2>
                        <p>Our platform offers a comprehensive range of courses designed to empower you with the skills & knowledge.</p>
                        <button class="btn btn-connexion">Connexion</button>
                        <!-- Add your illustration image here -->
                        <!-- <img src="{{ asset('assets/images/welcome-illustration.png') }}" alt="Illustration" class="welcome-illustration"> -->
                    </div>

                    <!-- Stats Cards -->
                    <div class="stats-container">
                        <div class="stat-card">
                            <h5>Apparnant</h5>
                            <h3>
                                {{ $stats['apprenants']['count'] }}
                                <span class="percentage positive">
                                    0% <i class="bi bi-arrow-up"></i>
                                </span>
                            </h3>
                        </div>
                        <div class="stat-card">
                            <h5>Cours</h5>
                            <h3>
                                1
                                <span class="percentage negative">
                                    0% <i class="bi bi-arrow-down"></i>
                                </span>
                            </h3>
                        </div>
                        <div class="stat-card">
                            <h5>Projets</h5>
                            <h3>
                                -
                                <span class="percentage positive">
                                    0% <i class="bi bi-arrow-up"></i>
                                </span>
                            </h3>
                        </div>
                    </div>
                </div>

                <!-- Right Section: Balance Card -->
                <div class="col-lg-4 mb-4">
                    <div class="balance-card">
                        <img src="{{ asset('assets/images/background/logo-dash.png') }}" alt="MaxSkills Logo" class="logo">
                        <h3>Balance Total</h3>
                        <h1 style="color:white;">{{ $stats['balance']['total'] }} DT</h1>
                        <div class="growth">
                            <span>+12.34%</span>
                            <i class="bi bi-arrow-up"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row mt-3">
                <!-- Revenue Curve -->
                <div class="col-lg-6 mb-4">
                    <div class="chart-card">
                        <h4>Courbe des revenues</h4>
                        <p>Il suit vos progrès depuis le début jusqu'à maintenant</p>
                        <canvas id="revenueChart"></canvas>
                        <div class="chart-info">
                            <div>
                                <small>Minimum</small>
                                <span>100DT</span>
                            </div>
                            <div>
                                <small>Maximum</small>
                                <span>1250DT</span>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="badge-vente">Vente ce jour 150 DT</div>
                        </div>
                    </div>
                </div>

                <!-- Pie Chart -->
                <div class="col-lg-6 mb-4">
                    <div class="pie-chart-card">
                        <h4>Statut des Utilisateurs</h4>
                        <div style="max-width: 300px; margin: 0 auto;">
                            <canvas id="pieChart"></canvas>
                        </div>
                        <div class="pie-legend">
                            <div class="pie-legend-item">
                                <div class="percentage" style="color: #007bff;">75%</div>
                                <div class="label">
                                    <span class="color-dot active"></span>
                                    Active
                                </div>
                            </div>
                            <div class="pie-legend-item">
                                <div class="percentage" style="color: #ffc107;">25%</div>
                                <div class="label">
                                    <span class="color-dot inactive"></span>
                                    Inactif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Charts Row -->
            <div class="row mt-3">
                <!-- Radar Chart -->
                <div class="col-lg-4 mb-4">
                    <div class="chart-card">
                        <h4>Visionnage par Chapitre</h4>
                        <canvas id="radarChart"></canvas>
                    </div>
                </div>

                <!-- Bar Chart -->
                <div class="col-lg-4 mb-4">
                    <div class="chart-card">
                        <h4>Visionnage par heure</h4>
                        <canvas id="barChart"></canvas>
                    </div>
                </div>

                <!-- Projects -->
                <div class="col-lg-4 mb-4">
                    <div class="projects-card">
                        <h4>Projets</h4>
                        <div class="project-item">
                            <div class="number">1</div>
                            <div class="text">
                                <strong>Projet 1</strong><br>
                                <small>Chapitre 1 | Introduction Photoshop</small>
                            </div>
                        </div>
                        <div class="project-item">
                            <div class="number">6</div>
                            <div class="text">
                                <strong>Projet 6</strong><br>
                                <small>Chapitre 2 | Base de Photoshop</small>
                            </div>
                        </div>
                        <div class="projects-filter">
                            <span class="active">Tous</span>
                            <span>Chapitre 1</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Revenue Curve (Line Chart)
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'],
            datasets: [{
                label: 'Revenus',
                data: [100, 110, 300, 200, 150, 100, 150, 350, 250, 150, 100, 150],
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: '#007bff',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#007bff',
                    padding: 12,
                    cornerRadius: 8
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 500,
                    ticks: {
                        stepSize: 150,
                        color: '#999'
                    },
                    grid: {
                        color: '#f0f0f0'
                    }
                },
                x: {
                    ticks: {
                        color: '#999'
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Pie Chart (Active/Inactive)
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    const pieChart = new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: ['Active', 'Inactif'],
            datasets: [{
                data: [75, 25],
                backgroundColor: ['#007bff', '#ffc107'],
                borderWidth: 0,
                cutout: '70%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: true
                }
            }
        }
    });

    // Radar Chart (Viewing by Chapter)
    const radarCtx = document.getElementById('radarChart').getContext('2d');
    const radarChart = new Chart(radarCtx, {
        type: 'radar',
        data: {
            labels: ['Chapitre1', 'Chapitre2', 'Chapitre', 'Chapitre', 'Chapitre', 'Chapitre6'],
            datasets: [{
                label: 'Visionnage',
                data: [85, 70, 90, 75, 65, 50],
                backgroundColor: 'rgba(64, 224, 208, 0.2)',
                borderColor: 'rgba(64, 224, 208, 1)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(64, 224, 208, 1)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                r: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        stepSize: 25,
                        color: '#999'
                    },
                    grid: {
                        color: '#e0e0e0'
                    },
                    pointLabels: {
                        color: '#666',
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });

    // Bar Chart (Viewing by Hour)
    const barCtx = document.getElementById('barChart').getContext('2d');
    const barChart = new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: ['6', '7', '8', '9', '10', '11', '12', '1'],
            datasets: [{
                label: 'Visionnage',
                data: [25, 35, 55, 40, 30, 75, 85, 70],
                backgroundColor: '#28a745',
                borderRadius: 5,
                barThickness: 20
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        stepSize: 25,
                        color: '#999'
                    },
                    grid: {
                        color: '#f0f0f0'
                    }
                },
                x: {
                    ticks: {
                        color: '#999'
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
});
</script>
@endsection