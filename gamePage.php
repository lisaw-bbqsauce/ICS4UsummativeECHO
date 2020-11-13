<!-- Game page, main page, where the user plays the game -->

<?php
	/* starts session and checks if the user has logged in or not */
	session_start();
	if( isset( $_SESSION['sLoginID'] ) ) {
		//user has logged in	
	}else {
		//not login, use Guest as user ID
		$_SESSION['sLoginID'] = "Guest";
	}
	$sLoginID = $_SESSION['sLoginID'];
		
	$msg = "You are logged in as ".  $_SESSION['sLoginID']." in this session.";
?>

<!DOCTYPE html>
<html>
<head>
<title>ECHO Game</title>
<script type="text/javascript" src="jquery-3.5.1.js"> </script>
<!-- Library for the dropdown arrow -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet"
	href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<!-- Including navbar code from navBar.css -->
<link href="navBar.css" rel="stylesheet">
<style>
	body {
		background-color: #B0A0CF;
		text-align: center;
	}
		
	p {
		color: black;
		text-align: center;
		font-family: "Segoe UI", sans-serif;
		padding: 0px;
		margin: 0px;
		font-size: 20px;
	}

	canvas {
		border:1px solid #d3d3d3;
		background-color: white;
	}
	
	table, tr, td {
		border: 1px solid black;
		border-collapse: collapse;
		width:1%;
		white-space:nowrap;
		margin-left: auto;
		margin-right: auto;
	}
				
	.button {
		top: 50%;
		border: none;
		color: white;
		padding: 15px 30px;
		text-align: center;
		text-decoration: none;
		display: inline-block;
		font-size: 20px;
		margin: 4px 4px;
		transition-duration: 0.1s;
		cursor: pointer;
	}

	.button1 {
		margin: 0px auto;
		display: block;
		background-color: white;
		color: #5F1D9A;
	}

	.button1:hover {
		background-color: #5F1D9A;
		color: white;
	}
	
	.grid-container {
		display: grid;
		grid-template-columns: auto;
		grid-template-rows: auto 70px auto auto;
		background-color: #B0A0CF;
		padding: 0px;
	}

	.grid-item {
		background-color: #B0A0CF;
		text-align: center;
		padding: 10px;
		display: flex;
		justify-content: center;
		align-items: center;
	}
	
	.grid-item2 {
		background-color: white;
		text-align: left;
		padding: 10px;
		display: flex;
		margin: 0px 190px;
		justify-content: left;
		align-items: left;
	}
	
</style>
</head>
<body onload="startGame()"> <!-- Calls the startGame() function when the page loads -->
	<!-- Navbar links -->
	<ul>
		<li><a href="aboutPage.html">About us</a></li>
		<li class="dropdown"><a href="javascript:void(0)" class="dropbtn">Account
				<i class="fa fa-caret-down"></i>
		</a>
			<div class="dropdown-content">
				<a href="accountPage.html">Account</a> 
				<a href="registerLoginPage.html">Register/Login</a> 
				<a href="progressPage.html">Progress</a>
			</div>
		</li>
		<li> <a href="landingPage.html">Home</a></li>
		<li style="float: left"><a class="active" href="landingPage.html">ECHO</a></li>
	</ul>

	<script>
		
		var loginID = "<?php echo $sLoginID; ?>";
		var definition = "";
		var fullWord = "";
		var word = "";
		var difficulty = 0;
		var message = "";

		//Game set up, adds the canvas and all the other parts
		function startGame() {
			message = "";
			myDefinition = new component(50, 50, "blue", 500, 110, definition);
			myWord = new component(50, 50, "#5F1D9A", 500, 160, word);
			myMessage = new componentMsg(30, 30, "black", 500, 210, message);
			myGameArea.start();
		}

		//Creating the canvas
		var myGameArea = {
			canvas : document.createElement("canvas"),
			start : function() {
				this.canvas.width = 1000;
				this.canvas.height = 250;
				this.context = this.canvas.getContext("2d");
				this.canvas.textAlign = 'center';
				document.body.insertBefore(this.canvas, document.body.childNodes[0]);
				this.interval = setInterval(updateGameArea, 20); //Calls update function every 20 miliseconds
			},
			clear : function() {
				this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);
			}
		}

		//Component that writes the word and definition on the canvas
		function component(width, height, color, x, y) {
			this.width = width;
			this.height = height;
			this.x = x;
			this.y = y;
			this.update = function(text) {
				ctx = myGameArea.context;
				ctx.fillStyle = color;
				ctx.font = "40px Arial";
				ctx.textAlign = 'center';
				ctx.fillText(text, this.x, this.y);
			}    
		}
		
		//Message component, writes the message at the bottom of the canvas
		function componentMsg(width, height, color, x, y) {
			this.width = width;
			this.height = height;
			this.x = x;
			this.y = y;
			this.update = function(text) {
				ctx = myGameArea.context;
				ctx.fillStyle = color;
				ctx.font = "20px Arial";
				ctx.textAlign = 'center';
				ctx.fillText(text, this.x, this.y);
			}    
		}

		//Updates the canvas
		function updateGameArea() {
			myGameArea.clear();
			myWord.update(word);
			myDefinition.update(definition);
			myMessage.update(message);
		}

		//Function for each button. When button is clicked, it adds the value to the variable "word"
		//When word matches fullWord, it calls the add_score() function"
		function clickButton(text) {
			word = word + text; 
			if (word.localeCompare(fullWord) == 0) {
				add_score();
			}
		}
		
		//Backspace button, won't delete the given letters
		function clickBackspace() {
			if (word.length > 2) {
				word = word.substring(0, word.length - 1);		
			}
		}
		
		function add_score() {
			var score = 10 * difficulty;			
			$.post('add_score.php', {postLoginID:loginID,postScore:score}, //id and score are the variables defined
			function(data) //variable "data" contains the data sent from server script "add_score.php" 
			{
				message = data;
				myMessage.update(message);				
			});
		}
		
		//submit to server to get a new word from DB
		function retrieve_new_word(){	
			$.post('retrieve_random_word.php', {},
			function(data) //variable "data" contains the data sent from server script "retrieve_random_word.php" 
			{				
				var objWord = JSON.parse(data);
				definition = objWord.definition;
				fullWord = objWord.word;				
				word = objWord.hint;
				difficulty = objWord.level_of_difficulty;
				startGame();
			});
		}

	</script>

