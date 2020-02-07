<?php
$BIVES = '/bives.php';
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

	$filename = $dir . '/f1';
	$openFile = fopen($filename, "r");
	$readFile1 = fread($openFile, filesize($filename));
	fclose($openFile);

	$filename = $dir . '/f2';

	$openFile = fopen($filename, "r");
	$readFile2 = fread($openFile, filesize($filename));
	fclose($openFile);

	//build bivesJob and call bives.php
	$bivesJobArr = array(
		'files' => array(
			$readFile1, $readFile2
		),
		'commands' => array("merge")
	);
	$bivesJob = json_encode($bivesJobArr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE |JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);

	echo "encode result:";
	echo $bivesJob;
	echo "check";

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $BIVES);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $bivesJob);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	
	$result = curl_exec($curl);
	echo curl_getinfo($curl) . '<br/>';
echo curl_errno($curl) . '<br/>';
echo curl_error($curl) . '<br/>';
	curl_close($curl);
var_dump("!!---!!",$result);
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
	fclose($openFile);


	echo $readFile;
} else {
	if(!file_exists($storage . '/' . $job . '/' . $getFile)) echo "file does not exists .." ;
	echo "  FAILED! ---> getFile:" . $getFile . " job: " . $job;
}
?>