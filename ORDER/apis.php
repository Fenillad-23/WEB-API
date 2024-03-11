<?php 

function getOrderDetail()
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

    $query = $con->prepare('SELECT * FROM customerorder');
    if ($query) {
        if ($query->execute()) {
            $result = $query->get_result();

            if ($result->num_rows > 0) {
                $res = $result->fetch_all(MYSQLI_ASSOC);
                $data = [
                    'status' => 200,
                    'message' => 'Order Record List Fetched Successfully',
                    'data' => $res
                ];
                header("HTTP/1.0 200 Ok");
                return json_encode($data);
            } else {
                $data = [
                    'status' => 404,
                    'message' => 'No order found',
                ];
                header("HTTP/1.0 404 Not Found");
                return json_encode($data);
            }
        } else {
            $data = [
                'status' => 500,
                'message' => 'Query execution error: ' . $query->error,
            ];
            header("HTTP/1.0 500 Internal Server Error");
            return json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'message' => 'Query preparation error: ' . $con->error,
        ];
        header("HTTP/1.0 500 Internal Server Error");
        return json_encode($data);
    }
}
function addOrder()
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
        // Decode JSON data
        $postData = json_decode(file_get_contents("php://input"), true);
        
        // Extract data from JSON
        $orderRecord = htmlspecialchars($_POST['oRec']);
    
        // Validate data
        if (empty($orderRecord)) {
            http_response_code(400);    
            echo json_encode(array('error' => 'Please fill in every detail'));
            exit();
        } else {
            // Prepare and execute the query
            $query = $con->prepare("INSERT INTO customerorder (recordingOfSale) VALUES (?)");
            if ($query) {
                $query->bind_param('s',$orderRecord); 
                if ($query->execute()) {
                    $response = array('status' => 'success', 'message' => 'customer order recorded successfully');
                    echo json_encode($response);
                } else {
                    http_response_code(500);
                    $response = array('status' => 'error', 'message' => 'Problem while adding comment: ' . $con->error);
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
    }
}

function updateOrderRecord(){
    global $con;
    parse_str(file_get_contents("php://input"), $_PUT);
    if(!$con){
        $data = ['status' => 500,'message'=> 'Database connection error'];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
        exit();
    }else{
        if(isset($_PUT['oId']) && !empty($_PUT['oId'])){
            $oId= $_PUT["oId"];
            $oRec = htmlspecialchars($_PUT['oRec']);
            
            
            
            $query = $con->prepare('UPDATE customerorder SET recordingOfSale=? where oId=?');
            if($query){
                $query->bind_param('si',$oRec,$oId );
                if($query->execute()){
                    $data = array('status' => '200', 'message' => 'order record successfully updated');
                    echo json_encode($data);
                }else {
                    http_response_code(500);
                    $response = array('status' => 'error', 'message' => 'Problem while updating order: ' . $con->error);
                    echo json_encode($response);
                }
                $query->close();
            }
            $con->close();
        }else{
            http_response_code(400);
            echo json_encode(array('error' => 'Please provide order id to update record'));
        }
    }

}
function deleteOrder()
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
        if (isset($_DELETE["oId"]) || !empty($_DELETE["oId"])) {
            $oId= $_DELETE["oId"];
            $query = $con->prepare("DELETE FROM customerorder where oId=?");
            if ($query) {
                $query->bind_param("s", $oId);
                if ($query->execute()) {
                    $data = array('status' => '200', 'message' => 'order record removed successfully');
                    echo json_encode($data);
                } else {
                    http_response_code(500);
                    $response = array('status' => 'error', 'message' => 'Problem while removing order record: ' . $con->error);
                    echo json_encode($response);
                }
                $query->close();
            }
            $con->close();
        } else {
            http_response_code(400);
            echo json_encode(array('error' => 'Please provide order id'));
        }
    }
}
