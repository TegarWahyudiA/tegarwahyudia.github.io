<?php
$ip = '10.133.2.121';

// ganti alamat ip di atas
// 
if(!ping($ip)) echo ("<br><span style='color:red'>error: ping gagal</span>");
if(!function_exists('curl_init')) die("<br><span style='color:red'>error: extension php_curl belum aktif</span>");

$ip=(isset($_GET['ip']))?$_GET['ip'] : $ip;
$tgl= (isset($_GET['tgl']))? $_GET['tgl'] : date('Y-m-d'); 
$tgl2= (isset($_GET['tgl2']))? $_GET['tgl2'] : $tgl; 
$absen = get($ip, $tgl, $tgl2);

if(count($absen)>1){
	echo '<pre>';
		foreach($absen as $row){
			echo json_encode($row)."\n";
		}
//	echo '<pre>'.json_encode($absen).'</pre>';
	echo '</pre>';
}

function ping($host,$port=80,$timeout=6) {
    $fsock = fsockopen($host, $port, $errno, $errstr, $timeout);
 	if ( ! $fsock ){
		return FALSE;
    } else {
        return TRUE;
    }
}

function get($ip, $tgl1, $tgl2){
	if(!$ip || !$tgl1 || !$tgl2) return;
	else{
		$data[]="sdate=".$tgl1;
		$data[]="edate=".$tgl2;
		$data[]='period=1';

		for ($i=1;$i<200;$i++) {
		        $data[]="uid={$i}";
		}

		$result = post("http://{$ip}/form/Download", implode('&',$data));

		$log = array();

		if($rows = explode("\n", $result)) {
			foreach($rows as $row){
				if(($cols = explode("\t", $row)) && count($cols)==5){
					$id = $cols[0];
					$nama = $cols[1];
					$timestamp = $cols[2];
					$key = $cols[4];
					$log[] = $cols;
				}
			}
		}
		return $log;
	}
}

function post($url,$data) { 
	$process = curl_init();
	$options = array(
		CURLOPT_URL => $url,
		CURLOPT_HEADER => false,
		CURLOPT_POSTFIELDS => $data,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_FOLLOWLOCATION => TRUE,
		CURLOPT_POST => TRUE,
		CURLOPT_BINARYTRANSFER => TRUE
	);
	curl_setopt_array($process, $options);
	$return = curl_exec($process); 
	curl_close($process); 
	return $return; 
}
