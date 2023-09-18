<?php
$this->widget('TbMenu', array(
	'type'=>'tabs',
	'items'=>array(
		array('label'=>'Presensi Harian','url'=>array('index')),
		array('label'=>'Proses','url'=>'#','active'=>true),
		array('label'=>'Rekap Bulanan','url'=>array('rekap')),
		array('label'=>'Ketidakhadiran','url'=>array('absen')),
		array('label'=>'Perubahan Jam','url'=>array('jam')),
		array('label'=>'Lembur','url'=>array('lembur')),
		array('label'=>'Hari Libur','url'=>array('libur')),
	)
));
