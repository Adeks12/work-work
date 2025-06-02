<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);   

class item_cat extends dbobject
{
    public function item_catList($data)
    {
        $table_name    = "item_category";
        $primary_key   = "item_cat_id";
        $columner = array(
            array( 'db' => 'item_cat_id', 'dt' => 0 ),
            array( 'db' => 'item_code', 'dt' => 1 ),
            array( 'db' => 'item_cat_name', 'dt' => 2 ),
            array( 
                'db' => 'item_status', 
                'dt' => 3,
                'formatter' => function( $d, $row ) {
                    return $d == '1' ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                }
            ),
            array( 'db' => 'created_at', 'dt' => 4 ),
            array( 
                'db' => 'item_cat_id', 
                'dt' => 5,
                'formatter' => function( $d, $row ) {
                    return '<div class="d-flex gap-1">
                                <button class="btn btn-sm btn-primary" onclick="edititem_cat('.$d.')">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteitem_cat('.$d.')">Delete</button>
                            </div>';
                }
            )
        );
        
        // Filter by merchant_id for security
        $merchant_id = $_SESSION['merchant_id'] ?? $data['merchant_id'] ?? '';
        $filter = " AND merchant_id = '$merchant_id'";

        $datatableEngine = new engine();
        echo $datatableEngine->generic_table($data, $table_name, $columner, $primary_key, $filter);
    }

