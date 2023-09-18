<?php
$this->pageCaption='Kegiatan Mingguan';
$this->pageTitle=$this->pageCaption;

$periode = strftime('%d %b',strtotime(date('Y').'W'.$mingguke.'1')).' - '.strftime('%d %b %Y',strtotime(date('Y').'W'.$mingguke.'5'));
$this->pageDescription='<span style="float:right">'.
	CHtml::link('<i class="g g-chevron-left"></i>',array('mingguan','ke'=>$mingguke-1)).' '.$periode.' '.
	CHtml::link('<i class="g g-chevron-right"></i>',array('mingguan','ke'=>$mingguke+1)).'</span>';

$this->breadcrumbs=array(
	'Indeks Kegiatan'=>array('unitkerja'),
	'Kegiatan Mingguan',
);

$mingguan = new CActiveDataProvider('TabelTargetMingguan', array(
	'criteria'=>array(
		'condition'=>'id_pegawai='.$model->id.' AND mingguke='.$mingguke,
	),
	));

$this->widget('TbGridView', array(
	'id'=>'tabel-sub-kegiatan-grid',
	'dataProvider'=>$mingguan,
//	'filter'=>$model,
	'cssFile'=>Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('ext.bootstrap-theme.widgets.assets')).'/gridview/styles.css',
	'itemsCssClass'=>'table',
	'template'=>'{items} {pager}',
	'columns'=>array(
		array('class'=>'IndexColumn'),
		array('header'=>'Kegiatan','type'=>'raw','value'=>function($data){ 
			return CHtml::link($data->kegiatan->nama_kegiatan,array('kegiatan','id'=>$data->id_kegiatan),array('title'=>$data->kegiatan->unitkerja->unitkerja));
		}),
//		array('header'=>'Unitkerja','type'=>'raw','headerHtmlOptions'=>array('class'=>'desktop'),'htmlOptions'=>array('class'=>'desktop'),'value'=>function($data){ return $data->kegiatan->unitkerja->unitkerja;}),
		'target_satuan',
		'jml_realisasi'
	),
)); 
