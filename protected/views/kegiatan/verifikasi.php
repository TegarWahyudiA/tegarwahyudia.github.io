<?php
$this->pageCaption='Verifikasi Realisasi';
$this->pageTitle=$this->pageCaption;
$this->pageDescription=Yii::app()->user->unitkerja;


$this->breadcrumbs=array(
	Yii::app()->user->unitkerja=>array('unitkerja','id'=>Yii::app()->user->id_unitkerja),
	'Verifikasi Realisasi',
);

$this->widget('TbGridView', array(
	'id'=>'tabel-sub-kegiatan-grid',
	'dataProvider'=>$dataProvider,
	'cssFile'=>Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('ext.bootstrap-theme.widgets.assets')).'/gridview/styles.css',
	'itemsCssClass'=>'table',
	'template'=>'{items} {pager}',
	'columns'=>array(
		array('class'=>'IndexColumn'),
		'kegiatan.nama_kegiatan',
		'pegawai.nama_pegawai',
		'str_tgl',
		'str_realisasi',
		'keterangan',
		array('header'=>'Verify','type'=>'raw','value'=>function($data){return CHtml::link(BHtml::icon('ok-sign'),'#',array('id'=>'v_'.$data->id,'onClick'=>'return verify('.$data->id.')'));}),
	),
)); 

Yii::app()->clientScript->registerScript('acc','
	function verify(id){
//		if(confirm("Konfirmasi verifikasi realisasi kegiatan ini?")){
			$.post("'.Yii::app()->createUrl("kegiatan/verifikasi_acc").'",{id:id}, function(data){
				if(data=="OK"){
					$("#v_"+id).closest("tr").fadeTo("slow",0.0, function(){ $(this).remove(); });
				} else
					alert(data);
			});
			return false;
//		}
		return false;
	};
', CClientScript::POS_END);
