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

