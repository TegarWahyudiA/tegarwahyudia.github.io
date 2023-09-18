<?php
$this->pageTitle=$model->nama_kegiatan;
$this->pageCaption='Detail Realisasi';
$this->pageDescription='Total : '.(int)$model->jml_realisasi.' '.$model->satuan->nama_satuan;
$this->breadcrumbs=array(
	'Daftar Kegiatan'=>array('/kegiatan'),
	$model->unitkerja->unitkerja=>array('unitkerja','id'=>$model->id_unitkerja,'tahun'=>substr($model->tgl_mulai,0,4),'bulan'=>substr($model->tgl_mulai,5,2)),
	$model->nama_kegiatan=>array('view','id'=>$model->id),
);

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'tabel-sub-kegiatan-grid',
	'dataProvider'=>$dataProvider,
	'cssFile'=>Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('ext.bootstrap-theme.widgets.assets')).'/gridview/styles.css',
	'itemsCssClass'=>'table',
	'template'=>'{items} {pager}',
	'columns'=>array(
		array('class'=>'IndexColumn'),
		'str_tgl',
		'pegawai.nama_pegawai',
		'jml_realisasi',
		'keterangan',
		array('visible'=>!$model->is_lock && ((Yii::app()->user->id_unitkerja==$model->id_unitkerja && Yii::app()->user->id_eselon) || Yii::app()->user->isAdmin),'type'=>'raw','htmlOptions'=>array('style'=>'width:20px'),'value'=>function($data){ 
			return  CHtml::link(BHtml::icon('pencil'),array('realisasi_update','id'=>$data->id),array('title'=>'Update'));
		}),
	),
));


if(!$model->is_lock && ((Yii::app()->user->id_unitkerja==$model->id_unitkerja && Yii::app()->user->id_eselon) || Yii::app()->user->isAdmin))
	echo CHtml::link(BHtml::icon('plus-sign').'Input Realisasi',array('realisasi_add','id'=>$model->id),array('title'=>'Input Realisasi Pegawai'));

