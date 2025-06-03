<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);   

class inventory extends dbobject
{
    public function inventoryList($data)
    {
        $GLOBALS['inventory_instance'] = $this;

        $table_name    = "inventory";
        $primary_key   = "item_id";
        $columner = array(
            array( 'db' => 'item_id', 'dt' => 0 ),
            array( 'db' => 'item_cond', 'dt' => 1 ),
            array( 'db' => 'item_cat_name', 'dt' => 2 ),
            array( 'db' => 'item_color', 'dt' => 3 ),
            array( 'db' => 'allocation_status', 'dt' => 4,
                'formatter' => function($d, $row) {
                    $status_colors = [
                        'Available' => 'success',
                        'Allocated' => 'primary',
                        'Reserved' => 'warning'
                    ];
                    $color = $status_colors[$d] ?? 'secondary';
                    return '<span class="badge bg-' . $color . '">' . $d . '</span>';
                }
            ),
            array( 'db' => 'usage_status', 'dt' => 5,
                'formatter' => function($d, $row) {
                    $status_colors = [
                        'Active' => 'success',
                        'Inactive' => 'secondary',
                        'Maintenance' => 'warning',
                        'Retired' => 'danger'
                    ];
                    $color = $status_colors[$d] ?? 'secondary';
                    return '<span class="badge bg-' . $color . '">' . $d . '</span>';
                }
            ),
            array( 'db' => 'allocated_officer', 'dt' => 6,
                'formatter' => function($d, $row) {
                    return $d ? $d : '-';
                }
            ),
            array( 'db' => 'allocated_date', 'dt' => 7,
                'formatter' => function($d, $row) {
                    return $d ? date('Y-m-d', strtotime($d)) : '-';
                }
            ),
            array( 'db' => 'created_at', 'dt' => 8,
                'formatter' => function($d, $row) {
                    return date('Y-m-d H:i', strtotime($d));
                }
            ),
            array( 'db' => 'item_id', 'dt' => 9,
                'formatter' => function( $d, $row ) {
                    return '<div class="d-flex gap-1">
                                <button class="btn btn-sm btn-primary" onclick="editInventory('.$d.')">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteInventory('.$d.')">Delete</button>
                            </div>';
                }
            )
        );

        // Filter by merchant_id for security
        $merchant_id = $_SESSION['merchant_id'] ?? $data['merchant_id'] ?? '';
        
        // JOIN with item_category to get category name
        $join = [
            ["item_category ic" => ["inventory.item_cat_id", "ic.item_cat_id"]]
        ];
        $filter = "inventory.merchant_id = '$merchant_id' AND inventory.delete_status != '1'";

        // Add category filter if set
        if (isset($data['item_cat_id']) && $data['item_cat_id'] !== '' && $data['item_cat_id'] !== 'all') {
            $cat_id = intval($data['item_cat_id']);
            $filter .= " AND inventory.item_cat_id = '$cat_id'";
        }

        // Add allocation status filter if set
        if (isset($data['allocation_status']) && $data['allocation_status'] !== '' && $data['allocation_status'] !== 'all') {
            $allocation_status = $data['allocation_status'];
            $filter .= " AND inventory.allocation_status = '$allocation_status'";
        }

        $datatableEngine = new engine();
        return $datatableEngine->generic_multi_table($data, $table_name, $columner, $primary_key, $join, $filter);
    }

    private function generateItemCode($merchantId) {
        $prefix = "ITM";
        do {
            $randomNumber = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $code = $prefix . $randomNumber;
            $checkCode = $this->db_query("SELECT item_id FROM inventory WHERE item_code = '$code' AND merchant_id = '$merchantId'", true);
        } while ($checkCode && count($checkCode) > 0);
        return $code;
    }

