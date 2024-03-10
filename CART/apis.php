<?php
// Function to fetch the list of products in the cart
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

    // SQL query to retrieve cart product information along with product description and user username
    $query = "SELECT cart.cartId, cart.Products, product.pDesc, cart.quantities, cart.uId, user.uUsername 
              FROM cart 
              INNER JOIN product ON cart.Products = product.pId 
              INNER JOIN user ON cart.uId = user.uid";

    // Execute the SQL query
    $result = $con->query($query);

    if ($result) {
        if ($result->num_rows > 0) {
            $cartProductList = [];

            // Fetch the result set and store it in an array
            while ($row = $result->fetch_assoc()) {
                $cartProductList[] = $row;
            }

            // Prepare the response data with fetched cart product list
            $data = [
                'status' => 200,
                'message' => 'Cart product list fetched successfully',
                'data' => $cartProductList
            ];
            header("HTTP/1.0 200 Ok");
            return json_encode($data);
        } else {
            // Return a 404 status code if no cart products are found
            $data = [
                'status' => 404,
                'message' => 'No cart products found in cart',
            ];
            header("HTTP/1.0 404 Not Found");
            return json_encode($data);
        }
    } else {
        // Return a 500 status code if there's an error executing the query
        $data = [
            'status' => 500,
            'message' => 'Query execution error: ' . $con->error,
        ];
        header("HTTP/1.0 500 Internal Server Error");
        return json_encode($data);
    }
}

// Function to update the quantity of a product in the cart
function updateCartProduct()
{
    global $con;
    parse_str(file_get_contents("php://input"), $_PUT);
    if(!$con){
        $data = ['status' => 500,'message'=> 'Database connection error'];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
        exit();
    }else{
        if(isset($_PUT['cartId']) && !empty($_PUT['cartId'])){
            
          // Extracting cart ID and updated quantity from the request
          $cid = $_PUT['cartId'];
          $cartProductQuantity = $_PUT['quantities'];
            $query = $con->prepare('UPDATE cart SET quantities=? where cartId=?');
            if($query){
                // Binding parameters to the prepared statement
                $query->bind_param('ss',$cartProductQuantity,$cid );
                if($query->execute()){
                    $data = array('status' => '200', 'message' => 'cart info successfully updated');
                    echo json_encode($data);
                }else {
                    // Return a 500 status code if there's an error executing the query
                    http_response_code(500);
                    $response = array('status' => 'error', 'message' => 'Problem while updating cart info: ' . $con->error);
                    echo json_encode($response);
                }
                $query->close();
            }
            $con->close();
        }else{
            // Return a 400 status code if cart ID is not provided in the request
            http_response_code(400);
            echo json_encode(array('error' => 'Please provide cart id to update record'));
        }
    }

}

// Function to add a product to the cart
function addCartProduct()
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
        if (isset($_POST['Products'], $_POST['quantities'], $_POST['uId'])) {
            // Extracting product ID, quantity, and user ID from the request
            $Product = htmlspecialchars($_POST['Products']);
            $quantities = htmlspecialchars($_POST['quantities']);
            $uId = htmlspecialchars($_POST['uId']);

            if (empty($Product) || empty($quantities) || empty($uId)) {
                // Return a 400 status code if any required parameter is missing
                http_response_code(400);
                echo json_encode(array('error' => 'Please fill in every detail'));
                exit();
            } else {
                // Prepare and execute the SQL query to insert a new record into the cart table
                $query = $con->prepare("INSERT INTO cart (Products, quantities, uId) VALUES (?, ?, ?)");
                if ($query) {
                    // Binding parameters to the prepared statement
                    $query->bind_param('iii', $Product, $quantities, $uId); // Assuming uId is an integer, adjust 'sii' if needed
                    if ($query->execute()) {
                        // Return success message if the query execution is successful
                        $response = array('status' => 'success', 'message' => 'Product added to cart successfully');
                        echo json_encode($response);
                    } else {
                        // Return a 500 status code if there's an error executing the query
                        http_response_code(500);
                        $response = array('status' => 'error', 'message' => 'Problem while adding product to cart: ' . $con->error);
                        echo json_encode($response);
                    }
                    $query->close();
                } else {
                    // Return a 500 status code if there's an error preparing the query
                    http_response_code(500);
                    $response = array('status' => 'error', 'message' => 'Problem preparing query: ' . $con->error);
                    echo json_encode($response);
                }
                $con->close();
            }
        } else {
            // Return a 400 status code if any required parameter is missing in the request
            http_response_code(400);
            echo json_encode(array('error' => 'Please provide all required data'));
            exit();
        }
    }
}

// Function to delete a product from the cart
function deleteCartProduct()
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
            // Extracting cart ID from the request
            $cId = $_DELETE["cartId"];
            // Prepare and execute the SQL query to delete a record from the cart table
            $query = $con->prepare("DELETE FROM cart where cartId=?");
            if ($query) {
                // Binding cart ID as parameter to the prepared statement
                $query->bind_param("s", $cId);
                if ($query->execute()) {
                    // Return success message if the query execution is successful
                    $data = array('status' => '200', 'message' => 'cart product removed successfully');
                    echo json_encode($data);
                } else {
                    // Return a 500 status code if there's an error executing the query
                    http_response_code(500);
                    $response = array('status' => 'error', 'message' => 'Problem while removing cart product : ' . $con->error);
                    echo json_encode($response);
                }
                $query->close();
            }
            $con->close();
        } else {
            // Return a 400 status code if cart ID is not provided in the request
            http_response_code(400);
            echo json_encode(array('error' => 'Please provide user id'));
        }
    }
}
