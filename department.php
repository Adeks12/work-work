<div class="container-fluid p-0">
    <div class="card">
        <div class="card-header">
            <h5 class="text-danger">Department Management</h5>
            <h6 class="card-subtitle text-muted">The report contains Departments that have been setup in the system.
            </h6>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <a class="btn btn-outline-warning mb-3" onclick="loadModal('setup/department_setup.php','modal_div')"
                href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">
                 Create Department
            </a>

            <a class="btn btn-outline-warning mb-3" onclick="loadModal('setup/departmentHead_setup.php','modal_div')"
                href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">
                 Assign Department Head
            </a>

                <div class="row">
                     <div class="col-12">
                        
               
                            <div class="card-body">
                                <div style="col-sm-12 table-responsive">
                                    <table id="datatables-departments" class="table table-striped table-bordered w-100">
                                        <thead>
                                            <tr>
                                                <th>S/N</th>
                                                <th>Department Name</th>
                                                <th>Department Code</th>
                                                <th>Department Head</th>
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
    </div>
</div>

<script>
    var table;
    var editor;
    var op = "Department.departmentList";

    $(document).ready(function () {
        table = $("#datatables-departments").DataTable({
           processing: true,
            columnDefs: [{
                    orderable: false,
                    targets: 0
                },
               
            ],
            serverSide: true,
            paging: true,
            oLanguage: {
                sEmptyTable: "No record was found, please try another query",
                sProcessing: "Loading departments..."
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
                }
            },
           
        });

        // Search functionality
        $("#searchBtn").click(function () {
            table.ajax.reload();
        });

        $("#clearBtn").click(function () {
            $("#searchInput").val('');
            table.ajax.reload();
        });

        $("#searchInput").keypress(function (e) {
            if (e.which == 13) { // Enter key
                table.ajax.reload();
            }
        });
    });

    function editDepartment(departmentId) {
        loadModal('setup/department_setup.php?op=edit&depmt_id=' + departmentId, 'modal_div');
        $('#defaultModalPrimary').modal('show');
    }

    function deleteDepartment(departmentId) {
        if (confirm('Are you sure you want to delete this department? This action cannot be undone.')) {
            $.post('utilities.php', {
                op: 'Department.deleteDepartment',
                depmt_id: departmentId
            }, function (response) {
                if (response.response_code == 0) {
                    alert('Department deleted successfully');
                    refreshDepartmentList();
                } else {
                    alert('Error: ' + response.response_message);
                }
            }, 'json').fail(function () {
                alert('An error occurred while deleting the department');
            });
        }
    }

    function refreshDepartmentList() {
        $('#datatables-departments').DataTable().ajax.reload();
    }

    function removeDepartmentHead(depmtId, depmtHead) {
        if (confirm('Are you sure you want to remove the department head?')) {
            $.post('utilities.php', {
                op: 'Department.removeDepartmentHead',
                depmt_id: depmtId,
                depmt_head: depmtHead
            }, function (re) {
                if (re.response_code == 0) {
                    alert('Department head removed successfully.');
                    refreshDepartmentList();
                } else {
                    alert('Failed to remove department head: ' + re.response_message);
                }
            }, 'json').fail(function () {
                alert('An error occurred while removing the department head.');
            });
        }
    }

    // Global function to refresh table after modal operations
    window.refreshDepartmentList = refreshDepartmentList;
</script>