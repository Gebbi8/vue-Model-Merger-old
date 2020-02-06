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
	$dir = $storage . '/' . $job;

	$decodeResult = json_decode($result)->merge;
	file_put_contents($dir . "/mergedModel", $decodeResult);
}

echo $result;
?>