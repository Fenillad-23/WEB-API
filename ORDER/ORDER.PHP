<?php
require ('../dbConnection.php');
include('apis.php'); 
$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod == "GET") {
    $OrderDetail = getOrderDetail();
     echo $OrderDetail;
} elseif ($requestMethod == "POST") {
    $OrderDetail = addOrder();
     
}
elseif($requestMethod =="DELETE"){
       $OrderDetail = deleteOrder();

} elseif($requestMethod=="PUT"){
   $OrderDetail = updateOrderRecord();

}
else {
    $data = [
        'status' => 405,
        'message'=> $requestMethod. ' request method not allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
