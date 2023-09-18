<?php 
$this->pageTitle = 'BBM';
$this->pageCaption = 'Pemeliharaan Kendaraan';
$this->pageDescription = '';

$this->widget('TbMenu', array(
	'type'=>'tabs',
	'encodeLabel'=>false,
	'items'=>array(
		array('label'=>'Bulan Ini','url'=>array('index')),
		array('label'=>'Rekap','url'=>array('rekap')),
		array('label'=>BHtml::icon('plus-sign'),'url'=>'#','active'=>true),
	)
));

$this->renderPartial('_form', array(
	'model'=>$model
));