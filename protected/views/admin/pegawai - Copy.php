<?php
$this->pageCaption='Master Pegawai';
$this->pageTitle=$this->pageCaption;
$this->pageDescription='';//$this->getState('isAdmin')?CHtml::link('<i class="icon icon-plus-sign"></i>',array('create'),array('title'=>'Insert new Pegawai')):'';
$this->breadcrumbs=array(
	'Master Pegawai',
);

$this->widget('TbGridView', array(
	'id'=>'master-pegawai-grid',
	'dataProvider'=>$model->search(),
	'cssFile'=>Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('ext.bootstrap-theme.widgets.assets')).'/gridview/styles.css',
	'itemsCssClass'=>'table',
	'template'=>'{items} {pager}',
	'columns'=>array(
		array('class'=>'IndexColumn'),
		'nama_pegawai',
		array('name'=>'nipbaru','headerHtmlOptions'=>array('style'=>'width:150px','class'=>'desktop')),
		'jabatan',
		'fungsional.fungsional',
//		'atasan.nama_pegawai',
		array('header'=>'Login Terakhir','value'=>function($data){return str_waktu($data->last_login);},'headerHtmlOptions'=>array('style'=>'width:100px','class'=>'desktop')),
		array('type'=>'raw','visible'=>$this->getState('isAdmin'),'htmlOptions'=>array('style'=>'width:20px'),'value'=>function($data){ 
			return CHtml::link(BHtml::icon('pencil'),array('pegawai_update','id'=>$data->id), array('title'=>'Update'));
		}),
	),
));

echo CHtml::link(BHtml::icon('plus-sign').'Tambah Pegawai',array('pegawai_add'));
echo CHtml::link(BHtml::icon('upload').'Import Excel',array('pegawai_import'), array('style'=>'float:right'));

function str_waktu($input) {
	$diff = time()-$input;

	if($diff < 60*60)
		return (int)date('i',$diff) .' menit';
	elseif($diff< 24*60*60 )
		return date('G',$diff) .' jam';
	elseif($diff < 7*60*60*24)
		return date('j',$diff) .' hari';
	elseif($diff < 30*60*60*24)
		return (int)date('W',$diff) .' minggu';
	elseif($diff < 365*60*60*24)
		return date('n',$diff) .' bulan';
	else
		return '-';
}
