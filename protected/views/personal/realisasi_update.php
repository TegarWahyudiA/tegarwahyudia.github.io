<?php
$this->pageCaption='Update TabelRealisasi '.$model->id;
$this->pageTitle=Yii::app()->name . ' - ' . $this->pageCaption;
$this->pageDescription='';
$this->breadcrumbs=array(
	'Kegiatan Saya'=>array('/personal'),
	$model->kegiatan->nama_kegiatan=>array('kegiatan','id'=>$model->id_kegiatan)
);
?>

<?php echo $this->renderPartial('realisasi_form', array('model'=>$model,'target'=>$target)); ?>