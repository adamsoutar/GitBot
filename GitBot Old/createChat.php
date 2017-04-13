<html>
<body>
<center>
<p>
<font color="#ffffff">

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
?>

</font>
</p>
</center>

<script type="text/javascript">
//Redirect to the chat page
var userToken = <?php echo $_GET['token']; ?>;
window.location = "mainChat.php?token=" + userToken.toString()
</script>

</body>
</html>