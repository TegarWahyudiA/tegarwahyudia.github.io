<?php
$this->pageCaption=$pegawai->nama_pegawai;
$this->pageTitle=$this->pageCaption;

$periode = strftime('%d %b',strtotime(date('Y').'W'.$mingguke.'1')).' - '.strftime('%d %b %Y',strtotime(date('Y').'W'.$mingguke.'5'));
$this->pageDescription='<span style="float:right">'.
	CHtml::link(BHtml::icon('chevron-left'),array('mingguan_view','pegawai'=>$pegawai->id,'mingguke'=>$mingguke-1)).' '.$periode.' '.
	CHtml::link(BHtml::icon('chevron-right'),array('mingguan_view','pegawai'=>$pegawai->id,'mingguke'=>$mingguke+1)).'</span>';

$this->breadcrumbs=array(
	'Monitoring',
	'Kegiatan Mingguan'=>array('mingguan','mingguke'=>$mingguke),
);

$this->widget('TbGridView', array(
	'id'=>'detail-monitoring-grid',
	'dataProvider'=>$dataProvider,
	'cssFile'=>Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('ext.bootstrap-theme.widgets.assets')).'/gridview/styles.css',
	'itemsCssClass'=>'table',
	'template'=>'{items}',
	'columns'=>array(
		array('class'=>'IndexColumn'),
		array('header'=>'Nama Kegiatan','type'=>'raw','value'=>function($data){ 
			return $data->kegiatan->nama_kegiatan;
//			return CHtml::link($data->kegiatan->nama_kegiatan, array('target/view','id'=>$data->target->id));
		}),
		'target_satuan',
		'jml_realisasi'
	),
)); 
