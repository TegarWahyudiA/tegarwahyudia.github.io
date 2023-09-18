<?php
$this->pageCaption='Lock Kegiatan';
$this->pageTitle=Yii::app()->name . ' - ' . $this->pageCaption;
$this->pageDescription='';
$this->breadcrumbs=array(
	'Daftar Kegiatan'=>array('/kegiatan'),
	'Lock Data',
);

$this->widget('TbGridView', array(
	'dataProvider'=>$dataProvider,
	'template'=>'{items} {pager}',
	'columns'=>array(
		array('class'=>'IndexColumn'),
		array('header'=>'Bulan','value'=>function($data){
			return strftime('%B %Y',mktime(0,0,0,$data->bulan,1,$data->tahun));
		}),
		array('header'=>'Status','type'=>'raw','value'=>function($data){
			if($data->is_lock)
				return BHtml::icon('lock',array('style'=>'color:red')).' Dikunci';
			else
				return 'Tidak dikunci';
		}),
		array('header'=>'','type'=>'raw','headerHtmlOptions'=>array('style'=>'width:20px'),'value'=>function($data){
			return CHtml::link(BHtml::icon('pencil'),array('lock_update','tahun'=>$data->tahun,'bulan'=>$data->bulan));
		}),
	),
));

echo  CHtml::link(BHtml::icon('plus-sign').' Tambah Bulan',array('lock_update'));