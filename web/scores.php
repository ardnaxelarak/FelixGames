<?php
include_once '../include/db_connect.php';

ini_set('display_errors', 'On');
error_reporting(E_ALL);

$sort = 1;
$items = 0;
$labels = array();
$games = array();

$game = "";

if (isset($_GET['game']))
	$game = $_GET['game'];

$display = "topten";

if (isset($_GET['display']))
	$display = $_GET['display'];

$stmt = $mysqli->prepare("SELECT id, display_name, default_sort, col1_name, col2_name, col3_name, col4_name, col5_name FROM scorelists WHERE short_name=?");
$stmt->bind_param('s', $game);
$stmt->execute();
$stmt->bind_result($index, $display_name, $sort, $col1, $col2, $col3, $col4, $col5);
if (!($stmt->fetch()))
{
	$stmt = $mysqli->prepare("SELECT id, short_name, display_name, default_sort, col1_name, col2_name, col3_name, col4_name, col5_name FROM scorelists JOIN (SELECT max(id) AS max_id FROM scorelists) max ON id = max_id");
	$stmt->execute();
	$stmt->bind_result($index, $game, $display_name, $sort, $col1, $col2, $col3, $col4, $col5);
	$stmt->fetch();
}
$stmt->close();

$cols = array(1 => $col1, $col2, $col3, $col4, $col5);
$cols = array_filter($cols);
$items = count($cols);
$halfwidth = 120 + 50 * $items;

$setkey = -1;
if (isset($_GET['sort']))
{
	$tempsort = $_GET['sort'];
	if (isset($cols[$tempsort]))
	{
		$setkey = $_GET['sort'];
		$sort = $setkey;
	}
}

if ($setkey >= 0)
	$sortlink = '&sort=' . $sort;
else
	$sortlink = '';

?>

<!DOCTYPE html>
<html>
	<head>
		<title>Highscores</title>
		<link rel="stylesheet" href="include/scores.css">
	</head>
	<body>
		<h1><?php echo $display_name?></h1>
		<p class="center">Display:
			<a href="scores.php?game=<?php echo $game; ?>&display=all<?php echo $sortlink; ?>">All</a>, 
			<a href="scores.php?game=<?php echo $game; ?>&display=topten<?php echo $sortlink; ?>">Top 10</a>, 
			<a href="scores.php?game=<?php echo $game; ?>&display=each<?php echo $sortlink; ?>">By name</a>
		</p>
<?php
	$stmt = $mysqli->prepare("SELECT short_name, display_name FROM scorelists WHERE id = ?");
	$stmt->bind_param('i', $checkindex);
?>
		<table class="center" style="border:0px">
			<tr>
				<td class="left" style="border:0px; width:<?php echo $halfwidth; ?>px">
<?php
	$checkindex = $index - 1;
	$stmt->execute();
	$stmt->bind_result($other_short_name, $other_display_name);
	if ($stmt->fetch())
		echo "<a href=\"scores.php?game=$other_short_name&display=$display\">$other_display_name</a>";
	else
		echo "&nbsp;";
?>
				</td>
				<td class="right" style="border:0px; width:<?php echo $halfwidth; ?>px">
<?php
	$checkindex = $index + 1;
	$stmt->execute();
	$stmt->bind_result($other_short_name, $other_display_name);
	if ($stmt->fetch())
		echo "<a href=\"scores.php?game=$other_short_name&display=$display\">$other_display_name</a>";
	else
		echo "&nbsp;";
	$stmt->close();
?>
				</td>
			</tr>
		</table>
		<table class="center">
			<tr>
				<th style="width:40px">&nbsp;</th>
				<th style="width:200px">Name</th>
<?php
	foreach ($cols as $key => $value)
		echo "<th style=\"width:100px\"><a href=\"scores.php?game=$game&display=$display&sort=$key\">$value</a></th>";
?>
			</tr>

<?php
if ($display == 'all')
	$stmt = $mysqli->prepare("SELECT name, col1, col2, col3, col4, col5 FROM scores WHERE scorelist_id = ? ORDER BY col$sort DESC");
else if ($display == 'topten')
	$stmt = $mysqli->prepare("SELECT name, col1, col2, col3, col4, col5 FROM scores WHERE scorelist_id = ? ORDER BY col$sort DESC LIMIT 10");
else
	$stmt = $mysqli->prepare("SELECT name, max(col1), max(col2), max(col3), max(col4), max(col5) FROM scores WHERE scorelist_id = ? GROUP BY name ORDER BY max(col$sort) DESC");

$stmt->bind_param('i', $index);
$stmt->execute();
$stmt->bind_result($name, $col1, $col2, $col3, $col4, $col5);

$i = 1;
while ($stmt->fetch())
{
	$score_cols = array(1 => $col1, $col2, $col3, $col4, $col5);
?>
			<tr>
				<td class="right"><?php echo $i++; ?></td>
				<td class="left"><?php echo $name; ?></td>
			<?php foreach ($cols as $key => $value) { ?>
				<td class="left"><?php echo $score_cols[$key]; ?></td>
			<?php } ?>
			</tr>
<?php
}
$stmt->close();
?>
		</table>
		<p class="center"><a href="index.html">Back to main index</a></p>
	</body>
</html>

