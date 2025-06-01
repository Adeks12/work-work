<div class="container-fluid p-0">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Department Management</h5>
            <h6 class="card-subtitle text-muted">The report contains Departments that have been setup in the system.
            </h6>
        </div>
        <div class="card-body">
            <a class="btn btn-warning mb-3" onclick="loadModal('setup/department_setup.php','modal_div')"
                href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">
                <i class="fas fa-plus"></i> Create Department
            </a>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Departments List</h5>
                            <h6 class="card-subtitle text-muted">Manage your organization's departments</h6>
                        </div>
                        <div class="card-body">
                            <table id="datatables-departments" class="table table-striped w-100">
                                <thead>
                                    <tr>
                                        <th>ID</th>
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

<script>
var table;
var editor;
var op = "Department.departmentList"; // Fixed: Changed from departmentlist to departmentList (case sensitive)

$(document).ready(function() {
    table = $("#datatables-departments").DataTable({
        "sDom": '<"top"i>rt<"bottom"flp><"clear">',
        processing: true,
        columnDefs: [{
                orderable: false,
                targets: 0
            },
            {
                width: "100px",
                targets: 6
            }
        ],
        serverSide: true,
        paging: true,
        oLanguage: {
            sEmptyTable: "No record was found, please try another query"
        },
        ajax: {
            url: "utilities.php",
            type: "POST",
            data: function(d, l) {
                d.op = op;
                d.li = Math.random();
            }
        }
    });
});

function editDepartment(departmentId) {
    loadModal('setup/department_setup.php?op=edit&depmt_id=' + departmentId, 'modal_div'); // Fixed: Changed department_id to depmt_id
    $('#defaultModalPrimary').modal('show');
}

function deleteDepartment(departmentId) {
    if (confirm('Are you sure you want to delete this department? This action cannot be undone.')) {
        $.post('utilities.php', {
            op: 'Department.deleteDepartment',
            depmt_id: departmentId // Fixed: Changed department_id to depmt_id to match backend
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

// Global function to refresh table after modal operations
window.refreshDepartmentList = refreshDepartmentList;
</script>