    public function createInventory($data)
    {
        try {
            $data['created_at'] = date("Y-m-d H:i:s");
            $data['created_officer'] = $_SESSION['username_sess'];
            $data['delete_status'] = '0'; // Not deleted

            // Auto-generate item code for new items
            if($data['operation'] == 'new') {
                $data['item_code'] = $this->generateItemCode($data['merchant_id']);
            }

            // Set allocation date and by if item is being allocated
            if($data['allocation_status'] == 'Allocated' && $data['operation'] == 'new') {
                $data['allocated_date'] = date("Y-m-d H:i:s");
                $data['allocated_by'] = $_SESSION['username_sess'];
            }

            // Validation rules
            $validation = $this->validate($data,
                array(
                    'item_cond' => 'required',
                    'item_cat_id' => 'required',
                    'allocation_status' => 'required',
                    'usage_status' => 'required'
                ),
                array(
                    'item_cond' => 'Item Condition',
                    'item_cat_id' => 'Item Category',
                    'allocation_status' => 'Allocation Status',
                    'usage_status' => 'Usage Status'
                )
            );

            // Additional validation for allocated items
            if($data['allocation_status'] == 'Allocated' && empty($data['allocated_officer'])) {
                return json_encode(array("response_code" => 20, "response_message" => "Allocated officer is required when allocation status is 'Allocated'"));
            }

            if(!$validation['error'])
            {
                if($data['operation'] == 'new')
                {
                    $excluded_keys = ['op', 'operation', 'nrfa-csrf-token-label'];
                    $res = $this->doInsert('inventory', $data, $excluded_keys);

                    if($res == "1")
                    {
                        return json_encode(array(
                            "response_code" => 0,
                            "response_message" => "Inventory item created successfully with code: " . $data['item_code']
                        ));
                    }
                    else
                    {
                        return json_encode(array("response_code" => 78, "response_message" => "Failed to create inventory item"));
                    }
                }
                elseif($data['operation'] == 'edit')
                {
                    // Set allocation date and by if status changed to allocated
                    $current_item = $this->db_query("SELECT allocation_status FROM inventory WHERE item_id = '{$data['item_id']}' AND merchant_id = '{$data['merchant_id']}'", true);
                    if($current_item && $current_item[0]['allocation_status'] != 'Allocated' && $data['allocation_status'] == 'Allocated') {
                        $data['allocated_date'] = date("Y-m-d H:i:s");
                        $data['allocated_by'] = $_SESSION['username_sess'];
                    }

                    $data['updated_at'] = date("Y-m-d H:i:s");
                    $data['updated_officer'] = $_SESSION['username_sess'];
                    $merchant_id = $data['merchant_id'];
                    $item_id = $data['item_id'];
                   
                    $excluded_keys = ['op', 'operation', 'nrfa-csrf-token-label'];
                    $res = $this->doUpdate('inventory', $data, $excluded_keys, ['item_id' => $item_id, 'merchant_id' => $merchant_id]);                   
                                      
                    if($res == "1" || $res === true)
                    {
                        return json_encode(array("response_code" => 0, "response_message" => "Inventory item updated successfully"));
                    }
                    else
                    {
                        return json_encode(array("response_code" => 79, "response_message" => "Failed to update inventory item"));
                    }
                }
            }
            else
            {
                return json_encode(array("response_code" => 20, "response_message" => $validation['messages'][0]));
            }
        }
        catch(Exception $e)
        {
            error_log("Inventory Creation Error: " . $e->getMessage());
            return json_encode(array("response_code" => 500, "response_message" => $e->getMessage()));
        }
    }
    
    public function getInventory($data)
    {
        try {
            $item_id = $data['item_id'];
            $merchant_id = $_SESSION['merchant_id'] ?? $data['merchant_id'];
            $sql = "SELECT i.*, ic.item_cat_name 
                    FROM inventory i 
                    LEFT JOIN item_category ic ON i.item_cat_id = ic.item_cat_id 
                    WHERE i.item_id='$item_id' AND i.merchant_id='$merchant_id' AND i.delete_status != '1' 
                    LIMIT 1";

            $item = $this->db_query($sql, true);
            
            if($item && count($item) > 0) {
                return json_encode(array("response_code" => 0, "data" => $item[0]));
            } else {
                return json_encode(array("response_code" => 404, "response_message" => "Inventory item not found"));
            }
        }
        catch(Exception $e)
        {
            error_log("Get Inventory Error: " . $e->getMessage());
            return json_encode(array("response_code" => 500, "response_message" => "An error occurred while fetching inventory item"));
        }
    }
    
