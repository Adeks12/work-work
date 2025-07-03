<?php
include_once("../libs/dbfunctions.php");
$dbobject = new dbobject();


$user = $_SESSION['username_sess'];
$sql = ("SELECT merchant_id FROM userdata WHERE username = '$user' LIMIT 1");
$doquery= $dbobject->db_query($sql, true);
$merchant_id = $doquery[0]['merchant_id'];
$sql1 = ("SELECT staff_id, full_name FROM staff WHERE depmt_head = '1' AND merchant_id = '$merchant_id' ORDER BY full_name ASC");
$departmentHead = $dbobject->db_query($sql1, true);

if(isset($_REQUEST['op']) && $_REQUEST['op'] == 'edit')
{
    $staff_id = $_REQUEST['staff_id'] ?? '';
    
    if(empty($staff_id)) {
        echo "<script>console.log('Error: staff ID is missing');</script>";
        $staff = null;
        $operation = 'new';
    } else {
        $sql = "SELECT * FROM staff WHERE staff_id='$staff_id' AND merchant_id='$merchant_id' LIMIT 1";
        $staff_result = $dbobject->db_query($sql, true);
        
        echo "<script>console.log('Staff ID: $staff_id');</script>";
        echo "<script>console.log('SQL Query: $sql');</script>";
        echo "<script>console.log('Query Result: " . json_encode($staff_result) . "');</script>";
        
        if($staff_result && is_array($staff_result) && count($staff_result) > 0) {
            $staff = $staff_result[0];
            $operation = 'edit';
            echo "<script>console.log('Staff Data: " . json_encode($staff) . "');</script>";
        } else {
            echo "<script>console.log('No staff found with ID: $staff_id');</script>";
            $staff = null;
            $operation = 'new';
        }
    }
}
else
{
    $operation = 'new';
    $staff = null;
}
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
    <h4 class="modal-title" style="font-weight:bold"><?php echo ($operation=="edit")?"Edit ":""; ?>Staff Setup
        <div><small style="font-size:12px">All asterik fields are compulsory</small></div>
    </h4>
    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body m-3">
    <form id="form1" onsubmit="return false" autocomplete="off">
        <input type="hidden" name="op" value="staff.createStaff">
        <input type="hidden" name="operation" value="<?php echo $operation; ?>">
        <input type="hidden" name="merchant_id" id="merchant_id" value="<?php echo $merchant_id; ?>">
        <?php if($operation == "edit"): ?>
        <input type="hidden" name="staff_id" value="<?php echo $staff_id; ?>">
        <?php endif; ?>

        <!-- Progress Bar -->
        <div class="progress mb-4" style="height: 20px;">
            <div class="progress-bar" id="formProgressBar" role="progressbar" style="width: 33%;" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100">Step 1 of 3</div>
        </div>

        <!-- Step 1: Personal Information -->
        <div class="form-step" id="step-1">
            <h5>Personal Information</h5>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="form-label">Full Name<span class="asterik">*</span></label>
                        <input type="text" name="full_name" id="full_name" class="form-control"
                            value="<?php echo ($operation == "edit" && $staff && isset($staff['full_name'])) ? htmlspecialchars($staff['full_name']) : ""; ?>"
                            placeholder="Enter full name" required>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="form-label">Address<span class="asterik">*</span></label>
                        <input type="text" name="address" id="address" class="form-control"
                            value="<?php echo ($operation == "edit" && $staff && isset($staff['address'])) ? htmlspecialchars($staff['address']) : ""; ?>"
                            placeholder="Enter address" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="form-label">Phone Number </label>
                        <input type="text" name="cell_phone" id="cell_phone" class="form-control"
                            value="<?php echo ($operation == "edit" && $staff && isset($staff['cell_phone'])) ? htmlspecialchars($staff['cell_phone']) : ""; ?>"
                            placeholder="(         )">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="form-label">Phone Number (optional)</label>
                        <input type="text" name="home_phone" id="home_phone" class="form-control"
                            value="<?php echo ($operation == "edit" && $staff && isset($staff['home_phone'])) ? htmlspecialchars($staff['home_phone']) : ""; ?>"
                            placeholder="(         )">
                    </div>
                </div>
                
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="personal_email" id="personal_email" class="form-control"
                            value="<?php echo ($operation == "edit" && $staff && isset($staff['personal_email'])) ? htmlspecialchars($staff['personal_email']) : ""; ?>"
                            placeholder="Enter personal email">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="form-label">State Means of ID & Number</label>
                        <input type="text" name="state_id_number" id="state_id_number" class="form-control"
                            value="<?php echo ($operation == "edit" && $staff && isset($staff['state_id_number'])) ? htmlspecialchars($staff['state_id_number']) : ""; ?>"
                            placeholder="e.g. NIN/Passport/Driver’s license number">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="form-label">Birth Date</label>
                        <input type="date" name="birth_date" id="birth_date" class="form-control"
                            value="<?php echo ($operation == "edit" && $staff && isset($staff['birth_date'])) ? htmlspecialchars($staff['birth_date']) : ""; ?>">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="form-label">Marital Status</label>
                        <select name="marital_status" id="marital_status" class="form-select">
                            <option value="">:: SELECT ::</option>
                            <option value="Single" <?php echo ($operation == "edit" && $staff && isset($staff['marital_status']) && $staff['marital_status'] == 'Single') ? 'selected' : ''; ?>>Single</option>
                            <option value="Married" <?php echo ($operation == "edit" && $staff && isset($staff['marital_status']) && $staff['marital_status'] == 'Married') ? 'selected' : ''; ?>>Married</option>
                            <option value="Divorced" <?php echo ($operation == "edit" && $staff && isset($staff['marital_status']) && $staff['marital_status'] == 'Divorced') ? 'selected' : ''; ?>>Divorced</option>
                            <option value="Widowed" <?php echo ($operation == "edit" && $staff && isset($staff['marital_status']) && $staff['marital_status'] == 'Widowed') ? 'selected' : ''; ?>>Widowed</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="form-label">Spouse’s Name</label>
                        <input type="text" name="spouse_name" id="spouse_name" class="form-control"
                            value="<?php echo ($operation == "edit" && $staff && isset($staff['spouse_name'])) ? htmlspecialchars($staff['spouse_name']) : ""; ?>"
                            placeholder="Enter spouse's name">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="form-label">Spouse’s Employer</label>
                        <input type="text" name="spouse_employer" id="spouse_employer" class="form-control"
                            value="<?php echo ($operation == "edit" && $staff && isset($staff['spouse_employer'])) ? htmlspecialchars($staff['spouse_employer']) : ""; ?>"
                            placeholder="Enter spouse's employer">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="form-label">Spouse’s Cell Phone</label>
                        <input type="text" name="spouse_cell_phone" id="spouse_cell_phone" class="form-control"
                            value="<?php echo ($operation == "edit" && $staff && isset($staff['spouse_cell_phone'])) ? htmlspecialchars($staff['spouse_cell_phone']) : ""; ?>"
                            placeholder="(         )">
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <button type="button" class="btn btn-primary" id="nextBtn1">Next</button>
            </div>
        </div>

        <!-- Step 2: Job Information -->
        <div class="form-step d-none" id="step-2">
            <h5>Job Information</h5>
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="form-label">Title</label>
                        <input type="text" name="job_title" id="job_title" class="form-control"
                            value="<?php echo ($operation == "edit" && $staff && isset($staff['job_title'])) ? htmlspecialchars($staff['job_title']) : ""; ?>"
                            placeholder="Enter job title">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="form-label">Head of Dept</label>
                        <select name="head_of_dept" id="head_of_dept" class="form-select">
                            <option value="">:: SELECT HEAD OF DEPARTMENT ::</option>
                            <?php
                            foreach($departmentHead as $head) {
                                $selected = ($operation == "edit" && $staff && isset($staff['depmt_head']) && $staff['depmt_head'] == $head['staff_id']) ? 'selected' : '';
                                echo "<option value='{$head['staff_id']}' $selected>{$head['full_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="form-label">Work Location</label>
                        <input type="text" name="work_location" id="work_location" class="form-control"
                            value="<?php echo ($operation == "edit" && $staff && isset($staff['work_location'])) ? htmlspecialchars($staff['work_location']) : ""; ?>"
                            placeholder="Enter work location">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="form-label">Work E-mail Address</label>
                        <input type="email" name="work_email" id="work_email" class="form-control"
                            value="<?php echo ($operation == "edit" && $staff && isset($staff['work_email'])) ? htmlspecialchars($staff['work_email']) : ""; ?>"
                            placeholder="Enter work email">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="form-label">Work Phone</label>
                        <input type="text" name="work_phone" id="work_phone" class="form-control"
                            value="<?php echo ($operation == "edit" && $staff && isset($staff['work_phone'])) ? htmlspecialchars($staff['work_phone']) : ""; ?>"
                            placeholder="(         )">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="form-label">Work Cell Phone</label>
                        <input type="text" name="work_cell_phone" id="work_cell_phone" class="form-control"
                            value="<?php echo ($operation == "edit" && $staff && isset($staff['work_cell_phone'])) ? htmlspecialchars($staff['work_cell_phone']) : ""; ?>"
                            placeholder="(         )">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control"
                            value="<?php echo ($operation == "edit" && $staff && isset($staff['start_date'])) ? htmlspecialchars($staff['start_date']) : ""; ?>">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="form-label">Salary</label>
                        <input type="text" name="salary" id="salary" class="form-control"
                            value="<?php echo ($operation == "edit" && $staff && isset($staff['salary'])) ? htmlspecialchars($staff['salary']) : ""; ?>"
                            placeholder="Enter salary">
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <button type="button" class="btn btn-secondary" id="prevBtn2">Previous</button>
                <button type="button" class="btn btn-primary" id="nextBtn2">Next</button>
            </div>
        </div>

        <!-- Step 3: Emergency Contact -->
        <div class="form-step d-none" id="step-3">
            <h5>Emergency Contact Information</h5>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="form-label">Emergency Contact Full Name</label>
                        <input type="text" name="emergency_full_name" id="emergency_full_name" class="form-control"
                            value="<?php echo ($operation == "edit" && $staff && isset($staff['emergency_full_name'])) ? htmlspecialchars($staff['emergency_full_name']) : ""; ?>"
                            placeholder="Enter emergency contact full name">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="form-label">Emergency Contact Address</label>
                        <input type="text" name="emergency_address" id="emergency_address" class="form-control"
                            value="<?php echo ($operation == "edit" && $staff && isset($staff['emergency_address'])) ? htmlspecialchars($staff['emergency_address']) : ""; ?>"
                            placeholder="Enter emergency contact address">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="form-label">Primary Phone</label>
                        <input type="text" name="emergency_primary_phone" id="emergency_primary_phone" class="form-control"
                            value="<?php echo ($operation == "edit" && $staff && isset($staff['emergency_primary_phone'])) ? htmlspecialchars($staff['emergency_primary_phone']) : ""; ?>"
                            placeholder="(         )">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="form-label">Emergency Cell Phone</label>
                        <input type="text" name="emergency_cell_phone" id="emergency_cell_phone" class="form-control"
                            value="<?php echo ($operation == "edit" && $staff && isset($staff['emergency_cell_phone'])) ? htmlspecialchars($staff['emergency_cell_phone']) : ""; ?>"
                            placeholder="(         )">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="form-label">Relationship</label>
                        <input type="text" name="emergency_relationship" id="emergency_relationship" class="form-control"
                            value="<?php echo ($operation == "edit" && $staff && isset($staff['emergency_relationship'])) ? htmlspecialchars($staff['emergency_relationship']) : ""; ?>"
                            placeholder="Enter relationship">
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <button type="button" class="btn btn-secondary" id="prevBtn3">Previous</button>
                <button type="button" id="save_facility" class="btn btn-success" onclick="saveRecord()">
                    <?php echo ($operation == "edit") ? "Update Staff" : "Create Staff"; ?>
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div id="server_mssg"></div>
            </div>
        </div>
        <?php include("form-footer.php"); ?>
    </form>
</div>

<script>
    $(document).ready(function () {
        // Debug: Log form data on page load
        console.log('Operation: <?php echo $operation; ?>');
        console.log('Staff First Name Value: ' + $('#staff_first_name').val());
        console.log('Staff Last Name Value: ' + $('#staff_last_name').val());

        // Form validation styling
        $('#form1 input, #form1 select, #form1 textarea').on('blur change', function () {
            if ($(this).prop('required') && !$(this).val()) {
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        // Navigation between form steps
        $('#nextBtn1').click(function () {
            // Validate step 1
            var valid = true;
            $('#step-1 [required]').each(function () {
                if (!$(this).val().trim()) {
                    $(this).addClass('is-invalid');
                    valid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            if (!valid) {
                showMessage("Please fill all required fields in Step 1.", "error");
                return;
            }

            // Show step 2
            $('#step-1').addClass('d-none');
            $('#step-2').removeClass('d-none');
            updateProgressBar(66, "Step 2 of 3");
        });

        $('#prevBtn2').click(function () {
            // Show step 1
            $('#step-2').addClass('d-none');
            $('#step-1').removeClass('d-none');
            updateProgressBar(33, "Step 1 of 3");
        });

        $('#nextBtn2').click(function () {
            // Validate step 2
            var valid = true;
            $('#step-2 [required]').each(function () {
                if (!$(this).val().trim()) {
                    $(this).addClass('is-invalid');
                    valid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            if (!valid) {
                showMessage("Please fill all required fields in Step 2.", "error");
                return;
            }

            // Show step 3
            $('#step-2').addClass('d-none');
            $('#step-3').removeClass('d-none');
            updateProgressBar(100, "Step 3 of 3");
        });

        $('#prevBtn3').click(function () {
            // Show step 2
            $('#step-3').addClass('d-none');
            $('#step-2').removeClass('d-none');
            updateProgressBar(66, "Step 2 of 3");
        });
    });

    function updateProgressBar(percentage, stepText) {
        var progressBar = $('#formProgressBar');
        progressBar.css('width', percentage + '%').attr('aria-valuenow', percentage);
        progressBar.text(stepText);
    }

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
        // Client-side validation
        var valid = true;
        var firstInvalidField = null;

        $('#form1 [required]').each(function () {
            if (!$(this).val().trim()) {
                $(this).addClass('is-invalid');
                if (!firstInvalidField) {
                    firstInvalidField = $(this);
                }
                valid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!valid) {
            showMessage("Please fill all required fields.", "error");
            if (firstInvalidField) {
                firstInvalidField.focus();
            }
            return;
        }

       
        // Additional validation for full name
        var fullName = $("#full_name").val().trim();
        if (fullName.length < 2) {
            showMessage("Full name must be at least 2 characters long.", "error");
            $("#full_name").focus();
            return;
        }

        $("#save_facility").html('<i class="fas fa-spinner fa-spin"></i> Processing...');
        $("#save_facility").prop('disabled', true);

        var dd = $("#form1").serialize();

        $.post("utilities.php", dd, function (re) {
            console.log(re);
            $("#save_facility").html("<?php echo ($operation == 'edit') ? 'Update Staff' : 'Create Staff'; ?>");
            $("#save_facility").prop('disabled', false);

            if (re.response_code == 0) {
                showMessage(re.response_message, "success");

                // Refresh the table after successful operation
                if (typeof refreshStaffList === 'function') {
                    refreshStaffList();
                } else if (typeof getpage === 'function') {
                    getpage('staff_list.php', 'page');
                }

                // Clear form for new entries
                if ("<?php echo $operation; ?>" === "new") {
                    $("#form1")[0].reset();
                    $("#staff_status").val('1'); // Reset to Active
                }

                setTimeout(function () {
                    $('#defaultModalPrimary').modal('hide');
                }, 1500);

            } else {
                showMessage(re.response_message, "error");
            }
        }, 'json').fail(function (xhr, status, error) {
            console.log("Ajax Error:", xhr.responseText);
            $("#save_facility").html("<?php echo ($operation == 'edit') ? 'Update Staff' : 'Create Staff'; ?>");
            $("#save_facility").prop('disabled', false);
            showMessage("An error occurred while processing your request. Please try again.", "error");
        });
    }
</script>