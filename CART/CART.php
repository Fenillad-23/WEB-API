<?php
require ('../dbConnection.php');
include('apis.php'); 
$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod == "GET") {
    $cartProductList = getCartProductList();
    echo $cartProductList;
} elseif ($requestMethod == "POST") {
     $cartProductList = addCartProduct();
}
elseif($requestMethod =="DELETE"){
     $cartProductList = deleteCartProduct();

} elseif($requestMethod=="PUT"){
   $cartProductList = updateCartProduct();

}
else {
    $data = [
        'status' => 405,
        'message'=> $requestMethod. ' request method not allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
