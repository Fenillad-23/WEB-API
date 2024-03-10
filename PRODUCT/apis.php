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
    }
    else {
        $data = [
            'status' => 500,
            'message' => 'Query execution error: ' . mysqli_error($con),
        ];
        header("HTTP/1.0 500 Internal Server Error");
        return json_encode($data);
    }
}

