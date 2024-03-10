<?php
function getCartProductList()
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

    $query = "SELECT cart.cartId, cart.Products, product.pDesc, cart.quantities, cart.uId, user.uUsername 
              FROM cart 
              INNER JOIN product ON cart.Products = product.pId 
              INNER JOIN user ON cart.uId = user.uid";

    $result = $con->query($query);

    if ($result) {
        if ($result->num_rows > 0) {
            $cartProductList = [];

            while ($row = $result->fetch_assoc()) {
                $cartProductList[] = $row;
            }

            $data = [
                'status' => 200,
                'message' => 'Cart product list fetched successfully',
                'data' => $cartProductList
            ];
            header("HTTP/1.0 200 Ok");
            return json_encode($data);
        } else {
            $data = [
                'status' => 404,
                'message' => 'No cart products found in cart',
            ];
            header("HTTP/1.0 404 Not Found");
            return json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'message' => 'Query execution error: ' . $con->error,
        ];
        header("HTTP/1.0 500 Internal Server Error");
        return json_encode($data);
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
        if (isset($_DELETE["cartId"]) || !empty($_DELETE["cartId"])) {
            $cId = $_DELETE["cartId"];
            $query = $con->prepare("DELETE FROM cart where cartId=?");
            if ($query) {
                $query->bind_param("s", $cId);
                if ($query->execute()) {
                    $data = array('status' => '200', 'message' => 'cart product removed successfully');
                    echo json_encode($data);
                } else {
                    http_response_code(500);
                    $response = array('status' => 'error', 'message' => 'Problem while removing cart product : ' . $con->error);
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

