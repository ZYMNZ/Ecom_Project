<?php
$userInfo = new User($_GET['id']);
$isSuccessful = User::updatePersonalInfo(
    $_POST['firstName'],
    $_POST['lastName'],
    $userInfo->getEmail(),
    $userInfo->getPassword(),
    $_POST['description'],
    $_POST['phoneNumber'],
    $_GET['id']
);
header('Location: /?controller=user&action=viewBuyers');