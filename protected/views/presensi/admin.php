<?php
$this->pageCaption='Manage Personal Calendars';
$this->pageTitle=$this->pageCaption;
$this->pageDescription=CHtml::link('<i class="icon icon-plus-sign"></i>',array('create'),array('title'=>'Create new Personal Calendars'));
$this->breadcrumbs=array(
	'Personal Calendars',
	'Manage',
);

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'personal-calendar-grid',
	'dataProvider'=>$model->search(),
//	'filter'=>$model,
	'cssFile'=>Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('ext.bootstrap-theme.widgets.assets')).'/gridview/styles.css',
	'itemsCssClass'=>'table',
	'template'=>'{items} {pager}',
	'columns'=>array(
		array('header'=>'#','htmlOptions'=>array('style'=>'width:20px'),'value'=>'$this->grid->dataProvider->getPagination()->getOffset()+$row+1'),
		'id',
		'FingerPrintID',
		'PersonalCalendarDate',
		'TimeCome',
		'TimeHome',
		'LateIn',
		/*
		'EarlyOut',
		'PersonalCalendarReason',
		*/
		array('type'=>'raw','htmlOptions'=>array('style'=>'width:40px'),'value'=>function($data){ 
			return CHtml::link('<i class="icon icon-search" title="View"></i>',array('view','id'=>$data->id)).' '. CHtml::link('<i class="icon icon-pencil" title="Update"></i>',array('update','id'=>$data->id));
		}),
	),
)); ?>
