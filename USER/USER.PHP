<?php
require ('../dbConnection.php');
include('apis.php'); 




$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod == "GET") {
    $userList = getUserList();
    echo $userList;
} elseif ($requestMethod == "POST") {
    $userList = addUser();
    echo $userList;
}elseif($requestMethod =="DELETE"){
    $userList = deleteUser();
    echo $userList;
} elseif($requestMethod=="PUT"){
    $userList = updateUser();
    echo $userList;
}else {
    $data = [
        'status' => 405,
        'message'=> $requestMethod. ' request method not allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}

