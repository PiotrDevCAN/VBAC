<style type="text/css" class="init">
body {
	background: url('./public/img/vBAC2.jpg')
		no-repeat center center fixed;
	-webkit-background-size: cover;
	-moz-background-size: cover;
	-o-background-size: cover;
	background-size: cover;
}
</style>



<div class="container">
	<!--  <div class="jumbotron"> -->
		<h1 id='welcomeJumotron'>Ventus Boarding & Access Control</h1>

		<button type='button' class='btn btn-default' id='onBoardingBtn'><span class="glyphicon glyphicon-log-in"></span>&nbsp;On Boarding</button>
		<button type='button' class='btn btn-default' id='offBoardingBtn'><span class="glyphicon glyphicon-log-out"></span>&nbsp;Off Boarding</button>
	<!-- </div> -->
</div>

<script>
$(document).ready(function() {
	console.log('start listening');
	var person = new personRecord();
	person.listenForOnBoarding();
	person.listenForOffBoarding();
})

</script>