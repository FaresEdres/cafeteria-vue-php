<?php
// function validateUserData($data, $isUpdate = false) {
//     $errors = [];

//     if (!$isUpdate) {
//         if (empty($data['firstname'])) {
//             $errors['firstname'] = 'First name is required';
//         }
//         if (empty($data['lastname'])) {
//             $errors['lastname'] = 'Last name is required';
//         }
//         if (empty($data['role'])) {
//             $errors['role'] = 'role is required';
//         }
//         if (empty($data['email'])) {
//             $errors['email'] = 'Email is required';
//         // } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
//         //     $errors['email'] = 'Invalid email format';
//         }
//         if (empty($data['password'])) {
//             $errors['password'] = 'Password is required';
//         } elseif (strlen($data['password']) < 8) {
//             $errors['password'] = 'Password must be at least 8 characters';
//         }
//     }

//     if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
//         $errors['email'] = 'Invalid email format';
//     }

//     if (isset($data['password']) && strlen($data['password']) < 8) {
//         $errors['password'] = 'Password must be at least 8 characters';
//     }
    
//     // Role validation
//     if (isset($data['role']) && !in_array($data['role'], ['admin', 'user'])) {
//         $errors['role'] = 'Role must be either admin or user';
//     }

//     return $errors;
// }

require_once __DIR__ .'/../db/connector.php';

function validateUserDataLogin($email, $password){
    $errors = [];

    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Password must be at least 6 characters';
    }

    return $errors;
}

function validateUserData($data){

    $errors = [];
    foreach ($data as $key => $value) {

        if(! isset($value) && $value == ""){
            $errors[$key] = ucfirst("{$key} is required");
        }else{
                if ($key === 'email' and ! filter_var($value, FILTER_VALIDATE_EMAIL)){
                    $errors['email'] = "Invalid Email Format";
                    break;
                }
                else if($key === 'password' and strlen($value) < 8){
                    $errors['password'] = "8 Character or more";
                    break;
                }
                else if ($key === 'firstname' or $key === 'lastname') {
                    if (!preg_match("/^[a-zA-Z]+$/", $value)) {
                        $errors[$key] = "{$key} must contain only letters";
                        break;
                    }
                }
                else if ($key === 'room' or $key === 'ext') {
                    if (!preg_match("/^[0-9]+$/", $value)) {
                        $errors[$key] = "{$key} must contain only numbers";
                        break;
                    }
                }
            }
        }
    return $errors;
}


function validIMG($fileInfo,&$formErrors){

    $imageName = $fileInfo['name'];
    $imageTmp = $fileInfo['tmp_name'];
    
    if(empty($imageName) and empty($imageTmp)) {

        $formErrors['image'] = "Please upload a valid image";
        return;

    }
    else{

        if (count($formErrors)){
            return;
        }

        $ext = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];

        if (!in_array($ext, $allowed)) {
            $formErrors['image'] = "Invalid image format";
            return;
        }
        else {
            $newName = uniqid() . "." . $ext;
            move_uploaded_file($imageTmp, __DIR__ . "/../public/uploads/users/{$newName}");
        }
    }
    return $newName;
}

function emailExistence($email){
    try {
        $conn = SQLConnector::connectDatabase();
        $stmt = $conn->prepare("SELECT * FROM users WHERE LOWER(email) = LOWER(:email)");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    } catch (Exception $e) {
        echo $e->getMessage();
        return false;
    }
}


