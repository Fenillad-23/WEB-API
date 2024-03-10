<?php
function getUserList()
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

    $query = $con->prepare('SELECT * FROM user');
    if ($query) {
        if ($query->execute()) {
            $users = $query->get_result();
    
            if ($users->num_rows > 0) {
                $res = $users->fetch_all(MYSQLI_ASSOC);
                $data = [
                    'status' => 200,
                    'message' => 'user List Fetched Successfully',
                    'data' => $res
                ];
                header("HTTP/1.0 200 Ok");
                return json_encode($data);
            } else {
                $data = [
                    'status' => 404,
                    'message' => 'No users found',
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

function addUser()
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

        if (isset($_POST['uEmail'], $_POST['uPassword'], $_POST['uUsername'], $_POST['uPurchasehistory'], $_POST['uShippingAddress'])) {
            $email = htmlspecialchars($_POST['uEmail']);
            $username = htmlspecialchars($_POST['uUsername']);
            $password = htmlspecialchars($_POST['uPassword']);
            $purchaseHistory = htmlspecialchars($_POST['uPurchasehistory']);
            $shippingAddress = htmlspecialchars($_POST['uShippingAddress']);

            if (empty($username) || empty($email) || empty($password) || empty($purchaseHistory) || empty($shippingAddress)) {
                http_response_code(400);
                echo json_encode(array('error' => 'Please fill in every detail'));
                exit();
            } else {
                $query = $con->prepare("INSERT INTO user (uEmail, uPassword, uUsername,uPurchasehistory,uShippingAddress) VALUES (?, ?, ?,?,?)");
                if ($query) {
                    $query->bind_param('sssss', $email, $password, $username, $purchaseHistory, $shippingAddress);

                    if ($query->execute()) {
                        $response = array('status' => 'success', 'message' => 'user added successfully');
                        echo json_encode($response);
                    } else {
                        http_response_code(500);
                        $response = array('status' => 'error', 'message' => 'Problem while adding user: ' . $con->error);
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

function updateUser(){
    global $con;
    parse_str(file_get_contents("php://input"), $_PUT);
    if(!$con){
        $data = ['status' => 500,'message'=> 'Database connection error'];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
        exit();
    }else{
        if(isset($_PUT['id']) && !empty($_PUT['id'])){
            $uid = $_PUT['id'];
            $email = $_PUT['uEmail'];
            $username = $_PUT['uUsername'];
            $shippingAddress = $_PUT['uShippingAddress'];
            $query = $con->prepare('UPDATE user SET uEmail=?, uUsername=?,uShippingAddress=? where uid=?');
            if($query){
                $query->bind_param('ssss', $email, $username,$shippingAddress,$uid);
                if($query->execute()){
                    $data = array('status' => '200', 'message' => 'user info successfully updated');
                    echo json_encode($data);
                }else {
                    http_response_code(500);
                    $response = array('status' => 'error', 'message' => 'Problem while updating userinfo: ' . $con->error);
                    echo json_encode($response);
                }
                $query->close();
            }
            $con->close();
        }else{
            http_response_code(400);
            echo json_encode(array('error' => 'Please provide user id to update record'));
        }
    }

}
function deleteUser()
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
        if (isset($_DELETE["id"]) || !empty($_DELETE["id"])) {
            $uid = $_DELETE["id"];
            $query = $con->prepare("DELETE FROM user where uid=?");
            if ($query) {
                $query->bind_param("s", $uid);
                if ($query->execute()) {
                    $data = array('status' => '200', 'message' => 'user removed successfully');
                    echo json_encode($data);
                } else {
                    http_response_code(500);
                    $response = array('status' => 'error', 'message' => 'Problem while removing user: ' . $con->error);
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
