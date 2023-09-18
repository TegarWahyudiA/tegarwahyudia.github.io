<?php 
$this->pageTitle = 'BBM';
$this->pageCaption = 'Pemeliharaan Kendaraan';

$prev = $bulan<=1? array('index','tahun'=>$tahun-1,'bulan'=>12) : array('index','tahun'=>$tahun,'bulan'=>$bulan-1);
$next = $bulan>=12? array('index','tahun'=>$tahun+1,'bulan'=>1) : array('index','tahun'=>$tahun,'bulan'=>$bulan+1);

$this->pageDescription = '<span style="float:right">'.CHtml::link(BHtml::icon('chevron-left'),$prev).strftime('%B %Y',mktime(0,0,0,$bulan,1,$tahun)).' '.CHtml::link(BHtml::icon('chevron-right'),$next).'</span>';

$this->widget('TbMenu', array(
	'type'=>'tabs',
	'items'=>array(
		array('label'=>'Bulan Ini','url'=>'#','active'=>true),
		array('label'=>'Rekap','url'=>array('rekap')),
	)
));

$this->widget('TbGridView', array(
	'dataProvider'=>$dataProvider,
	'template'=>'{items} {pager}',
	'columns'=>array(
		array('class'=>'IndexColumn'),
		'str_tanggal',
		'jenis',
		'str_nilai',
		'keterangan'
	)
));

echo CHtml::link(BHtml::icon('plus-sign').'Input Data',array('add'));