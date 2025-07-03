<?php
include_once("../libs/dbfunctions.php");
$dbobject = new dbobject();

$staff_id = $_GET['staff_id'] ?? '';
$staff = [];
if ($staff_id) {
    $sql = "SELECT s.*, d.depmt_name FROM staff s LEFT JOIN department d ON s.depmt_id = d.depmt_id WHERE s.staff_id = '$staff_id' LIMIT 1";
    $result = $dbobject->db_query($sql, true);
    if ($result && count($result) > 0) {
        $staff = $result[0];
    }
}

$head_of_dept_name = '';
if (!empty($staff['head_of_dept'])) {
    $head_id = $staff['head_of_dept'];
    $head_sql = "SELECT full_name FROM staff WHERE staff_id = '$head_id' LIMIT 1";
    $head_result = $dbobject->db_query($head_sql, true);
    if ($head_result && count($head_result) > 0) {
        $head_of_dept_name = $head_result[0]['full_name'];
    }
}
?>

<div class="modal-header">
    <h4 class="modal-title" style="font-weight:bold">Staff Details</h4>
    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body m-3">
    <?php if ($staff): ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <tbody>
                <tr><th>Full Name</th><td><?php echo htmlspecialchars($staff['full_name'] ?? ''); ?></td></tr>
                <tr><th>Address</th><td><?php echo htmlspecialchars($staff['address'] ?? ''); ?></td></tr>
                <tr><th>Home Phone</th><td><?php echo htmlspecialchars($staff['home_phone'] ?? ''); ?></td></tr>
                <tr><th>Cell Phone</th><td><?php echo htmlspecialchars($staff['cell_phone'] ?? ''); ?></td></tr>
                <tr><th>Email Address</th><td><?php echo htmlspecialchars($staff['personal_email'] ?? ''); ?></td></tr>
                <tr><th>Means of ID/Number</th><td><?php echo htmlspecialchars($staff['state_id_number'] ?? ''); ?></td></tr>
                <tr><th>Birth Date</th><td><?php echo htmlspecialchars($staff['birth_date'] ?? ''); ?></td></tr>
                <tr><th>Marital Status</th><td><?php echo htmlspecialchars($staff['marital_status'] ?? ''); ?></td></tr>
                <tr><th>Spouse’s Name</th><td><?php echo htmlspecialchars($staff['spouse_name'] ?? ''); ?></td></tr>
                <tr><th>Spouse’s Employer</th><td><?php echo htmlspecialchars($staff['spouse_employer'] ?? ''); ?></td></tr>
                <tr><th>Spouse’s Cell Phone</th><td><?php echo htmlspecialchars($staff['spouse_cell_phone'] ?? ''); ?></td></tr>
                <tr><th>Job Title</th><td><?php echo htmlspecialchars($staff['job_title'] ?? ''); ?></td></tr>
                
                <tr><th>Head of Dept</th><td><?php echo htmlspecialchars($head_of_dept_name ?: ''); ?></td></tr>
                <tr><th>Work Location</th><td><?php echo htmlspecialchars($staff['work_location'] ?? ''); ?></td></tr>
                <tr><th>Work Email</th><td><?php echo htmlspecialchars($staff['work_email'] ?? ''); ?></td></tr>
                <tr><th>Work Phone</th><td><?php echo htmlspecialchars($staff['work_phone'] ?? ''); ?></td></tr>
                <tr><th>Work Cell Phone</th><td><?php echo htmlspecialchars($staff['work_cell_phone'] ?? ''); ?></td></tr>
                <tr><th>Start Date</th><td><?php echo htmlspecialchars($staff['start_date'] ?? ''); ?></td></tr>
                <tr><th>Salary</th><td><?php echo htmlspecialchars($staff['salary'] ?? ''); ?></td></tr>
                <tr><th>Department</th><td><?php echo htmlspecialchars($staff['depmt_name'] ?? ''); ?></td></tr>
                <tr><th>Status</th><td><?php echo ($staff['staff_status'] == '1') ? 'Employed' : 'Not Employed'; ?></td></tr>
                <tr><th>Emergency Contact Name</th><td><?php echo htmlspecialchars($staff['emergency_full_name'] ?? ''); ?></td></tr>
                <tr><th>Emergency Contact Address</th><td><?php echo htmlspecialchars($staff['emergency_address'] ?? ''); ?></td></tr>
                <tr><th>Emergency Primary Phone</th><td><?php echo htmlspecialchars($staff['emergency_primary_phone'] ?? ''); ?></td></tr>
                <tr><th>Emergency Cell Phone</th><td><?php echo htmlspecialchars($staff['emergency_cell_phone'] ?? ''); ?></td></tr>
                <tr><th>Emergency Relationship</th><td><?php echo htmlspecialchars($staff['emergency_relationship'] ?? ''); ?></td></tr>
                <tr><th>Created Date</th><td><?php echo htmlspecialchars($staff['created_at'] ?? ''); ?></td></tr>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <div class="alert alert-warning">Staff record not found.</div>
    <?php endif; ?>
</div>