<?php
$this->pageCaption = 'Monitoring SPJ';
$this->pageDescription = $model->jenis=='GU'? 'DRPP No. '.$model->drpp_nomor.', Tgl. '.$str_drpp_tanggal:'';

$this->breadcrumbs=array(
	'Monitoring SPJ'=>array('index'),
	CHtml::link(BHtml::icon('edit'),array('update_spm','id'=>$model->id),array('title'=>'Edit SPM ini')).' SPM No. '.$model->nomor.', Tgl. '.$model->str_tanggal,
	'View SPM',
);

$dataProvider = new CActiveDataProvider('SpjSpj', array(
	'pagination'=>array('pageSize'=>50),
	'criteria'=>array(
		'condition'=>'id_spm='.$model->id,
		'order'=>'no_urut,tanggal,nomor'
	)));

$this->widget('TbGridView', array(
	'dataProvider'=>$dataProvider,
	'template'=>'{items} {pager}',
	'columns'=>array(
//		array('class'=>'IndexColumn'),
		array('name'=>'no_urut','header'=>'#','headerHtmlOptions'=>array('style'=>'width:20px')),
		array('name'=>'str_tanggal','headerHtmlOptions'=>array('style'=>'width:75px')),
		array('name'=>'nomor','headerHtmlOptions'=>array('style'=>'width:55px')),
		array('name'=>'keperluan','type'=>'raw','value'=>function($data){
			return CHtml::link($data->keperluan,array('spj','id'=>$data->id),array('title'=>'View'));			
		}),
		'akun',
		array('name'=>'str_jumlah_kotor','headerHtmlOptions'=>array('style'=>'width:75px'),'htmlOptions'=>array('style'=>'text-align:right')),
		array('header'=>'Progress','type'=>'raw','headerHtmlOptions'=>array('style'=>'width:100px'), 'htmlOptions'=>array('style'=>'padding:0; line-height:0px; vertical-align:middle;','title'=>$data->jml_berkas.' berkas'),'value'=>function($data){
			$persen = $data->jml_berkas?number_format($data->progress,0) : 0;
			return Controller::createWidget('TbProgress',array('percent'=>$persen))->run();
		}),
	)
));

echo CHtml::link(BHtml::icon('plus-sign').'Tambah SPJ',array('create_spj','spm'=>$model->id)),'<br>';
echo CHtml::link(BHtml::icon('upload').'Import DRPP',array('import_drpp','spm'=>$model->id));
