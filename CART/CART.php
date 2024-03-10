<?php
require ('../dbConnection.php');
include('apis.php'); 
$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod == "GET") {
    $cartProductList = getCartProductList();
    echo $cartProductList;
} elseif ($requestMethod == "POST") {
    // $productList = addProduct();
    // echo $productList;
}
elseif($requestMethod =="DELETE"){
     $productList = deleteProduct();
    // echo $productList;
} elseif($requestMethod=="PUT"){
   // $productList = updateProduct();
   // echo $productList;
}
else {
    $data = [
        'status' => 405,
        'message'=> $requestMethod. ' request method not allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
