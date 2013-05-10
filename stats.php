<!DOCTYPE>
<html>
<head>
	<meta http-equiv="refresh" content="5" />
	<title>Movie Recommender</title>
	<link href="bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css" />
	<link href="index.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php

define('HOST', 'localhost');
define('pass', 'neeraj');
define('user', 'root');
define('_int', PDO::PARAM_INT);
define('_str', PDO::PARAM_STR);
$dbh = new PDO('mysql:host='.HOST.';dbname=movie_recommendation', user, pass);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if($_SERVER['REMOTE_ADDR'] == '172.16.84.214'){

	$query = 'SELECT `counter` FROM `hits`';
	$stmt = $dbh->prepare($query);
	$return = $stmt->execute();
	$row = $stmt->fetch();
	echo 'Total hits: '.$row['counter'].'<br /><br />';
	
	$query = 'SELECT count(*) FROM `users`';
	$stmt = $dbh->prepare($query);
	$return = $stmt->execute();
	$row = $stmt->fetch();
	echo 'Total users: '.$row['count(*)'].'<br /><br />';
	
	$query = 'SELECT count(*) FROM `movies`';
	$stmt = $dbh->prepare($query);
	$return = $stmt->execute();
	$row = $stmt->fetch();
	echo 'Total movies: '.$row['count(*)'].'<br /><br />';
	
	$query = 'SELECT COUNT(DISTINCT `movie_id`) AS `counter` FROM `has_movie`';
	$stmt = $dbh->prepare($query);
	$return = $stmt->execute();
	$row = $stmt->fetch();
	echo 'Movies at least owned by 1 person: '.$row['counter'].'<br /><br />';

	$query = 'SELECT COUNT(DISTINCT `user_id`) AS `counter` FROM `has_movie`';
	$stmt = $dbh->prepare($query);
	$return = $stmt->execute();
	$row = $stmt->fetch();
	echo 'Users added their list: '.$row['counter'].'<br /><br />';
	
	$query = 'SELECT count(*) FROM `user_rating`';
	$stmt = $dbh->prepare($query);
	$return = $stmt->execute();
	$row = $stmt->fetch();
	echo 'Total ratings: '.$row['count(*)'].'<br /><br />';
	
	$query = 'SELECT a.`user_id`, b.`name`, (SELECT count(*) FROM `has_movie` WHERE `user_id` = a.`user_id`) as `rated` FROM `has_movie` a, `users` b WHERE b.`user_id` = a.`user_id` GROUP BY `user_id` ORDER BY `rated` DESC LIMIT 0, 10';
	$stmt = $dbh->prepare($query);
	$return = $stmt->execute();
	echo '<div class="span6">Has Movie Ranking<br /><br /><br />';
	while($row = $stmt->fetch()){
		echo $row['name'].' '.$row['rated'].'<br/>';
	}
	echo '<br /><br /><br /><br /></div>';
	
	$query = 'SELECT a.`user_id`, b.`name`, (SELECT count(*) FROM `user_rating` WHERE `user_id` = a.`user_id`) as `rated` FROM `user_rating` a, `users` b WHERE b.`user_id` = a.`user_id` GROUP BY `user_id` ORDER BY `rated` DESC LIMIT 0, 10';
	$stmt = $dbh->prepare($query);
	$return = $stmt->execute();
	echo '<div class="span6">Ranking according no. of movies rated<br /><br /><br />';
	while($row = $stmt->fetch()){
		echo $row['name'].' '.$row['rated'].'<br/>';
	}
	echo '</div>';
}

?>
</body>
</html>
<script type="text/javascript">
</script>
