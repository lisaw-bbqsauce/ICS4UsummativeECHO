<?php
/* Starts session and checks if user logged in or not */
session_start();
$score = $_POST['postScore'];

if( isset( $_SESSION['sScore'] ) ) {
	//user has logged in	
	$_SESSION['sScore'] += $score;
}else {
	//not login, use Guest as user ID
	$_SESSION['sScore'] = $score;
}		

$loginID = $_SESSION['sLoginID'];

/* Server information */
$servername = "localhost:3306";
$username = "root";
$password = "Password1";
$dbname = "TestDB";

//https://phpdelusions.net/pdo_examples/select

$sessionScore = $_SESSION['sScore'];

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	// select a particular row by id
	$sqlRet = "SELECT login_ID, best_score, last_login_date FROM tuser WHERE login_ID = ?";
    $stmt = $conn->prepare($sqlRet);
    $stmt->execute([$loginID]);

    // set the resulting array to associative
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
	$row = $stmt->fetch();
	if($row){
		$bestScore = $row["best_score"];
		
		//check score
		if($sessionScore > $bestScore){
			//update the best score
			$sqlUpdate = "UPDATE tuser SET best_score = ?, last_login_date = ? WHERE login_ID = ?";
			$stmt = $conn->prepare($sqlUpdate);
			$stmt->execute([$sessionScore, date("Y-m-d"), $loginID]);
			echo "High score: ".$sessionScore;
		}else{
			echo "Your score is: ".$sessionScore;
			echo " High score: ".$bestScore." by ".$loginID;
		}
	}else{
		echo "no record in DB with login_ID = ".$loginID;
	}
}
catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$conn = null;

//echo "<br>Done";
?>
