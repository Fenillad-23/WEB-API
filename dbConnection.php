<?php
global $con;
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "webtechnologies";
    try {
        
        
        $con = new mysqli($host, $username, $password, $dbname);
        if(!$con){
            die("".mysqli_error($con));
        }else{
            echo "connection established";
        }
    } catch (mysqli_sql_exception $e) {
        echo "". $e->getMessage() ."";
    }
?>