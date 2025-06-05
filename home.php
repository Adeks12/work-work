<?php
require_once('libs/dbfunctions.php');
$dbobject = new dbobject();

@session_start();
if (!isset($_SESSION['username_sess'])) {
    header('location: sign_in.php');
}

require_once('class/menu.php');
$menu = new Menu();
$menu_list = $menu->generateMenu($_SESSION['role_id_sess']);
$menu_list = $menu_list['data'];

$sql = "SELECT bank_name,account_no,account_name,registration_completed, merchant_id FROM userdata WHERE username = '$_SESSION[username_sess]' LIMIT 1 ";
$user_det = $dbobject->db_query($sql);

$registration_complete = isset($user_det[0]['registration_completed']) ? $user_det[0]['registration_completed'] : 0;
$merchant_id = isset($user_det[0]['merchant_id']) ? $user_det[0]['merchant_id'] : 0;

$sql2 = "SELECT merchant_first_name FROM merchant_reg WHERE merchant_id = '$merchant_id' LIMIT 1";
$sql3 = "SELECT merchant_last_name FROM merchant_reg WHERE merchant_id = '$merchant_id' LIMIT 1";
$user_det2 = $dbobject->db_query($sql2);
$user_det3 = $dbobject->db_query($sql3);
$merchant_first_name = isset($user_det2[0]['merchant_first_name']) ? $user_det2[0]['merchant_first_name'] : '';
$merchant_last_name = isset($user_det3[0]['merchant_last_name']) ? $user_det3[0]['merchant_last_name'] : '';

header("Cache-Control: no-cache;no-store, must-revalidate");
header_remove("X-Powered-By");
header_remove("Server");
header('X-Frame-Options: SAMEORIGIN');

// Get active department count using your Department class
require_once('class/department.php');
$deptObj = new Department();
$merchant_id = $_SESSION['merchant_id'] ?? '';
$active_dept_count = 0;
if ($merchant_id) {
    $sql = "SELECT COUNT(*) as cnt FROM department WHERE merchant_id='$merchant_id' AND depmt_status='1'";
    $result = $dbobject->db_query($sql);
    $active_dept_count = isset($result[0]['cnt']) ? $result[0]['cnt'] : 0;
}

