<?php
$this->pageCaption='Lock Data';
$this->pageTitle=Yii::app()->name . ' - ' . $this->pageCaption;
$this->pageDescription='';
$this->breadcrumbs=array(
	'Daftar Kegiatan'=>array('/kegiatan'),
	'Lock Data'=>array('lock'),
);
$arr_lock = array('0'=>'Tidak dikunci', '1'=>'Data dikunci');
$arr_bulan = array(''=>'-- Pilih --');
for($i=1; $i<=12; $i++){
	$arr_bulan[$i] = $i.'. '.strftime('%B',mktime(0,0,0,$i));
}
if(!$model->tahun) 
	$model->tahun = date('Y');
?>

<div class="form">

<?php $form=$this->beginWidget('BActiveForm', array(
	'id'=>'target-pegawai-form',
	'enableAjaxValidation'=>false,
)); ?>

	<div class="<?php echo $form->fieldClass($model, 'tahun'); ?>">
		<?php echo $form->labelEx($model,'tahun'); ?>
		<div class="input">
			<?php echo $form->textField($model,'tahun',array('style'=>'width:40px')); ?>
			<?php echo $form->error($model,'tahun'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'bulan'); ?>">
		<?php echo $form->labelEx($model,'bulan'); ?>
		<div class="input">
			<?php echo $form->dropDownList($model,'bulan',$arr_bulan); ?>
			<?php echo $form->error($model,'bulan'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'is_lock'); ?>">
		<?php echo $form->labelEx($model,'is_lock'); ?>
		<div class="input">
			<?php echo $form->dropDownList($model,'is_lock',$arr_lock); ?>
			<?php echo $form->error($model,'is_lock'); ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label"></label>
		<div class="input">
			<?php echo CHtml::link('Cancel',array('lock'),array('class'=>'btn')); ?> 
			<?php echo BHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
		</div>
	</div>

	<?php echo CHtml::hiddenField('returnUrl',Yii::app()->request->urlReferrer); ?>

<?php $this->endWidget(); ?>

</div><!-- form -->
