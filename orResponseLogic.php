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
        moreToResponse("Are you looking for a user or a repository?");
        break;
    case "1":
        //They responded with user
        moreToResponse("Cool. Which user are you looking for? You can be vague to start a search.");
        break;
    case "2":
        //They responded with org
        moreToResponse("Awesome. Which repo are you looking for?");
        moreToResponse("You can be more vague to initiate a search, or be more specific with 'username/repo'.");
        break;
    case "3":
        //Ask for a specific user ID
        moreToResponse("If you give me a specific GitHub user ID, I can give you details on that user.");
        moreToResponse("Which ID would you like more details on?");
        break;
    case "4":
        //After the details view
        moreToResponse("<br />Next, would you like to view the user's repos, get details on someone else, or go back to the start?");
        break;
    case "5":
        //Ask for a repo ID
        moreToResponse("If you give me a specific repo ID (username/repo) I can give details about that repo.");
        moreToResponse("Which repo would you like to know more about?");
        break;
    case "6":
        //We just got details for a repo
        moreToResponse("<br />Now, would you like to get details on a different repo, or go back to the start?");
        break;
    default:
        //Something went wrong, GitBot doesn't know what to do
        moreToResponse("Eeek! GitBot broke and doesn't know what to do! Perhaps... if you refresh the page...");
        moreToResponse("It can't get any worse, can it?");
        break;
}

?>