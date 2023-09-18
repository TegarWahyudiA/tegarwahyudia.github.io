<?php
$i=1; $arr_utama = array();
$j=1; $arr_tambahan = array();

foreach($target_pegawai as $target){
	$arr = array(
			$target->kegiatan->nama_kegiatan, 
			$target->kegiatan->satuan->nama_satuan, 
			$target->jml_target
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
$s->setMargins(0.45);
$s->setRow(7,30);
foreach(array(4,10.6,60,18,10,23) as $key=>$val) $s->setColumn($key,$key,$val); 

$f_pojok =& $xls->addFormat();
$f_pojok->setHalign('center');
$f_pojok->setValign('vcenter');
$f_pojok->setBold();
$f_pojok->setSize(14);
$f_pojok->setBorder(2);
$s->write(0,5,"CKP-T",$f_pojok);

$f_judul =& $xls->addFormat();
$f_judul->setHalign('center');
$f_judul->setValign('vcenter');
$f_judul->setBold();
$f_judul->setSize(12);

$s->setMerge(1,0,1,5);
$s->write(1,0,"TARGET KINERJA PEGAWAI TAHUN ".$tahun,$f_judul);

$s->write(3,0,"Satuan Organisasi");	$s->write(3,2,": ".Yii::app()->user->satuankerja);
$s->setMerge(3,0,3,1);
$s->write(4,0,"Nama");	$s->write(4,2,": ".$model->nama_pegawai);
$s->setMerge(4,0,4,1);
$s->write(5,0,"Jabatan"); $s->write(5,2,": ".$model->jabatan);
$s->setMerge(5,0,5,1);
$s->write(6,0,"Bulan");	$s->write(6,2,": ".strftime('%B %Y',mktime(0,0,0,$bulan,1,$tahun)));
$s->setMerge(6,0,6,1);

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

$s->write(7,0,"No",$f_th);				
$s->write(7,1,"Uraian Kegiatan",$f_th);
$s->writeBlank(7,2,$f_th);
$s->setMerge(7,1,7,2);
$s->write(7,3,"Satuan",$f_th);			
$s->write(7,4,"Target Kuantitas",$f_th);			
$s->write(7,5,"Keterangan",$f_th);		

$s->write(8,0,"(1)",$f_th_no); 
$s->write(8,1,"(2)",$f_th_no); 
$s->writeBlank(8,2,$f_th_no);
$s->setMerge(8,1,8,2); 
for($i=3; $i<=5;$i++) $s->write(8,$i,"(".$i.")",$f_th_no); 

$s->write(9,0,"UTAMA",$f_utama); $s->setMerge(9,0,9,2);
$s->writeBlank(9,3,$f_1); $s->writeBlank(9,5,$f_1); 
$baris = 10;

foreach($arr_utama as $no=>$row) {
	$s->write($baris,0,$no,$f_1);
	$s->write($baris,1,$row[0]);
	$s->write($baris,3,$row[1],$f_1);
	$s->write($baris,4,$row[2],$f_1);
	$s->write($baris,5,'',$f_2);
	$baris++;	
}
$s->writeBlank($baris,0,$f_1); $s->writeBlank($baris,3,$f_1); $s->writeBlank($baris,5,$f_1); 

$s->write(++$baris,0,"TAMBAHAN",$f_utama); $s->setMerge($baris,0,$baris,2);
$s->writeBlank($baris,3,$f_1); $s->writeBlank($baris,5,$f_1); 
$baris++;
foreach($arr_tambahan as $no=>$row) {
	$s->write($baris,0,$no,$f_1);
	$s->write($baris,1,$row[0]);
	$s->write($baris,3,$row[1],$f_1);
	$s->write($baris,4,$row[2],$f_1);
	$s->write($baris,5,'',$f_2);
	$baris++;	
}
$s->writeBlank($baris,0,$f_1); $s->writeBlank($baris,3,$f_1); $s->writeBlank($baris,5,$f_1); 

$s->write(++$baris,0,"JUMLAH",$f_th); $s->setMerge($baris,0,$baris,4);
$s->setRow($baris,18);
for($x=1;$x<=4;$x++) $s->writeBlank($baris,$x,$f_th); 
$s->writeBlank($baris,5,$f_block);

$s->write(++$baris,1,"Kesepakatan Target",$f_bold); 
$s->write(++$baris,1,"Tanggal : 1 ".strftime('%B %Y',mktime(0,0,0,$bulan,1,$tahun))); 

$s->write(++$baris,1,"Pegawai yang Dinilai,",$f_center);  $s->setMerge($baris,1,$baris,2);
$s->write($baris,3,"Pejabat Penilai,",$f_center);  $s->setMerge($baris,3,$baris,5);
$baris+=3;
$s->write(++$baris,1,$model->nama_pegawai,$f_center);  $s->setMerge($baris,1,$baris,2);
$s->write($baris,3,(isset($model->penilai)? $model->penilai->nama_pegawai: ''),$f_center);  $s->setMerge($baris,3,$baris,5);

$s->write(++$baris,1,"NIP. ".$model->nipbaru,$f_center);  $s->setMerge($baris,1,$baris,2);
$s->write($baris,3,"NIP. ".(isset($model->penilai)? $model->penilai->nipbaru: ''),$f_center);  $s->setMerge($baris,3,$baris,5);

$xls->send($bulan.'. CKPT - '.$model->nama_pegawai.' - '.strftime('%b %Y',mktime(0,0,0,$bulan,1,$tahun)).'.xls');
$xls->close();
