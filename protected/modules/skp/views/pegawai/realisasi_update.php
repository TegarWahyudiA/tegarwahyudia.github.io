<?php
$this->pageTitle=$model->kegiatan->nama_kegiatan;
$this->pageCaption='Update Realisasi';
$this->pageDescription='';
$this->breadcrumbs=array(
	'SKP Saya'=>array('index', 'tahun'=>$model->kegiatan->tahun),
	$model->kegiatan->nama_kegiatan=>array('kegiatan','id'=>$model->id_kegiatan),
);
?>

<?php echo $this->renderPartial('realisasi_form', array('model'=>$model)); ?>