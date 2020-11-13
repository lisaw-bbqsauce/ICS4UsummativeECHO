<?php

//echo "working"

$servername = "localhost:3306";
$username = "root";
$password = "Password1";
$dbname = "TestDB";

//https://phpdelusions.net/pdo_examples/select


try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sqlCount = "SELECT count(*) as total FROM twords";
	 $stmt = $conn->prepare($sqlCount);
	 // set the resulting array to associative
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $stmt->execute();
	$row = $stmt->fetch();
	$wordCount = $row['total'];
		
	// select a particular row by id
	$id = rand(1, $wordCount);//generate a random nmber between 1 and word count inclusively.
	$sqlWord = "SELECT id, word, definition, level_of_difficulty FROM twords WHERE id=:id";
    $stmt = $conn->prepare($sqlWord);
    $stmt->execute(['id' => $id]);

    // set the resulting array to associative
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
	$row = $stmt->fetch();
	if($row){
		$word = trim($row['word']);
		$definition = trim($row['definition']);
		$hint = substr($word, 0, 2);
		//add to the associated array
		$row += array("hint" => $hint);
		$row += array("word" => $word);
		$row += array("definition" => $definition);
		echo json_encode($row);
	}else{
		echo "no record in DB with id = ".$id;
	}
}
catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$conn = null;
?>