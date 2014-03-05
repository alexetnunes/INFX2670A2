<?php

#Delete any files pass through properly

if(isset($_GET['file'])){

$checkfile=explode('/',$_GET['file']);
		
$file='filestore/'.end($checkfile);

unlink($file);

header('Location: index.php ');
exit;}


?>

<!DOCTYPE html>	
	<head>

        <meta charset="utf-8">
        <meta name="author" content="Alex Nunes">
        <meta name="description" content="Assignement Two INFX 2670">

        <title>Assignment Two: Uploads</title>
		
		<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" media="all" />
 		<link href="css/custom.css" rel="stylesheet" type="text/css" media="all" />
 		

 		<script type="text/javascript" src="js/jquery-2.0.3.min.js"></script> 
 		<script type="text/javascript" src="js/bootstrap.min.js"></script>		
 		<script type="text/javascript" src="js/custom.js"></script>

    </head>

	
	<body>

	<div id="wrap">
		<h2>Alex Nunes - Assignment 2 - INFX 2670</h2>
		
	
		
		<div id="uform">
		
		
		<form action="" method="post" enctype="multipart/form-data" class="form-inline"role="form">
			<div class="form-group">
			<label for="filename">File Upload</label>
			<input type="file" name="filename" id="filename"><br>
			</div>		
			<input type="submit" name="filesubmit" class="btn btn-primary" value="Upload">
			

		</form>
		</div>
		
		
		

			
			
			<?php
			
			#Set for use below
			#Strips script takes for modified
			$content=str_replace(array('<script>','</script>'), '',file_get_contents($_FILES['filename']['tmp_name']));
			$filename=pathinfo($_FILES['filename']['name'], PATHINFO_FILENAME);
			$ext='.'.pathinfo(($_FILES['filename']['name']), PATHINFO_EXTENSION);
			$filecheck='filestore/'.$filename.'-original';
			$modID;

	
			#Check if upload button has been set
			if (isset($_POST['filesubmit'])){
			
			#Check for valid file type and print error if false or empty
			if($_FILES['filename']['type']!='text/plain'||$content==''){
				echo '<p id="er"> Please upload a valid text file.</p>';
			}else{
				
				#Make directory if it doesn't exist yet
				mkdir('./filestore');
				
				#Check if file exists
				if(!file_exists($filecheck.$ext)){
					#Move file if does not exist
					move_uploaded_file($_FILES['filename']['tmp_name'], './'.$filecheck.$ext);
				}else{
					# Variables and a loop to apend a number to the file name
					# if the file already exists and the move it to the 
					# Filestore directory 
					$tempcheck=$filecheck;
					$id=0;					
					while (file_exists($tempcheck.$ext)){
						++$id;
						$tempcheck= $filecheck."-".$id;
					}
					move_uploaded_file($_FILES['filename']['tmp_name'], './'.$tempcheck.$ext);
					$modID .= "-".$id;
					
					#Set permissions
					chmod($tempcheck.$ext, 444);

				}
			
			# Sets Regex patterns and replacements
			$content=preg_replace('/\(\d{3}\)/','',$content);
			$pattern1='/(\d{3})\.(\d{3})\.(\d{4})/';
			$pattern2='/(\d{3})-(\d{4})/';
			$pattern3='/((http|https|ftp|ftps|idap|news|mailto|tel|telnet|urn)\:\S+)/';
			$replacement1='<em>$1-$2-$3</em>';
			$replacement2='<em>902-$1-$2</em>';
			$replacement3='<a href="$1">$1</a>';
			
			#Replace any patterns found in the file
			$content=preg_replace($pattern1, $replacement1, $content);
			$content=preg_replace($pattern2, $replacement2, $content);
			$content=preg_replace($pattern3, $replacement3, $content);
			
			
			#Save the modified document to filestore
			$X=fopen('filestore/'.$filename.'-modified'.$modID.$ext, 'w+');
			fwrite($X, $content);
			fclose($X);
			
			#Set permissions
			chmod($filename.'-modified'.$modID.$ext.$ext, 444);
			
			#Set Timezone
			date_default_timezone_set('Canada/Atlantic');
			
			#Create or append file containing upload dates of all files in filestore
			if(file_exists('upload_dates.txt')){
				$uploadDate=fopen('upload_dates.txt', 'a');
			}else{
				$uploadDate=fopen('upload_dates.txt', 'w+');
			}
			fwrite($uploadDate, $filename.'-original'.$modID.$ext."@".date('r')."@");
			fwrite($uploadDate, $filename.'-modified'.$modID.$ext."@".date('r')."@");
			fclose($uploadDate);
			}
			}

			
			?>
			
			
			<div class="output">
			<h4>Description:</h4>
				<ul>
					<li><strong>Download</strong> link causes a direct download of the file.</li>
					<li><strong>View</strong> displays the file content on a new page along with the number 
						words that begin with "t" and end with "e".</li>
					<li><strong>Delete</strong> immediately deletes the file with no extra prompt.</li>
				</ul>
			</div>
						
			<div class="output">
		
			<?php
			#Open filestore directory and add content names to fileList array
			if (is_dir('filestore')){
			$textDir=opendir('filestore');
			
			while ($entry = readdir($textDir)){
					$fileList[]=$entry;
			}
			
			closeDir($textDir);
			
			#Get fileList length
			$idx=count($fileList);
			
			#Sort fileList
			sort($fileList);
			?>
			
			<table class="table">
				<tr>
					<th>Filename</th>
					<th>Upload Date</th>
					<th>Download</th>
					<th>View</th>
					<th>Delete</th>
				</tr>
				<tbody>
				<?php
				
				#Read file and create uDates array to hold file names and dates
				$stringOdates=file_get_contents('upload_dates.txt');
				
				$uDates=explode("@", $stringOdates);
				
				$last=-1;
				
				# Loops through to print rows in a table for each file in filestore
				# File Name | Upload Time | Download | View | Delete
				# Download cause a direct download of the file
				# View display the file content on a new page along with the number
				# words that begin with "t" and end with "e"
				# Delete immediately deletes the file with no extra prompt
				for($fle=0; $fle<$idx; $fle++){
					if(($fileList[$fle] != '.') && ($fileList[$fle] != '..')){ 
						echo "<tr><td>$fileList[$fle]</td>";
						
						$fleID= 'filestore/'.$fileList[$fle];
						
						if(in_array($fileList[$fle], $uDates)){
							foreach($uDates as $key => $value){
								if($value == $fileList[$fle]){
									$last=$key;
								}
							}
							echo '<td>'.$uDates[$last+1].'</td>';
						}
						echo '<td><a href="download.php?file='.$fileList[$fle].'">Download</a></td>';
						echo '<td><a href="view.php?file='.$fileList[$fle].'">View</a></td>';
						echo '<td><a href="index.php?file='.$fileList[$fle].'">Delete</a></td></tr>';
					}
				}
			}
			
				
				?>
				
				</tbody>
			</table>
		</div>
	</div>
	</body>
	
</html>