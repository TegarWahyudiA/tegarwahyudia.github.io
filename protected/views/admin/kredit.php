<?php
$this->pageCaption='Master Angka Kredit';
$this->pageTitle=$this->pageCaption;
$this->pageDescription='<span style="float:right;margin-top:-5px"><input id="search" placeholder="Pencarian"></span>';
$this->breadcrumbs=array(
	'Master Angka Kredit',
);

$this->widget('TbGridView', array(
	'id'=>'personal-calendar-grid',
	'dataProvider'=>$model->search(),
//	'filter'=>$model,
	'cssFile'=>Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('ext.bootstrap-theme.widgets.assets')).'/gridview/styles.css',
	'itemsCssClass'=>'table',
	'template'=>'{items} {pager}',
	'columns'=>array(
//		'id',
//		'kode',
		'tingkat',
//		'kode_tingkat',
		'kode_perka',
//		'kode_unsur',
//		'unsur',
		'kegiatan',
		'uraian_singkat',
		'satuan_hasil',
/*		'bukti_fisik',
		'angka_kredit',
		'pelaksana_kegiatan',
		'keterangan',
		'pelaksana',
		'pelaksana_lanjutan',
		'penyelia',
		'pertama',
		'muda',
		'madya',
		'bidang',
		'seksi',
		*/
		array('type'=>'raw','headerHtmlOptions'=>array('style'=>'width:20px'),'value'=>function($data){
			return CHtml::link(BHtml::icon('eye-open'),array('kredit_view','id'=>$data->id));
		})
	),
)); 

Yii::app()->clientScript->registerScript('js','
	$("#search").keyup(function(e){
		if(e.keyCode==13){
			location.href="?r=admin/kredit&search="+$(this).val();
		}
	})
');
