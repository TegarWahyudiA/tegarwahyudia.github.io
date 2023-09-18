<?php
$this->pageTitle=$model->kegiatan->nama_kegiatan;
$this->pageCaption='Tambah Realisasi';
$this->pageDescription='';
$this->breadcrumbs=array(
	'Daftar Kegiatan'=>array('/kegiatan'),
	$model->kegiatan->unitkerja->unitkerja=>array('unitkerja','id'=>$model->kegiatan->id_unitkerja),
	$model->kegiatan->nama_kegiatan=>array('view','id'=>$model->id_kegiatan),
	'Detail Realisasi'=>array('realisasi','id'=>$model->id_kegiatan)
);
?>

<?php echo $this->renderPartial('realisasi_form', array('model'=>$model)); ?>