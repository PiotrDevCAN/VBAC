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
<h1 id='welcomeJumotron'>Training Links &amp; Feedback</h1>

<div class="alert alert-warning helplink">
  <strong>Unexpected Results ? </strong>&nbsp;Please note only the <strong>Firefox</strong> browser is 100% supported for use with vBAC. If you experience an <strong>"unexpected result"</strong> and are NOT using Firefox, then please retry using Firefox before you escalate for support.
</div>


<div class="panel panel-default">
<div class='panel-body'>
<ul class='helpLink'>
<li><a href='https://kyndryl.ent.box.com/folder/83248737881' target='_blank' class=''>User Guide</a>&nbsp;<small>(Request Access from <a href='mailto:aurora.central.pmo@kyndryl.com'>aurora.central.pmo@kyndryl.com</a>)</small></li>
<li><a href='https://yourlearning.ibm.com/#channel/CNL_LCB_1547052865067' target='_blank' class=''>Ventus Your Learning</a></li>
<li>For ID and Access process issues please contact  :<a href='mailto:Kyndryl.LBG.IAM.Requests@kyndryl.com'>Kyndryl.LBG.IAM.Requests@kyndryl.com</a></li>
</ul>
</div>
</div>
</div>

<script type="text/javascript">


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


