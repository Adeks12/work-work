<?php
include_once("dbobject.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

class Department extends dbobject
{
    public function departmentList($data)
    {
        $table_name = "department";
        $primary_key = "depmt_id";
        $columner = array(
            array('db' => 'depmt_id', 'dt' => 0),
            array('db' => 'Depmt_name', 'dt' => 1),
            array('db' => 'depmt_code', 'dt' => 2),
            array('db' => 'Depmt_head', 'dt' => 3),
            array(
                'db' => 'Depmt_status',
                'dt' => 4,
                'formatter' => function ($d, $row) {
                    return $d == 'active' ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Not Active</span>';
                }
            ),
            array('db' => 'created_at', 'dt' => 5),
            array(
                'db' => 'depmt_id',
                'dt' => 6,
                'formatter' => function ($d, $row) {
                    return '<button class="btn btn-sm btn-primary" onclick="editDepartment(' . $d . ')">Edit</button> 
                            <button class="btn btn-sm btn-danger" onclick="deleteDepartment(' . $d . ')">Delete</button>';
                }
            )
        );

        // Filter by merchant_id for security
        $merchant_id = $_SESSION['merchant_id'] ?? '';
        if (empty($merchant_id)) {
            return json_encode(array("response_code" => 403, "response_message" => "Merchant ID not provided. Please log in."));
        }
        $filter = "merchant_id = ?";

        $datatableEngine = new engine();
        return $datatableEngine->generic_table($data, $table_name, $columner, $filter, $primary_key, [$merchant_id]);
    }

    public function createDepartment($data)
    {
        try {
            if ( !isset($_SESSION['username_sess'])) {
                return json_encode(array("response_code" => 403, "response_message" => "Session not valid. Please log in."));
            }

            $data['created_at'] = date("Y-m-d H:i:s");
            $data['created_officer'] = $_SESSION['username_sess'];
            $data['updated_at'] = date("Y-m-d H:i:s");
            

            // Validation rules
            $validation = $this->validate($data, array(
                'depmt_name' => 'required',
                'depmt_code' => 'required',
                'depmt_head' => 'required',
                'depmt_status' => 'required'
            ), array(
                'depmt_name' => 'Department Name',
                'depmt_code' => 'Department Code',
                'depmt_head' => 'Department Head',
                'depmt_status' => 'Department Status'
            ));

            if ($validation['error']) {
                return json_encode(array("response_code" => 20, "response_message" => $validation['messages'][0]));
            }

            // Check for duplicate department name or code
            $sql = "SELECT depmt_id FROM department WHERE (Depmt_name = ? OR depmt_code = ?) AND merchant_id = ?";
            $params = [$data['Depmt_name'], $data['depmt_code'], $data['merchant_id']];
            if ($data['operation'] == 'edit') {
                $sql .= " AND depmt_id != ?";
                $params[] = $data['depmt_id'];
            }
            $check = $this->db_query($sql, true, $params);

            if ($check && count($check) > 0) {
                return json_encode(array("response_code" => 21, "response_message" => "Department name or code already exists"));
            }

            if ($data['operation'] == 'new') {
                $excluded_keys = ['op', 'operation', 'depmt_id', 'nrfa-csrf-token-label'];
                $res = $this->doInsert('department', $data, $excluded_keys);
                if ($res === "1" || $res === true) {
                    return json_encode(array("response_code" => 0, "response_message" => "Department created successfully"));
                } else {
                    error_log("Insert failed: " . print_r($res, true));
                    return json_encode(array("response_code" => 78, "response_message" => "Failed to create department"));
                }
            } elseif ($data['operation'] == 'edit') {
                $excluded_keys = ['op', 'operation', 'nrfa-csrf-token-label'];
                $whereClause = "depmt_id = ? AND merchant_id = ?";
                $res = $this->doUpdate('department', $data, $excluded_keys, $whereClause, [$data['depmt_id'], $data['merchant_id']]);
                if ($res === "1" || $res === true) {
                    return json_encode(array("response_code" => 0, "response_message" => "Department updated successfully"));
                } else {
                    error_log("Update failed: " . print_r($res, true));
                    return json_encode(array("response_code" => 79, "response_message" => "Failed to update department"));
                }
            }
        } catch (Exception $e) {
            error_log("Create Department Error: " . $e->getMessage() . " | Data: " . print_r($data, true));
            return json_encode(array("response_code" => 500, "response_message" => "An error occurred while processing your request"));
        }
    }

    public function getDepartment($data)
    {
        try {
            if (!isset($data['department_id'])) {
                return json_encode(array("response_code" => 400, "response_message" => "Department ID is required"));
            }
            if (!isset($_SESSION['merchant_id']) && !isset($data['merchant_id'])) {
                return json_encode(array("response_code" => 403, "response_message" => "Merchant ID not provided. Please log in or provide merchant_id."));
            }

            $department_id = filter_var($data['department_id'], FILTER_VALIDATE_INT);
            if ($department_id === false) {
                return json_encode(array("response_code" => 400, "response_message" => "Invalid Department ID"));
            }
            $merchant_id = $_SESSION['merchant_id'] ?? $data['merchant_id'];

            $sql = "SELECT depmt_id, Depmt_name, depmt_code, Depmt_head, Depmt_status, depmt_description, created_at, updated_at, created_officer, merchant_id 
                    FROM department WHERE depmt_id = ? AND merchant_id = ? LIMIT 1";
            $result = $this->db_query($sql, true, [$department_id, $merchant_id]);

            if ($result === false || $result === null) {
                error_log("Query failed in getDepartment: $sql | Params: " . print_r([$department_id, $merchant_id], true));
                return json_encode(array("response_code" => 500, "response_message" => "Database query failed"));
            }

            if (count($result) > 0) {
                return json_encode(array("response_code" => 0, "data" => $result[0]));
            } else {
                return json_encode(array("response_code" => 404, "response_message" => "Department not found"));
            }
        } catch (Exception $e) {
            error_log("Get Department Error: " . $e->getMessage() . " | Department ID: $department_id | Merchant ID: $merchant_id");
            return json_encode(array("response_code" => 500, "response_message" => "An error occurred while fetching department"));
        }
    }

    public function deleteDepartment($data)
    {
        try {
            if (!isset($data['department_id'])) {
                return json_encode(array("response_code" => 400, "response_message" => "Department ID is required"));
            }
            if (!isset($_SESSION['merchant_id']) && !isset($data['merchant_id'])) {
                return json_encode(array("response_code" => 403, "response_message" => "Merchant ID not provided. Please log in or provide merchant_id."));
            }

            $department_id = filter_var($data['department_id'], FILTER_VALIDATE_INT);
            if ($department_id === false) {
                return json_encode(array("response_code" => 400, "response_message" => "Invalid Department ID"));
            }
            $merchant_id = $_SESSION['merchant_id'] ?? $data['merchant_id'];

            // Check if department is assigned to employees
            $sql = "SELECT COUNT(*) as count FROM employees WHERE department_id = ?";
            $checkUsage = $this->db_query($sql, true, [$department_id]);
            if ($checkUsage === false || $checkUsage === null) {
                error_log("Check usage query failed: $sql | Params: " . print_r([$department_id], true));
                return json_encode(array("response_code" => 500, "response_message" => "Database query failed"));
            }
            if ($checkUsage[0]['count'] > 0) {
                return json_encode(array("response_code" => 23, "response_message" => "Cannot delete department. It is currently assigned to employees."));
            }

            $sql = "DELETE FROM department WHERE depmt_id = ? AND merchant_id = ?";
            $result = $this->db_query($sql, false, [$department_id, $merchant_id]);

            if ($result !== false && $result !== null) {
                return json_encode(array("response_code" => 0, "response_message" => "Department deleted successfully"));
            } else {
                error_log("Delete failed: $sql | Params: " . print_r([$department_id, $merchant_id], true));
                return json_encode(array("response_code" => 80, "response_message" => "Failed to delete department"));
            }
        } catch (Exception $e) {
            error_log("Delete Department Error: " . $e->getMessage() . " | Department ID: $department_id | Merchant ID: $merchant_id");
            return json_encode(array("response_code" => 500, "response_message" => "An error occurred while deleting department"));
        }
    }

    public function getAllDepartments($data)
    {
        try {
            if (!isset($_SESSION['merchant_id']) && !isset($data['merchant_id'])) {
                return json_encode(array("response_code" => 403, "response_message" => "Merchant ID not provided. Please log in or provide merchant_id."));
            }

            $merchant_id = $_SESSION['merchant_id'] ?? $data['merchant_id'];
            $sql = "SELECT depmt_id, Depmt_name, depmt_code FROM department WHERE merchant_id = ? AND Depmt_status = 'active' ORDER BY Depmt_name";
            $result = $this->db_query($sql, true, [$merchant_id]);

            if ($result === false || $result === null) {
                error_log("Query failed in getAllDepartments: $sql | Params: " . print_r([$merchant_id], true));
                return json_encode(array("response_code" => 500, "response_message" => "Database query failed"));
            }

            return json_encode(array("response_code" => 0, "data" => $result));
        } catch (Exception $e) {
            error_log("Get All Departments Error: " . $e->getMessage() . " | Merchant ID: $merchant_id");
            return json_encode(array("response_code" => 500, "response_message" => "An error occurred while fetching departments"));
        }
    }
}