<?php
$this->pageTitle='SKP-R '.$tahun;
$this->pageCaption='SKP-R';

$prev = array('skp','jenis'=>'realisasi','tahun'=>$tahun-1);
$next = array('skp','jenis'=>'realisasi','tahun'=>$tahun+1);

$this->pageDescription = '<span style="float:right">'.CHtml::link('<i class="g g-chevron-left"></i>',$prev).' '.$tahun.' '.CHtml::link('<i class="g g-chevron-right"></i>',$next).'</span>';

$this->breadcrumbs=array(
	'Kegiatan Saya'=>array('index','tahun'=>$tahun),
	'SKP-R',
);

if(Yii::app()->user->id_fungsional && Yii::app()->user->id_fungsional<30)
	$kolom_kredit = MasterFungsional::model()->findByPk(Yii::app()->user->id_fungsional)->kolom_kredit;

$data='';
$arr_persen = array();
$arr_kualitas = array();
$arr_angka_kredit = array();
$tambahan = '';
$i=1;
$j=1;
foreach($target_pegawai as $target){
/*	if($target->kegiatan->id_jenis<>1){
		$tambahan.=" <tr height=24 style='mso-height-source:userset;height:18.0pt'>
		  <td height=24 class=xl747477 style='height:18.0pt'>".($j++)."</td>
		  <td colspan=2 class=xl1077477 style='border-right:.5pt solid black;
		  border-left:none'>".$target->kegiatan->nama_kegiatan."</td>
		  <td class=xl767477>".$target->kegiatan->satuan->nama_satuan."</td>
		  <td class=xl777477>".$target->jml_target."</td>
		  <td class=xl777477>".$target->jml_realisasi_acc."</td>
		  <td class=xl787477>".number_format($target->jml_realisasi_acc/$target->jml_target*100,2)."</td>
		  <td class=xl787477>".$target->persen_kualitas."</td>
		  <td class=xl797477>".$target->kredit->kode_perka."</td>
		  <td class=xl807477>".$angka_kredit."</td>
		  <td class=xl817477>&nbsp;</td>
		 </tr>";
		continue;
	}

*/	$kode_perka = ($target->fungsional && $target->kredit)? $target->kredit->kode_perka : null;
	$angka_kredit = ($target->fungsional && $target->kredit)? $target->kredit->{$target->fungsional->kolom_kredit} * $target->jml_realisasi : null;
	$data.=" <tr height=24 style='mso-height-source:userset;height:18.0pt'>
  <td height=24 class=xl747477 style='height:18.0pt'>".($i++)."</td>
  <td colspan=2 class=xl1077477 style='border-right:.5pt solid black;
  border-left:none'>".$target->kegiatan->nama_kegiatan."</td>
  <td class=xl767477>".$target->kegiatan->satuan->nama_satuan."</td>
  <td class=xl777477>".$target->jml_target."</td>
  <td class=xl777477>".$target->jml_realisasi."</td>
  <td class=xl787477>".number_format($target->jml_realisasi/$target->jml_target*100,2)."</td>
  <td class=xl787477>".$target->persen_kualitas."</td>
  <td class=xl797477>".$kode_perka."</td>
  <td class=xl807477>".$angka_kredit."</td>
  <td class=xl817477>&nbsp;</td>
 </tr>";

 $arr_persen[] = $target->jml_realisasi/$target->jml_target*100;
 $arr_kualitas[] = $target->persen_kualitas;
 $arr_angka_kredit[] = $angka_kredit;
} 

$src = file_get_contents('protected/template/ckpr.htm');

$r_persen = sizeof($arr_persen)? number_format(array_sum($arr_persen)/sizeof($arr_persen),2) : 0;
$r_kualitas = sizeof($arr_kualitas)? number_format(array_sum($arr_kualitas)/sizeof($arr_kualitas),2) : 0;

$arr = array(
	'{{NAMA}}'			=> $model->nama_pegawai,
	'{{NIP}}'			=> $model->nipbaru,
	'{{JABATAN}}'		=> $model->jabatan,
	'{{PENILAI}}'		=> isset($model->penilai)? $model->penilai->nama_pegawai: '',
	'{{NIP_PENILAI}}'	=> isset($model->penilai)? $model->penilai->nipbaru: '',
	'{{SATUANKERJA}}'	=> Yii::app()->user->satuankerja, //Yii::app()->params['satuankerja'],
	'{{TAHUN}}'			=> date('Y'),
//	'{{PERIODE}}'		=> date('1-t',mktime(0,0,0,$bulan)).' '.strftime('%B %Y',mktime(0,0,0,$bulan)),
	'{{ANGKA_KREDIT}}'	=> array_sum($arr_angka_kredit)? array_sum($arr_angka_kredit) : '',
	'{{R_PERSEN}}'		=> $r_persen,
	'{{R_KUALITAS}}'	=> $r_kualitas,
	'{{CKP}}'			=> number_format(($r_persen+$r_kualitas)/2,2),
//	'{{TANGGAL}}'		=> strftime('1 %B %Y',mktime(0,0,0,$bulan+1)),
	'{{UTAMA}}'			=> $data,
	'{{TAMBAHAN}}'		=> '',//$tambahan,
);

foreach($arr as $key=>$val){
	$src = str_replace($key, $val, $src);
}

echo $src;

if($link){
	echo CHtml::link('<i class="g g-download"></i> Export ke Excel',array('skp','tahun'=>$tahun,'jenis'=>'realisasi','download'=>'now'));
	echo CHtml::link('<i class="g g-circle-arrow-right"></i> SKP-T',array('skp','tahun'=>$tahun,'jenis'=>'target'),array('style'=>'float:right'));
}
