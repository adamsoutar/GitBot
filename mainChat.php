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
function loadHistoryToText() {
    $handle = fopen("./users/history/".$_GET['token'].".txt", "r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            // process the line read.
            switch ($line[0]) {
                case "0":
                    //Message is from the bot
                    displayBotMessage(substr($line, 1, strlen($line) - 1));
                    break;
                case "1":
                    //Message is form the user
                    displayUsrMessage(substr($line, 1, strlen($line) - 1));
                    break;
            }
        }

        fclose($handle);
    } else {
        // error opening the file.
        displayBotMessage("ERROR LOADING HISTORY! Did you mess with the F12 key again? :)");
    } 
}
$messageNumber = 0;
$GLOBALS['msgnum'] = $messageNumber;
function displayBotMessage($msg) {
    //TODO: Style this
    $messageNumber = $GLOBALS['msgnum'];
    $showTime = 200 * $messageNumber;
    echo "<span class='message' id='".$messageNumber."'><b>GitBot: </b>".$msg."<br /><br /></span><script type='text/javascript'>setTimeout(function(){ document.getElementById('".$messageNumber."').className = 'message show'; }, ".$showTime.");</script>";
    $GLOBALS['msgnum'] = $GLOBALS['msgnum'] + 1;
}
function displayUsrMessage($msg) {
    //TODO: Style this
    $messageNumber = $GLOBALS['msgnum'];
    $showTime = 200 * $messageNumber;
    echo "<span class='message' id='".$messageNumber."'><b>User: </b>".$msg."<br /><br /></span><script type='text/javascript'>setTimeout(function(){ document.getElementById('".$messageNumber."').className = 'message show'; }, ".$showTime.");</script>";
    $GLOBALS['msgnum'] = $GLOBALS['msgnum'] + 1;
}
function sendBotMessageWithHistory($sendStr) {
    displayBotMessage($sendStr);
    //0 for bot messages,
    //1 for user messages.
    addToHistory("0".$sendStr."\n");
}
function addToHistory($addString) {
    //Write the string
    $hisFile = fopen("./users/history/".$_GET['token'].".txt", "a");
    fwrite($hisFile, $addString);
    fclose($hisFile);
}

//Main GitBot code
$userToken = $_GET['token'];
//Read the current stage
$stageFile = fopen("./users/stages/".$userToken.".txt", "r");
$currentStage = fread($stageFile, filesize("./users/stages/".$userToken.".txt"));
fclose($stageFile);
printToChromeConsole("Stage: ".$currentStage);

//Display message history so far
loadHistoryToText();

switch ($currentStage) {
    case "0":
        //Send message for stage 0
        sendBotMessageWithHistory("Hey! I'm GitBot, I can help you navigate GitHub.");
        sendBotMessageWithHistory("First thing's first, are you looking for a user or organisation?");
        break;
    case "1":
        //They responded with user
        sendBotMessageWithHistory("Cool. Which user are you looking for? You don't have to be precise.");
        break;
    case "2":
        //They responded with org
        sendBotMessageWithHistory("Awesome. Which organisation are you looking for? You don't have to be precise.");
        break;
    case "3":
        //Ask for a specific user ID
        sendBotMessageWithHistory("If you give me a specific GitHub user ID, I can give you details on that user.");
        sendBotMessageWithHistory("Which ID would you like more details on?");
        break;
    case "4":
        //After the details view
        sendBotMessageWithHistory("Next, would you like to view the user's repos, get details on someone else, or go back to the start?");
        break;
    default:
        //Something went wrong, GitBot doesn't know what to do
        sendBotMessageWithHistory("Eeek! GitBot broke and doesn't know what to do! Perhaps... if you refresh the page...");
        sendBotMessageWithHistory("It can't get any worse, can it?");
        break;
}
?>

</font></p></center>
<script type="text/javascript">
var UserToken = <?php echo $_GET['token']; ?>
</script>
</body>
</html>