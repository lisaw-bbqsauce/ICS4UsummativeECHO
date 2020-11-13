<?php
session_start();

$loginID = $_POST['postloginID']; //Lisa123@gmail.com
$loginPassword = $_POST['postloginPassword']; //123456

$servername = "localhost:3306";
$username = "root";
$password = "Password1";
$dbname = "testdb";
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$sqlUser = "SELECT first_name, best_score, last_login_date FROM tuser WHERE login_ID = ? AND password = ?";
    $stmt = $conn->prepare($sqlUser);
    $stmt->execute([$loginID, $loginPassword]);
    // set the resulting array to associative
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
	$row = $stmt->fetch();
	if($row){
		$lastLoginDate = $row['last_login_date'];
		$bestScore = $row['best_score'];
		$firstName = $row['first_name'];
		//save login ID to session
		$_SESSION['sLoginID'] = $loginID;
		echo "Success";
		
	}else{		
		echo "Invalid login ID or password"; 
	}
}
catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$conn = null;


?>