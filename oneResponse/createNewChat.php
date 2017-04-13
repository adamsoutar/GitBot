<?php
//Start new chat with GitBot
//Generate necessary files
$userToken = $_GET['token'];

//Generate stage file
$stageFile = fopen("./users/stages/".$userToken.".txt", "w");
//At the start, we're on stage 0
fwrite($stageFile, "0");
fclose($stageFile);
//Generate history file
$hisFile = fopen("./users/history/".$userToken.".txt", "w");
//At the start, history is empty
fwrite($hisFile, "");
fclose($hisFile);

echo "success";
?>