    private function generateitem_catCode($item_catName, $merchantId) {
        $merchantId = $_SESSION['merchant_id'] ?? $merchantId;
        $sql = "SELECT merchant_business_name FROM merchant_reg WHERE merchant_id = '$merchantId' LIMIT 1";
        $company = $this->db_query($sql, true);
        $companyName = isset($company[0]['merchant_business_name']) ? $company[0]['merchant_business_name'] : 'ITEM';
        
        // Get first 3 letters of company name, convert to uppercase
        $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $companyName), 0, 3));
        $prefix = str_pad($prefix, 3, 'X');

        // Generate unique code
        do {
            $randomNumber = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
            $code = $prefix . $randomNumber;
            $checkCode = $this->db_query("SELECT item_cat_id FROM item_category WHERE item_code = '$code' AND merchant_id = '$merchantId'", true);
        } while ($checkCode && count($checkCode) > 0);

        return $code;
    }

    public function createitem_cat($data)
    {
        try {
            $data['created_at'] = date("Y-m-d H:i:s");
            $data['created_officer'] = $_SESSION['username_sess'];

            // Auto-generate item_cat code
            if($data['operation'] == 'new') {
                $data['item_code'] = $this->generateitem_catCode($data['item_cat_name'], $data['merchant_id']);
            }

            // Validation rules
            $validation = $this->validate($data,
                array(
                    'item_cat_name' => 'required',
                    'item_status' => 'required'
                ),
                array(
                    'item_cat_name' => 'Category Name',
                    'item_status' => 'Status'
                )
            );

            if(!$validation['error'])
            {
                if($data['operation'] == 'new')
                {
                    // Check for duplicate item category name within same merchant
                    $checkName = $this->db_query("SELECT item_cat_id FROM item_category WHERE item_cat_name = '{$data['item_cat_name']}' AND merchant_id = '{$data['merchant_id']}'", true);
                    if($checkName && count($checkName) > 0) {
                        return json_encode(array("response_code" => 22, "response_message" => "Item category name already exists"));
                    }

                    $excluded_keys = ['op', 'operation', 'nrfa-csrf-token-label'];
                    $res = $this->doInsert('item_category', $data, $excluded_keys);

                    if($res == "1")
                    {
                        return json_encode(array(
                            "response_code" => 0,
                            "response_message" => "Item category created successfully with code: " . $data['item_code']
                        ));
                    }
                    else
                    {
                        return json_encode(array("response_code" => 78, "response_message" => "Failed to create item category"));
                    }
                }
                elseif($data['operation'] == 'edit')
                {
                    // Check for duplicate item category name within same merchant (excluding current record)
                    $checkName = $this->db_query("SELECT item_cat_id FROM item_category WHERE item_cat_name = '{$data['item_cat_name']}' AND merchant_id = '{$data['merchant_id']}' AND item_cat_id != '{$data['item_cat_id']}'", true);
                    if($checkName && count($checkName) > 0) {
                        return json_encode(array("response_code" => 22, "response_message" => "Item category name already exists"));
                    }

                    $data['updated_at'] = date("Y-m-d H:i:s");
                    $data['updated_officer'] = $_SESSION['username_sess'];
                    $merchant_id = $data['merchant_id'];
                    $item_cat_id = $data['item_cat_id'];
                   
                    $excluded_keys = ['op', 'operation', 'nrfa-csrf-token-label'];
                    $res = $this->doUpdate('item_category', $data, $excluded_keys, ['item_cat_id' => $item_cat_id, 'merchant_id' => $merchant_id]);                   
                                      
                    if($res == "1" || $res === true)
                    {
                        return json_encode(array("response_code" => 0, "response_message" => "Item category updated successfully"));
                    }
                    else
                    {
                        return json_encode(array("response_code" => 79, "response_message" => "Failed to update item category"));
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
            error_log("Item Category Creation Error: " . $e->getMessage());
            return json_encode(array("response_code" => 500, "response_message" => $e->getMessage()));
        }
    }
    
    public function getitem_cat($data)
    {
        try {
            $item_cat_id = $data['item_cat_id'] ?? $data['item_cat_id'];
            $merchant_id = $_SESSION['merchant_id'] ?? $data['merchant_id'];
            $sql = "SELECT * FROM item_category WHERE item_cat_id='$item_cat_id' AND merchant_id='$merchant_id' LIMIT 1";

            $item_cat = $this->db_query($sql, true);
            
            if($item_cat && count($item_cat) > 0) {
                return json_encode(array("response_code" => 0, "data" => $item_cat[0]));
            } else {
                return json_encode(array("response_code" => 404, "response_message" => "Item category not found"));
            }
        }
        catch(Exception $e)
        {
            error_log("Get Item Category Error: " . $e->getMessage());
            return json_encode(array("response_code" => 500, "response_message" => "An error occurred while fetching item category"));
        }
    }
    
    public function deleteitem_cat($data)
    {
        try {
            $item_cat_id = $data['item_cat_id'] ?? $data['item_cat_id'];
            $merchant_id = $_SESSION['merchant_id'] ?? $data['merchant_id'];

            // Check if item category is being used by any items
            // Uncomment and modify this if you have items table that references item_cat_id
            // $checkUsage = $this->db_query("SELECT COUNT(*) as count FROM items WHERE item_cat_id = '$item_cat_id'", true);
            // if($checkUsage && $checkUsage[0]['count'] > 0) {
            //     return json_encode(array("response_code" => 23, "response_message" => "Cannot delete item category. It is currently assigned to items."));
            // }

            $sql = "DELETE FROM item_category WHERE item_cat_id = '$item_cat_id' AND merchant_id = '$merchant_id'";
            $result = $this->db_query($sql, false);

            if($result) {
                return json_encode(array("response_code" => 0, "response_message" => "Item category deleted successfully"));
            } else {
                return json_encode(array("response_code" => 80, "response_message" => "Failed to delete item category"));
            }
        }
        catch(Exception $e)
        {
            error_log("Delete Item Category Error: " . $e->getMessage());
            return json_encode(array("response_code" => 500, "response_message" => $e->getMessage()));
        }
    }
    
    public function getAllitem_cats($data)
    {
        try {
            $merchant_id = $_SESSION['merchant_id'] ?? $data['merchant_id'];
            $sql = "SELECT item_cat_id as id, item_cat_name, item_code FROM item_category WHERE merchant_id = '$merchant_id' AND item_status = '1' ORDER BY item_cat_name";
            $result = $this->db_query($sql, true);

            return json_encode(array("response_code" => 0, "data" => $result));
        }
        catch(Exception $e)
        {
            error_log("Get All Item Categories Error: " . $e->getMessage());
            return json_encode(array("response_code" => 500, "response_message" => "An error occurred while fetching item categories"));
        }
    }
}