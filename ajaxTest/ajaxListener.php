<?php

//LISTENER FOR DEMO

header('Content-Type: application/json');
$messageBack = "";
$messageResult = $_POST['msg'];

$messageBack = "Hey! I'm the listener, you said: ".$messageResult;

$encoderArray = array('success' => 'true', 'message' => $messageBack);
echo json_encode($encoderArray);
?>