    public function deleteInventory($data)
    {
        try {
            $item_id = $data['item_id'];
            $merchant_id = $_SESSION['merchant_id'] ?? $data['merchant_id'];

            // Soft delete - set delete_status to 1
            $sql = "UPDATE inventory SET delete_status = '1', deleted_at = NOW(), deleted_by = '{$_SESSION['username_sess']}' 
                    WHERE item_id = '$item_id' AND merchant_id = '$merchant_id'";
            $result = $this->db_query($sql, false);

            if($result) {
                return json_encode(array("response_code" => 0, "response_message" => "Inventory item deleted successfully"));
            } else {
                return json_encode(array("response_code" => 80, "response_message" => "Failed to delete inventory item"));
            }
        }
        catch(Exception $e)
        {
            error_log("Delete Inventory Error: " . $e->getMessage());
            return json_encode(array("response_code" => 500, "response_message" => $e->getMessage()));
        }
    }
    
    public function getAllInventory($data)
    {
        try {
            $merchant_id = $_SESSION['merchant_id'] ?? $data['merchant_id'];
            $where = "i.merchant_id = '$merchant_id' AND i.delete_status != '1'";
            
            if(isset($data['allocation_status']) && $data['allocation_status']) {
                $where .= " AND i.allocation_status = '{$data['allocation_status']}'";
            }
            
            if(isset($data['usage_status']) && $data['usage_status']) {
                $where .= " AND i.usage_status = '{$data['usage_status']}'";
            }

            $sql = "SELECT i.item_id as id, i.item_code, i.item_cond, i.item_color, 
                           i.allocation_status, i.usage_status, ic.item_cat_name
                    FROM inventory i 
                    LEFT JOIN item_category ic ON i.item_cat_id = ic.item_cat_id 
                    WHERE $where 
                    ORDER BY i.created_at DESC";
            
            $result = $this->db_query($sql, true);

            return json_encode(array("response_code" => 0, "data" => $result));
        }
        catch(Exception $e)
        {
            error_log("Get All Inventory Error: " . $e->getMessage());
            return json_encode(array("response_code" => 500, "response_message" => "An error occurred while fetching inventory items"));
        }
    }

    public function getInventoryStats($data)
    {
        try {
            $merchant_id = $_SESSION['merchant_id'] ?? $data['merchant_id'];
            
            $stats = array();
            
            // Total items
            $total = $this->db_query("SELECT COUNT(*) as count FROM inventory WHERE merchant_id = '$merchant_id' AND delete_status != '1'", true);
            $stats['total'] = $total[0]['count'];
            
            // Available items
            $available = $this->db_query("SELECT COUNT(*) as count FROM inventory WHERE merchant_id = '$merchant_id' AND allocation_status = 'Available' AND delete_status != '1'", true);
            $stats['available'] = $available[0]['count'];
            
            // Allocated items
            $allocated = $this->db_query("SELECT COUNT(*) as count FROM inventory WHERE merchant_id = '$merchant_id' AND allocation_status = 'Allocated' AND delete_status != '1'", true);
            $stats['allocated'] = $allocated[0]['count'];
            
            // Active items
            $active = $this->db_query("SELECT COUNT(*) as count FROM inventory WHERE merchant_id = '$merchant_id' AND usage_status = 'Active' AND delete_status != '1'", true);
            $stats['active'] = $active[0]['count'];

            return json_encode(array("response_code" => 0, "data" => $stats));
        }
        catch(Exception $e)
        {
            error_log("Get Inventory Stats Error: " . $e->getMessage());
            return json_encode(array("response_code" => 500, "response_message" => "An error occurred while fetching inventory stats"));
        }
    }
}