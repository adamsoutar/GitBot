<?php
//ONE RESPONSE ANSWER INTERPRETER
//New version which responds is JSON

$GLOBALS['response'] = "";

function setStageNum($newStage) {
    $stageFile = fopen("./users/stages/".$_POST['token'].".txt", "w");
    fwrite($stageFile, $newStage);
    fclose($stageFile);
}
function addToResponse($addString) {
    //Write the string
    $GLOBALS['response'] = $GLOBALS['response'].$addString."<br />";
}

//cURL - Important for getting responses from the GitHub API
//Returns JSON as string lumps which require decoding
function basicCurl($curlURL) {
    $url = $curlURL;
    $cURL = curl_init();
    curl_setopt($cURL, CURLOPT_URL, $url);
    curl_setopt($cURL, CURLOPT_HTTPGET, true);
    curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Accept: application/json'
    ));
    curl_setopt($cURL, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($cURL, CURLOPT_USERAGENT, "spider");
    $result = curl_exec($cURL);
    
if ($result === FALSE) {
    return "cURL Error: " . curl_error($cURL);
} else {
	return $result;
}
    curl_close($cURL);
    
}
//Functions specific to stages in conversation
function searchForUser($searchString) {
    $GitHubResponse = basicCurl("https://api.github.com/search/users?q=".$searchString);
    $jsonResult = json_decode($GitHubResponse, true);
    $returnString = "You searched for ".$searchString.", I found: <br />";
    $itemsArray = $jsonResult['items'];
    foreach ($itemsArray as $currentItem) {
        $returnString = $returnString.$currentItem['login']."<br />";
    }
    return $returnString;
}
function detailsForUser($searchString) {
    $GitHubResponse = basicCurl("https://api.github.com/users/".$searchString);
    $jsonResult = json_decode($GitHubResponse, true);
    if ($jsonResult['message']!="Not Found") {
        $returnString = "Here are the details for the user ".$searchString.": <br />";
        $returnString = $returnString."<i>Login:</i> ".$jsonResult['login']."<br /><i>Real name:</i> ".$jsonResult['name']."<br /><i>GitHub url:</i> <a href='".$jsonResult['html_url']."'>".$jsonResult['html_url']."</a><br /><i>User Bio:</i><br />".$jsonResult['bio'];
        return $returnString;
    } else {
        return "The user couldn't be found! :(";
    }    
}
function detailsForRepo($searchString) {
    $GitHubResponse = basicCurl("https://api.github.com/repos/".$searchString);
    $jsonResult = json_decode($GitHubResponse, true);
    if ($jsonResult['message']!="Not Found") {
        $returnString = "Here are the details for the repo ".$searchString.": <br />";
        $returnString = $returnString."<b> ".$jsonResult['name']."</b><br /><i>Owner:</i> ".$jsonResult['owner']['login']."<br /><i>GitHub url:</i> <a href='".$jsonResult['html_url']."'>".$jsonResult['html_url']."</a><br /><i>Description: </i><br />".$jsonResult['description']."<br /><i>Language: </i>".$jsonResult['language']."<br /><i>Watchers: </i>".$jsonResult['watchers_count']."<br /><i>Open issues: </i>".$jsonResult['open_issues_count']."<br /><i>Main branch: </i>".$jsonResult['default_branch']."<br /><i>Subscribers: </i>".$jsonResult['subscribers_count'];
        return $returnString;
    } else {
        return "That repo couldn't be found! :(";
    }    
}
function listUserRepos($userName) {
    $GitHubResponse = basicCurl("https://api.github.com/users/".$userName."/repos");
    $jsonResult = json_decode($GitHubResponse, true);
    if ($jsonResult['message']!="Not Found") {
        $returnString = "Here are the repos for the user ".$searchString.": <br />";
        foreach ($jsonResult as $currentItem) {
            $returnString = $returnString."<br /><b>".$currentItem['name']."</b><br /><i>Repo ID: </i>".$currentItem['full_name']."<br /><i>Description:</i> ".$currentItem['description']."<br /><i>Language: </i>".$currentItem['language']."<br /><i>Repo link: </i> <a href='".$currentItem['html_url']."'>".$currentItem['html_url']."</a><br />";
        }
        return $returnString;
    } else {
        return "An error occurred. :(";
    }    
}
function searchForRepos($searchString) {
    $GitHubResponse = basicCurl("https://api.github.com/search/repositories?q=".$searchString);
    $jsonResult = json_decode($GitHubResponse, true);
    $returnString = "You searched for ".$searchString.", I found: <br />";
    $itemsArray = $jsonResult['items'];
    foreach ($itemsArray as $currentItem) {
        $returnString = $returnString.$currentItem['full_name']."<br />";
    }
    return $returnString;
}

