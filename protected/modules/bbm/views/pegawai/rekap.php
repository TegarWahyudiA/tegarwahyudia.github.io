<?php 
$this->pageTitle = 'BBM';
$this->pageCaption = 'Pemeliharaan Kendaraan';

$this->pageDescription = '<span style="float:right">'.CHtml::link(BHtml::icon('chevron-left'),array('rekap','tahun'=>$tahun-1)).$tahun.' '.CHtml::link(BHtml::icon('chevron-right'),array('rekap','tahun'=>$tahun+1)).'</span>';

$this->widget('TbMenu', array(
	'type'=>'tabs',
	'items'=>array(
		array('label'=>'Bulan Ini','url'=>array('index')),
		array('label'=>'Rekap','url'=>array('rekap'),'active'=>true),
	)
));

$rekap = Yii::app()->db->createCommand()->select('month(tanggal) as bulan, sum(case when id_jenis=1 then nilai else 0 end) as bensin, sum(case when id_jenis=2 then nilai else 0 end) as bengkel')->from('t_bbm')->where('year(tanggal)='.$tahun)->group('month(tanggal)')->order('month(tanggal)')->queryAll();

echo '<pre>';
print_r($rekap);
echo '</pre>';