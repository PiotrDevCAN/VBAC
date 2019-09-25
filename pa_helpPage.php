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
<li><a href='https://ibm.ent.box.com/folder/83248737881' target='_blank' class=''>User Guide</a>&nbsp;<small>(Request Access from <a href='mailto:Aurora.Central.PMO@uk.ibm.com'>Aurora Central PMO/UK/IBM</a>)</small></li>
<li><a href='https://yourlearning.ibm.com/#channel/CNL_LCB_1547052865067' target='_blank' class=''>Ventus Your Learning</a></li>

<li class=' feedback'>Questions
<div id='feedbackDiv'>
<form id='feedbackForm' onsubmit='return false'>
<div class='row'>
<div class='form-group col-sm-6'>
<textarea id='feedbackText' rows="4" cols="25" style='font-size:14px' placeholder="Type questions for the IAM support team then click 'Send' below. But please check FAQ first, how to resolve many issues are covered in the FAQ"></textarea>
</div>
</div>
<div class='row'>
<div class='form-group col-sm-1'>
<button id='feedbackSend' class='btn btn-xs btn-primary form-control col-sm-2'>Send</button>
</div>
</div>
</form>
</div>
<li>For ID and Access process issues please contact  :<a href='mailto:IBM.LBG.IAM.Requests@uk.ibm.com'>IBM LBG IAM Requests/UK/IBM</a></li>
<li>For technical issues please contact application support :<a href='https://ventusdelivery.slack.com/messages/C8DLE1DFH/' target='_blank' ># sm-cognitive-delivery</a></li>
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


