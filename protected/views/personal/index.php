<?php
$this->pageCaption='Kegiatan Bulan '.strftime('%B',mktime(0,0,0,$bulan));
$this->pageTitle=$this->pageCaption;

$prev_bulan = ($bulan==1)? array('index','tahun'=>$tahun-1,'bulan'=>12) : array('index','tahun'=>$tahun,'bulan'=>$bulan-1);
$next_bulan = ($bulan==12)? array('index','tahun'=>$tahun+1,'bulan'=>1) : array('index','tahun'=>$tahun,'bulan'=>$bulan+1);

$this->pageDescription = '<span style="float:right">'.CHtml::link('<i class="g g-chevron-left"></i>',$prev_bulan).' '.strftime('%B %Y',mktime(0,0,0,$bulan,1,$tahun)).' '.CHtml::link('<i class="g g-chevron-right"></i>',$next_bulan).'</span>';
$this->breadcrumbs=array(
	'Indeks Kegiatan'=>array('unitkerja'),
	'Kegiatan Saya',
);

$filter = ' AND year(tgl_mulai)='.$tahun.' AND month(tgl_mulai)<='.$bulan.' AND month(tgl_selesai)>='.$bulan;

$target_pegawai = TabelTargetPegawai::model()->findAll(array(
		'with'=>'kegiatan',
		'condition'=>"id_pegawai=".Yii::app()->user->id. $filter,
		'order'=>'nama_kegiatan ASC'
	));

if(Yii::app()->user->id_fungsional && Yii::app()->user->id_fungsional<30)
	$kolom_kredit = MasterFungsional::model()->findByPk(Yii::app()->user->id_fungsional)->kolom_kredit;
?>

<table class="table">
<thead>
	<tr><th style="width:20px">#</th><th>Nama Kegiatan</th><th class=desktop style="width:80px">Jadwal</th><th style="width:110px">Target</th><th style="width:100px">Progress</th><th class=desktop style="width:100px; text-align: center">Nilai <?php if(Yii::app()->params['mingguan']) echo '&amp; Alokasi';?></th></tr>
</thead>
<tbody>
<?php 
$i = 1;
$arr_kuantitas = array();
$arr_kualitas = array();
$arr_angka_kredit = array();

foreach($target_pegawai as $target){
	$img_alokasi = ($target->child_target==$target->jml_target)? 'ok.png' : 'error.png';
	echo '<tr><td>'.($i++).'</td>
	<td>'.($target->kegiatan->id_pegawai_usulan? BHtml::icon('question-sign', array('title'=>'Kegiatan usulan','style'=>'color:'.($data->id_flag==1?'#bd362f':'#0088cc'))) : '').CHtml::link($target->kegiatan->nama_kegiatan,array('kegiatan','id'=>$target->id_kegiatan),array('title'=>$target->kegiatan->unitkerja->unitkerja)).'</td>
	<td class=desktop>'.$target->kegiatan->jadwal.'</td>
	<td>'.$target->target_satuan.'</td>
	<td style="padding:0; line-height:0px; vertical-align:middle">';
	Controller::createWidget('TbProgress',array('percent'=>number_format($target->jml_realisasi/$target->jml_target*100,0)))->run();
	echo '</td><td class=desktop style="text-align:center">';

	if($target->kegiatan->is_ckp==1){
		echo ($target->persen_kualitas? $target->persen_kualitas : '--');
		$arr_kuantitas[] = $target->jml_realisasi/$target->jml_target*100;
		$arr_kualitas[] = $target->persen_kualitas;
	} else
		echo '--';

	if(Yii::app()->params['mingguan'])
		echo ' &nbsp; '.CHtml::image('images/'.$img_alokasi,$target->child_target);

	if($target->fungsional && $target->kredit){
		$angka_kredit = $target->kredit->{$target->fungsional->kolom_kredit} * $target->jml_realisasi;
		$arr_angka_kredit[] = $angka_kredit;
		echo '&nbsp; '.CHtml::link(BHtml::icon('search'),array('kegiatan_fungsional','id'=>$target->id_kegiatan),array('title'=>'Fungsional : '.$target->kredit->simple,'style'=>'font-size:smaller'));
	}
	echo '</td></tr>';

} ?>
</tbody></table>

<?php 
if(count($arr_kuantitas)){
	$r_kuantitas = number_format(array_sum($arr_kuantitas)/count($arr_kuantitas),2);
	$r_kualitas = number_format(array_sum($arr_kualitas)/count($arr_kualitas),2);
	$this->pageCaption.=" <small>Progress : ".$r_kuantitas."% </small>";

	$ckp = CKP::model()->find(array('condition'=>'id_pegawai='.Yii::app()->user->id.' AND tahun='.date('Y').' AND bulan='.$bulan));
	if(!$ckp){
		$ckp = new CKP;
		$ckp->id_pegawai = Yii::app()->user->id;
		$ckp->tahun = date('Y');
		$ckp->bulan = $bulan;
	}
	$ckp->jml_kegiatan = count($arr_kuantitas);
	$ckp->r_kuantitas = $r_kuantitas;
	$ckp->r_kualitas = $r_kualitas;
	$ckp->nilai_ckp = number_format(($r_kuantitas+$r_kualitas)/2,2);
	$ckp->angka_kredit = array_sum($arr_angka_kredit);
	$ckp->updated_on = date('Y-m-d H:i:s');
	$ckp->save();
}

if($target_pegawai){
	echo CHtml::link('<i class="g g-download"></i> CKP-T',array('ckp','bulan'=>$bulan,'tahun'=>$tahun,'jenis'=>'target'),array('class'=>'dekstop'));
	echo CHtml::link('<i class="g g-download"></i> CKP-R',array('ckp','bulan'=>$bulan,'tahun'=>$tahun,'jenis'=>'realisasi'),array('style'=>'float:right','class'=>'dekstop'));
}
?>
