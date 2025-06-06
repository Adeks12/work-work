<?php
require_once('libs/dbfunctions.php');
$dbobject = new dbobject();
$crossorigin = 'anonymous';

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

// Add this before your HTML output to get total new inventory count
$total_new_inventory = 0;
$sql_inventory = "SELECT COUNT(*) as cnt FROM inventory WHERE merchant_id='$merchant_id' AND delete_status != '1'";
$result_inventory = $dbobject->db_query($sql_inventory, true);
$total_new_inventory = isset($result_inventory[0]['cnt']) ? $result_inventory[0]['cnt'] : 0;
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
   <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link class="js-stylesheet" href="css/light.css" rel="stylesheet">
    <link href="css/app.css" rel="stylesheet">

    <!-- Core JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- Custom JS -->
    <script src="js/settings.js"></script>

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-Q3ZYEKLQ68"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'G-Q3ZYEKLQ68');
    </script>

    <style>
        /* Custom styles to ensure Select2 matches your theme */
        .select2-container--bootstrap-5 .select2-selection {
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            padding: 0.375rem 0.75rem;
            height: calc(1.5em + 0.75rem + 2px);
        }
        
        .select2-container--bootstrap-5 .select2-selection--single {
            background-color: #fff;
            border: 1px solid #dee2e6;
        }
        
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            color: #212529;
            line-height: 1.5;
        }
        
        .select2-container--bootstrap-5 .select2-dropdown {
            border-color: #dee2e6;
            border-radius: 0.25rem;
        }
        
        .select2-container--bootstrap-5 .select2-results__option--highlighted[aria-selected] {
            background-color: #0d6efd;
            color: #fff;
        }

        /* Add this to your CSS */
        .sidebar {
            transition: transform 0.3s ease;
        }

        .sidebar.sidebar-mobile-open {
            transform: translateX(0);
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 1050;
                top: 0;
                left: 0;
                height: 100vh;
                width: 260px;
                background: #fff;
            }
            .sidebar.sidebar-mobile-open {
                transform: translateX(0);
            }
        }
    </style>
    
