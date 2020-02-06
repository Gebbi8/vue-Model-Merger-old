<?php
$Bives = "/simpleMerge.php";
$storage = '/tmp/mergestorage';
$f1 = $_FILES['file1'];
$f2 = $_FILES['file2'];
$job = $_GET['jobID'];
$getFile = $_GET['getFile'];



	echo $job ." <---> " .$getFile;


if (isset($f1) && !empty($f2) && isset($f2) && !empty($f2) && !isset($job)) {
	// save both to $storage
	$rnd = md5(time());
	while (is_dir($storage . '/' . $rnd)) $rnd = md5(time ());
	$dir = $storage . '/' . $rnd;
	mkdir($dir, 0750, true);
	move_uploaded_file($_FILES['file1']['tmp_name'], $dir . '/f1');
	move_uploaded_file($_FILES['file2']['tmp_name'], $dir . '/f2');

	echo $rnd;

} else if (isset($job) && !empty($job) && isset($getFile) && !empty($getFile) &&
	!preg_match('[^A-Za-z0-9]', $job)
	){
		if(!file_exists($storage . '/' . $job . '/' . $getFile) ){
			//build bivesJob and call bives.php
			$bivesJobArr = array(
				"files" => [
					$storage . '/' . $job . '/f1',
					$storage . '/' . $job . '/f2'
				],
				"commands" => ["merge"],
			);			
			$bivesJobArr = json_encode($bivesJobArr);
	


			$ch = curl_init();
			//Get first file if the files where uploaded top temp
			curl_setopt($ch, CURLOPT_URL, $Bives . "?bivesJob=" . $job);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$f1 = curl_exec($ch);
			if (curl_errno($ch)) {
				echo 'Error!!!!:' . curl_error($ch);
			}
			curl_close($ch);
		}

		header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
		header("Content-Type: file/xml");
		header("Content-Transfer-Encoding: Binary");
		header("Content-Length:".filesize($storage . '/' . $job . '/' . $getFile));
		header("Content-Disposition: attachment; filename=mergedModel.xml");

		$filename = $storage . '/' . $job . '/' . $getFile;

		$openFile = fopen($filename, "r");
		$readFile = fread($openFile, filesize($filename));
		fclose($handle);

		
		echo $readFile;
		
} else {
	echo "failed";
}
?>
