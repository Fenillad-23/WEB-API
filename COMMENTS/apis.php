<?php 
function getCommentList()
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

    $query = $con->prepare('SELECT * FROM comments');
    if ($query) {
        if ($query->execute()) {
            $result = $query->get_result();

            if ($result->num_rows > 0) {
                $res = $result->fetch_all(MYSQLI_ASSOC);
                echo ''. $result->num_rows .'';;
                echo json_encode($res);
                $data = [
                    'status' => 200,
                    'message' => 'Comment List Fetched Successfully',
                    'data' => $res
                ];
                header("HTTP/1.0 200 Ok");
                return json_encode($data);
            } else {
                $data = [
                    'status' => 404,
                    'message' => 'No Comment found',
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

function addComment()
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
        $pId = htmlspecialchars($postData['pId']);
        $uId = htmlspecialchars($postData['uId']);
        $rating = htmlspecialchars($postData['rating']);
        $images = htmlspecialchars($postData['images']);
        $text = htmlspecialchars($postData['text']);

        // Validate data
        if (empty($pId) || empty($uId) || empty($rating) || empty($text) || empty($images)) {
            http_response_code(400);    
            echo json_encode(array('error' => 'Please fill in every detail'));
            exit();
        } else {
            // Prepare and execute the query
            $query = $con->prepare("INSERT INTO comments (pId, uId, rating, images, text) VALUES (?, ?, ?, ?, ?)");
            if ($query) {
                $query->bind_param('iisss', $pId, $uId, $rating, $images, $text); 
                if ($query->execute()) {
                    $response = array('status' => 'success', 'message' => 'comment added successfully');
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
function updateComment(){
    global $con;
    parse_str(file_get_contents("php://input"), $_PUT);
    if(!$con){
        $data = ['status' => 500,'message'=> 'Database connection error'];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
        exit();
    }else{
        if(isset($_PUT['cmtId']) && !empty($_PUT['cmtId'])){
            $cmtId= intval($_PUT["cmtId"]);
            $rating = htmlspecialchars($_PUT['rating']);
            $images = htmlspecialchars($_PUT['images']);
            $text = htmlspecialchars($_PUT['text']);
            
          
            $query = $con->prepare('UPDATE comments SET rating=?, images=?,text=? where cmtId=?');
            if($query){
                $query->bind_param('sssi', $rating,$images,$text,$cmtId);
                if($query->execute()){
                    $data = array('status' => '200', 'message' => 'comment info successfully updated');
                    echo json_encode($data);
                }else {
                    http_response_code(500);
                    $response = array('status' => 'error', 'message' => 'Problem while updating comment: ' . $con->error);
                    echo json_encode($response);
                }
                $query->close();
            }
            $con->close();
        }else{
            http_response_code(400);
            echo json_encode(array('error' => 'Please provide comment id to update record'));
        }
    }

}
function deleteComment()
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
        if (isset($_DELETE["cmtId"]) && !empty($_DELETE["cmtId"])) {
            $cmtId = $_DELETE["cmtId"];
           
            $query = $con->prepare("DELETE FROM comments WHERE cmtId = ?");
            if ($query) {
                $query->bind_param("i", $cmtId);
                if ($query->execute()) {
                    $data = array('status' => 200, 'message' => 'Comment removed successfully');
                    echo json_encode($data);
                } else {
                    http_response_code(500);
                    $response = array('status' => 'error', 'message' => 'Problem while removing comment: ' . $con->error);
                    echo json_encode($response);
                }
                $query->close();
            } else {
                http_response_code(500);
                $response = array('status' => 'error', 'message' => 'Problem preparing query: ' . $con->error);
                echo json_encode($response);
            }
            $con->close();
        } else {
            http_response_code(400);
            echo json_encode(array('error' => 'Please provide comment id'));
        }
    }
}
