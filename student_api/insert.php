<?php

include 'config.php';

$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];

$sql = "INSERT INTO students(name,email,phone)
VALUES('$name','$email','$phone')";

if($conn->query($sql)){
    echo json_encode([
        "success"=>true,
        "message"=>"Inserted"
    ]);
}else{
    echo json_encode([
        "success"=>false
    ]);
}

?>