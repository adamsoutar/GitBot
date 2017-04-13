<html>
<body>
<?php
//ANSWER INTERPRETER

$GLOBALS['redir'] = "mainChat.php?token=".$_GET['token'];
$userToken = $_GET['token'];

function setStageNum($newStage) {
    $stageFile = fopen("./users/stages/".$_GET['token'].".txt", "w");
    fwrite($stageFile, $newStage);
    fclose($stageFile);
}
function addToHistory($addString) {
    //Write the string
    $hisFile = fopen("./users/history/".$_GET['token'].".txt", "a");
    fwrite($hisFile, $addString);
    fclose($hisFile);
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
function listUserRepos($userName) {
    $GitHubResponse = basicCurl("https://api.github.com/users/".$userName."/repos");
    $jsonResult = json_decode($GitHubResponse, true);
    if ($jsonResult['message']!="Not Found") {
        $returnString = "Here are the repos for the user ".$searchString.": <br />";
        foreach ($jsonResult as $currentItem) {
            $returnString = $returnString."<br /><b>".$currentItem['name']."</b><br /><i>Description:</i> ".$currentItem['description']."<br /><i>Language: </i>".$currentItem['language']."<br /><i>Repo link: </i> <a href='".$currentItem['html_url']."'>".$currentItem['html_url']."</a><br />";
        }
        return $returnString;
    } else {
        return "An error occurred. :(";
    }    
}

//Read the current stage
$stageFile = fopen("./users/stages/".$userToken.".txt", "r");
$currentStage = fread($stageFile, filesize("./users/stages/".$userToken.".txt"));
fclose($stageFile);

$userMessage = $_GET['msg'];

switch ($currentStage) {
    case "-1" :
        //RESET THE CHAT
        $GLOBALS['redir'] = "createChat.php?token=".($userToken + 1);
    case "0":
        //We're looking for either User or Organisation
        if (strpos(strtolower($userMessage), 'user') !== false) {
            //If the string contains user
            setStageNum("1");
        } elseif (strpos(strtolower($userMessage), 'org') !== false) {
            //If the string contains org
            setStageNum("2");
        } else {
            addToHistory("0Sorry, I didn't understand the answer you gave. Let's try again.\n");
            //Keep the stage number at 0 so it repeats the phrase
        }
        break;
    case "1":
        //They're searching for a user
        $searchResponse = searchForUser($userMessage);
        addToHistory("0".$searchResponse."\n");
        //TODO: Skip straight to details if there was only 1 result for the search
        setStageNum("3");
        break;
    case "2":
        //TODO: Implement this
        addToHistory("0Sorry! This conversation path isn't implemented yet. Please refresh the page.\n");
        break;
    case "3":
        //They've told us a specific user
        //TODO: Execute this in a function
        $searchResponse2 = detailsForUser($userMessage);
        addToHistory("0".$searchResponse2."\n");
        if ($searchResponse2!="The user couldn't be found! :(") {
            //Cache the user we're looking at
            setcookie("userCache", $userMessage, time() + (86400 * 2), "/"); // 86400 = 1 day
            setStageNum("4");
        } else {
            //We didn't find them, don't change stage
            addToHistory("0Please try another user.");
        }
        break;
    case "4":
        //They have responded with REPO, SOMEONE ELSE, or START
        if (strpos(strtolower($userMessage), 'repo') !== false) {
            //If the string contains REPO
            $searchResponse3 = listUserRepos($_COOKIE['userCache']);
            addToHistory("0".$searchResponse3."\n");
            if ($searchResponse3!="An error occurred. :(") {
                setStageNum("5");
            } else {
                //We didn't find them, don't change stage
                addToHistory("0Please try another user.");
            }
        } elseif (strpos(strtolower($userMessage), 'else') !== false) {
            //If the string contains SOMEONE ELSE
            setStageNum("3");
        } elseif (strpos(strtolower($userMessage), 'detail') !== false) {
            //If the string contains SOMEONE ELSE
            setStageNum("3");
        } elseif (strpos(strtolower($userMessage), 'start') !== false) {
            //RESET THE CHAT
            $GLOBALS['redir'] = "createChat.php?token=".($userToken + 1);
        } else {
            addToHistory("0Sorry, I didn't understand the answer you gave. Let's try again.\n");
            //Keep the stage number at 4 so it repeats the phrase
        }
    case "5":
        //REPOS
        //TODO: Implement this
}

?>
<script type="text/javascript">
//Redirect to answer interpretation
//var userToken = <?php echo $_GET['token']; ?>;
//window.location = "mainChat.php?token=" + userToken.toString();
window.location = "<?php echo $GLOBALS['redir']; ?>";
</script>

</body>
</html>