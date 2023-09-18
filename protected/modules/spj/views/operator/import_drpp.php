<?php
$this->pageCaption = 'Import DRPP';
$this->pageDescription = '';

$this->breadcrumbs=array(
	'Monitoring SPJ'=>array('index'),
	'SPM No. '.$spm->nomor.', Tgl. '.$spm->str_tanggal => array('spm','id'=>$spm->id),
);

?>

<div class="form">

<?php $form=$this->beginWidget('BActiveForm', array(
	'id'=>'drpp-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('enctype'=>'multipart/form-data'),
)); 
?>
	<div class="control-group">
		<label class="control-label"><span class="required"></span>Pilih file Excel</label>
		<div class="input">
			<?php echo CHtml::fileField('drpp'); ?> <br>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label"></label>
		<div class="input">
			<?php echo CHtml::link(BHtml::icon('download').'Template DRPP',array('template_drpp')); ?> 
		</div>
	</div>

	<div class="control-group">
		<label class="control-label"></label>
		<div class="input">
			<?php echo BHtml::submitButton('Upload'); ?> 
			<?php echo CHtml::link('Cancel',  array('spm','id'=>$spm->id), array('class'=>'btn')); ?> 
		</div>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->