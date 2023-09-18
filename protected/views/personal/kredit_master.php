<?php
$this->pageCaption='Master Angka Kredit';
$this->pageTitle=$this->pageCaption;
$this->pageDescription=CHtml::textField('search',$term,array('placeholder'=>'Pencarian','style'=>'float:right; margin-top:-5px; /*display:none*/'));
$this->breadcrumbs=array(
	'Master Angka Kredit',
);

$this->widget('TbGridView', array(
	'id'=>'master-kredit-grid',
	'dataProvider'=>$dataProvider,//$model->search(),
//	'filter'=>$model,
	'cssFile'=>Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('ext.bootstrap-theme.widgets.assets')).'/gridview/styles.css',
	'itemsCssClass'=>'table',
	'template'=>'{items} {pager}',
	'columns'=>array(
//		'id',
		'kode',
//		'tingkat',
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
			return CHtml::link(BHtml::icon('eye-open'),array('kredit_view','id'=>$data->id),array('target'=>'_blank'));
		})
	),
)); 

/*$form=$this->beginWidget('BActiveForm', array('id'=>'search-form', 'action'=>Yii::app()->createUrl($this->route), 'method'=>'get'));
echo CHtml::activeHiddenField($model,'kegiatan');
*/
$form=$this->beginWidget('BActiveForm', array('id'=>'search-form', 'action'=>Yii::app()->createUrl($this->route)));
echo CHtml::hiddenField('term');
$this->endWidget(); 

Yii::app()->clientScript->registerScript('js','
	$("#search").keyup(function(e){
		if(e.keyCode==13){
			$("#term").val($(this).val());
			$("#search-form").submit();
		}
	});
');

$this->beginWidget('zii.widgets.jui.CJuiDialog',array(
    'id'=>'mydialog',
    'options'=>array(
        'title'=>'Kamus Angka Kredit',
        'autoOpen'=>false,
    ),
));
echo '<div id=result></div>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
