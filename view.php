<!DOCTYPE html>	
	<head>

        <meta charset="utf-8">
        <meta name="author" content="Alex Nunes">
        <meta name="description" content="Assignement Two INFX 2670">

        <title>Assignment Two: Upload Views</title>
		
		<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" media="all" />
 		<link href="css/custom.css" rel="stylesheet" type="text/css" media="all" />
 		

 		<script type="text/javascript" src="js/jquery-2.0.3.min.js"></script> 
 		<script type="text/javascript" src="js/bootstrap.min.js"></script>		
 		<script type="text/javascript" src="js/custom.js"></script>

    </head>

	
	<body>

	<div id="wrap">
	
		
		<h2>Alex Nunes - Assignment 2 - INFX 2670</h2>

		<a class="output" href="index.php">Back to Directory</a>
		
		<div class="output">
		
		
		
		<?php
		
		#Check file existance in filestore directory
		$checkfile=explode('/',$_GET['file']);
		
		$file='filestore/'.end($checkfile);
		
		# Check to see if file exists
		# If it exists, javascript tags are removed
		if(!file_exists($file)){
			echo '<p id="er">File does not exist.</p>';
		}else{
		$content= file_get_contents($file);
		$content=str_replace(array('<script>','</script>'), '', $content);
		
		#Display number of matches beginning with "t" and ending with "e".
		echo '<p> The number of words that begin with "t" and end in "e" are: '.preg_match_all('/\bt\w+e\b/i', $content).'</p><br>';
		
		#Display file content
		echo $content;
		}
		?>
		
		</div>
	</div>
	
	
	
	</body>
</html>