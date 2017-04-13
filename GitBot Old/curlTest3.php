<html>
<body>
<p>
Result of curl when searching for "Adybo123":<br />
<?php
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
function detailsForUser($searchString) {
    $GitHubResponse = basicCurl("https://api.github.com/users/".$searchString);
    $jsonResult = json_decode($GitHubResponse, true);
    if ($jsonResult['message']!="Not Found") {
        $returnString = "Here are the details for the user ".$searchString.": <br />";
        $returnString = $returnString."Login: ".$jsonResult['login']."<br />Real name: ".$jsonResult['name']."<br />GitHub url: <a href='".$jsonResult['html_url']."'>".$jsonResult['html_url']."</a><br />User Bio:<br />".$jsonResult['bio'];
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
            $returnString = $returnString."<b>".$currentItem['name']."</b><br /><i>Description:</i> ".$currentItem['description']."<br /><i>Language: </i>".$currentItem['language']."<br /><i>Repo link: </i>".$currentItem['html_url']."<br />";
        }
        return $returnString;
    } else {
        return "An error occurred. :(";
    }    
}

echo "Bot response: <br />".listUserRepos("Adybo123");
?>
</p>
</body>
</html>