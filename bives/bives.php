<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


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

curl_setopt($curl,CURLOPT_URL,$BIVES);
curl_setopt($curl,CURLOPT_FOLLOWLOCATION,true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false );
curl_setopt($curl, CURLOPT_AUTOREFERER, true );
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );
curl_setopt($curl, CURLOPT_USERAGENT, "stats website diff generator");
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $bivesJob);
curl_setopt($curl, CURLOPT_HTTPHEADER, array ("Content-Type: application/json"));

$headers = array();
$headers[] = 'Content-Type: application/json';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if ($result === false) {
    throw new Exception(curl_error($ch), curl_errno($ch));
}

echo curl_getinfo($curl) . '<br/>';
echo curl_errno($curl) . '<br/>';
echo curl_error($curl) . '<br/>';
curl_close($ch);


if ($saveMerge) {
	$dir = $storage . '/' . $job;

	$decodeResult = json_decode($result)->merge;
	file_put_contents($dir . "/mergedModel", $decodeResult);
}

echo "Result from BiVeS" . $result;
?>