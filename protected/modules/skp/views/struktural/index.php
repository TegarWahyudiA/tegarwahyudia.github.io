<?php
$this->pageCaption = MasterUnitkerja::model()->findByPk($unitkerja)->unitkerja;


$prev = array('index','unitkerja'=>$unitkerja,'tahun'=>$tahun-1);
$next = array('index','unitkerja'=>$unitkerja,'tahun'=>$tahun+1);

$this->pageDescription = '<span style="float:right">'.CHtml::link(BHtml::icon('chevron-left'),$prev).' '.$tahun.' '.CHtml::link(BHtml::icon('chevron-right'),$next).'</span>';

$this->breadcrumbs=array(
	'SKP',
	'Struktural',
);

$this->widget('TbGridView', array(
	'dataProvider'=>$dataProvider,
	'template'=>'{items} {pager}',
	'columns'=>array(
		array('class'=>'IndexColumn'),
		array('header'=>'Nama Kegiatan','type'=>'raw','value'=>function($data){return CHtml::link($data->nama_kegiatan, array('view','id'=>$data->id),array('title'=>$data->unitkerja->unitkerja));}),
		array('name'=>'target_satuan','headerHtmlOptions'=>array('style'=>'width:120px')),
		array('name'=>'jml_bulan','headerHtmlOptions'=>array('style'=>'width:80px')),
		array('header'=>'Progress','type'=>'raw','headerHtmlOptions'=>array('style'=>'width:120px'), 'htmlOptions'=>array('style'=>'padding:0; line-height:0px; vertical-align:middle;'),'value'=>function($data){
			$persen = $data->jml_target?number_format($data->progress,0) : 0;
//			return $excel? $persen.' %' : Controller::createWidget('TbProgress',array('percent'=>$persen))->run();
			return Controller::createWidget('TbProgress',array('percent'=>$persen))->run();
		}),
		array('header'=>'Alokasi','type'=>'raw','headerHtmlOptions'=>array('style'=>'width:60px'),'value'=>function($data){
			if(!$data->child_target)
				return '-';
			elseif($data->child_target==$data->jml_target)
				return BHtml::icon('ok',array('style'=>'color:green'));
			else
				return BHtml::icon('warning-sign', array('style'=>'color:orangered'));
		}),
	)
));

echo CHtml::link(BHtml::icon('plus-sign').'Tambah Kegiatan',array('create','tahun'=>$tahun));