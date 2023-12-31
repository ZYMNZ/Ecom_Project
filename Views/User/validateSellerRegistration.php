<?php
include_once 'Models/User.php';
include_once 'Models/UserRole.php';
include_once 'Models/Role.php';

if (isset($_POST['submit'])) {
    $user = new User($_SESSION['user_id']);
    if ($user->getPassword() === md5($_POST['password'])) {
        $role = Role::getRoleByName('seller');
        UserRole::createUserRole($user->getUserId(), $role->getRoleId());
        $_SESSION['userRoles'][] = 'seller';
        header("Location: /?controller=product&action=sellerProduct");
    } else {
        $_SESSION['error'] = 'wrongPassword';
        header("Location: /?controller=user&action=sellerRegister");
    }
}
else {
    header("Location: /?controller=general&action=error");
}