//Read the current stage
$stageFile = fopen("./users/stages/".$userToken.".txt", "r");
$currentStage = fread($stageFile, filesize("./users/stages/".$userToken.".txt"));
fclose($stageFile);

$userMessage = $_POST['msg'];

switch ($currentStage) {
    case "-1" :
        //RESET THE CHAT
        //TODO: Implement this
    case "0":
        //We're looking for either User or Organisation
        if (strpos(strtolower($userMessage), 'user') !== false) {
            //If the string contains user
            setStageNum("1");
        } elseif (strpos(strtolower($userMessage), 'repo') !== false) {
            //If the string contains org
            setStageNum("2");
        } else {
            addToResponse("Sorry, I didn't understand the answer you gave. Let's try again.");
            //Keep the stage number at 0 so it repeats the phrase
        }
        break;
    case "1":
        //They're searching for a user
        $searchResponse = searchForUser($userMessage);
        addToResponse($searchResponse);
        //TODO: Skip straight to details if there was only 1 result for the search
        setStageNum("3");
        break;
    case "2":
        //They're searching for a repo
        $searchResponse3 = searchForRepos($userMessage);
        addToResponse($searchResponse3);
        //TODO: Skip straight to details if there was only 1 result for the search
        setStageNum("5");
        break;
    case "3":
        //They've told us a specific user
        //TODO: Execute this in a function
        $searchResponse2 = detailsForUser($userMessage);
        addToResponse($searchResponse2);
        if ($searchResponse2!="The user couldn't be found! :(") {
            //Cache the user we're looking at
            setcookie("userCache", $userMessage, time() + (86400 * 2), "/"); // 86400 = 1 day
            setStageNum("4");
        } else {
            //We didn't find them, don't change stage
            addToResponse("Please try another user.");
        }
        break;
    case "4":
        //They have responded with REPO, SOMEONE ELSE, or START
        if (strpos(strtolower($userMessage), 'repo') !== false) {
            //If the string contains REPO
            $searchResponse3 = listUserRepos($_COOKIE['userCache']);
            addToResponse($searchResponse3);
            if ($searchResponse3!="An error occurred. :(") {
                setStageNum("5");
            } else {
                //We didn't find them, don't change stage
                addToResponse("Please try another user.");
            }
        } elseif (strpos(strtolower($userMessage), 'else') !== false) {
            //If the string contains SOMEONE ELSE
            setStageNum("3");
        } elseif (strpos(strtolower($userMessage), 'detail') !== false) {
            //If the string contains SOMEONE ELSE
            setStageNum("3");
        } elseif (strpos(strtolower($userMessage), 'start') !== false) {
            //RESET THE CHAT
            //TODO: Make this clear the screen
            setStageNum("0");
        } else {
            addToResponse("Sorry, I didn't understand the answer you gave. Let's try again.");
            //Keep the stage number at 4 so it repeats the phrase
        }
        break;
    case "5":
        //They're getting details on a repo
        $searchResponse2 = detailsForRepo($userMessage);
        addToResponse($searchResponse2);
        if ($searchResponse2!="That repo couldn't be found! :(") {
            setStageNum("6");
        } else {
            //We didn't find them, don't change stage
            addToResponse("Please ensure the repo name is in the format 'username/repo'.");
        }
        break;
    case "6";
        //Details on another repo or back to the start?
        if (strpos(strtolower($userMessage), 'repo') !== false) {
            //Details on another repo
            setStageNum("5");
        } elseif (strpos(strtolower($userMessage), 'detail') !== false) {
            //Another repo
            setStageNum("5");
        } elseif (strpos(strtolower($userMessage), 'start') !== false) {
            //RESET THE CHAT
            //TODO: Make this clear the screen
            setStageNum("0");
        } else {
            addToResponse("Sorry, I didn't understand the answer you gave. Let's try again.");
            //Keep the stage number at 4 so it repeats the phrase
        }
        break;
    default:
        //Handling in case of an unexpected scenario
        addToResponse("Ouch! There was a backend error in processing your response. Please refresh your browser.");
        addToResponse("Or... y'know... you could just hang with me here.");
        break;
}

?>