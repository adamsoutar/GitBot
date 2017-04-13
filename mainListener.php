<?php

//GitBot's main listener file
//AJAX requests are sent here

//This file should be POSTed to with:
//token = User token
//msg = User message

header('Content-Type: application/json');

$userToken = $_POST['token'];

function printToChromeConsole($debugMsg) {
    //echo "<script type='text/javascript'>console.log('".$debugMsg."')</script>";
}
printToChromeConsole("Processing message from user with token: ".$userToken);

//Run through logic calculations
include "orAnswerInterpreter.php";
include "orResponseLogic.php";

//Encode and send response
$logicAnswer = $GLOBALS['response'];
$encoderArray = array('success' => 'true', 'response' => $logicAnswer);
echo json_encode($encoderArray);
?>