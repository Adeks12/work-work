<?php
include_once("dbobject.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);   

class Department extends dbobject
{
    public function departmentList($data)
    {
        $table_name    = "department";
        $primary_key   = "depmt_id";
        $columner = array(
            array( 'db' => 'depmt_id', 'dt' => 0 ),
            array( 'db' => 'depmt_name', 'dt' => 1 ),
            array( 'db' => 'depmt_code', 'dt' => 2 ),
            array( 'db' => 'depmt_head', 'dt' => 3 ),
            array( 
                'db' => 'depmt_status', 
                'dt' => 4,
                'formatter' => function( $d, $row ) {
                    return $d == '1' ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                }
            ),
            array( 'db' => 'created_at', 'dt' => 5 ),
            array( 
                'db' => 'depmt_id', 
                'dt' => 6,
                'formatter' => function( $d, $row ) {
                    return '<button class="btn btn-sm btn-primary" onclick="editDepartment('.$d.')">Edit</button> 
                            <button class="btn btn-sm btn-danger" onclick="deleteDepartment('.$d.')">Delete</button>';
                }
            )
        );
        
        // Filter by merchant_id for security
        $merchant_id = $_SESSION['merchant_id'] ?? $data['merchant_id'] ?? '';
        $filter = " AND merchant_id = '$merchant_id'";
        
        // Add search functionality - Fixed: Check for search parameters in DataTables format
        if (!empty($data['search']['value'])) {
            $search_value = mysqli_real_escape_string($this->connection, $data['search']['value']);
            $filter .= " AND (depmt_name LIKE '%$search_value%' OR depmt_code LIKE '%$search_value%' OR depmt_head LIKE '%$search_value%')";
        }
        
        // Individual column search (if implemented)
        if (!empty($data['search_name'])) {
            $search_name = mysqli_real_escape_string($this->connection, $data['search_name']);
            $filter .= " AND depmt_name LIKE '%$search_name%'";
        }
        
        if (!empty($data['search_code'])) {
            $search_code = mysqli_real_escape_string($this->connection, $data['search_code']);
            $filter .= " AND depmt_code LIKE '%$search_code%'";
        }
        
        if (isset($data['search_status']) && $data['search_status'] !== '') {
            $search_status = mysqli_real_escape_string($this->connection, $data['search_status']);
            $filter .= " AND depmt_status = '$search_status'";
        }
        
        $datatableEngine = new engine();
        echo $datatableEngine->generic_table($data, $table_name, $columner, $primary_key, $filter);
    }

    public function createDepartment($data)
    {
        try {
            $data['created_at'] = date("Y-m-d H:i:s");
            $data['created_officer'] = $_SESSION['username_sess'];
            
            // Validation rules
            $validation = $this->validate($data,
                array(
                    'depmt_name' => 'required',
                    'depmt_code' => 'required',
                    'depmt_head' => 'required',
                    'depmt_status' => 'required'
                ),
                array(
                    'depmt_name' => 'Department Name',
                    'depmt_code' => 'Department Code',
                    'depmt_head' => 'Department Head', 
                    'depmt_status' => 'Department Status'
                )
            );
            
            if(!$validation['error'])
            {
                if($data['operation'] == 'new')
                {
                    // Check for duplicate department code within same merchant
                    $checkCode = $this->db_query("SELECT depmt_id FROM department WHERE depmt_code = '{$data['depmt_code']}' AND merchant_id = '{$data['merchant_id']}'", true);
                    
                    if($checkCode && count($checkCode) > 0) {
                        return json_encode(array("response_code" => 21, "response_message" => "Department code already exists"));
                    }
                    
                    // Check for duplicate department name within same merchant
                    $checkName = $this->db_query("SELECT depmt_id FROM department WHERE depmt_name = '{$data['depmt_name']}' AND merchant_id = '{$data['merchant_id']}'", true);
                    if($checkName && count($checkName) > 0) {
                        return json_encode(array("response_code" => 22, "response_message" => "Department name already exists"));
                    }
                    
                    $excluded_keys = ['op', 'operation', 'nrfa-csrf-token-label'];
                    $res = $this->doInsert('department', $data, $excluded_keys);
                    
                    if($res == "1")
                    {
                        return json_encode(array("response_code" => 0, "response_message" => "Department created successfully"));
                    }
                    else
                    {
                        return json_encode(array("response_code" => 78, "response_message" => "Failed to create department"));
                    }
                }
                elseif($data['operation'] == 'edit')
                {
                    // Check for duplicate department code within same merchant (excluding current record)
                    $checkCode = $this->db_query("SELECT depmt_id FROM department WHERE depmt_code = '{$data['depmt_code']}' AND merchant_id = '{$data['merchant_id']}' AND depmt_id != '{$data['depmt_id']}'", true);
                    
                    if($checkCode && count($checkCode) > 0) {
                        return json_encode(array("response_code" => 21, "response_message" => "Department code already exists"));
                    }
                    
                    // Check for duplicate department name within same merchant (excluding current record)
                    $checkName = $this->db_query("SELECT depmt_id FROM department WHERE depmt_name = '{$data['depmt_name']}' AND merchant_id = '{$data['merchant_id']}' AND depmt_id != '{$data['depmt_id']}'", true);
                    if($checkName && count($checkName) > 0) {
                        return json_encode(array("response_code" => 22, "response_message" => "Department name already exists"));
                    }
                    
                    $data['updated_at'] = date("Y-m-d H:i:s");
                    $data['updated_officer'] = $_SESSION['username_sess'];
                    
                    $excluded_keys = ['op', 'operation', 'nrfa-csrf-token-label'];
                    $whereClause = "depmt_id = '{$data['depmt_id']}' AND merchant_id = '{$data['merchant_id']}'";
                    $res = $this->doUpdate('department', $data, $excluded_keys, $whereClause);
                    
                    if($res == "1" || $res === true)
                    {
                        return json_encode(array("response_code" => 0, "response_message" => "Department updated successfully"));
                    }
                    else
                    {
                        return json_encode(array("response_code" => 79, "response_message" => "Failed to update department"));
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
            error_log("Department Creation Error: " . $e->getMessage());
            return json_encode(array("response_code" => 500, "response_message" => "An error occurred while processing your request"));
        }
    }
    
    public function getDepartment($data)
    {
        try {
            $department_id = $data['depmt_id'] ?? $data['department_id'];
            $merchant_id = $_SESSION['merchant_id'] ?? $data['merchant_id'];
            $sql = "SELECT * FROM department WHERE depmt_id='$department_id' AND merchant_id='$merchant_id' LIMIT 1";

            $dept = $this->db_query($sql, true);
            
            if($dept && count($dept) > 0) {
                return json_encode(array("response_code" => 0, "data" => $dept[0]));
            } else {
                return json_encode(array("response_code" => 404, "response_message" => "Department not found"));
            }
        }
        catch(Exception $e)
        {
            error_log("Get Department Error: " . $e->getMessage());
            return json_encode(array("response_code" => 500, "response_message" => "An error occurred while fetching department"));
        }
    }
    
    public function deleteDepartment($data)
    {
        try {
            $department_id = $data['depmt_id'] ?? $data['department_id'];
            $merchant_id = $_SESSION['merchant_id'] ?? $data['merchant_id'];

            // Check if department is being used by any employees/members
            $checkUsage = $this->db_query("SELECT COUNT(*) as count FROM employees WHERE department_id = '$department_id'", true);
            if($checkUsage && $checkUsage[0]['count'] > 0) {
                return json_encode(array("response_code" => 23, "response_message" => "Cannot delete department. It is currently assigned to employees."));
            }

            $sql = "DELETE FROM department WHERE depmt_id = '$department_id' AND merchant_id = '$merchant_id'";
            $result = $this->db_query($sql);

            if($result) {
                return json_encode(array("response_code" => 0, "response_message" => "Department deleted successfully"));
            } else {
                return json_encode(array("response_code" => 80, "response_message" => "Failed to delete department"));
            }
        }
        catch(Exception $e)
        {
            error_log("Delete Department Error: " . $e->getMessage());
            return json_encode(array("response_code" => 500, "response_message" => "An error occurred while deleting department"));
        }
    }
    
    public function getAllDepartments($data)
    {
        try {
            $merchant_id = $_SESSION['merchant_id'] ?? $data['merchant_id'];
            $sql = "SELECT depmt_id as id, depmt_name as department_name, depmt_code as department_code FROM department WHERE merchant_id = '$merchant_id' AND depmt_status = '1' ORDER BY depmt_name";
            $result = $this->db_query($sql, true);

            return json_encode(array("response_code" => 0, "data" => $result));
        }
        catch(Exception $e)
        {
            error_log("Get All Departments Error: " . $e->getMessage());
            return json_encode(array("response_code" => 500, "response_message" => "An error occurred while fetching departments"));
        }
    }
}