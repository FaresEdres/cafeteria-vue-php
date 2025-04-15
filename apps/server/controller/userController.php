<?php
require_once __DIR__ . '/../helper/errors.php';
require_once __DIR__ . '/../model/userModel.php';
require_once __DIR__ . '/../validation/userValidation.php';
require_once __DIR__ . '/../controller/authenticateController.php';

class UserController
{
    private $userModel;
    private $authenticateController;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->authenticateController = new AuthenticateController();
    }

    public function getAllUsers()
    {
        $user = $this->authenticateController->authenticated();
        if(! ($user && $user['role'] == 'admin')) {
            return ["error" => $user];
        }

        $users = $this->userModel->displayAllUsers();
        if (isset($users["error"])) {
            return ["error" => $users["error"]];
        } else {
            return ($users);
        }
    }

    public function getUserById($id)
    {
        $user = $this->authenticateController->authenticated();
        if(! ($user && $user['role'] == 'admin')) {
            return ["error" => $user];
        }
        
        $user = $this->userModel->displayUserById($id);
        if (isset($user["error"])) {
            return ["error" => $user["error"]] ;
        } else {
            return $user;
        }
    }

    public function addUser()
    {
        $user = $this->authenticateController->authenticated();
        if(! ($user && $user['role'] == 'admin')) {
            return ["error" => "Unauthorized"];
        }


        $data = [
            "firstname" => $_POST['firstname'],
            "lastname" => $_POST['lastname'],
            "email" => $_POST['email'],
            "password" => $_POST['password'],
            "room" => $_POST['room'],
            "ext" => $_POST['ext'],
        ];

        
        $validationErrors = validateUserData($data);
        $img = validIMG($_FILES['image'], $validationErrors);
        if(emailExistence($data['email'])) $validationErrors['email'] = "Email already exists";
        
        if (!empty($validationErrors)) {
            return ["errors" => $validationErrors];
        }

        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $data['image'] = $img;
        $result = $this->userModel->addUser($data);
        return $result;
    }

    public function updateUser($id)
    {
        $user = $this->authenticateController->authenticated();
        if(! ($user && $user['role'] == 'admin')) {
            return ["error" => $user];
        }
        $user = $this->userModel->displayUserById($id);

        if (isset($user["error"])) {
            return ["error" => $user["error"]];
        }
        

        $data = [
            "firstname" => $_POST['firstname'],
            "lastname" => $_POST['lastname'],
            "email" => $_POST['email'],
            "room" => $_POST['room'],
            "ext" => $_POST['ext'],
        ];

        
        $validationErrors = validateUserData($data);
        $imageName = $_FILES['image']['name'];
        $imageTmp = $_FILES['image']['tmp_name'];
        
        if($user['email'] != $data['email'] and emailExistence($data['email'])) {
            $validationErrors['email'] = "Email already exists";
        }
        if(!(empty($imageName) and empty($imageTmp))) {
            $img = validIMG($_FILES['image'], $validationErrors);
            
            if($img) {
                $oldimg = $user['image'];
                if (file_exists(__DIR__."/../public/uploads/users/".$oldimg)) {
                    unlink(__DIR__."/../public/uploads/users/".$oldimg);
                }
                $data['image'] = $img;
            }
        }
        
        if (!empty($validationErrors)) {
            return ["errors" => $validationErrors];
        }

        $result = $this->userModel->updateUser($id, $data);
        return $result;
    }

    public function deleteUser($id)
    {
        $user = $this->authenticateController->authenticated();
        if($user && $user['role'] == 'admin') {
            $user = $this->userModel->displayUserById($id);
            if (isset($user["error"])) {
                return ["error" => $user["error"]];
            }
            $oldimg = $user['image'];
            if (file_exists(__DIR__."/../public/uploads/users/".$oldimg)) {
                unlink(__DIR__."/../public/uploads/users/".$oldimg);
            }
            $result = $this->userModel->deleteUser($id);
            return $result;
        }
        else{
            return ["error" => "Unauthorized"];
        }
    }
}