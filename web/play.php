<?php
include_once '../include/db_connect.php';

class comment
{
	public $name, $time, $text;
	public function __construct($pieces)
	{
		$this->name = ucwords(trim($pieces[0], " _"));
		$this->text = $pieces[1];
		$this->time = $pieces[2];
	}
}

$game = "";

if (isset($_GET['game']))
	$game = $_GET['game'];

$stmt = $mysqli->prepare("SELECT id, sketch_name, display_name, description FROM games WHERE short_name=?");
$stmt->bind_param('s', $game);
$stmt->execute();
$stmt->bind_result($index, $sketch_name, $display_name, $description);
if (!($stmt->fetch()))
{
	$stmt = $mysqli->prepare("SELECT id, sketch_name, short_name, display_name, description FROM games JOIN (SELECT max(id) AS max_id FROM games) max ON id = max_id");
	$stmt->execute();
	$stmt->bind_result($index, $sketch_name, $game, $display_name, $description);
	$stmt->fetch();
}
$stmt->close();

$comments = array();

if ($commentfile = fopen("comments/" . $curgame->name . "-comments", "r"))
{
	while (($pieces = fgetcsv($commentfile)) !== false)
	{
		$comments[] = new comment($pieces);
	}
	fclose($commentfile);
}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title><?php echo $display_name?></title>
		<meta name="Generator" content="Processing" />
		<link rel="stylesheet" href="include/play.css">
		<!--[if lt IE 9]>
			<script type="text/javascript">alert("Your browser does not support the canvas tag.");</script>
		<![endif]-->
		<script src="include/processing.js" type="text/javascript"></script>
		<script src="include/start.js" type="text/javascript"></script>
		<script src="include/logging.js" type="text/javascript"></script>
		<script type="text/javascript">
			function submitted()
			{
				var nameValue = window.user;
				var commentValue = document.getElementById('comment').value;
				if (nameValue.trim() == "")
				{
					alert("You must enter a name.");
					return;
				}
				if (commentValue.trim() == "")
				{
					alert("What is the point of leaving a blank comment?");
					return;
				}
				writecomment("comments/<?php echo $game?>-comments", nameValue, commentValue);
				var ch1 = document.getElementById('commenttext');
				alert("Thank you! Your comment has been recorded. Please refresh the page to see it.");
				document.getElementById('comment').value = "";
			}
		</script>
		<script type="text/javascript">
			// convenience function to get the id attribute of generated sketch html element
			function getProcessingSketchId () { return '<?php echo $sketch_name?>'; }
		</script>
	</head>
	<body>
		<div id="content">
			<h1><?php echo $display_name?></h1>
			<div style="text-align:center;">
				<canvas id="<?php echo $sketch_name?>" data-processing-sources="pdes/<?php echo $sketch_name; ?>.pde" width="730" height="550">
					<p>Your browser does not support the canvas tag.</p>
					<!-- Note: you can put any alternative content here. -->
				</canvas>
				<noscript>
					<p>JavaScript is required to view the contents of this page.</p>
				</noscript>
			</div>
			<p id="description"><?php echo $description?></p>
			<p><a href="." title="Index">Return to main index</a>
		</div>
		<div id="commentarea">
<?php foreach ($comments as $comment) {?>
			<div class="comment">
				<h1 class="commentname"><?php echo $comment->name; ?></h1>
				<p class="commenttime"><?php echo $comment->time; ?></p>
				<h3 class="commenttext"><?php echo $comment->text; ?></h3>
			</div>
<?php } ?>
			<h1 id="commenttext">Leave a comment!</h1>
			<form id="commentform" action="javascript:submitted()">
				Comment:<br>
				<textarea rows="10" cols="50" id="comment"></textarea><br>
				<input namhe="Submit" type="submit" value="Submit"/>
			</form>
		</div>
	</body>
</html>
