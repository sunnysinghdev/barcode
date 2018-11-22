<?php
include '../db.php';
// header('Access-Control-Allow-Origin', 'http://l503025.emea.epicor.net:4200');
// header('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization');
// header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
// header('Content-Type: application/json');

header('Access-Control-Allow-Origin: *'); 
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');
session_start();

//$db = new mysqli($servername,$username,$password,$database);
if(!$db)
{
    echo json_encode(array('error' => "Connection failed: " . mysql_connect_error()));
}
if($_SERVER['REQUEST_METHOD'] == "POST")
{
    $data = json_decode(file_get_contents('php://input'), true);
    if(isset($data["user_id"])){
        $sql = "UPDATE barcode_session SET value ='" . $data["code"] . "' , updated = CURRENT_TIMESTAMP where user_id='" . $data["user_id"] ."'";
        $result = $db->query($sql);
        if($result != null && $result == true) {
            echo json_encode(array('user_id' => $data["user_id"]));
        }
        else {
            echo json_encode(array('error' => mysqli_error($db)));
        }
    }else{
        echo json_encode(array('error' => 'session not active'));
    }
    
}

if($_SERVER['REQUEST_METHOD'] == "GET")
{
    
    if(isset($_GET["action"]) && isset($_GET["user_id"])){
    if($_GET["action"] == 'create' ){
        $sql = "insert into barcode_session (user_id) value('" . $_GET["user_id"] . "')";
        $result = $db->query($sql);
        if($result != null && $result == true) {
            echo json_encode(array('success' => 'User created ' . $_GET["user_id"]));
        }
        else
        {
            echo json_encode(array('error' => mysqli_error($db)));
        }
    }
    elseif($_GET["action"] == "code"){
            $sql = "SELECT value, unix_timestamp(updated) as refresh_id FROM barcode_session WHERE user_id='" . $_GET["user_id"] . "'";
            $result = $db->query($sql);
            $record = array();
            if($result != null && $result == true) {
                while($row = $result->fetch_assoc()) {
                    $record["value"] = $row["value"] == null ? '' : $row["value"] ;
                    $record["refresh_id"] = $row["refresh_id"];
                    }
                echo json_encode($record);
            }
            else
            {
                echo json_encode(array('error' => mysqli_error($db)));
            }
        }else{
            echo json_encode(array('error' => 'session not active'));
        }
            
    }
}
    
?>