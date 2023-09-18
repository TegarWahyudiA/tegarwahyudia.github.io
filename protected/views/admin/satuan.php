<?php
$this->pageCaption='Manage Master Satuan';
$this->pageTitle=$this->pageCaption;
$this->pageDescription=CHtml::link(BHtml::icon('plus-sign'),array('satuan_add'),array('title'=>'Create new Master Satuan'));
$this->breadcrumbs=array(
	'Master Satuan',
);

$this->widget('TbGridView', array(
	'id'=>'master-satuan-grid',
	'dataProvider'=>$model->search(),
//	'filter'=>$model,
	'cssFile'=>Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('ext.bootstrap-theme.widgets.assets')).'/gridview/styles.css',
	'itemsCssClass'=>'table',
	'template'=>'{items} {pager}',
	'columns'=>array(
		array('class'=>'IndexColumn'),
		//'id',
		'nama_satuan',
		array('type'=>'raw','htmlOptions'=>array('style'=>'width:20px'),'value'=>function($data){ 
			return CHtml::link(BHtml::icon('pencil'),array('satuan_update','id'=>$data->id),array('title'=>'Update'));
		}),
	),
)); ?>
