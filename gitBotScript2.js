var GlobalToken = "";
var BubbleNumber = 0;

function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

function assignUserToken() {
    //Tokens are 7 digit numbers used to identify users
    var userToken = Math.floor((Math.random() * 9999998) + 1000001);
    GlobalToken = userToken;
}

function setupChat() {
    //Give us a token
    assignUserToken();
    //Set up a new chat with the bot, tell it our token
    jQuery.get("createNewChat.php?token=" + GlobalToken.toString());
    appendBotSpeechBubble("Hey, I'm GitBot! I can help you navigate GitHub.<br />First thing's first, are you looking for a user or a repository?");
}

function getTimeStamp() {
    var d = new Date();
    return d.getHours().toString() + ":" + d.getMinutes().toString();
}

function appendToMainSection(appendString) {
    document.getElementById("resBox").innerHTML = document.getElementById("resBox").innerHTML + appendString
}

function appendBotSpeechBubble(botStr) {
    BubbleNumber = BubbleNumber + 1;
    var BotBubbleHTML = '<div class="msgWrapper"><div class="avatarImg"><img src="bot_icon.png" alt="Bot avatar" width="40" height="40" /></div><div class="timeStamp">' + getTimeStamp() + '</div><div class="bot-bubble tri-right round btm-left slide-left" id="bubble' + BubbleNumber.toString() + '"><div class="talktext1"><p>' + botStr.toString() + "</p></div></div></div>";
    //appendToMainSection('<div class="bot-bubble tri-right round btm-left animation-element slide-left" id="bubble' + BubbleNumber.toString() + '"><div class="talktext1"><p>' + botStr.toString() + "</p></div></div><br />")
    appendToMainSection(BotBubbleHTML);
    $("#midBox").scrollTo($('#bubble' + BubbleNumber.toString()), 600);
    $('#bubble' + BubbleNumber.toString()).addClass('in-view');
}

function appendUserSpeechBubble(usrStr) {
    BubbleNumber = BubbleNumber + 1;
    var UserBubbleHTML = '<div class="msgWrapper"><div class="avatarImg2"><img src="user_icon.png" alt="User avatar" width="40" height="40" /></div><div class="timeStamp2">' + getTimeStamp() + '</div><div class="talk-bubble tri-right round btm-right slide-left" id="bubble' + BubbleNumber.toString() + '"><div class="talktext2"><p>' + usrStr.toString() + "</p></div></div></div>";
    //appendToMainSection('<div class="talk-bubble tri-right round btm-right animation-element slide-right" id="bubble' + BubbleNumber.toString() + '"><div class="talktext2"><p>' + usrStr.toString() + "</p></div></div><br />")
    appendToMainSection(UserBubbleHTML);
    $("#midBox").scrollTo($('#bubble' + BubbleNumber.toString()), 600);
    $('#bubble' + BubbleNumber.toString()).addClass('in-view');
}

function sendMessageToListener() {
    //Send message to PHP
    var userMessage = document.getElementById("msgInput").value;
    if (userMessage.replace(" ", "")!="") {
        document.getElementById("msgInput").value = "";
        appendUserSpeechBubble(userMessage);
        $.ajax({type: "POST", url: "mainListener.php",dataType: "json", data: {msg : userMessage, token : GlobalToken}, success: function(result){
            //document.getElementById("resBox").innerHTML = result.response;
            appendBotSpeechBubble(result.response);
        }});
    }
}

//Set up a chat
setupChat();