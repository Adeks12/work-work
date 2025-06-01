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

// Check if registration is complete
$registration_complete = isset($user_det[0]['registration_completed']) ? $user_det[0]['registration_completed'] : 0;
$merchant_id = isset($user_det[0]['merchant_id']) ? $user_det[0]['merchant_id'] : 0;


$sql2 = "SELECT merchant_first_name FROM merchant_reg WHERE merchant_id = '$merchant_id' LIMIT 1";
$sql3 = "SELECT merchant_last_name FROM merchant_reg WHERE merchant_id = '$merchant_id' LIMIT 1";
$user_det2 = $dbobject->db_query($sql2);
$user_det3 = $dbobject->db_query($sql3);
$merchant_first_name = isset($user_det2[0]['merchant_first_name']) ? $user_det2[0]['merchant_first_name'] : '';
$merchant_last_name = isset($user_det3[0]['merchant_last_name']) ? $user_det3[0]['merchant_last_name'] : '';
;

header("Cache-Control: no-cache;no-store, must-revalidate");
header_remove("X-Powered-By");
header_remove("Server");
header('X-Frame-Options: SAMEORIGIN');
?>
<!DOCTYPE html>
<!--
  HOW TO USE: 
  data-layout: fluid (default), boxed
  data-sidebar-theme: dark (default), colored, light
  data-sidebar-position: left (default), right
  data-sidebar-behavior: sticky (default), fixed, compact
