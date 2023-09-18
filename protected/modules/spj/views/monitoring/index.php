<?php
$this->pageCaption = 'Monitoring SPM';
$this->pageDescription = '<span style="float:right">'.CHtml::tag('ul',array('class'=>'nav nav-tabs','style'=>'float:left;font-size:smaller;margin-top:-5px;border-bottom:none')).
	CHtml::tag('li',array('class'=>$status=='belum'?'active':''),CHtml::link('Belum Lengkap',array('index','tahun'=>$tahun))).
	CHtml::tag('li',array('class'=>$status=='selesai'?'active':''),CHtml::link('Sudah Selesai',array('index','status'=>'selesai','tahun'=>$tahun))).
	'</ul><span style="float:right;margin-left:10px;">'.
	CHtml::link(BHtml::icon('chevron-left'),array('index','status'=>$status,'tahun'=>($tahun-1))).
	' '.$tahun.' '.
	CHtml::link(BHtml::icon('chevron-right'),array('index','status'=>$status,'tahun'=>($tahun+1))).
	'</span></span>';

$this->breadcrumbs=array(
	'Monitoring SPJ'
);

$filter = new CDbCriteria();
$filter->with = 'dokumen';
$filter->compare('persen',100);

$this->widget('TbGridView', array(
	'dataProvider'=>$dataProvider,
//	'filter'=>$filter,
	'template'=>'{items} {pager}',
	'columns'=>array(
		array('class'=>'IndexColumn'),
		array('name'=>'nomor','headerHtmlOptions'=>array('style'=>'width:100px'),'type'=>'raw','value'=>function($data){
			return CHtml::link($data->nomor,array('spm','id'=>$data->id),array('title'=>'View'));
		}),
		array('name'=>'str_tanggal','headerHtmlOptions'=>array('style'=>'width:80px')),
		array('name'=>'str_nominal','headerHtmlOptions'=>array('style'=>'width:80px')),
		'jenis',
		array('header'=>'Progress','type'=>'raw','headerHtmlOptions'=>array('style'=>'width:120px'), 'htmlOptions'=>array('style'=>'padding:0; line-height:0px; vertical-align:middle;'),'value'=>function($data){
			$persen = $data->persen?number_format($data->persen,0) : 0;
			return Controller::createWidget('TbProgress',array('percent'=>$persen))->run();
		}),
	)
));
