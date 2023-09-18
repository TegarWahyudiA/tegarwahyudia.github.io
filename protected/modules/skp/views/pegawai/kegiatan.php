<?php
$this->pageTitle=$model->kegiatan->nama_kegiatan;
$this->pageCaption='Target & Realisasi <small> Progress : '.number_format($model->jml_realisasi/$model->jml_target*100,1).'% </small>';
$this->pageDescription='';
$this->breadcrumbs=array(
	'SKP Saya'=>array('index', 'tahun'=>$model->kegiatan->tahun),
	$model->kegiatan->nama_kegiatan
);

$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'baseScriptUrl'=>false,
	'cssFile'=>false,
	'attributes'=>array(
		'target_satuan',
		//'kegiatan.satuan.nama_satuan',
		'jml_realisasi',
		array('label'=>'Kegiatan Fungsional','type'=>'raw', 'visible'=>Yii::app()->user->id_fungsional>0 && $model->kredit,
			'value'=>($model->kredit?$model->kredit->simple:'') .' '.
			CHtml::link(BHtml::icon('edit'),array('kegiatan_fungsional','id'=>$model->id_kegiatan),array('style'=>'font-size:smaller','title'=>'Update Fungsional'))
		),
		array('label'=>'Keterangan','type'=>'raw','value'=>'<span>'.$model->keterangan.'</span> &nbsp; '.BHtml::icon('edit',array('style'=>'font-size:smaller;cursor:pointer;color:#08c','title'=>'Edit Keterangan','id'=>'set_keterangan'))
		),
	),
)); 

?>

<!-- Tabel Realisasi -->
<div class="page-header" style="margin:20px 0 0 0px; padding: 5px 20px">
	<h4>Detail Realisasi <?php echo CHtml::link('<i class="g g-plus-sign"></i>',array('realisasi_add','kegiatan'=>$model->id_kegiatan),array('title'=>'Input Realisasi'));?></h4>
</div>

<table class="table" style="">
<thead><tr><th style="width:150px">Tanggal</th><th style="width:100px">Realisasi</th><th>Keterangan</th><th style="width:20px"></th></tr></thead>
<tbody>
<?php
foreach($model->realisasi as $realisasi)
	echo "<tr><td>".$realisasi->str_tgl."</td>
	<td>".$realisasi->str_realisasi."</td>
	<td>".CHtml::encode($realisasi->keterangan)."</td>
	<td>".CHtml::link('<i class="g g-pencil"></i>',array('realisasi_update','id'=>$realisasi->id),array('title'=>'Update'))."</td></tr>"; 
?>
</tbody></table>

<?php 
	Yii::app()->clientScript->registerScript('target','
	$("td[col=target] input").attr("style","width:30px; margin:0; display:none")
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

	Yii::app()->clientScript->registerScript('set','
	$("#set_keterangan").click(function(){
		var prev = $(this).parent().text().trim();
		var next = prompt("Input keterangan:",prev);
		if(next && next!=prev){
			$.post("'.Yii::app()->createUrl("personal/set_keterangan").'",{id:'.$model->id_kegiatan.',keterangan:next},function(data){
				if(data=="OK"){
					$("#set_keterangan").parent().find("span").text(next);
				}
			});
		}
	});
');

function get_target($tahun, $w, $model){
	$jml_target = Yii::app()->db->createCommand("SELECT SUM(jml_target) FROM ".TabelTargetMingguan::model()->tableName()." WHERE id_kegiatan=".$model->id_kegiatan.' AND id_pegawai='.Yii::app()->user->id.' AND mingguke='.$w)->queryScalar();
	return $jml_target? $jml_target : '-';
}

function get_realisasi($tahun, $w, $model){
	$jml_realisasi = Yii::app()->db->createCommand("SELECT SUM(jml_realisasi) FROM ".TabelRealisasi::model()->tableName()." WHERE id_kegiatan=".$model->id_kegiatan.' AND id_pegawai='.Yii::app()->user->id. ' AND week(tgl,1)='.$w)->queryScalar();
	return $jml_realisasi? $jml_realisasi :'-';
}
