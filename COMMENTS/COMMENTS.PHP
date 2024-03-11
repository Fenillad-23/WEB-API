<?php
require ('../dbConnection.php');
include('apis.php'); 
$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod == "GET") {
    $commentList = getCommentList();
    // echo $commentList;
} elseif ($requestMethod == "POST") {
     $commentList = addComment();
     
}
elseif($requestMethod =="DELETE"){
      $commentList = deleteComment();

} elseif($requestMethod=="PUT"){
   $commentList = updateComment();

}
else {
    $data = [
        'status' => 405,
        'message'=> $requestMethod. ' request method not allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
