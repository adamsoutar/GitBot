<html>
<head>
<style>
body {
    /*background-color: #bcbcbc;*/
    background-color: #000000;
}
.message {
    opacity: 0;
    transition: opacity 500ms;
}

.message.show {
    opacity: 1;
}
</style>
</head>
<body>
<center><p><font color="#ffffff" face="Arial">
<br />
<?php

//Definition of functions
function printToChromeConsole($debugMsg) {
    echo "<script type='text/javascript'>console.log('".$debugMsg."')</script>";
}
printToChromeConsole("User was assigned ticket: ".$_GET['token']);
$userName = "User";
$messageNumber = 0;
$GLOBALS['msgnum'] = $messageNumber;

//Main GitBot code
$userToken = $_GET['token'];
//Read the current stage
$stageFile = fopen("./users/stages/".$userToken.".txt", "r");
$currentStage = fread($stageFile, filesize("./users/stages/".$userToken.".txt"));
fclose($stageFile);
printToChromeConsole("Stage: ".$currentStage);

//What to send back to the ajax page
$responseToMessage = "";

switch ($currentStage) {
    case "0":
        //Send message for stage 0
        $responseToMessage = "Hey! I'm GitBot, I can help you navigate GitHub.<br />First thing's first, are you looking for a user or organisation?";
        break;
    case "1":
        //They responded with user
        $responseToMessage = "Cool. Which user are you looking for? You don't have to be precise.";
        break;
    case "2":
        //They responded with org
        $responseToMessage = "Awesome. Which organisation are you looking for? You don't have to be precise.";
        break;
    case "3":
        //Ask for a specific user ID
        $responseToMessage = "If you give me a specific GitHub user ID, I can give you details on that user.<br />Which ID would you like more details on?";
        break;
    case "4":
        //After the details view
        $responseToMessage = "Next, would you like to view the user's repos, get details on someone else, or go back to the start?";
        break;
    default:
        //Something went wrong, GitBot doesn't know what to do
        $responseToMessage = "Eeek! GitBot broke and doesn't know what to do! Perhaps... if you refresh the page...<br />It can't get any worse, can it?";
        break;
}
?>

</font></p></center>
<script type="text/javascript">
var UserToken = <?php echo $_GET['token']; ?>
</script>
</body>
</html>