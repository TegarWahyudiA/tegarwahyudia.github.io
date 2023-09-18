<?php
$i=1; $arr_utama = array();
$j=1; $arr_tambahan = array();

foreach($target_pegawai as $target){
	$angka_kredit = ($target->fungsional && $target->kredit)? $target->kredit->{$target->fungsional->kolom_kredit} * $target->jml_realisasi : null;

	$arr = array(
		$target->kegiatan->nama_kegiatan, 
		$target->kegiatan->satuan->nama_satuan, 
		$target->jml_target,
		$target->jml_realisasi_acc,
		number_format($target->jml_realisasi_acc/$target->jml_target*100,2),
		$target->persen_kualitas,
		($target->fungsional && $target->kredit? $target->kredit->kode_perka:''),
		$angka_kredit
	);

	if($target->kegiatan->id_jenis==1)
		$arr_utama[$i++] = $arr;
	else
		$arr_tambahan[$j++] = $arr;
}

include_once('Spreadsheet/Excel/Writer.php');
$xls = new Spreadsheet_Excel_Writer();

$s =& $xls->addWorksheet(strftime('%b %Y',mktime(0,0,0,$bulan,1,$tahun)));
$s->setLandscape();
$s->centerHorizontally();
$s->hideGridlines();
$s->setPaper(9); // A4
$s->setMargins(0.35);
$s->setRow(7,22);
$s->setRow(8,22);
foreach(array(4,10.6,40,10,9,9,7,10,9,8,15) as $key=>$val) 
	$s->setColumn($key,$key,$val); 

$f_pojok =& $xls->addFormat();
$f_pojok->setHalign('center');
$f_pojok->setValign('vcenter');
$f_pojok->setBold();
$f_pojok->setSize(14);
$f_pojok->setBorder(2);

$f_judul =& $xls->addFormat();
$f_judul->setHalign('center');
$f_judul->setValign('vcenter');
$f_judul->setBold();
$f_judul->setSize(12);

$f_th =& $xls->addFormat();
$f_th->setHalign('center');
$f_th->setValign('vcenter');
$f_th->setBold();
$f_th->setTextWrap(1);
$f_th->setBorder(1);

$f_th_no =& $xls->addFormat();
$f_th_no->setHalign('center');
$f_th_no->setValign('vcenter');
$f_th_no->setSize(10);
$f_th_no->setBorder(1);

$f_center =& $xls->addFormat();
$f_center->setHalign('center');

$f_bold =& $xls->addFormat();
$f_bold->setBold();

$f_1 =& $xls->addFormat(array('align'=>'center','left'=>1,'right'=>1));
$f_2 =& $xls->addFormat(array('left'=>1,'right'=>1));

$f_utama =& $xls->addFormat(array('left'=>1, 'bold'=>1));

$xls->setCustomColor(64,99,99,99);
$f_block =& $xls->addFormat();
$f_block->setBorder(1);
$f_block->setBgColor(64);

$s->write(0,10,"CKP-R",$f_pojok);
$s->write(1,0,"CAPAIAN KINERJA PEGAWAI TAHUN ".$tahun,$f_judul);
$s->setMerge(1,0,1,10);

$s->write(3,0,"Satuan Organisasi");	$s->write(3,2,": ".Yii::app()->user->satuankerja);
$s->setMerge(3,0,3,1);
$s->write(4,0,"Nama");				$s->write(4,2,": ".$model->nama_pegawai);
$s->setMerge(4,0,4,1);
$s->write(5,0,"Jabatan");			$s->write(5,2,": ".$model->jabatan);
$s->setMerge(5,0,5,1);
$s->write(6,0,"Periode");			$s->write(6,2,": ".date('1-t',mktime(0,0,0,$bulan)).' '.strftime('%B %Y',mktime(0,0,0,$bulan,1,$tahun)));
$s->setMerge(6,0,6,1);

$s->write(7,0,"No",$f_th); $s->setMerge(7,0,8,0); $s->writeBlank(8,0,$f_th);
$s->write(7,1,"Uraian Kegiatan",$f_th);	$s->setMerge(7,1,8,2); $s->writeBlank(7,2,$f_th);
$s->write(7,3,"Satuan",$f_th); $s->setMerge(7,3,8,3); $s->writeBlank(8,3,$f_th);
$s->write(7,4,"Kuantitas",$f_th); $s->setMerge(7,4,7,6); $s->writeBlank(7,5,$f_th); $s->writeBlank(7,6,$f_th);
$s->write(8,4,"Target",$f_th); $s->write(8,5,"Realisasi",$f_th); $s->write(8,6,"%",$f_th);
$s->write(7,7,"Tingkat Kualitas (%)",$f_th); $s->setMerge(7,7,8,7); $s->writeBlank(8,7,$f_th);
$s->write(7,8,"Kode Butir Kegiatan",$f_th);	$s->setMerge(7,8,8,8); $s->writeBlank(8,8,$f_th);
$s->write(7,9,"Angka Kredit",$f_th); $s->setMerge(7,9,8,9); $s->writeBlank(8,9,$f_th);
$s->write(7,10,"Keterangan",$f_th);	$s->setMerge(7,10,8,10); $s->writeBlank(8,10,$f_th);

