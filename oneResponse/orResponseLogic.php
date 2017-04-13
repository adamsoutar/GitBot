<?php

//Main GitBot response generation code

//Read the current stage
$stageFile = fopen("./users/stages/".$userToken.".txt", "r");
$currentStage = fread($stageFile, filesize("./users/stages/".$userToken.".txt"));
fclose($stageFile);
printToChromeConsole("Stage: ".$currentStage);

function moreToResponse($addStr) {
    $GLOBALS['response'] = $GLOBALS['response'].$addStr."<br />";
}

switch ($currentStage) {
    case "0":
        //Don't run this
        break;
    case "1":
        //They responded with user
        moreToResponse("Cool. Which user are you looking for? You don't have to be precise.");
        break;
    case "2":
        //They responded with org
        moreToResponse("Awesome. Which organisation are you looking for? You don't have to be precise.");
        break;
    case "3":
        //Ask for a specific user ID
        moreToResponse("If you give me a specific GitHub user ID, I can give you details on that user.");
        moreToResponse("Which ID would you like more details on?");
        break;
    case "4":
        //After the details view
        moreToResponse("Next, would you like to view the user's repos, get details on someone else, or go back to the start?");
        break;
    default:
        //Something went wrong, GitBot doesn't know what to do
        moreToResponse("Eeek! GitBot broke and doesn't know what to do! Perhaps... if you refresh the page...");
        moreToResponse("It can't get any worse, can it?");
        break;
}

?>