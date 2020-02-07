<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$BIVES = 'https://merge-proto.bio.informatik.uni-rostock.de/bives/bives.php';
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
/*	$bivesJobArr = new \stdClass();
	$bivesJobArr->success = false;
	$bivesJobArr->files = array($readFile1, $readFile2);
	$bivesJobArr->commands = array("merge");
*/
	$postField = array();
	$postField['bivesJob'] = json_encode(array(
		'files' => array(
			$readFile1,
			$readFile2
		),
		'commands' => array(
			"merge"
		)

		));


	$bivesJob = json_encode($postField, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE |JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);


	$curl = curl_init();
	curl_setopt($curl,CURLOPT_URL,$BIVES);
	curl_setopt($curl,CURLOPT_FOLLOWLOCATION,true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt($curl, CURLOPT_AUTOREFERER, true );
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );
	curl_setopt($curl, CURLOPT_USERAGENT, "stats website diff generator");
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $postField);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array ("Content-Type: application/json"));
	
	$result = curl_exec($curl);
	curl_close($curl);
	if ($result === false) {
		throw new Exception(curl_error($curl), curl_errno($curl));
	} else {echo $result;}
	
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