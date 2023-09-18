<?php
$this->pageCaption='Update Data Pegawai ';
$this->pageTitle=$this->pageCaption;
$this->pageDescription='';
$this->breadcrumbs=array(
	'Master Pegawai'=>array('pegawai'),
	'Update',
);

?>

<?php echo $this->renderPartial('pegawai_form', array('model'=>$model)); ?>