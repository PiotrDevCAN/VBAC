<?php

?>

<style type="text/css" class="init">
body {
	background: url('./public/img/vBAC2.jpg')
		no-repeat center center fixed;
	-webkit-background-size: cover;
	-moz-background-size: cover;
	-o-background-size: cover;
	background-size: cover;
}

.mood {
    font-size: 16px;
	border: none;
	padding: 5px;
    cursor: pointer;
}

.moodSmile {
	color:OliveDrab;

}

.moodMeh {
	color:Orange;

}

.moodFrown {
	color:OrangeRed;
}

.moodSmile:hover {
	color:#8ab82e;
    font-size: 12px;
}

.moodMeh:hover {
	color: #ffb833;
    font-size: 12px;
}

.moodFrown:hover {
	color: #ff6933;
    font-size: 12px;
}
</style>



<div class="container">
<!-- <div class="jumbotron"> -->
<h1 id='welcomeJumotron'>Training Links & Feedback</h1>
<!-- </div> -->

<ul class='helpLink'>
<li><a href='https://w3.tap.ibm.com/medialibrary/media_set_view?id=47864' target='_blank' class=''>User Guide and Training Videos</a></li>


<li class=' feedback'>Feedback
<div id='feedbackDiv'>
<form id='feedbackForm' onsubmit='return false'>

<div class='row'>
<div class='form-group col-sm-6'>
<textarea id='feedbackText' rows="4" cols="25" style='font-size:14px' placeholder="Type feedback or comments,  then click 'Send' below"></textarea>
</div>
</div>
<div class='row'>
<div class='form-group col-sm-1'>
<button id='feedbackSend' class='btn btn-primary form-control col-sm-2'>Send</button>
</div>
</div>
</form>
</div>
<li>Find us on Slack&nbsp;<a href='https://ventusdelivery.slack.com/messages/C8DLE1DFH/' target='_blank' >here</a></li>
</li>
</ul>

</div>

<<script type="text/javascript">


$(document).click('#feedbackSend',function (){
	var sender = '<?=$_SESSION['ssoEmail']?>'
	var feedback = $('#feedbackText').val();
    $.ajax({
        url: "ajax/emailFeedback.php",
        type: 'POST',
        data: { feedback:feedback,
                sender:sender },
        success: function(result){
        	$('#feedbackText').val('');
        }
  });




});
</script>


