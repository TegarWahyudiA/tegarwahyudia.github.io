<?php
$this->pageCaption='SKP Tahun '.$tahun;
$this->pageTitle=$this->pageCaption;

$prev = array('index','tahun'=>$tahun-1);
$next = array('index','tahun'=>$tahun+1);

$this->pageDescription = '<span style="float:right">'.CHtml::link('<i class="g g-chevron-left"></i>',$prev).' '.$tahun.' '.CHtml::link('<i class="g g-chevron-right"></i>',$next).'</span>';
$this->breadcrumbs=array(
	'SKP Saya',
);

$filter = ' AND kegiatan.tahun='.$tahun;

$target_pegawai = SkpPegawai::model()->findAll(array(
		'with'=>'kegiatan',
		'condition'=>"id_pegawai=".Yii::app()->user->id. $filter,
		'order'=>'nama_kegiatan ASC'
	));

if(Yii::app()->user->id_fungsional && Yii::app()->user->id_fungsional<30)
	$kolom_kredit = MasterFungsional::model()->findByPk(Yii::app()->user->id_fungsional)->kolom_kredit;
?>

<table class="table">
<thead>
	<tr><th style="width:20px">#</th><th>Nama Kegiatan</th><th style="width:110px">Target</th><th style="width:100px">Progress</th><th class=desktop style="width:40px; text-align: center">Nilai</th></tr>
</thead>
<tbody>
<?php 
$i = 1;
foreach($target_pegawai as $target){
	echo '<tr><td>'.($i++).'</td>
	<td>'.($target->kegiatan->id_flag==1? BHtml::icon('question-sign', array('title'=>'Kegiatan usulan, menunggu konfirmasi','style'=>'color:orange')) : '').CHtml::link($target->kegiatan->nama_kegiatan,array('kegiatan','id'=>$target->id_kegiatan),array('title'=>$target->kegiatan->unitkerja->unitkerja)).'</td>
	<td>'.$target->target_satuan.'</td>
	<td style="padding:0; line-height:0px; vertical-align:middle">';
	Controller::createWidget('TbProgress',array('percent'=>number_format($target->jml_realisasi/$target->jml_target*100,0)))->run();
	echo '</td><td class=desktop style="text-align:center">';
	echo $target->persen_kualitas? $target->persen_kualitas : '--';

	if($target->fungsional && $target->kredit){
		$angka_kredit = $target->kredit->{$target->fungsional->kolom_kredit} * $target->jml_realisasi;
		$arr_angka_kredit[] = $angka_kredit;
		echo '&nbsp; '.CHtml::link(BHtml::icon('search'),array('kegiatan_fungsional','id'=>$target->id_kegiatan),array('title'=>'Fungsional : '.$target->kredit->simple,'style'=>'font-size:smaller'));
	}
	echo '</td></tr>';

} ?>
</tbody></table>

<?php
if($target_pegawai){
	echo CHtml::link('<i class="g g-download"></i> SKP-T',array('skp','tahun'=>$tahun,'jenis'=>'target'),array('class'=>'dekstop'));
	echo CHtml::link('<i class="g g-download"></i> SKP-R',array('skp','tahun'=>$tahun,'jenis'=>'realisasi'),array('style'=>'float:right','class'=>'dekstop'));
}
