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
    $(document).ready(function () {
        // Initialize DataTable
        $('#datatables-departments').DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: 'utilities.php',
                type: 'POST',
                data: {
                    'op': 'Department.departmentList'
                }
            },
            columns: [{
                    data: 0
                }, // ID
                {
                    data: 1
                }, // Department Name
                {
                    data: 2
                }, // Department Code  
                {
                    data: 3
                }, // Department Head
                {
                    data: 4
                }, // Status
                {
                    data: 5
                }, // Created Date
                {
                    data: 6, // Actions
                    orderable: false,
                    searchable: false
                }
            ],
            order: [
                [0, 'desc']
            ],
            pageLength: 25,
            responsive: true,
            language: {
                processing: "Loading departments...",
                emptyTable: "No departments found",
                zeroRecords: "No matching departments found"
            }
        });
    });

    function editDepartment(departmentId) {
        loadModal('setup/department_setup.php?op=edit&department_id=' + departmentId, 'modal_div');
        $('#defaultModalPrimary').modal('show');
    }

    function deleteDepartment(departmentId) {
        if (confirm('Are you sure you want to delete this department? This action cannot be undone.')) {
            $.post('utilities.php', {
                op: 'Department.deleteDepartment',
                department_id: departmentId
            }, function (response) {
                if (response.response_code == 0) {
                    alert('Department deleted successfully');
                    $('#datatables-departments').DataTable().ajax.reload();
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
</script>