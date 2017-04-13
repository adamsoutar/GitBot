<html>
<body>
<p>
Result of curl when searching for "Microsoft":<br />
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
echo "LOGIN: ".searchForUser("Microsoft");
?>
</p>
</body>
</html>