<div class="grid-container">
	<div class="grid-item">	
		<p> <?php  echo ( $msg ); ?> </p> 
	</div>
	<div class="grid-item">	
		<p>Read the blue definition and try to complete the word by clicking the buttons with the correct letters.</p>
	</div>
	<div class="grid-item">
		<button class="button button1" id=playAgainBtn onclick="retrieve_new_word()"><b>New Word</b></button>
	</div>
	<div class="grid-item2">
		<div style="text-align:center;width:600px;">
		   <table style="width:100%" class ="table">
				<!-- Table of all the buttons in QWERTY order -->
				<tr>
					<td> <button class="button button1" onclick="clickButton('q')">q</button> </td>
					<td> <button class="button button1" onclick="clickButton('w')">w</button> </td>
					<td> <button class="button button1" onclick="clickButton('e')">e</button> </td>
					<td> <button class="button button1" onclick="clickButton('r')">r</button> </td>
					<td> <button class="button button1" onclick="clickButton('t')">t</button> </td>
					<td> <button class="button button1" onclick="clickButton('y')">y</button> </td>
					<td> <button class="button button1" onclick="clickButton('u')">u</button> </td>
					<td> <button class="button button1" onclick="clickButton('i')">i</button> </td>
					<td> <button class="button button1" onclick="clickButton('o')">o</button> </td>
					<td> <button class="button button1" onclick="clickButton('p')">p</button> </td>
					<td> <button class="button button1" onclick="clickBackspace()">backspace</button> </td>
				</tr>
				<tr>
					<td> </td>
					<td> <button class="button button1" onclick="clickButton('a')">a</button> </td>
					<td> <button class="button button1" onclick="clickButton('s')">s</button> </td>
					<td> <button class="button button1" onclick="clickButton('d')">d</button> </td>
					<td> <button class="button button1" onclick="clickButton('f')">f</button> </td>
					<td> <button class="button button1" onclick="clickButton('g')">g</button> </td>
					<td> <button class="button button1" onclick="clickButton('h')">h</button> </td>
					<td> <button class="button button1" onclick="clickButton('j')">j</button> </td>
					<td> <button class="button button1" onclick="clickButton('k')">k</button> </td>
					<td> <button class="button button1" onclick="clickButton('l')">l</button> </td>
					<td> </td>
				</tr>
				<tr>
					<td> </td>
					<td> </td>
					<td> <button class="button button1" onclick="clickButton('z')">z</button> </td>	
					<td> <button class="button button1" onclick="clickButton('x')">x</button> </td>
					<td> <button class="button button1" onclick="clickButton('c')">c</button> </td>
					<td> <button class="button button1" onclick="clickButton('v')">v</button> </td>
					<td> <button class="button button1" onclick="clickButton('b')">b</button> </td>
					<td> <button class="button button1" onclick="clickButton('n')">n</button> </td>
					<td> <button class="button button1" onclick="clickButton('m')">m</button> </td>
					<td> </td>
					<td> </td>
				</tr>
		</div>
	</div>
</body>
</html>