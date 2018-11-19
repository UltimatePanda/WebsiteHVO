<?php
	session_start();
//	header('Location:http://www.hetvierdeoor.be/mijnoor/index.php?i=loginok');
	require_once('../lib/data.inc');
	require_once('../lib/datatable.php');
	require_once('../lib/functions.php');
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 
<html>
<head>
<title>Het Vierde Oor ~ reserveren</title>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
<link href="../css/style.css" rel="stylesheet" type="text/css">

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-26196938-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>
<body>
<?php
	include("../includes/menu.php");
?>
<div id="contentwrapper">
	<div id="content">
		<div id="container">
		  <div id="content_left">
			<h2>reserveren</h2>
			<div id="sidemenu">
				<ul>
					<?php 
						if(!isset($_GET['i']))
							{
							echo '<li class="current">';
							}
						else
							{
							echo '<li>';
							}
					?><a href="index.php">info</a></li>
					<?php 
						if($_GET['i'] == 'form')
							{
							echo '<li class="current">';
							}
						else
							{
							echo '<li>';
							}
					?><a href="index.php?i=form">formulier</a></li>
				</ul>
			</div>
		  </div>
			<?php 
				if($_GET['i'] == 'form')
					{
						include('reserveren_formulier.php');
					}
				elseif($_GET['i'] == 'seats')
					{
						include('seats.php');
					}
				else
					{
						include('reserveren_info.php');
					}
			?>
		</div>
	</div>
</div>
<?php
	include("../includes/footer.php");
?>
</body>
</html>
