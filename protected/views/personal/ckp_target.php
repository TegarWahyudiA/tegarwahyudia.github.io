<?php
$this->pageTitle='CKP-T '.strftime('%B %Y',mktime(0,0,0,$bulan,1,$tahun));
$this->pageCaption=$this->pageTitle;

$prev_bulan = ($bulan==1)? array('ckp','tahun'=>$tahun-1,'bulan'=>12) : array('ckp','tahun'=>$tahun,'bulan'=>$bulan-1);
$next_bulan = ($bulan==12)? array('ckp','tahun'=>$tahun+1,'bulan'=>1) : array('ckp','tahun'=>$tahun,'bulan'=>$bulan+1);

$this->pageDescription = '<span style="float:right">'.CHtml::link('<i class="g g-chevron-left"></i>',$prev_bulan).' '.strftime('%B %Y',mktime(0,0,0,$bulan,1,$tahun)).' '.CHtml::link('<i class="g g-chevron-right"></i>',$next_bulan).'</span>';

$this->breadcrumbs=array(
	'Kegiatan Saya'=>array('index'),
	'CKP-T',
);

$data='';
$i=1;
$tambahan = '';
$j=1;
foreach($target_pegawai as $target){
	if($target->kegiatan->id_jenis<>1){
		$tambahan.=" <tr class=xl7026738 height=18 style='mso-height-source:userset;height:13.5pt'>
	  <td height=18 class=xl7126738 style='height:13.5pt; vertical-align:top;'>".($j++)."</td>
	  <td colspan=2 class=xl9626738 width=553 style='border-right:.5pt solid black;
	  border-left:none;width:415pt;overflow:hidden'>".$target->kegiatan->nama_kegiatan."</td>
	  <td class=xl9626738 style='text-align:center; border-right:.5pt solid black;
  border-left:none;overflow:hidden; vertical-align:top;'>".$target->kegiatan->satuan->nama_satuan."</td>
	  <td class=xl7326738 style='vertical-align:top;'>".$target->jml_target."</td>
	  <td class=xl7426738 style='vertical-align:top;'>&nbsp;</td>
	 </tr>";
		continue;
	}

	$data.=" <tr class=xl7026738 height=18 style='mso-height-source:userset;height:13.5pt'>
  <td height=18 class=xl7126738 style='height:13.5pt; vertical-align:top;'>".($i++)."</td>
  <td colspan=2 class=xl9626738 width=553 style='border-right:.5pt solid black;
  border-left:none;width:415pt;overflow:hidden; vertical-align:top;'>".$target->kegiatan->nama_kegiatan."</td>
  <td class=xl9626738 style='text-align:center; border-right:.5pt solid black;
  border-left:none;overflow:hidden; vertical-align:top;'>".$target->kegiatan->satuan->nama_satuan."</td>
  <td class=xl7326738 style='vertical-align:top;'>".$target->jml_target."</td>
  <td class=xl7426738 style='vertical-align:top;'>&nbsp;</td>
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
	'{{TAHUN}}'			=> $tahun,
	'{{BULAN}}'			=> strftime('%B %Y',mktime(0,0,0,$bulan,1,$tahun)),
	'{{UTAMA}}'			=> $data,
	'{{TAMBAHAN}}'		=> $tambahan,
);

foreach($arr as $key=>$val){
	$src = str_replace($key, $val, $src);
}

echo $src;

if($link){
	echo '<div class=noprint>';
	echo CHtml::link('<i class="g g-download"></i> Export ke Excel',array('ckp','bulan'=>$bulan,'tahun'=>$tahun,'download'=>'now'));
	echo CHtml::link('<i class="g g-circle-arrow-right"></i> CKP-R',array('ckp','bulan'=>$bulan,'tahun'=>$tahun,'jenis'=>'realisasi'),array('style'=>'float:right'));
	echo '</div>';
	echo '<style>@media print{ .container{margin:0 0 0 50px} table{margin:-40px auto;width:2000pt} .page-header{display:none} .appcontent{border:none;box-shadow:none;}}</style>';

}