// Get total number of employed staffs (staff_status = '1')
$staff_count = 0;
$sql_staff = "SELECT COUNT(*) as cnt FROM staff WHERE merchant_id='$merchant_id' AND staff_status='1'";
$result_staff = $dbobject->db_query($sql_staff, true);
$staff_count = isset($result_staff[0]['cnt']) ? $result_staff[0]['cnt'] : 0;
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark" data-layout="fluid" data-sidebar-theme="dark" data-sidebar-position="left" data-sidebar-behavior="compact">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Responsive Bootstrap 5 Admin &amp; Dashboard Template">
    <meta name="author" content="Bootlab">
    <title>Dashboard | Vuvaa Lifestyle</title>
    <link rel="canonical" href="home.php" />
    <link rel="shortcut icon" href="img/favicon.ico">
    <link rel="icon" href="img/icon.png" sizes="32x32" />
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&amp;display=swap" rel="stylesheet">
    <link href="css/app.css" rel="stylesheet">
    <script src="js/settings.js"></script>
    <style>
        /* Add to your CSS */
        .sidebar-footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            background: var(--bs-sidebar-bg, #232e3c);
        }
        .sidebar-logout-btn {
            background: var(--bs-sidebar-bg, #232e3c) !important;
            color: var(--bs-sidebar-color, #fff) !important;
            border: none;
            transition: background 0.2s, color 0.2s;
        }
        .sidebar-logout-btn:hover, .sidebar-logout-btn:focus {
            background: var(--bs-sidebar-hover-bg, #1a2230) !important;
            color: var(--bs-sidebar-hover-color, #fff) !important;
        }
        .custom-modal {
          display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100vw; height: 100vh;
          background: rgba(0,0,0,0.5); justify-content: center; align-items: center;
        }
        .custom-modal.show { display: flex; }
        .custom-modal-content {
          background: #fff; padding: 2rem 1.5rem 1.5rem 1.5rem; border-radius: 8px; max-width: 600px; width: 95vw; position: relative;
          box-shadow: 0 8px 32px rgba(0,0,0,0.2);
        }
        .custom-modal-close {
          position: absolute; top: 10px; right: 20px; font-size: 2rem; color: #333; cursor: pointer; z-index: 2;
        }
        @media (max-width: 600px) {
          .custom-modal-content { padding: 1rem; }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <nav id="sidebar" class="sidebar">
        <div class="sidebar-content js-simplebar">
            <a class="sidebar-brand" href="home.php">
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" viewBox="0 0 20 20">
                    <path d="M19.4,4.1l-9-4C10.1,0,9.9,0,9.6,0.1l-9,4C0.2,4.2,0,4.6,0,5s0.2,0.8,0.6,0.9l9,4C9.7,10,9.9,10,10,10s0.3,0,0.4-0.1l9-4
                      C19.8,5.8,20,5.4,20,5S19.8,4.2,19.4,4.1z"/>
                    <path d="M10,15c-0.1,0-0.3,0-0.4-0.1l-9-4c-0.5-0.2-0.7-0.8-0.5-1.3c0.2-0.5,0.8-0.7,1.3-0.5l8.6,3.8l8.6-3.8c0.5-0.2,1.1,0,1.3,0.5
                      c0.2,0.5,0,1.1-0.5,1.3l-9,4C10.3,15,10.1,15,10,15z"/>
                    <path d="M10,20c-0.1,0-0.3,0-0.4-0.1l-9-4c-0.5-0.2-0.7-0.8-0.5-1.3c0.2-0.5,0.8-0.7,1.3-0.5l8.6,3.8l8.6-3.8c0.5-0.2,1.1,0,1.3,0.5
                      c0.2,0.5,0,1.1-0.5,1.3l-9,4C10.3,20,10.1,20,10,20z"/>
                </svg>
                <span class="align-middle me-3">Vuvaa Lifestyle</span>
            </a>
            <ul class="sidebar-nav">
                <li class="sidebar-item active">
                    <a href="home.php" class="sidebar-link">
                        <i class="align-middle fa fa-tachometer-alt"></i> <span class="align-middle">Dashboard</span>
                    </a>
                </li>
                <!-- Dynamic Menu Items -->
                <?php foreach ($menu_list as $value) : ?>
                    <?php if ($value['has_sub_menu'] == false) : ?>
                        <li class="sidebar-item">
                            <a href="javascript:getpage('<?php echo $value['menu_url'] ?>','page')" class="sidebar-link"
                               title="<?php echo ucfirst($value['menu_name']) ?>">
                                <i class="fa fa-<?php 
                                    $menu_name = strtolower($value['menu_name']);
                                    if (strpos($menu_name, 'user') !== false) echo 'users';
                                    elseif (strpos($menu_name, 'church') !== false) echo 'church';
                                    elseif (strpos($menu_name, 'finance') !== false || strpos($menu_name, 'money') !== false) echo 'money';
                                    elseif (strpos($menu_name, 'report') !== false) echo 'chart-bar';
                                    elseif (strpos($menu_name, 'setting') !== false) echo 'cog';
                                    elseif (strpos($menu_name, 'member') !== false) echo 'group';
                                    elseif (strpos($menu_name, 'tithe') !== false) echo 'leaf';
                                    elseif (strpos($menu_name, 'offering') !== false) echo 'gift';
                                    elseif (strpos($menu_name, 'project') !== false) echo 'building';
                                    elseif (strpos($menu_name, 'donation') !== false) echo 'heart';
                                    elseif (strpos($menu_name, 'transaction') !== false) echo 'exchange-alt';
                                    elseif (strpos($menu_name, 'department') !== false) echo 'building';
                                    elseif (strpos($menu_name, 'staff') !== false) echo 'user-tie';
                                    elseif (strpos($menu_name, 'items') !== false) echo 'box';
                                    elseif (strpos($menu_name, 'inventory') !== false) echo 'warehouse';
                                    else echo 'circle';
                                ?>" aria-hidden="true"></i>
                                <span class="align-middle"><?php echo ucfirst($value['menu_name']) ?></span>
                            </a>
                        </li>
                    <?php elseif ($value['has_sub_menu'] == true) : ?>
                        <li class="sidebar-item">
                            <a data-bs-target="#submenu-<?php echo $value['menu_id'] ?>" data-bs-toggle="collapse"
                               class="sidebar-link collapsed"
                               aria-expanded="false"
                               title="<?php echo ucfirst($value['menu_name']) ?>">
                                <i class="<?php echo !empty($value['icon']) ? $value['icon'] : 'fa fa-folder'; ?>" aria-hidden="true"></i>
                                <span class="align-middle"><?php echo ucfirst($value['menu_name']) ?></span>
                            </a>
                            <ul id="submenu-<?php echo $value['menu_id'] ?>" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                                <?php foreach ($value['sub_menu'] as $sub_item) : ?>
                                    <li class="sidebar-item">
                                        <a class="sidebar-link"
                                           href="javascript:loadNavPage('<?php echo $sub_item['menu_url'] ?>','page', '<?php echo $sub_item['menu_id'] ?>')"
                                           title="<?php echo ucfirst($sub_item['name']) ?>">
                                            <i class="fa fa-angle-right" aria-hidden="true"></i>
                                            <span><?php echo ucfirst($sub_item['name']) ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
            <!-- Profile & Logout under menu -->
            <div class="sidebar-footer mt-4">
                <div class="d-flex align-items-center px-3 py-2">
                    <img src="<?php echo $_SESSION['photo_path_sess'] ?>" class="rounded-circle me-2" alt="Profile" width="40" height="40" onerror="this.src='img/avatars/avatar.jpg'">
                    <div>
                        <div class="fw-bold"><?php echo $merchant_first_name . ' ' . $merchant_last_name; ?></div>
                        <div class="text-muted small"><?php echo $_SESSION['role_id_sess']; ?></div>
                    </div>
                </div>
                <div class="d-flex flex-column gap-2 px-3 pb-3">
                    <a href="javascript:getpage('profile.php','page')" class="btn btn-outline-light btn-sm w-100">
                        <i class="fa fa-user me-1"></i> Profile
                    </a>
                    <button class="sidebar-logout-btn btn btn-sm w-100" onclick="confirmLogout()">
                        <i class="fa fa-sign-out-alt me-1"></i> Logout
                    </button>
                </div>
            </div>
        </div>
    </nav>
    <div class="main">
        <nav class="navbar navbar-expand navbar-bg">
            <a class="sidebar-toggle">
                <i class="hamburger align-self-center"></i>
            </a>
            <form class="d-none d-sm-inline-block">
                <div class="input-group input-group-navbar">
                    <input type="text" class="form-control" placeholder="Search projects…" aria-label="Search">
                    <button class="btn" type="button">
                        <i class="align-middle" data-lucide="search"></i>
                    </button>
                </div>
            </form>
            <ul class="navbar-nav navbar-align ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
                        <img src="<?php echo $_SESSION['photo_path_sess'] ?>" class="avatar img-fluid rounded-circle me-1" alt="Profile" />
                        <span class="text-dark"><?php echo $_SESSION['firstname_sess'] . ' ' . $_SESSION['lastname_sess']; ?></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="javascript:getpage('profile.php','page')">
                            <i class="align-middle me-1" data-feather="user"></i> Profile
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php">Sign out</a>
                    </div>
                </li>
            </ul>
        </nav>
        <main class="content">
            <div class="container-xl p-0" id="page" style="max-width:900px;">
                <!-- 1. Welcome Card, Active Departments, and Total Earnings on the same line -->
                <?php if ($registration_complete == 0): ?>
                <script>
                    // On page load, load complete_onboarding.php into #page
                    document.addEventListener('DOMContentLoaded', function () {
                        getpage('complete_onboarding.php', 'page');
                    });
                </script>
                <?php else: ?>
                <div class="row g-3 mb-3 align-items-stretch">
                    <!-- Welcome Card (left) -->
                    <div class="col-12 col-lg-6 d-flex">
                        <div class="card illustration flex-fill shadow-sm border-0 bg-gradient-primary text-white h-100">
                            <div class="card-body p-0 d-flex flex-fill">
                                <div class="row g-0 w-100">
                                    <div class="col-8 d-flex flex-column justify-content-center">
                                        <div class="p-4">
                                            <h4 class="fw-bold mb-1">Welcome Back, <?php echo $merchant_first_name; ?>!</h4>
                                            <p class="mb-0">Vuvaa Dashboard</p>
                                        </div>
                                    </div>
                                    <div class="col-4 align-self-end text-end">
                                        <img src="img/illustrations/customer-support.png" alt="Customer Support" class="img-fluid illustration-img" style="max-height:70px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Active Departments (top right) -->
                    <div class="col-6 col-lg-3 d-flex">
                        <div class="card flex-fill shadow-sm border-0 h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="stat-icon bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                                    <i class="fa fa-building fa-lg"></i>
                                </div>
                                <div>
                                    <h3 class="mb-1 fw-bold"><?php echo $active_dept_count; ?></h3>
                                    <div class="text-muted">Active Departments</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Total Earnings (top right) -->
                    <div class="col-6 col-lg-3 d-flex">
                        <div class="card flex-fill shadow-sm border-0 h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="stat-icon bg-success text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                                    <i class="fa fa-dollar-sign fa-lg"></i>
                                </div>
                                <div>
                                    <h3 class="mb-1 fw-bold">$24,300</h3>
                                    <div class="text-muted">Total Earnings</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 2. Statistics Analysis Cards -->
                <div class="row g-3 mb-3">
                    <div class="col-12 col-sm-6 col-lg-4 d-flex">
                        <div class="card flex-fill shadow-sm border-0">
                            <div class="card-body py-4 d-flex align-items-center">
                                <div class="stat-icon bg-info text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                                    <i class="fa fa-users fa-lg"></i>
                                </div>
                                <div>
                                    <h3 class="mb-1 fw-bold"><?php echo $staff_count; ?></h3>
                                    <div class="text-muted">Currently Employed</div>
                                    <div class="d-flex align-items-center">
                                        <span
                                            class="badge bg-info-subtle text-info me-2"><?php echo $staff_count; ?></span>
                                        <span class="text-muted small">Staffs Employed</span>
                                    </div>
                                   
                                </div>
                            </div>
                        </div>
                    </div>
                   
                    <div class="col-12 col-sm-6 col-lg-4 d-flex">
                        <div class="card flex-fill shadow-sm border-0">
                            <div class="card-body py-4 d-flex align-items-center">
                                <div class="stat-icon bg-warning text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                                    <i class="fa fa-briefcase fa-lg"></i>
                                </div>
                                <div>
                                    <h3 class="mb-1 fw-bold">320</h3>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-warning-subtle text-warning me-2">-1.2%</span>
                                        <span class="text-muted small">Projects</span>
                                    </div>
                                    <div class="text-muted">Active</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pie Chart Card for Statistics -->
                    <div class="col-12 col-lg-4 d-flex">
                        <div class="card flex-fill shadow-sm border-0">
                            <div class="card-header bg-white border-0 pb-0">
                                <h5 class="card-title mb-0">Statistics Overview</h5>
                            </div>
                            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                <canvas id="dashboard-stats-pie" style="max-width:180px;max-height:180px;"></canvas>
                                <div class="w-100 mt-3">
                                    <div class="row text-center">
                                        <div class="col">
                                            <span class="badge bg-success" style="width:12px;height:12px;display:inline-block;border-radius:50%;"></span>
                                            <span class="small">Earnings</span>
                                        </div>
                                        <div class="col">
                                            <span class="badge bg-info" style="width:12px;height:12px;display:inline-block;border-radius:50%;"></span>
                                            <span class="small">Users</span>
                                        </div>
                                        <div class="col">
                                            <span class="badge bg-warning" style="width:12px;height:12px;display:inline-block;border-radius:50%;"></span>
                                            <span class="small">Projects</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                    
                    <?php endif; ?>
                </div>
               
              
            </div>
        </main>
        <style>
            .stat-icon {
                font-size: 1.5rem;
                box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            }
            .bg-success-subtle { background: #e6f4ea !important; }
            .bg-info-subtle { background: #e7f3fa !important; }
            .bg-warning-subtle { background: #fff7e6 !important; }
            @media (max-width: 1400px) {
                .container-xl {
                    max-width: 98vw !important;
                }
            }
        </style>
        <script>
            // Statistics Pie Chart (custom, not from backend)
            $(document).ready(function() {
                if ($("#dashboard-stats-pie").length) {
                    new Chart(document.getElementById("dashboard-stats-pie"), {
                        type: 'doughnut',
                        data: {
                            labels: ['Earnings', 'Users', 'Projects'],
                            datasets: [{
                                data: [24300, 1250, 320],
                                backgroundColor: [
                                    '#22c55e', // green
                                    '#0ea5e9', // blue
                                    '#f59e42'  // orange
                                ],
                                borderWidth: 2,
                                borderColor: '#fff',
                                hoverOffset: 8
                            }]
                        },
                        options: {
                            cutout: '70%',
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.label || '';
                                            let value = context.parsed || 0;
                                            return `${label}: ${value}`;
                                        }
                                    }
                                }
                            },
                            animation: {
                                animateRotate: true,
                                animateScale: true
                            }
                        }
                    });
                }
            });
        </script>
    </div>
</div>
<!-- Custom Modal -->
<div id="customModal" class="custom-modal">
  <div class="custom-modal-content" id="customModalContent">
    <span class="custom-modal-close" onclick="closeCustomModal()">&times;</span>
    <!-- Modal content will be loaded here -->
  </div>
</div>
<div class="modal fade" id="defaultModalPrimary" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" id="modal_div">
      <!-- Modal content will be loaded here -->
    </div>
  </div>
</div>
<script src="js/app.js"></script>
<script src="js/jquery-3.6.0.min.js"></script>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/sweet_alerts.js"></script>
<script src="js/jquery.blockUI.js"></script>
<script src="js/main.js"></script>
<script src="js/parsely.js"></script>
<script src="js/owl.carousel.js"></script>
<script src="js/chart.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script>
    function confirmLogout() {
        if (typeof Swal === 'undefined') {
            if (confirm('Are you sure you want to sign out?')) {
                window.location.href = 'logout.php';
            }
            return;
        }
        Swal.fire({
            title: 'Sign Out?',
            text: 'Are you sure you want to sign out of your account?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Sign Out',
            cancelButtonText: 'Cancel',
            customClass: { popup: 'logout-confirmation' }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Signing Out...',
                    text: 'Please wait while we sign you out.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => { Swal.showLoading(); }
                });
                setTimeout(() => { window.location.href = 'logout.php'; }, 1000);
            }
        }).catch((error) => {
            window.location.href = 'logout.php';
        });
    }
    window.confirmLogout = confirmLogout;

    function setActiveMenuByUrl(url) {
        // Remove active from all
        $('.sidebar-item').removeClass('active');
        // For main menu
        $('.sidebar-link').each(function() {
            let href = $(this).attr('href');
            // For javascript:getpage('page.php','page') or javascript:loadNavPage('page.php','page',...)
            if (href && href.indexOf(url) !== -1) {
                $(this).closest('.sidebar-item').addClass('active');
                // If submenu, also open parent
                $(this).parents('.sidebar-dropdown').addClass('show');
                $(this).parents('.sidebar-item').children('.sidebar-link').removeClass('collapsed');
            }
        });
    }

    function getpage(url, target) {
        $("#" + target).html('<div class="text-center p-5"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>');
        $.get(url, function(data) {
            $("#" + target).html(data);
            setActiveMenuByUrl(url);
        });
    }

    function loadNavPage(url, target, menu_id) {
        $("#" + target).html('<div class="text-center p-5"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>');
        $.get(url, function(data) {
            $("#" + target).html(data);
            setActiveMenuByUrl(url);
        });
    }

    $(document).ready(function() {
        // Load Pie Distribution with a beautiful chart design
        $.post('utilities.php', {
            op: 'Dashboard.topFiveChurches'
        }, function(dd) {
            $("#tfive").html(dd.topfive);

            // Enhanced Pie Chart Design
            const ctxPie = document.getElementById("chartjs-dashboard-pie").getContext('2d');
            new Chart(ctxPie, {
                type: 'doughnut',
                data: dd.pie.data,
                options: {
                    responsive: true,
                    cutout: '65%', // Makes it a doughnut
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                color: getComputedStyle(document.body).getPropertyValue('--bs-body-color') || '#333',
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                padding: 20,
                                boxWidth: 18
                            }
                        },
                        tooltip: {
                            enabled: true,
                            backgroundColor: '#fff',
                            titleColor: '#333',
                            bodyColor: '#333',
                            borderColor: '#e5e7eb',
                            borderWidth: 1,
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.parsed || 0;
                                    let total = context.chart._metasets[context.datasetIndex].total || 1;
                                    let percent = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value} (${percent}%)`;
                                }
                            }
                        }
                    },
                    animation: {
                        animateRotate: true,
                        animateScale: true
                    }
                }
            });
        }, 'json');
        
        // Line Chart
        $.post('utilities.php', {
            op: 'Dashboard.remittance'
        }, function(dd) {
            new Chart(document.getElementById("chartjs-dashboard-line"), dd);
        }, 'json');

        // DataTable
        $("#datatables-dashboard-projects").DataTable({
            destroy: true,
            pageLength: 6,
            lengthChange: false,
            bFilter: false,
            autoWidth: false
        });

        // Statistics Pie Chart (custom, not from backend)
        if ($("#dashboard-stats-pie").length) {
            new Chart(document.getElementById("dashboard-stats-pie"), {
                type: 'doughnut',
                data: {
                    labels: ['Earnings', 'Users', 'Projects'],
                    datasets: [{
                        data: [24300, 1250, 320],
                        backgroundColor: [
                            '#22c55e', // green
                            '#0ea5e9', // blue
                            '#f59e42'  // orange
                        ],
                        borderWidth: 2,
                        borderColor: '#fff',
                        hoverOffset: 8
                    }]
                },
                options: {
                    cutout: '70%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.parsed || 0;
                                    return `${label}: ${value}`;
                                }
                            }
                        }
                    },
                    animation: {
                        animateRotate: true,
                        animateScale: true
                    }
                }
            });
        }
    });
    function openCustomModal(content) {
      document.getElementById('customModalContent').innerHTML = '<span class="custom-modal-close" onclick="closeCustomModal()">&times;</span>' + content;
      document.getElementById('customModal').classList.add('show');
    }
    function closeCustomModal() {
      document.getElementById('customModal').classList.remove('show');
    }
    // Optional: close modal on background click
    document.getElementById('customModal').onclick = function(e) {
      if (e.target === this) closeCustomModal();
    };
</script>
</body>
</html>