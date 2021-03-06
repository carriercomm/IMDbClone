<?php
session_start();
$song_id = $_GET['id'];
if(empty($song_id))
{
	header( 'Location: songs.php' ) ;
}
require_once("./dbconnect.php");
$db_connection = DbUtil::openDataReadOnlyConnection();

$stmt = $db_connection->stmt_init();
	
if($stmt->prepare("SELECT song_id, title, year, artist, runtime FROM songs WHERE song_id=?")) {
	$stmt->bind_param('i', $song_id);
	$stmt->execute();
		
	$stmt->bind_result($song_id, $title, $year, $artist, $runtime);
		
	$stmt->fetch();
		
	$stmt->close();
}

?>

<head>
	<title><?php echo $title; ?></title>
	<style type="text/css" title="currentStyle">
		@import "media/css/jquery.dataTables.css";
		@import "media/css/TableTools.css";
	</style>
	<link href="media/css/bootstrap.min.css" rel="stylesheet">
	<script type="text/javascript" language="javascript" src="media/js/jquery.js"></script>
	<script type="text/javascript" language="javascript" src="media/js/jquery.dataTables.js"></script>
	<script type="text/javascript" language="javascript" src="media/js/TableTools.min.js"></script>
	<script>
		$(document).ready(function() {
			$('#featured_in').dataTable( {
				"sDom": 'T<"clear">frtip',
				"oTableTools": {
					"aButtons": [
						"copy",
						"csv",
						"pdf",
						"print"
					]
				}
			} );
		} );
	</script>
</head>
<body>
<?php include './menu.php'; ?>
<div class="container">
	<h2><?php echo $title; ?></h2>
	<?php if(isset($_SESSION['valid']) && $_SESSION['valid']){ ?> <h3><a href="song_edit?id=<?php echo $song_id; ?>">Edit</a> <a href="song_delete?id=<?php echo $song_id ?>">Delete</a></h3> <?php } ?>
	Year: <?php echo $year; ?><br>
	Artist: <?php echo $artist; ?><br>
	Runtime: <?php echo $runtime; ?><br>

	<h2>Featured In:</h2>
	<table id="featured_in" border=1 width=100%>
	<thead>
		<tr background=#ff8c00>
			<th>Title</th>
			<th>Year</th>
			<th>Runtime</th>
			<th>Rating</th>
		</tr>
	</thead>
	<tbody>
<?php

$stmt = $db_connection->stmt_init();

if($stmt->prepare("SELECT movie_id, title, year, runtime, rating FROM movies WHERE movie_id IN (SELECT movie_id FROM featured_in WHERE song_id=?)")) {

	$stmt->bind_param('i', $song_id);

	$stmt->execute();
	
	$stmt->bind_result($movie_id, $title, $year, $runtime, $rating);
	
	while ($stmt->fetch()) {
		echo '<tr><td><a href="movie?id=' . $movie_id . '">' . $title . '</a></td>';
		echo "\n";
		echo '<td>' . $year . '</td>';
		echo "\n";
		echo "<td>" . $runtime . " min</td>";
		echo "\n";
		echo "<td>" . $rating . "</td></tr>";
		echo "\n";
		echo "\n";
	}
		
	echo "</tbody></table>";
	$stmt->close();
}

$db_connection->close();

?>


	
</div>
</body>
</html>
