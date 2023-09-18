<?php
$this->pageCaption='Split Database';
$this->pageTitle=Yii::app()->name . ' - ' . $this->pageCaption;
$this->pageDescription='';
$this->breadcrumbs=array(
	'Split Database'=>array('index'),
	'Create New'
);
$arr_lock = array('0'=>'Tidak dikunci', '1'=>'Data dikunci');
$arr_bulan = array(''=>'-- Pilih --');
for($i=1; $i<=12; $i++){
	$arr_bulan[$i] = $i.'. '.strftime('%B',mktime(0,0,0,$i));
}
?>

<div class="form">

<?php $form=$this->beginWidget('BActiveForm', array(
	'id'=>'target-pegawai-form',
	'enableAjaxValidation'=>false,
)); ?>

	<div class="control-group">
		<label class="control-label">Pilih Bulan</label>
		<div class="input">
			<?php echo CHtml::dropDownList('bulan',$bulan,$arr_bulan); ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">Tahun</label>
		<div class="input">
			<?php echo CHtml::textField('tahun',$tahun,array('style'=>'width:40px;text-align:center','maxlength'=>4)); ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label"></label>
		<div class="input">
			<?php echo BHtml::submitButton('Split Database'); ?>
		</div>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php 
if($log){
	echo '<pre>'.$log.'</pre>';
	echo CHtml::link('Download Sekarang', array('download','file'=>$filename),array('class'=>'btn btn-primary')).' '.
		CHtml::link('Download Nanti', array('index'),array('class'=>'btn'));
} elseif(isset($tahun) && isset($bulan)) {
	echo '<pre>Data tidak tersedia.</pre>';
}