$s->write(9,0,"(1)",$f_th_no); $s->setMerge(9,1,9,2); $s->writeBlank(9,2,$f_th);
$s->write(9,1,"(2)",$f_th_no);  
for($i=3; $i<=10;$i++) $s->write(9,$i,"(".$i.")",$f_th_no); 

$s->setMerge(10,0,10,2); $s->write(10,0,"UTAMA",$f_utama);

$baris = 11;
foreach($arr_utama as $no=>$row) {
	$s->write($baris,0,$no,$f_1);
	$s->write($baris,1,$row[0]);
	$s->write($baris,3,$row[1],$f_1);
	$s->write($baris,4,$row[2],$f_1);
	$s->write($baris,5,$row[3],$f_1);
	$s->write($baris,6,$row[4],$f_1);
	$s->write($baris,7,$row[5],$f_1);
	$s->write($baris,8,$row[6],$f_1);
	$s->write($baris,9,$row[7],$f_1);
	$s->write($baris,10,'',$f_2);
	$baris++;	
}

$s->write(++$baris,0,"TAMBAHAN",$f_utama); $s->setMerge($baris,0,$baris,2);

$baris++;
foreach($arr_tambahan as $no=>$row) {
	$s->write($baris,0,$no,$f_1);
	$s->write($baris,1,$row[0]);
	$s->write($baris,3,$row[1],$f_1);
	$s->write($baris,4,$row[2],$f_1);
	$s->write($baris,5,$row[3],$f_1);
	$s->write($baris,6,$row[4],$f_1);
	$s->write($baris,7,$row[5],$f_1);
	$s->write($baris,8,$row[6],$f_1);
	$s->write($baris,9,$row[7],$f_1);
	$s->write($baris,10,'',$f_2);
	$baris++;	
}

$s->write(++$baris,0,"JUMLAH",$f_th); $s->setMerge($baris,0,$baris,7);  
//for($x=1;$x<=7;$x++) $s->writeBlank($baris,$x,$f_th); 
$s->writeBlank($baris,8,$f_1); $s->writeBlank($baris,9,$f_1); $s->writeBlank($baris,10,$f_block);
$s->write(++$baris,0,"RATA-RATA",$f_th); $s->setMerge($baris,0,$baris,5);  
//for($i=1;$i<=5;$i++) $s->writeBlank($baris,$i,$f_th);
$s->write($baris,6,"0.00",$f_th); $s->write($baris,7,"0.00",$f_th); 
$s->writeBlank($baris,9,$f_block); //$s->writeBlank($baris,10,$f_block);

$s->write(++$baris,0,"CAPAIAN KINERJA PEGAWAI (CKP)",$f_th); $s->setMerge($baris,0,$baris,5); 
$s->writeFormula($baris,6,"=(G".($baris)."+H".($baris).")/2",$f_th); $s->writeBlank($baris,7,$f_th); $s->setMerge($baris,6,$baris,7);   
//$s->writeBlank($baris,9,$f_block); //$s->writeBlank($baris,10,$f_block);

$s->write(++$baris,1,"Penilaian Kinerja",$f_bold); 
$s->write(++$baris,1,"Tanggal : ".strftime('1 %B %Y',mktime(0,0,0,$bulan+1,1,$tahun))); 

$s->write(++$baris,1,"Pegawai yang Dinilai,",$f_center);  $s->setMerge($baris,1,$baris,4);
$s->write($baris,5,"Pejabat Penilai,",$f_center);  $s->setMerge($baris,5,$baris,8);
$baris+=3;
$s->write(++$baris,1,$model->nama_pegawai,$f_center);  $s->setMerge($baris,1,$baris,4);
$s->write($baris,5,(isset($model->penilai)? $model->penilai->nama_pegawai: ''),$f_center);  $s->setMerge($baris,5,$baris,8);

$s->write(++$baris,1,"NIP. ".$model->nipbaru,$f_center);  $s->setMerge($baris,1,$baris,4);
$s->write($baris,5,"NIP. ".(isset($model->penilai)? $model->penilai->nipbaru: ''),$f_center);  $s->setMerge($baris,5,$baris,8);

$xls->send($bulan.'. CKPR - '.$model->nama_pegawai.' - '.strftime('%b %Y',mktime(0,0,0,$bulan,1,$tahun)).'.xls');
$xls->close();
