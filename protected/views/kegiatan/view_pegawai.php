<?php
$tahun = date('Y',strtotime($model->kegiatan->tgl_mulai));
$bulan = date('m',strtotime($model->kegiatan->tgl_mulai));

$this->pageTitle=$model->pegawai->nama_pegawai;
$this->pageCaption=$model->pegawai->nama_pegawai.' <small> Progress : '.number_format($model->jml_realisasi/$model->jml_target*100,1).'% </small>';
$this->pageDescription='';
$this->breadcrumbs=array(
	'Daftar Kegiatan'=>array('/kegiatan','tahun'=>$tahun,'bulan'=>$bulan),
	$model->kegiatan->unitkerja->unitkerja=>array('unitkerja','id'=>$model->kegiatan->id_unitkerja,'tahun'=>$tahun,'bulan'=>$bulan),
	$model->kegiatan->nama_kegiatan=>array('view','id'=>$model->id_kegiatan),
);

$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'baseScriptUrl'=>false,
	'cssFile'=>false,
	'attributes'=>array(
		'target_satuan',
		'kegiatan.jadwal',
		'jml_realisasi',
		'keterangan',
	),
)); 

if(Yii::app()->params['mingguan']){
?>

<!-- Tabel Target Mingguan -->
<!--
<div class="page-header" style="margin:20px 0 0 0px; padding: 5px 20px"><h4>Target Mingguan <small>Klik pada kolom target untuk input target mingguan</small></h4></div>
<table class="table" style="max-width:350px">
<thead><tr><th style="width:150px">Periode</th><th style="width:100px">Target</th><th style="width:100px">Realisasi</th></tr></thead>
<tbody>
<?php 
for($w=date("W",strtotime($model->kegiatan->tgl_mulai)); $w<=date("W",strtotime($model->kegiatan->tgl_selesai)); $w++) {
	echo '<tr key="'.$w.'"><td>'.str_periode($tahun,$w,$model->kegiatan).'</td>
	<td col="target"><span>'.get_target($tahun,$w,$model).'</span>'.(!$model->kegiatan->is_lock?'<input>':'').'</td>
	<td>'.get_realisasi($tahun,$w,$model).'</td></tr>';
}
?>
</tbody></table>

<?php } ?>
-->
<!-- Tabel Realisasi -->
<div class="page-header" style="margin:20px 0 0 0px; padding: 5px 20px">
	<h4>Detail Realisasi <?php if(!$model->kegiatan->is_lock) echo CHtml::link('<i class="g g-plus-sign"></i>',array('realisasi_add','id'=>$model->id_kegiatan),array('title'=>'Input Realisasi'));?></h4>
</div>

<table class="table" style="">
<thead><tr><th style="width:150px">Tanggal</th><th style="width:100px">Realisasi</th><th style="width:100px">Verifikasi</th><th>Keterangan</th><th style="width:20px"></th></tr></thead>
<tbody>
<?php
foreach($model->realisasi as $realisasi)
	echo "<tr><td>".$realisasi->str_tgl."</td>
	<td>".$realisasi->str_realisasi."</td>
	<td>".($realisasi->acc_on?CHtml::image('images/ok.png','',array('title'=>$realisasi->str_acc)):'')."</td>
	<td>".CHtml::encode($realisasi->keterangan)."</td>
	<td>".($realisasi->acc_on?'':CHtml::link('<i class="g g-pencil"></i>',array('realisasi_update','id'=>$realisasi->id),array('title'=>'Update')))."</td></tr>"; 
?>
</tbody></table>

<?php 
if(!$model->kegiatan->is_lock)
	Yii::app()->clientScript->registerScript('target','
	$("td[col=\'target\'] input").attr("style","width:30px; margin:0; display:none")
		.blur(function(){
			var w = $(this).closest("tr").attr("key");
			var t_target = $(this);
			var jml_target = $(this).val();
			$.post("'.Yii::app()->createUrl('personal/mingguan_set',array('kegiatan'=>$model->id_kegiatan)).'",{
					mingguke:w,
					jml_target:jml_target,
				},function(data){
					if(data==jml_target){
						t_target.css("display","none");
						if(jml_target=="0") data="-";
						t_target.parent().css("padding-top","8px").find("span").text(data).css("display","block");
					} else 
						alert(data);								
				});
		}).keyup(function(e){
			if(e.keyCode==13){
				$(this).blur();
			}
		});

	$("td[col=\'target\']")
		.css("cursor","pointer").attr("title","Click to update")
		.click(function(){
			$(this).css("padding","3px 0 0 7px");
			$(this).find("span").css("display","none");
			$(this).find("input").css("display","block").focus();
		});
');


function str_periode($tahun, $w, $kegiatan) {
	if($w<10) $w='0'.$w;
	
	if(strtotime($tahun.'W'.$w.'1')<strtotime($kegiatan->tgl_mulai))
		$tgl1 = strftime('%d %b', strtotime($kegiatan->tgl_mulai));
	else
		$tgl1 = strftime('%d %b', strtotime($tahun.'W'.$w.'1'));
		
	if(strtotime($tahun.'W'.$w.'5')>strtotime($kegiatan->tgl_selesai))
		$tgl2 = strftime('%d %b', strtotime($kegiatan->tgl_selesai));
	else
		$tgl2 = strftime('%d %b', strtotime($tahun.'W'.$w.'5'));
		
	if($tgl1>=$tgl2)
		return $tgl1;
	else
		return $tgl1.' - '.$tgl2;
}

function get_target($tahun, $w, $model){
	$jml_target = Yii::app()->db->createCommand("SELECT SUM(jml_target) FROM ".TabelTargetMingguan::model()->tableName()." WHERE id_kegiatan=".$model->id_kegiatan.' AND id_pegawai='.Yii::app()->user->id.' AND mingguke='.$w)->queryScalar();
	return $jml_target? $jml_target : '-';
}

function get_realisasi($tahun, $w, $model){
	$jml_realisasi = Yii::app()->db->createCommand("SELECT SUM(jml_realisasi) FROM ".TabelRealisasi::model()->tableName()." WHERE id_kegiatan=".$model->id_kegiatan.' AND id_pegawai='.Yii::app()->user->id. ' AND week(tgl,1)='.$w)->queryScalar();
	return $jml_realisasi? $jml_realisasi :'-';
}