</head>
<body data-theme="default" data-layout="fluid" data-sidebar-position="left" data-sidebar-behavior="sticky">
<div class="wrapper">
    <!-- Sidebar Navigation (Refactored to match your pasted code) -->
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
                <li class="sidebar-header">Navigation</li>
            <li class="sidebar-item <?php echo ($_SERVER['PHP_SELF'] == '/home.php') ? 'active' : ''; ?>">
                <a class="sidebar-link" href="home.php">
                    <i class="align-middle" data-lucide="home"></i>
                    <span class="align-middle">Dashboard</span>
                </a>
            </li>

 
            <?php
            // Icon mapping for menu names (add more as needed)
            $icon_map = [
                'dashboard' => 'home',
                'users' => 'users',
                'user' => 'user',
                'customers' => 'users',
                'customer' => 'user',
                'orders' => 'shopping-cart',
                'order' => 'shopping-cart',
                'products' => 'package',
                'product' => 'package',
                'reports' => 'bar-chart-2',
                'report' => 'bar-chart-2',
                'settings' => 'settings',
                'profile' => 'user',
                'finance' => 'credit-card',
                'wallet' => 'wallet',
                'transactions' => 'repeat',
                'transaction' => 'repeat',
                'messages' => 'message-circle',
                'support' => 'life-buoy',
                'analytics' => 'activity',
                'calendar' => 'calendar',
                'notifications' => 'bell',
                'files' => 'file-text',
                'file manager' => 'folder',
                'department' => 'grid',
                'tasks' => 'check-square',
                'task' => 'check-square',
                'projects' => 'folder',
                'project' => 'folder',
                'invoice' => 'file-text',
                'pricing' => 'tag',
                'email' => 'mail',
                'chat' => 'message-circle',
                'apps' => 'grid',
                'tools' => 'tool',
                'components' => 'layers',
                'pages' => 'layout',
                'auth' => 'lock',
                'logout' => 'log-out',
                // fallback
                'default' => 'circle'
            ]; // <-- CLOSE ARRAY AND ADD SEMICOLON HERE

            function get_icon($name, $icon_map) {
                $key = strtolower(trim($name));
                return $icon_map[$key] ?? $icon_map['default'];
            }
            ?>

            <?php foreach ($menu_list as $value): ?>
                <?php
                    $icon = get_icon($value['menu_name'], $icon_map);
                ?>
                <?php if (!$value['has_sub_menu']): ?>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="javascript:getpage('<?php echo $value['menu_url'] ?>','page')">
                            <i class="align-middle" data-lucide="<?php echo $icon; ?>"></i>
                            <span class="align-middle"><?php echo ucfirst($value['menu_name']) ?></span>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="sidebar-item">
                        <a class="sidebar-link collapsed" data-bs-target="#menu-<?php echo $value['menu_id'] ?>" data-bs-toggle="collapse" href="#">
                            <i class="align-middle" data-lucide="<?php echo $icon; ?>"></i>
                            <span class="align-middle"><?php echo ucfirst($value['menu_name']) ?></span>
                        </a>
                        <ul id="menu-<?php echo $value['menu_id'] ?>" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            <?php foreach ($value['sub_menu'] as $sub): ?>
                                <?php
                                    $sub_icon = get_icon($sub['name'], $icon_map);
                                ?>
                                <li class="sidebar-item">
                                    <a class="sidebar-link" href="javascript:getpage('<?php echo $sub['menu_url'] ?>','page')">
                                        <i class="align-middle" data-lucide="<?php echo $sub_icon; ?>"></i>
                                        <span class="align-middle"><?php echo ucfirst($sub['name']) ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>

            <li class="sidebar-header">Account</li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="javascript:getpage('profile.php','page')">
                    <i class="align-middle" data-lucide="user"></i>
                    <span class="align-middle">Profile</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="logout.php">
                    <i class="align-middle" data-lucide="log-out"></i>
                    <span class="align-middle">Logout</span>
                </a>
            </li>
        </ul>
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
        <main class="content" id ="page">
             <?php if ($registration_complete != 1) {
        include('complete_onboarding.php');
    } else { ?>
    <div class="container-fluid p-0">
              <div class="row mb-2 mb-xl-3">
            <div class="col-auto d-none d-sm-block">
                <h3>Dashboard</h3>
            </div>
             <div class="col-auto ms-auto text-end mt-n1">
                <div class="dropdown me-2 d-inline-block position-relative">
                    <a class="btn btn-light bg-white shadow-sm dropdown-toggle" href="#" data-bs-toggle="dropdown" data-bs-display="static">
                        <i class="align-middle mt-n1" data-lucide="calendar"></i> Today
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <h6 class="dropdown-header">Settings</h6>
                        <a class="dropdown-item" href="#">Action</a>
                        <a class="dropdown-item" href="#">Another action</a>
                        <a class="dropdown-item" href="#">Something else here</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Separated link</a>
                    </div>
                </div>
                <button class="btn btn-primary shadow-sm">
                    <i class="align-middle" data-lucide="filter">&nbsp;</i>
                </button>
                <button class="btn btn-primary shadow-sm">
                    <i class="align-middle" data-lucide="refresh-cw">&nbsp;</i>
                </button>
            </div>
        </div>

         <!-- Stat Cards -->
        <div class="row">
            <div class="col-12 col-sm-6 col-xxl-3 d-flex">
                <div class="card illustration flex-fill">
                    <div class="card-body p-0 d-flex flex-fill">
                        <div class="row g-0 w-100">
                            <div class="col-6">
                                <div class="illustration-text p-3 m-1">
                                    <h4 class="illustration-text">Welcome Back, <?php echo $merchant_first_name; ?>!</h4>
                                    <p class="mb-0">AppStack Dashboard</p>
                                </div>
                            </div>
                            <div class="col-6 align-self-end text-end">
                                <img src="img/illustrations/customer-support.png" alt="Customer Support" class="img-fluid illustration-img">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xxl-3 d-flex">
                <div class="card flex-fill">
                    <div class="card-body py-4">
                        <div class="d-flex align-items-start">
                            <div class="flex-grow-1">
                                <h3 class="mb-2">$ 18,600</h3>
                                <p class="mb-2">Total Revenue</p>
                                <div class="mb-0">
                                    <span class="badge badge-subtle-success me-2"> +5.35% </span>
                                    <span class="text-muted">Since last week</span>
                                </div>
                            </div>
                            <div class="d-inline-block ms-3">
                                <div class="stat">
                                    <i class="align-middle text-success" data-lucide="dollar-sign"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                     
        </div>

        
        <!-- Stat Cards -->
        <div class="row g-3 mb-3">
            <!-- Active Departments -->
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
            <!-- Total New Inventory -->
            <div class="col-6 col-lg-3 d-flex">
                <div class="card flex-fill shadow-sm border-0 h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-success text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                            <i class="fa fa-box fa-lg"></i>
                        </div>
                        <div>
                            <h3 class="mb-1 fw-bold"><?php echo $total_new_inventory; ?></h3>
                            <div class="text-muted">Total New Inventory</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Newly Employed -->
            <div class="col-12 col-sm-6 col-lg-3 d-flex">
                <div class="card flex-fill shadow-sm border-0 h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-info text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                            <i class="fa fa-users fa-lg"></i>
                        </div>
                        <div>
                            <h3 class="mb-1 fw-bold"><?php echo $staff_count; ?></h3>
                            <div class="text-muted">Newly Employed</div>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-info-subtle text-info me-2"><?php echo $staff_count; ?></span>
                                <span class="text-muted small">Staffs Employed</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Projects (leave as is or remove if not needed) -->
            
            <!-- Pie Chart Card for Statistics -->
            <div class="col-12 col-lg-4 d-flex">
                <div class="card flex-fill shadow-sm border-0">
                    <div class="card-header bg-white border-0 pb-0">
                        <h5 class="card-title mb-0">Statistics Overview</h5>
                    </div>
                    <div class="card-body d-flex flex-column align-items-center justify-content-center">
                        <!-- Make sure the canvas has an ID and width/height for Chart.js -->
                        <canvas id="dashboard-stats-pie" width="180" height="180" style="max-width:180px;max-height:180px;"></canvas>
                        <div class="w-100 mt-3">
                            <div class="row text-center">
                                <div class="col">
                                    <span class="badge bg-primary" style="width:12px;height:12px;display:inline-block;border-radius:50%;"></span>
                                    <span class="small">Active Departments</span>
                                </div>
                                <div class="col">
                                    <span class="badge bg-info" style="width:12px;height:12px;display:inline-block;border-radius:50%;"></span>
                                    <span class="small">Newly Employed</span>
                                </div>
                                <div class="col">
                                    <span class="badge bg-success" style="width:12px;height:12px;display:inline-block;border-radius:50%;"></span>
                                    <span class="small">Total New Inventory</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Stat Cards -->
        <?php } ?>
    </div>
