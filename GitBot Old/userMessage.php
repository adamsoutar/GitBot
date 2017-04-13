<html>
<body><center><p>
<font color="#ffffff">
<?php
//Send message script
$usrMsg = $_GET['msg'];
$usrToken = $_GET['token'];
function addToHistory($addString) {
    //Write the string
    $hisFile = fopen("./users/history/".$_GET['token'].".txt", "a");
    fwrite($hisFile, $addString);
    fclose($hisFile);
}

addToHistory("1".$usrMsg."\n");

//echo "User with token: ".$usrToken." submitted: ".$usrMsg;
?>
</font></p></center>

<script type="text/javascript">
//Redirect to answer interpretation
var userToken = <?php echo $_GET['token']; ?>;
window.location = "answerInterpreter.php?token=" + userToken.toString() + "&msg=" + "<?php echo $_GET['msg']; ?>";
</script>

</body>
</html>