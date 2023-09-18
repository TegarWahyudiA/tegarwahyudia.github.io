<?php
$this->pageTitle='SKP-T '.$tahun;
$this->pageCaption=$this->pageTitle;

$prev = array('skp','tahun'=>$tahun-1);
$next = array('skp','tahun'=>$tahun+1);

$this->pageDescription = '<span style="float:right">'.CHtml::link('<i class="g g-chevron-left"></i>',$prev).' '.$tahun.' '.CHtml::link('<i class="g g-chevron-right"></i>',$next).'</span>';

$this->breadcrumbs=array(
	'Kegiatan Saya'=>array('index'),
	'CKP-T',
);

$data='';
$i=1;
$tambahan = '';
$j=1;
foreach($target_pegawai as $target){
/*	if($target->kegiatan->id_jenis<>1){
		$tambahan.=" <tr class=xl7026738 height=18 style='mso-height-source:userset;height:13.5pt'>
	  <td height=18 class=xl7126738 style='height:13.5pt'>".($j++)."</td>
	  <td colspan=2 class=xl9626738 width=553 style='border-right:.5pt solid black;
	  border-left:none;width:415pt'>".$target->kegiatan->nama_kegiatan."</td>
	  <td class=xl7226738>".$target->kegiatan->satuan->nama_satuan."</td>
	  <td class=xl7326738>".$target->jml_target."</td>
	  <td class=xl7426738>&nbsp;</td>
	 </tr>";
		continue;
	}
*/
	$data.=" <tr class=xl7026738 height=18 style='mso-height-source:userset;height:13.5pt'>
  <td height=18 class=xl7126738 style='height:13.5pt'>".($i++)."</td>
  <td colspan=2 class=xl9626738 width=553 style='border-right:.5pt solid black;
  border-left:none;width:415pt'>".$target->kegiatan->nama_kegiatan."</td>
  <td class=xl7226738>".$target->kegiatan->satuan->nama_satuan."</td>
  <td class=xl7326738>".$target->jml_target."</td>
  <td class=xl7426738>&nbsp;</td>
 </tr>";
} 

$src = file_get_contents('protected/template/ckpt.htm');

$arr = array(
	'{{NAMA}}'			=> $model->nama_pegawai,
	'{{NIP}}'			=> $model->nipbaru,
	'{{JABATAN}}'		=> $model->jabatan,
	'{{PENILAI}}'		=> isset($model->penilai)? $model->penilai->nama_pegawai: '',
	'{{NIP_PENILAI}}'	=> isset($model->penilai)? $model->penilai->nipbaru: '',
	'{{SATUANKERJA}}'	=> Yii::app()->user->satuankerja, //Yii::app()->params['satuankerja'],
	'{{TAHUN}}'			=> date('Y'),
//	'{{BULAN}}'			=> strftime('%B %Y',mktime(0,0,0,$bulan)),
	'{{UTAMA}}'			=> $data,
	'{{TAMBAHAN}}'		=> '',//$tambahan,
);

foreach($arr as $key=>$val){
	$src = str_replace($key, $val, $src);
}

echo $src;

if($link){
	echo CHtml::link('<i class="g g-download"></i> Export ke Excel',array('skp','tahun'=>$tahun,'download'=>'now'));
	echo CHtml::link('<i class="g g-circle-arrow-right"></i> SKP-R',array('skp','tahun'=>$tahun,'jenis'=>'realisasi'),array('style'=>'float:right'));
}
