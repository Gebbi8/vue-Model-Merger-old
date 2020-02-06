<?php

ini_set('max_execution_time', 600);
$BIVES = "https://bives.bio.informatik.uni-rostock.de/";
$DOCS = "/simpleMerge.php";
$storage = '/tmp/mergestorage';


$saveMerge = FALSE;
$postParams =  $_POST["postParams"];
$paramDecode = json_decode($postParams);
$job = $paramDecode->jobID[0];
$bivesJob = $paramDecode;
unset($bivesJob->jobID);
if (in_array("merge", $bivesJob->commands)) {
	$saveMerge = TRUE;
}

if (!isset($bivesJob) || empty($bivesJob)){
	//die("no job description");
}


if (isset($job) && !empty($job)) {




	// Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
	$ch = curl_init();
	//Get first file if the files where uploaded top temp
	curl_setopt($ch, CURLOPT_URL, $DOCS . "?jobID=" . $job . "&getFile=" . "f1");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$f1 = curl_exec($ch);
	if (curl_errno($ch)) {
		echo 'Error!!!!:' . curl_error($ch);
	}
	curl_close($ch);

	//Get second file if the files where uploaded top temp
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $DOCS . "?jobID=" . $job . "&getFile=" . "f2");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$f2 = curl_exec($ch);
	if (curl_errno($ch)) {
		echo 'Error!!!!!---_!:' . curl_error($ch);
	}
	curl_close($ch);



	//construct new BiVeS Job
	$bivesJob->files[0] = $f1;
	$bivesJob->files[1] = $f2;
}


$bivesJob = json_encode($bivesJob);
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

if ($saveMerge) {
	$dir;
	if(empty($job) || !isset($job)){
		$rnd = md5(time());
		while (is_dir($storage . '/' . $rnd)) $rnd = md5(time ());
		$dir = $storage . '/' . $rnd;
		mkdir($dir, 0750, true);
	} else {
		$dir = $storage . '/' . $job;
	}

	$decodeResult = json_decode($result)->merge;
	file_put_contents($dir . "/mergedModel", $decodeResult);
}

echo $result;
?>