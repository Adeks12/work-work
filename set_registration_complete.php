<?php
<?php
@session_start();
$_SESSION['registration_completed'] = 1;
echo json_encode(['status' => 'ok']);