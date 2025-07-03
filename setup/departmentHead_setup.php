<?php
include_once("../libs/dbfunctions.php");
$dbobject = new dbobject();

$user = $_SESSION['username_sess'];
$sql = ("SELECT merchant_id FROM userdata WHERE username = '$user' LIMIT 1");
$doquery= $dbobject->db_query($sql, true);
$merchant_id = $doquery[0]['merchant_id'];

// Fetch staff list
$staff_sql = "SELECT staff_id, full_name FROM staff WHERE merchant_id='$merchant_id' AND status='1' ";
$staff_list = $dbobject->db_query($staff_sql, true);

// Fetch department list
$dept_sql = "SELECT depmt_id, depmt_name FROM department WHERE merchant_id='$merchant_id' AND depmt_head = ''";
$dept_list = $dbobject->db_query($dept_sql, true);
?>

<style>
    #login_days>label {
        margin-right: 10px;
    }

    .asterik {
        color: red;
    }

    .form-group {
        margin-bottom: 1rem;
    }
</style>
<div class="modal-header">
    <h4 class="modal-title" style="font-weight:bold"><?php echo ($operation=="edit")?"Edit ":""; ?>Department Setup
        <div><small style="font-size:12px">All asterik fields are compulsory</small></div>
    </h4>
    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3 ">
    <form id="form1" onsubmit="return false" autocomplete="off">
        <input type="hidden" name="op" value="Department.assignDepartmentHead">
        <input type="hidden" name="operation" value="<?php echo $operation; ?>">
        <input type="hidden" name="merchant_id" id="merchant_id" value="<?php echo $merchant_id; ?>">
        <?php if($operation == "edit"): ?>
        <input type="hidden" name="depmt_id" value="<?php echo $department_id; ?>">
        <?php endif; ?>
        
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="form-label">Select Staff<span class="asterik">*</span></label>
                    <select name="staff_id" id="staff_id" class="form-select" required>
                        <option value="">:: SELECT STAFF ::</option>
                        <?php foreach($staff_list as $staff): ?>
                            <option value="<?php echo $staff['staff_id']; ?>">
                                <?php echo htmlspecialchars($staff['full_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="form-label">Select Department<span class="asterik">*</span></label>
                    <select name="depmt_id" id="depmt_id" class="form-select" required>
                        <option value="">:: SELECT DEPARTMENT ::</option>
                        <?php foreach($dept_list as $dept): ?>
                            <option value="<?php echo $dept['depmt_id']; ?>">
                                <?php echo htmlspecialchars($dept['depmt_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div id="server_mssg"></div>
            </div>
        </div>
        <button type="button" id="save_facility" class="btn btn-primary" onclick="saveRecord()">
            Assign Department Head
        </button>
    </form>
    
</div>

<script>
    $(document).ready(function () {
        // Debug: Log form data on page load
        console.log('Operation: <?php echo $operation; ?>');
        console.log('Department Name Value: ' + $('#depmt_name').val());
        console.log('Department Head Value: ' + $('#depmt_head').val());

        // Form validation styling
        $('#form1 input, #form1 select, #form1 textarea').on('blur change', function () {
            if ($(this).prop('required') && !$(this).val()) {
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
    });

    function showMessage(message, type) {
        $("#server_mssg").html('<div class="alert alert-' + (type === 'success' ? 'success' : 'danger') +
            ' alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
            message + '</div>');

        // Auto-hide success messages
        if (type === 'success') {
            setTimeout(function () {
                $("#server_mssg").html('');
            }, 3000);
        }
    }

    // Save record function
    function saveRecord() {
        // Get form values
        var staff_id = $('#staff_id').val();
        var depmt_id = $('#depmt_id').val();

        // Simple validation
        if (!staff_id || !depmt_id) {
            showMessage("Please select both staff and department.", "error");
            return;
        }

        // Disable button and show loading
        $("#save_facility").html('<i class="fas fa-spinner fa-spin"></i> Processing...');
        $("#save_facility").prop('disabled', true);

        // Send AJAX request
        $.ajax({
            url: "utilities.php",
            type: "POST",
            data: {
                op: "Department.assignDepartmentHead",
                staff_id: staff_id,
                depmt_id: depmt_id
            },
            dataType: "json",
            success: function (re) {
                $("#save_facility").html("Assign Department Head");
                $("#save_facility").prop('disabled', false);

                if (re.response_code == 0) {
                    showMessage(re.response_message, "success");
                    setTimeout(function () {
                        $('#defaultModalPrimary').modal('hide');
                        if (typeof refreshDepartmentList === 'function') {
                            refreshDepartmentList();
                        }
                    }, 1200);
                } else {
                    showMessage(re.response_message, "error");
                }
            },
            error: function () {
                $("#save_facility").html("Assign Department Head");
                $("#save_facility").prop('disabled', false);
                showMessage("An error occurred. Please try again.", "error");
            }
        });
    }
</script>