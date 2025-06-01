<?php
include_once("../libs/dbfunctions.php");
$dbobject = new dbobject();

$user = $_SESSION['username_sess'];
$sql = ("SELECT merchant_id FROM userdata WHERE username = '$user' LIMIT 1");
$doquery= $dbobject->db_query($sql, true);
$merchant_id = $doquery[0]['merchant_id'];

if(isset($_REQUEST['op']) && $_REQUEST['op'] == 'edit')
{
    $department_id = $_REQUEST['depmt_id'] ?? $_REQUEST['department_id']; // Fixed: Added fallback
    // Fixed: Changed table name from 'departments' to 'department'
    $dept_result = $dbobject->db_query("SELECT * FROM department WHERE depmt_id='$department_id' AND merchant_id='$merchant_id' LIMIT 1", true);
    $dept = $dept_result ? $dept_result[0] : null; // Fixed: Handle null result
    $operation = 'edit';
}
else
{
    $operation = 'new';
    $dept = null; // Fixed: Initialize dept for new operations
}
?>

<style>
    #login_days>label {
        margin-right: 10px;
    }
    .asterik {
        color: red;
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
        <input type="hidden" name="op" value="Department.createDepartment">
        <input type="hidden" name="operation" value="<?php echo $operation; ?>">
        <input type="hidden" name="merchant_id" id="merchant_id" value="<?php echo $merchant_id; ?>">
        <?php if($operation == "edit"): ?>
        <input type="hidden" name="depmt_id" value="<?php echo $department_id; ?>">
        <?php endif; ?>

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="form-label">Department Name<span class="asterik">*</span></label>
                    <input type="text" name="depmt_name" class="form-control"
                        value="<?php echo ($operation == "edit" && $dept) ? htmlspecialchars($dept['depmt_name']) : ""; ?>"
                        placeholder="Enter department name" autocomplete="off" required>
                    <div class="invalid-feedback">Please enter the department name.</div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="form-label">Department Code<span class="asterik">*</span></label>
                    <input type="text" name="depmt_code" class="form-control"
                        value="<?php echo ($operation == "edit" && $dept) ? htmlspecialchars($dept['depmt_code']) : ""; ?>"
                        placeholder="Enter department code (e.g., DEPT001)" autocomplete="off" required>
                    <div class="invalid-feedback">Please enter a valid department code.</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="form-label">Department Head<span class="asterik">*</span></label>
                    <input type="text" name="depmt_head"
                        value="<?php echo ($operation == "edit" && $dept) ? htmlspecialchars($dept['depmt_head']) : "" ?>"
                        class="form-control" placeholder="Enter department head name" autocomplete="off" required>
                    <div class="invalid-feedback">Please enter the department head name.</div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="form-label" style="display:block !important">Department Status<span
                            class="asterik">*</span></label>
                    <div class="input-group">
                        <select class="form-select" name="depmt_status" id="depmt_status" required>
                            <option value="">:: SELECT STATUS ::</option>
                            <option value="1"
                                <?php echo ($operation == "edit" && $dept && $dept['depmt_status'] == '1') ? 'selected' : ''; ?>>
                                Active</option>
                            <option value="0"
                                <?php echo ($operation == "edit" && $dept && $dept['depmt_status'] == '0') ? 'selected' : ''; ?>>
                                Inactive</option>
                        </select>
                        <div class="invalid-feedback">Please select the department status.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label class="form-label">Department Description</label>
                    <textarea name="depmt_description" class="form-control" rows="3"
                        placeholder="Enter department description (optional)"><?php echo ($operation == "edit" && $dept) ? htmlspecialchars($dept['depmt_description'] ?? '') : ""; ?></textarea>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div id="server_mssg"></div>
            </div>
        </div>

        <?php include("form-footer.php"); ?>
        <button type="button" id="save_facility" class="btn btn-primary" onclick="saveRecord()">
            <?php echo ($operation == "edit") ? "Update Department" : "Create Department"; ?>
        </button>
    </form>
</div>

<script>
$(document).ready(function() {
    // Auto-generate department code based on department name
    $("input[name='depmt_name']").on('blur', function () {
        var deptName = $(this).val().trim();
        var deptCode = $("input[name='depmt_code']").val().trim();
        if (deptName && !deptCode && "<?php echo $operation; ?>" === "new") {
            var code = deptName.substring(0, 4).toUpperCase() + Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            $("input[name='depmt_code']").val(code);
        }
    });

    // Validate department code format
    $("input[name='depmt_code']").on('blur', function () {
        var code = $(this).val().trim();
        if (code && !/^[A-Z0-9]{3,10}$/.test(code)) {
            showMessage("Department code should be 3-10 characters, uppercase letters and numbers only", "error");
            $(this).addClass('is-invalid');
        } else {
            $("#server_mssg").text("");
            $(this).removeClass('is-invalid');
        }
    });
});

function showMessage(message, type) {
    $("#server_mssg").text(message);
    if (type === "success") {
        $("#server_mssg").css({'color':'green','font-weight':'bold'});
    } else {
        $("#server_mssg").css({'color':'red','font-weight':'bold'});
    }
}

// Save record function
function saveRecord() {
    // Client-side validation
    var valid = true;
    $('#form1 [required]').each(function() {
        if (!$(this).val()) {
            $(this).addClass('is-invalid');
            valid = false;
        } else {
            $(this).removeClass('is-invalid');
        }
    });
    if (!valid) {
        showMessage("Please fill all required fields.", "error");
        return;
    }

    $("#save_facility").text("Loading......");
    var dd = $("#form1").serialize();
    $.post("utilities.php", dd, function(re) {
        console.log(re);
        $("#save_facility").text("<?php echo ($operation == "edit") ? "Update Department" : "Create Department"; ?>");
        if (re.response_code == 0) {
            showMessage(re.response_message, "success");
            // Fixed: Refresh the table after successful operation
            if (typeof refreshDepartmentList === 'function') {
                refreshDepartmentList();
            } else if (typeof getpage === 'function') {
                getpage('user_list.php','page');
            }
            setTimeout(()=>{
                $('#defaultModalPrimary').modal('hide');
            },1000)
        } else {
            showMessage(re.response_message, "error");
        }
    },'json').fail(function() {
        $("#save_facility").text("<?php echo ($operation == "edit") ? "Update Department" : "Create Department"; ?>");
        showMessage("An error occurred while processing your request", "error");
    });
}
</script>