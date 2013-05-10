<!DOCTYPE>
<html>
<head>
	<title>H-4's Movie Database</title>
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

$query = 'UPDATE `hits` SET `counter` = `counter` + 1';
$stmt = $dbh->prepare($query);
$return = $stmt->execute();

$query = 'SELECT * FROM users WHERE ip_addr = ?';
$stmt = $dbh->prepare($query);
$stmt->bindParam(1, $_SERVER['REMOTE_ADDR'], _int);
$return = $stmt->execute();
$row = $stmt->fetch();

if(!empty($row)){
	$user_id = $row['user_id'];
	if($row['room'] == '0'){
?>
<div class="container" style="min-height: 550px">
	<div class="row" style="margin-top: 100px">
		<div class="row" style="text-align:center">
			<h1>H-4's Movie World</h1>
			<p>H-4's centralized movie database.</p>
		</div>
		<div class="row" style="text-align: center; margin-top: 50px">
			<input type="text" id="relogin-room" placeholder="Room eg: dorm1, 423" class="span2" style="height: 27px; margin-bottom: 0px !important"/>
			<input type="button" class="btn btn-primary" id="reregister" value="Login"/>
		</div>
	</div>
	</div>
</div>
<?php
	}
	else{
	$query = 'SELECT * FROM `has_movie` WHERE user_id = '.$user_id;
	$stmt = $dbh->prepare($query);
	$return = $stmt->execute();
	$movies = array();
	while($row = $stmt->fetch()){
		$movies[] = $row['movie_id'];
	}
?>		

<div class="container-fluid" style="min-height: 100%; padding-left: 0px;">
	<div class="row-fluid" style="min-height: inherit">
		<div class="span10" style="min-height: inherit; text-align:center; background-color: #CCC;">
			<h2>H-4's Movie World</h2>
			<p>H-4's centralized movie database.</p>
			<div class="row" style="margin-top: 50px">
			<?php 
				$ratings = '<option value="0">Not Seen</option>';
				for($i = 1; $i <= 5 ; $i += .5){
					$ratings .= '<option value="'.$i.'">'.$i.'</option>';
				}
				
				$query = 'SELECT *, (SELECT avg(`rating`) FROM `user_rating` WHERE `movies`.`movie_id` = `user_rating`.`movie_id`) AS `avg_rating` FROM `movies` ORDER BY `movie_name`';
				$stmt = $dbh->prepare($query);
				$stmt->bindParam(1, $_SERVER['REMOTE_ADDR'], _int);
				$return = $stmt->execute();
				$i = 0;
				$html = '<table class="table table-striped" style="width:100%;">
					<thead>
					<tr>
					<th style="text-align:center; width: 510px;">
						<h3>Movie</h3>
					</th>
					<th style="text-align:center;">
						<h3>Status (Click to change.)</h3>
					</th>
					<th style="text-align:center;">
						<h3>Rating</h3>
					</th></tr>
					</thead><tbody>';
				while($row = $stmt->fetch()){
					$html .= '<tr>
					<td style="text-align: center; cursor: pointer;" title="Click to see who has this movie." class="has-movie" data-id="'.$row['movie_id'].'">
						<h4>'.$row['movie_name'].'</h4>
					</td>
					<td style="text-align:center;">
						<input type="button" title="Status of the movie: You have it or not. Click to change the status." class="btn ';
					if(in_array($row['movie_id'], $movies)){
						$html .= 'btn-success ';
						$value = 'You have it!';
					}
					else{
						$html .= 'btn-default ';
						$value = 'You don\'t have it!';
					}
					$html .= 'have_btn" value="'.$value.'" data-movie="'.$row['movie_id'].'"/>
					</td>
					<td>
						<select data-movie="'.$row['movie_id'].'" class="rating" style="margin-bottom: 0; width: 120px;">'.$ratings.'
						</select>&nbsp;&nbsp;
						<span style="font-size: 12px;">Avg Rating: '.round($row['avg_rating'], 2).'</span>
					</td>
					</tr>';
					$i++;
				}
				echo $html.'</tbody></table>';
			?>
			</div>
		</div>
		<div class="span2" style="margin-left: 0px; text-align: center; position: fixed; right: 5px;">
			<div class="row">
				<h2>Recommended Movie</h2>
			</div>
			<div class="row recommendation" style="margin-top: 25px;">
				<?php 
					include_once 'recommendation.php';
					Recommendation($user_id, $dbh);
				?>
			</div>
		</div>
	</div>
	</div>
</div>	
<?php
	}
?>
<script type="text/javascript">var user_id = <?php echo $user_id; ?>;</script>
<?php
}
else{
?>
<div class="container" style="min-height: 550px">
	<div class="row" style="margin-top: 100px">
		<div class="row" style="text-align:center">
			<h1>H-4's Movie World</h1>
			<p>H-4's centralized movie database.</p>
		</div>
		<div class="row" style="text-align: center; margin-top: 50px">
			<input type="text" id="login-name" placeholder="Your Full Name" class="span4" style="height: 27px; margin-bottom: 0px !important"/>
			<input type="text" id="login-room" placeholder="Room eg: dorm1, 423" class="span2" style="height: 27px; margin-bottom: 0px !important"/>
			<input type="button" class="btn btn-primary" id="register" value="Login"/>
		</div>
	</div>
	</div>
</div>
<?php
}

?>
<div class="messagebox" id="messagebox">
	<p></p>
	<p id="messagebox-button-p"><input type="button" id="messagebox-button-ok" class="btn btn-primary" value="Okay!"/></p>
</div>
</body>
<script type="text/javascript">var ip_addr = '<?php echo $_SERVER['REMOTE_ADDR']; ?>';</script>
<script type="text/javascript" src="jquery.js" ></script>
<script type="text/javascript" src="index.js"></script>
</html>
