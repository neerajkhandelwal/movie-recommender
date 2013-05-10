<?php

/*function exceptions_error_handler($severity, $message, $filename, $lineno) {
	if (error_reporting() == 0) {
	    return;
	}
	if (error_reporting() & $severity) {
		throw new ErrorException($message, 0, $severity, $filename, $lineno);
	}
}
set_error_handler('exceptions_error_handler');*/

function Recommendation($user_id, $dbh){
	$query = 'SELECT * FROM `user_rating` ORDER BY `user_id`';
	$stmt = $dbh->prepare($query);
	$return = $stmt->execute();
	
	$ratings = array();

	while($row = $stmt->fetch()){
		$ratings[$row['user_id']][$row['movie_id']] = $row['rating'];
	}
	$recommendations = getRecommendation($ratings, $user_id);
	$html = '';
	if(!empty($recommendations)){
		$movies = getMovies($recommendations[0], $dbh, $recommendations[1]);
	
		foreach($movies[0] as $movie){
			$html .= '
				<div class="span12" style="margin-top: 10px;">
					<h4 style="cursor:pointer;" title="Click to see who has this movie." class="has-movie" data-id="'.$movie[1].'">'.$movie[0].'</h4><span>Based on likes of: ';
			
			foreach($movies[1][$movie[0]] as $user){
				$html .= $user.', ';
			}
			
			$html = rtrim($html, " ,");
			$html .= '</span></div>';
		}
	}
	echo $html;
}
	

	
function similarity($prefs, $p1, $p2){
	$si = array();
	if(!array_key_exists($p1, $prefs)) return 0;
	foreach($prefs[$p1] as $item => $value){	
        	if(array_key_exists($item, $prefs[$p2])){
                	$si[$item] = 1;
	        }
	}

	$n = sizeof($si);
	if($n == 0)
		return 0;
		#For numerator sigma(x*y) - (sigma(x)* sigma(y)/n)
	#              sigma(x*y) - ( sum1 * sum2 / n)
	$sum1 = 0;
	$sum2 = 0;
	$pSum = 0;
	$sum1sq = 0;
	$sum2sq = 0;
	foreach($si as $it => $rate){
		$sum1 += $prefs[$p1][$it];
		$sum2 += $prefs[$p2][$it];        	
		$pSum += $prefs[$p1][$it] * $prefs[$p2][$it];
		$sum1sq += $prefs[$p1][$it] * $prefs[$p1][$it];
		$sum2sq += $prefs[$p2][$it] * $prefs[$p2][$it];
	}
		#For denominator underoot( ( sigma(x^2)-((sigma(x))^2/n) ) * ( sigma(y^2)-((sigma(y)^2)/n) ) )
	#                underoot( ( sum1sq-(sum^2/n) ) * ( sum2sq-((sum2^2)/n) ) )
	$num = $pSum - ($sum1 * $sum2 / $n);
	$den = sqrt(($sum1sq - (($sum1 * $sum1) / $n)) * ($sum2sq - (($sum2 * $sum2) / $n)));
	if($den == 0)
		return 0;
		$sim_corr = $num/$den;
	return $sim_corr;
}

function getRecommendation($prefs, $person, $n = 5){
	$totals = array();
	$simSums = array();
	$based_on_users = array();
	foreach($prefs as $other=>$val){
	        if($other == $person) continue;
	        $sim = similarity($prefs, $person, $other);
	        if($sim <= 0) continue;
	        foreach($prefs[$other] as $item => $rating){
	                if(!in_array($item, array_keys($prefs[$person]))){
	                        $totals[$item] = 0;
	                        $totals[$item] += $prefs[$other][$item]*$sim;
 
	                        $simSums[$item] = 0;
	                        $simSums[$item] += $sim;
	                        $based_on_users[$item][] = $other;
	                }
	        }
 	}
	$rankings = array();
	foreach($totals as $item => $total){
		$rankings[$total] = $item;
	}
	krsort($rankings);
	$list = array();
	foreach($rankings as $total => $item){
		$list[] = $item;
	}
	return array($list, $based_on_users);
}

function getUser($dbh){
	$query = 'SELECT `user_id`, `name` FROM `users`';
	$stmt = $dbh->prepare($query);
	$return = $stmt->execute();
	$user = array();
	while($row = $stmt->fetch()){
		$user[$row['user_id']] = $row['name'];
	}
	return $user;
}	

function getMovies($r, $dbh, $u){
	$users = getUser($dbh);
	$related = array();
	$query = 'SELECT `movie_id`, `movie_name` FROM `movies` WHERE `movie_id` = ?';
	$stmt = $dbh->prepare($query);
	$movies = array();
	foreach($r as $i){
		$stmt->bindParam(1, $i, _int);
		$return = $stmt->execute();
		
		while($row = $stmt->fetch()){
			$movies[] = array($row['movie_name'], $row['movie_id']);
			foreach($u[$i] as $id){
				$related[$row['movie_name']][] = $users[$id];
			}
		}

	}
	return array($movies,$related);
}
	
?>
