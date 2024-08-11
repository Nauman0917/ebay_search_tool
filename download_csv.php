<?php
 // download the csv file
 
 	$filename = $_GET['fname'];
		
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.basename($filename));
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($filename));
			ob_clean();
			flush();
			readfile($filename);
			//exit;
		
?>