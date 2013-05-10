<?php

define('HOST', 'localhost');
define('pass', 'neeraj');
define('user', 'root');
define('_int', PDO::PARAM_INT);
define('_str', PDO::PARAM_STR);
$dbh = new PDO('mysql:host='.HOST.';dbname=movie_recommendation', user, pass);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



if(isset($_GET['user_id']) && !isset($_GET['ratings'])){
	include_once 'recommendation.php';
	Recommendation($_GET['user_id'] ,$dbh);	
}

if(isset($_POST['name'], $_POST['room']) && !isset($_POST['ip'])){
	$query = 'UPDATE `users` SET `room` = ? WHERE `user_id` = ?';
	$stmt = $dbh->prepare($query);
	$stmt->bindParam(1, $_POST['room'], _str);
	$stmt->bindParam(2, $_POST['name'], _int);
	$return = $stmt->execute();
}

else if(isset($_POST['name'], $_POST['ip'], $_POST['room'])){
	$query = 'INSERT INTO `users` (`name`, `ip_addr`, `room`) VALUES (?, ?, ?)';
	echo $query;
	$stmt = $dbh->prepare($query);
	$stmt->bindParam(1, $_POST['name'], _str);
	$stmt->bindParam(2, $_POST['ip'], _str);
	$stmt->bindParam(3, $_POST['room'], _str);
	var_dump($stmt);
	$return = $stmt->execute();
}

if(isset($_POST['user_id'], $_POST['movie_id'], $_POST['rating'])){
	if($_POST['rating'] == 0){
		$query = 'DELETE FROM `user_rating` WHERE `user_id` = ? AND `movie_id` = ?';
	}
	else{
		$query = 'REPLACE INTO `user_rating`(`user_id`, `movie_id`, `rating`) VALUES (?, ?, ?)';
	}
	$stmt = $dbh->prepare($query);
	$stmt->bindParam(1, $_POST['user_id'], _int);
	$stmt->bindParam(2, $_POST['movie_id'], _int);
	if($_POST['rating'] != 0)
		$stmt->bindParam(3, $_POST['rating'], _str);
	$return = $stmt->execute();

	$query = 'UPDATE `hits` SET `counter` = `counter` + 1';
	$stmt = $dbh->prepare($query);
	$return = $stmt->execute();
}

if(isset($_GET['user_id'], $_GET['ratings'])){
	$query = 'SELECT * FROM `user_rating` WHERE `user_id` = ?';
	$stmt = $dbh->prepare($query);
	$stmt->bindParam(1, $_GET['user_id'], _int);
	$return = $stmt->execute();
	$rated = array();
	while($row = $stmt->fetch()){
		$rated[] = array('id'=>$row['movie_id'], 'rating'=>$row['rating']);
	}
	echo json_encode(array('data'=>$rated));
	
	$query = 'UPDATE `hits` SET `counter` = `counter` + 1';
	$stmt = $dbh->prepare($query);
	$return = $stmt->execute();
}
if(isset($_POST['movie_id'], $_POST['user_id'], $_POST['action'])){
	if($_POST['action'] == 'add')
		$query = 'REPLACE INTO `has_movie`(`user_id`, `movie_id`) VALUES(?, ?)';
	if($_POST['action'] == 'delete')
		$query = 'DELETE FROM `has_movie` WHERE `user_id` = ? AND `movie_id` = ?';
	$stmt = $dbh->prepare($query);
	$stmt->bindParam(1, $_POST['user_id'], _int);
	$stmt->bindParam(2, $_POST['movie_id'], _int);
	$return = $stmt->execute();

	$query = 'UPDATE `hits` SET `counter` = `counter` + 1';
	$stmt = $dbh->prepare($query);
	$return = $stmt->execute();
}
if(isset($_GET['movie_id'])){
	$query = 'SELECT table2.`name`, table2.`room` FROM `has_movie` table1, `users` table2 WHERE table1.`user_id` = table2.`user_id` AND `movie_id` = ? LIMIT 0, 10';
	$stmt = $dbh->prepare($query);
	$stmt->bindParam(1, $_GET['movie_id'], _int);
	$return = $stmt->execute();
	$user = '<p>People who have this movie:</p>';
	$users = '';
	while($row = $stmt->fetch()){
		$users .= $row['name'].' (Room No: '.$row['room'].')<br />';
	}
	if($users == '')
		$users = 'Nobody has it yet!';
	echo $user.$users;
}
?>
