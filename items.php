<div class="container-fluid p-0">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Item Management</h5>
            <h6 class="card-subtitle text-muted">The report contains Items that have been setup in the system.
            </h6>
        </div>
        <div class="card-body">
            <a class="btn btn-outline-warning mb-3" onclick="loadModal('setup/item_cat_setup.php','modal_div')"
                href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">
               Create Item 
            </a>

            <div class="mb-3">
                <label for="parentCatFilter" class="form-label">Filter by Allocation Status:</label>
                <select id="parentCatFilter" class="form-select" style="width:auto; display:inline-block;">
                    <option value="all">Filter by Allocation Status</option>
                    <!-- Main categories will be loaded here by JS -->
                </select>
           
                <label for="parentCatFilter" class="form-label">Filter by Category/ Sub category:</label>
                <select id="parentCatFilter" class="form-select" style="width:auto; display:inline-block;">
                    <option value="all">Filter by Category</option>
                    <!-- Main categories will be loaded here by JS -->
                </select>
            </div>


            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Item Category List</h5>
                            <h6 class="card-subtitle text-muted">Manage your organization's item categories</h6>
                        </div>
                        <div class="card-body">
                            <!-- Controls Row: Search & Pagination above table -->
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-6">
                                    <div id="itemCatsTable_filter" class="dataTables_filter"></div>
                                </div>
                                <div class="col-md-6 text-end">
                                    <div id="itemCatsTable_length" class="dataTables_length"></div>
                                </div>
                            </div>
                            <table id="datatables-item-cats" class="table table-striped table-bordered w-100">
                            <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Item Code</th>
                                        <th>Item Name</th>
                                        <th>Item Cat Name</th>
                                        <th>Condition</th>
                                        <th>Color</th>
                                        <th>Quantity</th>
                                        <th>Status</th>
                                        <th>Purchase Date</th>
                                        <th>warranty </th>
                                        <th>Location</th>
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
    var op = "items.itemsList";
    // var username = $("#username").val();
    // alert(username);
    $(document).ready(function() {
        table = $("#datatables-item-cats").DataTable({
            processing: true,
            columnDefs: [{
                orderable: false,
                targets: 0
            }],
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
                    // Add any other filters here if needed
                }
            }
        });
    });

    function editItem(id) {
        loadModal('setup/item_setup.php?op=edit&item_id=' + id, 'modal_div');
    }

    function deleteItem(id) {
        if (confirm("Are you sure you want to delete this item category?")) {
            $.post('utilities.php', { op: 'items.deleteItem', item_id: id }, function (resp) {
                if (resp.response_code == 0) {
                    alert(resp.response_message);
                    $('#datatable').DataTable().ajax.reload();
                } else {
                    alert(resp.response_message);
                }
            }, 'json');
        }
    }

    function loadModal(url, target) {
        $("#" + target).html('<div class="text-center p-5"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>');
        $.get(url, function(data) {
            $("#" + target).html(data);
            $('#defaultModalPrimary').modal('show');
        });
    }
</script>