<div class="container-fluid p-0">
    <div class="card">
        <div class="card-header">
            <h5 class="text-danger">Staff Management</h5>
            <h6 class="card-subtitle text-muted">The report contains Staff that have been setup in the system.</h6>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <a class="btn btn-outline-warning mb-3" onclick="loadModal('setup/staff_setup.php','modal_div')"
                href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">
                 Create Staff
            </a>
            

            <div class="row">
                <div class="col-12">
                    
                        <div class="card-body">
                            
                            <div class="col-sm-12 table-responsive">
                                <table id="datatables-staffs" class="table table-striped table-bordered" style= "width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>First Name</th>
                                            <th>Staff Code</th>
                                            <th>Personal Email</th>
                                            <th>Phone Number</th>
                                            <th>Job Title</th>
                                            <th>Start Date</th>
                                            <th>Status</th>
                                            <th>Created Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                   
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var table;
    var editor;
    var op = "staff.staffList"; // Operation for DataTable

    $(document).ready(function () {
        table = $("#datatables-staffs").DataTable({
           processing: true,
            columnDefs: [{
                    orderable: false,
                    targets: 0
            }],           
            serverSide: true,
            paging: true,
            oLanguage: {
                sEmptyTable: "No record was found, please try another query",
                sProcessing: "Loading staffs..."
            },
            ajax: {
                url: "utilities.php",
                type: "POST",
                data: function (d, l) {
                    d.op = op;
                    d.li = Math.random();
                     // Add search parameter if exists
                    if ($("#searchInput").val()) {
                        d.search = {
                            value: $("#searchInput").val()
                        };
                    }
                },
                 error: function (xhr, error, thrown) {
                    console.log("DataTable Error:", error, thrown);
                    alert('An error occurred while loading the staff data. Please try again later.');
                }
                
            }
            
        });
    });

    function editstaff(staffId) {
        loadModal('setup/staff_setup.php?op=edit&staff_id=' + staffId, 'modal_div');
        $('#defaultModalPrimary').modal('show');
    }

    function deletestaff(staffId) {
        if (confirm('Are you sure you want to delete this staff? This action cannot be undone.')) {
            $.post('utilities.php', {
                op: 'staff.deleteStaff', // Fixed method name to match class
                staff_id: staffId
            }, function (response) {
                if (response.response_code == 0) {
                    alert('Staff deleted successfully');
                    refreshStaffList();
                } else {
                    alert('Error: ' + response.response_message);
                }
            }, 'json').fail(function () {
                alert('An error occurred while deleting the staff');
            });
        }
    }

    function sackstaff(staffId) {
        if (confirm('Are you sure you want to terminate this staff\'s contract? This will mark them as no longer employed.')) {
            $.post('utilities.php', {
                op: 'staff.sackStaff',
                staff_id: staffId
            }, function (response) {
                if (response.response_code == 0) {
                    alert('Staff contract terminated.');
                    refreshStaffList();
                } else {
                    alert('Error: ' + response.response_message);
                }
            }, 'json').fail(function () {
                alert('An error occurred while terminating the staff.');
            });
        }
    }

    function refreshStaffList() {
        $('#datatables-staffs').DataTable().ajax.reload();
    }

    function viewstaff(staffId) {
        loadModal('view/staff_view.php?staff_id=' + staffId, 'modal_div');
        $('#defaultModalPrimary').modal('show');
    }

    // Global function to refresh table after modal operations
    window.refreshStaffList = refreshStaffList;
</script>