-->
<html lang="en" data-layout="fluid" data-sidebar-position="left" data-sidebar-behavior="sticky">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Responsive Bootstrap 5 Admin &amp; Dashboard Template">
    <meta name="author" content="Bootlab">
    <meta http-equiv="Cache-control" content="no-cache;no-store">

    <title>Dashboard | Vuvaa Lifestyle</title>

    <link rel="canonical" href="home.php" />
    <link rel="shortcut icon" href="img/favicon.ico">
    <link rel="icon" href="img/icon.png" sizes="32x32" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    
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
       <!-- Enhanced Sidebar Navigation -->
       <nav id="sidebar" class="sidebar">
       	<div class="sidebar-content js-simplebar">
       		<!-- Logo Section -->
       		<a class="sidebar-brand" href="home.php" title="Vuvaa Lifestyle Dashboard">
       			<img src="img/logo.png" alt="Vuvaa Logo">
       		</a>

       		<!-- Navigation Menu -->
       		<ul class="sidebar-nav">
       			<!-- Dashboard Link -->
       			<li class="sidebar-item">
       				<a href="home" class="sidebar-link sidebar-link-active" title="Dashboard">
       					<i class="fa fa-tachometer" aria-hidden="true"></i>
       					<span class="align-middle">Dashboard</span>
       				</a>
       			</li>

       			<!-- Dynamic Menu Items -->
       			<?php foreach ($menu_list as $value) : ?>
       			<?php if ($value['has_sub_menu'] == false) : ?>
       			<li class="sidebar-item">
       				<a href="javascript:getpage('<?php echo $value['menu_url'] ?>','page')" class="sidebar-link"
       					title="<?php echo ucfirst($value['menu_name']) ?>">
       					<i class="fa fa-<?php 
                                // Assign appropriate icons based on menu name
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
                                else echo 'circle';
                            ?>" aria-hidden="true"></i>
       					<span class="align-middle"><?php echo ucfirst($value['menu_name']) ?></span>
       				</a>
       			</li>
       			<?php elseif ($value['has_sub_menu'] == true) : ?>
       			<li class="sidebar-item" id="menu-<?php echo $value['menu_id'] ?>">
       				<a data-bs-target="#submenu-<?php echo $value['menu_id'] ?>" data-bs-toggle="<?php echo $value['has_sub_menu'] ? 'collapse' : ''; ?>"
       					class="sidebar-link <?php echo $value['has_sub_menu'] ? 'collapsed' : ''; ?>" 
       					<?php if(!$value['has_sub_menu']): ?>
       					href="javascript:loadNavPage('<?php echo $value['menu_url'] ?>','page', '<?php echo $value['menu_id'] ?>')"
       					<?php endif; ?>
       					aria-expanded="false"
       					title="<?php echo ucfirst($value['menu_name']) ?>">
       					<i class="<?php echo !empty($value['icon']) ? $value['icon'] : 'fa fa-folder'; ?>" aria-hidden="true"></i>
       					<span class="align-middle"><?php echo ucfirst($value['menu_name']) ?></span>
       				</a>
       				<?php if($value['has_sub_menu']): ?>
       				<ul id="submenu-<?php echo $value['menu_id'] ?>" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
       					<?php foreach ($value['sub_menu'] as $sub_item) : ?>
       					<li class="sidebar-item" id="submenu-item-<?php echo $sub_item['menu_id'] ?>">
       						<a class="sidebar-link"
       							href="javascript:loadNavPage('<?php echo $sub_item['menu_url'] ?>','page', '<?php echo $sub_item['menu_id'] ?>')"
       							title="<?php echo ucfirst($sub_item['name']) ?>">
       							<i class="fa fa-angle-right" aria-hidden="true"></i>
       							<span><?php echo ucfirst($sub_item['name']) ?></span>
       						</a>
       					</li>
       					<?php endforeach; ?>
       				</ul>
       				<?php endif; ?>
       			</li>
       			<?php endif; ?>
       			<?php endforeach; ?>
       		</ul>

       		<!-- User Profile Section -->
       		<div class="sidebar-bottom d-none d-lg-block">
       			<div class="user-profile-section">
       				<div class="media">
       					<div class="user-avatar-container">
       						<img class="rounded-circle user-avatar" src="<?php echo $_SESSION['photo_path_sess']; ?>"
       							alt="<?php echo $merchant_first_name . ' ' . $merchant_last_name; ?>"
       							width="40" height="40" onerror="this.src='img/avatars/avatar.jpg'">
       						<div class="user-status-indicator"></div>
       					</div>
       					<div class="media-body">
       						
       						<p class="user-role mb-2">
       							<?php echo $merchant_first_name  . ' ' . $merchant_last_name; ?>
       						</p>
       						<div class="user-actions">
       							<button class="btn btn-outline-light btn-sm profile-btn"
       								onclick="getpage('profile.php','page')" title="View Profile">
       								<i class="fa fa-user" aria-hidden="true"></i>
       								Profile
       							</button>
       							<button class="btn btn-danger btn-sm logout-btn" onclick="confirmLogout()"
       								title="Sign Out">
       								<i class="fa fa-sign-out-alt" aria-hidden="true"></i>
       								Logout
       							</button>
       						</div>
       					</div>
       				</div>
       			</div>
       		</div>
       	</div>
       </nav>

        <div class="main">
            <nav class="navbar navbar-expand navbar-light navbar-bg">
                <a class="sidebar-toggle">
                    <i class="hamburger align-self-center"></i>
                </a>

                <a href="javascript:void(0)" class="d-flex mr-2">
                    <?php $state_loc = ":" . $dbobject->getitemlabel('lga', 'stateid', $_SESSION['state_id_sess'], 'State'); ?>
                    Welcome: &nbsp; 
                    <span style="font-weight:bold; color:#000">
                        <?php 
                        echo $merchant_first_name . ' ' . $merchant_last_name;
                        
                        ?>
                    </span>
                </a>

                <div class="navbar-collapse collapse">
                    <ul class="navbar-nav navbar-align">
                        <li class="nav-item dropdown">
                            <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">
                                <i class="align-middle" data-feather="settings"></i>
                            </a>

                            <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
                                <img src="<?php echo $_SESSION['photo_path_sess'] ?>" 
                                     class="avatar img-fluid rounded-circle mr-1" 
                                     alt="<?php echo $_SESSION['firstname_sess'] . ' ' . $_SESSION['lastname_sess']; ?>" />
                                <span class="text-dark">
                                    <?php echo $_SESSION['firstname_sess'] . ' ' . $_SESSION['lastname_sess']; ?>
                                </span>
                            </a>
                            
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="javascript:getpage('profile.php','page')">
                                    <i class="align-middle mr-1" data-feather="user"></i> Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="logout.php">Sign out</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="content" id="page">
                <?php

                if ($registration_complete != 1){
                    include('complete_onboarding.php');
                } else {
                ?>
                <div class="row owl-carousel" id="">
                    <div class="col-12 col-sm-6 col-xl d-flex">
                        <div class="card flex-fill">
                            <div class="card-body py-4">
                                <div class="media">
                                    <div class="d-inline-block mt-2 mr-3">
                                        <i class="fa fa-tree text-info" style="font-size:35px"></i>
                                    </div>
                                    <div class="media-body">
                                        <h3 class="mb-2">2.562</h3>
                                        <div class="mb-0">Tithe</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl d-flex">
                        <div class="card flex-fill">
                            <div class="card-body py-4">
                                <div class="media">
                                    <div class="d-inline-block mt-2 mr-3">
                                        <!--											<i class="feather-lg text-warning" data-feather="activity"></i>-->
                                        <i class="fa fa-shopping-basket text-warning" style="font-size:35px"></i>
                                    </div>
                                    <div class="media-body">
                                        <h3 class="mb-2">17.212</h3>
                                        <div class="mb-0">Offering</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl d-flex">
                        <div class="card flex-fill">
                            <div class="card-body py-4">
                                <div class="media">
                                    <div class="d-inline-block mt-2 mr-3">
                                        <!--											<i class="feather-lg text-success" data-feather="dollar-sign"></i>-->
                                        <i class="fa fa-building text-success" style="font-size:35px"></i>
                                    </div>
                                    <div class="media-body">
                                        <h3 class="mb-2">$ 24.300</h3>
                                        <div class="mb-0">Projects</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl d-flex">
                        <div class="card flex-fill">
                            <div class="card-body py-4">
                                <div class="media">
                                    <div class="d-inline-block mt-2 mr-3">
                                        <!--											<i class="feather-lg text-danger" data-feather="shopping-bag"></i>-->
                                        <i class="fa fa-money text-danger" style="font-size:35px"></i>
                                    </div>
                                    <div class="media-body">
                                        <h3 class="mb-2">43</h3>
                                        <div class="mb-0">Donations</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl d-none d-xxl-flex">
                        <div class="card flex-fill">
                            <div class="card-body py-4">
                                <div class="media">
                                    <div class="d-inline-block mt-2 mr-3">
                                        <i class="feather-lg text-info" data-feather="dollar-sign"></i>
                                    </div>
                                    <div class="media-body">
                                        <h3 class="mb-2">$ 18.700</h3>
                                        <div class="mb-0">Evangelism</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-lg-8 d-flex">
                        <div class="card flex-fill w-100">
                            <div class="card-header">
                                <!--									<span class="badge badge-primary float-right">Monthly</span>-->
                                <h5 class="card-title mb-0">Total Revenue </h5>
                            </div>
                            <div class="card-body">
                                <div class="chart chart-lg">
                                    <canvas id="chartjs-dashboard-line"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 d-flex">
                        <div class="card flex-fill w-100">
                            <div class="card-header">

                                <h5 class="card-title mb-0">Pie Distribution</h5>
                            </div>
                            <div class="card-body d-flex">
                                <div class="align-self-center w-100">
                                    <div class="py-3">
                                        <div class="chart chart-xs">
                                            <canvas id="chartjs-dashboard-pie"></canvas>
                                        </div>
                                    </div>

                                    <table class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th>Source</th>
                                                <th class="text-right">Revenue</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tfive">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="card flex-fill">
                        <div class="card-header">
                            <div class="card-actions float-end">
                                <div class="dropdown position-relative">
                                    <a href="#" data-bs-toggle="dropdown" data-bs-display="static">
                                        <i class="align-middle" data-feather="more-horizontal"></i>
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="#">Action</a>
                                        <a class="dropdown-item" href="#">Another action</a>
                                        <a class="dropdown-item" href="#">Something else here</a>
                                    </div>
                                </div>
                            </div>
                            <h5 class="card-title mb-0">Latest Projects</h5>
                        </div>
                        <table id="datatables-dashboard-projects" class="table table-striped my-0">
                            <thead>
                                <tr role="row">
                                    <th>Paying Church</th>
                                    <th>Amount</th>
                                    <th>Bank</th>
                                    <th>Account Name</th>
                                    <th>Account Number</th>
                                    <th>Paymnent ID</th>
                                    <th>Paymnent Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $filter = ($_SESSION['role_id_sess'] == 001) ? "" : " AND church_id = '$_SESSION[church_id_sess]' OR source_acct = '$_SESSION[church_id_sess]'";
                                    $sql = "SELECT * FROM transaction_table WHERE 1 = 1 $filter ORDER BY created desc LIMIT 10";
                                    $result = $dbobject->db_query($sql);
                                    foreach ($result as $row) {
                                    ?>
                                <tr>
                                    <td><?php echo $dbobject->getitemlabel("church_table", "church_id", $row['source_acct'], "church_name"); ?>
                                    </td>
                                    <td><?php echo "&#x20a6; " . number_format($row['transaction_amount'], 2); ?></td>
                                    <td><?php $destination_bank = (isset($row['destination_bank'])) ? $row['destination_bank'] : "";
                                                echo $dbobject->getitemlabel("banks", "bank_code", $destination_bank, "bank_name"); ?>
                                    </td>
                                    <td><?php $account_name = (isset($row['account_name'])) ? $row['account_name'] : "";
                                                echo $account_name; ?></td>
                                    <td><?php echo $row['destination_acct']; ?></td>
                                    <td><?php $payment_id = (isset($row['payment_id'])) ? $row['payment_id'] : "";
                                                echo $payment_id; ?></td>
                                    <td><?php echo $row['response_message']; ?></td>
                                </tr>
                                <?php
                                    }
                                    ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php
                }
                ?>
            </main>

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row text-muted">
                        <div class="col-6 text-left">
                            <ul class="list-inline">

                                <li class="list-inline-item">
                                    <a class="text-muted" href="#">Help Center</a>
                                </li>

                            </ul>
                        </div>
                        <div class="col-6 text-right">
                            <p class="mb-0">
                                &copy; <?php echo date('Y'); ?> - <a target="_blank" href="home.php"
                                    class="text-muted">Vuvaa Lifestyle Website</a>
                            </p>
                        </div>
                    </div>
                </div>
            </footer>

            <div class="modal fade" id="defaultModalPrimary" tabindex="-1" role="dialog" aria-hidden="true" 
                 data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog" role="document">
                    <div class="modal-content" id="modal_div">
                        <div class="modal-header">
                            <h5 class="modal-title">Default modal</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
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
    </div>
			

    <!-- Scripts -->
    <script src="js/app.js"></script>
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/sweet_alerts.js"></script>
    <script src="js/jquery.blockUI.js"></script>
    <script src="js/sweet_alerts.js"></script>
    <script src="js/jquery.blockUI.js"></script>
    <script src="js/main.js"></script>
    <script src="js/parsely.js"></script>
    <script src="js/owl.carousel.js"></script>
	<script src="js/chart.min.js"></script>
    
    <!-- Add Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- Initialize Select2 -->
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').each(function() {
                $(this).select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    dropdownParent: $(this).parent(),
                    placeholder: $(this).data('placeholder') || 'Select an option',
                    allowClear: true
                });
            });
            
            // Fix Select2 inside Bootstrap Modal
            $('.modal').on('shown.bs.modal', function () {
                $(this).find('.select2').each(function() {
                    $(this).select2({
                        theme: 'bootstrap-5',
                        dropdownParent: $(this).closest('.modal'),
                        width: '100%'
                    });
                });
            });

            // Load dashboard data
            $.post('utilities.php', {
                op: 'Dashboard.topFiveChurches'
            }, function(dd) {
                $("#tfive").html(dd.topfive);
                new Chart(document.getElementById("chartjs-dashboard-pie"), dd.pie);
            }, 'json');
            
            $.post('utilities.php', {
                op: 'Dashboard.remittance'
            }, function(dd) {
                console.log('record from dashboard ', dd);
                new Chart(document.getElementById("chartjs-dashboard-line"), dd);
            }, 'json');

            // Initialize carousel
            $("#carousel_div").owlCarousel({
                jsonPath: "utilities.php?op=Dashboard.carousel",
                items: 4,
                navigation: true
            });
        });
    </script>

    
       <script>
       // Replace your existing confirmLogout function with this enhanced version
       function confirmLogout() {
       console.log('confirmLogout function called'); // Debug log

       // Check if SweetAlert is loaded
       if (typeof Swal === 'undefined') {
       console.error('SweetAlert not loaded, falling back to confirm dialog');
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
       customClass: {
       popup: 'logout-confirmation'
       }
       }).then((result) => {
       if (result.isConfirmed) {
       // Show loading state
       Swal.fire({
       title: 'Signing Out...',
       text: 'Please wait while we sign you out.',
       allowOutsideClick: false,
       showConfirmButton: false,
       willOpen: () => {
       Swal.showLoading();
       }
       });

       // Redirect to logout
       setTimeout(() => {
       window.location.href = 'logout.php';
       }, 1000);
       }
       }).catch((error) => {
       console.error('SweetAlert error:', error);
       // Fallback to direct logout
       window.location.href = 'logout.php';
       });
       }

       // Alternative function for direct logout (no confirmation)
       function directLogout() {
       window.location.href = 'logout.php';
       }

       // Ensure function is available globally
       window.confirmLogout = confirmLogout;
       window.directLogout = directLogout;

       	// Add active state management for menu items
       	document.addEventListener('DOMContentLoaded', function () {
       		// Remove active class from all menu items
       		function clearActiveStates() {
       			document.querySelectorAll('.sidebar-link').forEach(link => {
       				link.classList.remove('sidebar-link-active');
       			});
       		}

       		// Add click handler for menu items
       		document.querySelectorAll('.sidebar-link').forEach(link => {
       			link.addEventListener('click', function () {
       				if (!this.hasAttribute('data-bs-toggle')) {
       					clearActiveStates();
       					this.classList.add('sidebar-link-active');
       				}
       			});
       		});

       		// Add smooth scrolling for sidebar
       		const sidebar = document.querySelector('.sidebar-content');
       		if (sidebar) {
       			sidebar.style.scrollBehavior = 'smooth';
       		}

       		// Add loading animation for menu items
       		const menuItems = document.querySelectorAll('.sidebar-item');
       		menuItems.forEach((item, index) => {
       			item.style.animationDelay = `${index * 0.1}s`;
       		});
       	});

       	// Enhanced mobile menu toggle
       	function toggleMobileMenu() {
       		const sidebar = document.getElementById('sidebar');
       		sidebar.classList.toggle('sidebar-mobile-open');
       	}

       	// Add keyboard navigation support
       	document.addEventListener('keydown', function (e) {
       		if (e.altKey && e.key === 'm') {
       			e.preventDefault();
       			toggleMobileMenu();
       		}
       	});
       
    </script>

</body>
</html>