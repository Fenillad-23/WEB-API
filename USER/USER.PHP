<?php
require ('../dbConnection.php');
include('function.php'); 




$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod == "GET") {
    $customerList = getUserList();
    echo $customerList;
} elseif ($requestMethod == "POST") {
    $customerList = addUser();
    echo $customerList;
}elseif($requestMethod =='DELETE'){
    $customerList = deleteUser();
} else {
    $data = [
        'status' => 405,
        'message'=> $requestMethod. ' request method not allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
?>
