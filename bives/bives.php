<?php
ini_set('max_execution_time', 600);
$BIVES = "https://bives.bio.informatik.uni-rostock.de/";
$DOCS = "/simpleMerge.php";
$storage = '/tmp/mergestorage';

$bivesJob = $_POST['bivesJob'];
$jobID = $_POST['job'];
$saveMerge = TRUE;


if (!isset($bivesJob) || empty($bivesJob))
	die("no job description");

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $BIVES);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $bivesJob);

$headers = array();
$headers[] = 'Content-Type: application/json';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);


if ($saveMerge) {
	$dir = $storage . '/' . $job;

	$decodeResult = json_decode($result)->merge;
	file_put_contents($dir . "/mergedModel", $decodeResult);
}

echo $result;
?>