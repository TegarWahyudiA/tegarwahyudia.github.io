<?php
$this->pageTitle='Input Realisasi '.$model->kegiatan->nama_kegiatan;
$this->pageCaption='Input Realisasi';
$this->pageDescription='Jadwal : '.$model->kegiatan->jadwal;
$this->breadcrumbs=array(
	'Kegiatan Saya'=>array('/personal'),
	$model->kegiatan->nama_kegiatan=>array('kegiatan','id'=>$model->id_kegiatan),
);
?>

<?php echo $this->renderPartial('realisasi_form', array('model'=>$model,'target'=>$target)); ?>