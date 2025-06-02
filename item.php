<div class="container-fluid p-0">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Item Category Management</h5>
            <h6 class="card-subtitle text-muted">The report contains Item Categories that have been setup in the system.
            </h6>
        </div>
        <div class="card-body">
            <a class="btn btn-warning mb-3" onclick="loadModal('setup/item_setup.php','modal_div')"
                href="javascript:void(0)" data-toggle="modal" data-target="#defaultModalPrimary">
                <i class="fas fa-plus"></i> Create Item Category
            </a>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Item Category List</h5>
                            <h6 class="card-subtitle text-muted">Manage your organization's item categories</h6>
                        </div>
                        <div class="card-body">
                            <table id="datatables-item-cats" class="table table-striped w-100">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Item Code</th>
                                        <th>Category Name</th>
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
    var op = "item_cat.item_catList"; // Operation for DataTable

    $(document).ready(function () {
        table = $("#datatables-item-cats").DataTable({
            "sDom": '<"top"i>rt<"bottom"flp><"clear">',
            processing: true,
            columnDefs: [{
                    orderable: false,
                    targets: 0
                },
                {
                    width: "100px",
                    targets: 3
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
                data: function (d, l) {
                    d.op = op;
                    d.li = Math.random();
                }
            }
        });
    });

    function edititem_cat(itemCatId) {
        loadModal('setup/item_cat_setup.php?op=edit&item_cat_id=' + itemCatId, 'modal_div');
        $('#defaultModalPrimary').modal('show');
    }

    function deleteitem_cat(itemCatId) {
        if (confirm('Are you sure you want to delete this item category? This action cannot be undone.')) {
            $.post('utilities.php', {
                op: 'item_cat.deleteitem_cat',
                item_cat_id: itemCatId
            }, function (response) {
                if (response.response_code == 0) {
                    alert('Item category deleted successfully');
                    refreshItemCatList();
                } else {
                    alert('Error: ' + response.response_message);
                }
            }, 'json').fail(function () {
                alert('An error occurred while deleting the item category');
            });
        }
    }

    function refreshItemCatList() {
        $('#datatables-item-cats').DataTable().ajax.reload();
    }

    // Global function to refresh table after modal operations
    window.refreshItemCatList = refreshItemCatList;
</script>