<?php
$this->pageCaption='Monitoring Kegiatan Mingguan';
$this->pageTitle=$this->pageCaption;

$prev = $mingguke<=1? CHtml::link('<i class="g g-chevron-left"></i>',array('mingguan','tahun'=>$tahun-1,'mingguke'=>52)) : CHtml::link('<i class="g g-chevron-left"></i>',array('mingguan','tahun'=>$tahun,'mingguke'=>((int)$mingguke<=10? '0'.($mingguke-1) : $mingguke-1)));
$next = $mingguke>=52? CHtml::link('<i class="g g-chevron-right"></i>',array('mingguan','tahun'=>$tahun+1, 'mingguke'=>'01')) : CHtml::link('<i class="g g-chevron-right"></i>',array('mingguan','tahun'=>$tahun, 'mingguke'=>((int)$mingguke<9?'0'.($mingguke+1) : $mingguke+1)));

$periode = strftime('%d %b',strtotime($tahun.'W'.$mingguke.'1')).' - '.strftime('%d %b %Y',strtotime($tahun.'W'.$mingguke.'5'));

$this->pageDescription='<span style="float:right">'.$prev.' '.$periode.' '.$next.'</span>';

$this->breadcrumbs=array(
	'Monitoring',
	'Kegiatan Mingguan',
);

$this->widget('TbGridView', array(
	'id'=>'tabel-sub-kegiatan-grid',
	'dataProvider'=>$dataProvider,
	'cssFile'=>Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('ext.bootstrap-theme.widgets.assets')).'/gridview/styles.css',
	'itemsCssClass'=>'table',
	'template'=>'{items} {pager}',
	'columns'=>array(
		array('class'=>'IndexColumn'),
		array('header'=>'Nama Pegawai', 'type'=>'raw', 'value'=>function($data)use($mingguke,$arr_data){
			return isset($arr_data[$data->id])? CHtml::link($data->nama_pegawai,array('mingguan_view','mingguke'=>$mingguke,'pegawai'=>$data->id)) : $data->nama_pegawai;}),
		'jabatan',
		array('header'=>'Jml Kegiatan','value'=>function($data) use($arr_data){
			return isset($arr_data[$data->id])? $arr_data[$data->id] : '';
		}),
	),
)); 

Yii::app()->clientScript->registerScript('hide','
	$("tr").each(function(){
		var ada = $(this).find("i.g.g-search");
		if(!ada)
			$(this).css("display","none");
	})
');