<?php
function validateUserData($data, $isUpdate = false) {
    $errors = [];

    if (!$isUpdate) {
        if (empty($data['firstname'])) {
            $errors['firstname'] = 'First name is required';
        }
        if (empty($data['lastname'])) {
            $errors['lastname'] = 'Last name is required';
        }
        if (empty($data['role'])) {
            $errors['role'] = 'role is required';
        }
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        // } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        //     $errors['email'] = 'Invalid email format';
        }
        if (empty($data['password'])) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($data['password']) < 8) {
            $errors['password'] = 'Password must be at least 8 characters';
        }
    }

    if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }

    if (isset($data['password']) && strlen($data['password']) < 8) {
        $errors['password'] = 'Password must be at least 8 characters';
    }
    
    // Role validation
    if (isset($data['role']) && !in_array($data['role'], ['admin', 'user'])) {
        $errors['role'] = 'Role must be either admin or user';
    }

    return $errors;
}