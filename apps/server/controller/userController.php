<?php
require_once __DIR__ . '/../helper/errors.php';
require_once __DIR__ . '/../model/userModel.php';
require_once __DIR__ . '/../validation/userValidation.php';

class UserController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function getAllUsers()
    {
        $users = $this->userModel->displayAllUsers();
        if (isset($users["error"])) {
            // return json_encode(["error" => $users["error"]]);
            return json_encode(["error" => $users["error"]]);
        } else {
            return ($users);
        }
    }

    public function getUserById($id)
    {
        $user = $this->userModel->displayUserById($id);
        if (isset($user["error"])) {
            echo json_encode(["error" => $user["error"]]);
        } else {
            echo json_encode($user);
        }
    }

    public function addUser()
    {
        $inputData = json_decode(file_get_contents("php://input"), true);
        if (!$inputData) {
            echo json_encode(["error" => "Invalid JSON input"]);
            return;
        }

        $validationErrors = validateUserData($inputData);
        if (!empty($validationErrors)) {
            echo json_encode(["errors" => $validationErrors]);
            return;
        }

        // Hash password before storing
        $inputData['password'] = password_hash($inputData['password'], PASSWORD_DEFAULT);

        $result = $this->userModel->addUser($inputData);
        echo json_encode($result);
    }

    public function updateUser($id)
    {
        $inputData = json_decode(file_get_contents("php://input"), true);
        if (!$inputData) {
            echo json_encode(["error" => "Invalid JSON input"]);
            return;
        }


        $validationErrors = validateUserData($inputData);
        if (!empty($validationErrors)) {
            echo json_encode(["errors" => $validationErrors]);
            return;
        }

        // Remove role from update data if present
        if (isset($inputData['role'])) {
            unset($inputData['role']);
        }

        // Don't update password unless it's provided
        if (isset($inputData['password'])) {
            $inputData['password'] = password_hash($inputData['password'], PASSWORD_DEFAULT);
        }

        $result = $this->userModel->updateUser($id, $inputData);
        echo json_encode($result);
    }

    public function deleteUser($id)
    {
        $result = $this->userModel->deleteUser($id);
        echo json_encode($result);
    }
}