</main>
<!-- ...rest of your code remains unchanged... -->
<!-- Custom Modal -->
<div class="modal fade" id="defaultModalPrimary" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="modal_div">
            <div class="modal-header">
                <h5 class="modal-title">Default modal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-3">
                <p class="mb-0">Use Bootstrap's JavaScript modal plugin to add dialogs to your site for lightboxes, user notifications, or completely custom content.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
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
<script src="js/chart.min.js"></script>
<script>
    // Logout confirmation using SweetAlert or fallback to confirm dialog
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
        }).catch(() => {
            window.location.href = 'logout.php';
        });
    }
    window.confirmLogout = confirmLogout;

    // Sidebar active state management
    function setActiveMenuByUrl(url) {
        // Remove active from all
        document.querySelectorAll('.sidebar-item').forEach(function(item) {
            item.classList.remove('active');
        });
        // Find the sidebar-link that matches the url
        document.querySelectorAll('.sidebar-link').forEach(function(link) {
            // For javascript:getpage('page.php','page')
            let href = link.getAttribute('href');
            if (href && href.indexOf(url) !== -1) {
                link.closest('.sidebar-item').classList.add('active');
                // If submenu, also open parent
                let parentDropdown = link.closest('.sidebar-dropdown');
                if (parentDropdown) {
                    parentDropdown.classList.add('show');
                    let parentItem = parentDropdown.closest('.sidebar-item');
                    if (parentItem) {
                        parentItem.querySelector('.sidebar-link').classList.remove('collapsed');
                    }
                }
            }
        });
    }

    // Intercept sidebar-link clicks for active state and logout
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.sidebar-link').forEach(function(link) {
            link.addEventListener('click', function(e) {
                // If it's the logout link, show confirmation
                if (this.getAttribute('href') === 'logout.php') {
                    e.preventDefault();
                    confirmLogout();
                    return false;
                }
                // Remove active from all, add to this
                document.querySelectorAll('.sidebar-item').forEach(function(item) {
                    item.classList.remove('active');
                });
                let parent = this.closest('.sidebar-item');
                if (parent) parent.classList.add('active');
            });
        });
    });

    // If you use getpage() or loadNavPage(), call setActiveMenuByUrl(url) after loading
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
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Remove any previous chart instance to avoid errors
    if (window.statsPieChart && typeof window.statsPieChart.destroy === 'function') {
        window.statsPieChart.destroy();
    }
    var pieCanvas = document.getElementById("dashboard-stats-pie");
    if (pieCanvas && typeof Chart !== 'undefined') {
        window.statsPieChart = new Chart(pieCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Active Departments', 'Newly Employed', 'Total New Inventory'],
                datasets: [{
                    data: [
                        <?php echo (int)$active_dept_count; ?>,
                        <?php echo (int)$staff_count; ?>,
                        <?php echo (int)$total_new_inventory; ?>
                    ],
                    backgroundColor: [
                        '#2563eb', // blue
                        '#0ea5e9', // info
                        '#22c55e'  // green
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
<!-- ...existing code... -->