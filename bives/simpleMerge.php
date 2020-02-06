<?php
$BIVES = 'bives.php';
$storage = '/tmp/mergestorage';
$f1 = $_FILES['file1'];
$f2 = $_FILES['file2'];
$job = $_GET['jobID'];
$getFile = $_GET['getFile'];






if (isset($f1) && !empty($f2) && isset($f2) && !empty($f2) && !isset($job)) {
	// save both to $storage
	$rnd = md5(time());
	while (is_dir($storage . '/' . $rnd)) $rnd = md5(time());
	$dir = $storage . '/' . $rnd;
	mkdir($dir, 0750, true);
	move_uploaded_file($_FILES['file1']['tmp_name'], $dir . '/f1');
	move_uploaded_file($_FILES['file2']['tmp_name'], $dir . '/f2');

	//build bivesJob and call bives.php
	$bivesJob = array(
		"files" => [
			$dir . '/f1',
			$dir . '/f2'
		],
		"commands" => ["merge"],
	);

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $BIVES);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_AUTOREFERER, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_USERAGENT, "stats website diff generator");
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $bivesJob);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
	
	$result = curl_exec($curl);
	curl_close($curl);

	echo $rnd;
	
} else if (
	isset($job) && !empty($job) && isset($getFile) && !empty($getFile) &&
	!preg_match('[^A-Za-z0-9]', $job) &&
	file_exists($storage . '/' . $job . '/' . $getFile)
) {
	header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
	header("Content-Type: file/xml");
	header("Content-Transfer-Encoding: Binary");
	header("Content-Length:" . filesize($storage . '/' . $job . '/' . $getFile));
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