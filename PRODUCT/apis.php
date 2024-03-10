<?php
function getProductList()
{
    global $con;

    if (!$con) {
        $data = [
            'status' => 500,
            'message' => 'Database connection error',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        return json_encode($data);
    }

    $query = $con->prepare('SELECT * FROM product');
    if ($query) {
        if ($query->execute()) {
            $product = $query->get_result();

            if ($product->num_rows > 0) {
                $res = $product->fetch_all(MYSQLI_ASSOC);
                $data = [
                    'status' => 200,
                    'message' => 'Product List Fetched Successfully',
                    'data' => $res
                ];
                header("HTTP/1.0 200 Ok");
                return json_encode($data);
            } else {
                $data = [
                    'status' => 404,
                    'message' => 'No products found',
                ];
                header("HTTP/1.0 404 Not Found");
                return json_encode($data);
            }
        }
    } else {
        $data = [
            'status' => 500,
            'message' => 'Query execution error: ' . mysqli_error($con),
        ];
        header("HTTP/1.0 500 Internal Server Error");
        return json_encode($data);
    }
}

function addProduct()
{
    global $con;

    if (!$con) {
        $data = [
            'status' => 500,
            'message' => 'Database connection error',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
        exit();
    } else {
        if (isset($_POST['pDesc'], $_POST['pPrice'], $_POST['pImg'], $_POST['pShippingCost'])) {
            $pDesc = htmlspecialchars($_POST['pDesc']);
            $pPrice = htmlspecialchars($_POST['pPrice']);
            $pImg = htmlspecialchars($_POST['pImg']);
            $pShippingCost = htmlspecialchars($_POST['pShippingCost']);
            if (empty($pDesc) || empty($pPrice) || empty($pImg) || empty($pShippingCost)) {
                http_response_code(400);
                echo json_encode(array('error' => 'Please fill in every detail'));
                exit();
            } else {
                $query = $con->prepare("INSERT INTO product (pDesc, pPrice,pImg,pShipingCost) VALUES (?, ?,?,?)");
                if ($query) {
                    $query->bind_param('ssss', $pDesc, $pPrice, $pImg, $pShippingCost);
                    if ($query->execute()) {
                        $response = array('status' => 'success', 'message' => 'product added successfully');
                        echo json_encode($response);
                    } else {
                        http_response_code(500);
                        $response = array('status' => 'error', 'message' => 'problem while adding product: ' . $con->error);
                        echo json_encode($response);
                    }
                    $query->close();
                } else {
                    http_response_code(500);
                    $response = array('status' => 'error', 'message' => 'Problem preparing query: ' . $con->error);
                    echo json_encode($response);
                }
                $con->close();
            }
        } else {

            http_response_code(400);
            echo json_encode(array('error' => 'Please provide all required data'));
            exit();
        }
    }
}

function updateProduct(){
    global $con;
    parse_str(file_get_contents("php://input"), $_PUT);
    if(!$con){
        $data = ['status' => 500,'message'=> 'Database connection error'];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
        exit();
    }else{
        if(isset($_PUT['pId']) && !empty($_PUT['pId'])){
            $pId= intval($_PUT["pId"]);
            $pDesc = htmlspecialchars($_PUT['pDesc']);
            $pPrice = htmlspecialchars($_PUT['pPrice']);
            $pImg = htmlspecialchars($_PUT['pImg']);
            
            $pShippingCost = htmlspecialchars($_PUT['pShippingCost']); 
            $query = $con->prepare('UPDATE product SET pDesc=?, pPrice=?,pImg=?,pShipingCost=? where pId=?');
            if($query){
                $query->bind_param('sssss', $pDesc,$pPrice,$pImg,$pShippingCost,$pId);
                if($query->execute()){
                    $data = array('status' => '200', 'message' => 'product info successfully updated');
                    echo json_encode($data);
                }else {
                    http_response_code(500);
                    $response = array('status' => 'error', 'message' => 'Problem while updating productinfo: ' . $con->error);
                    echo json_encode($response);
                }
                $query->close();
            }
            $con->close();
        }else{
            http_response_code(400);
            echo json_encode(array('error' => 'Please provide product id to update record'));
        }
    }

}
function deleteProduct()
{
    global $con;
    parse_str(file_get_contents("php://input"), $_DELETE);
    if (!$con) {
        $data = [
            'status' => 500,
            'message' => 'Database connection error',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
        exit();
    } else {
        if (isset($_DELETE["pId"]) || !empty($_DELETE["pId"])) {
            $pid = $_DELETE["pId"];
            $query = $con->prepare("DELETE FROM product where pId=?");
            if ($query) {
                $query->bind_param("s", $pid);
                if ($query->execute()) {
                    $data = array('status' => '200', 'message' => 'product removed successfully');
                    echo json_encode($data);
                } else {
                    http_response_code(500);
                    $response = array('status' => 'error', 'message' => 'Problem while adding user: ' . $con->error);
                    echo json_encode($response);
                }
                $query->close();
            }
            $con->close();
        } else {
            http_response_code(400);
            echo json_encode(array('error' => 'Please provide user id'));
        }
    }
}
