<?php
$this->pageCaption=$model->pegawai->nama_pegawai;
$this->pageTitle=Yii::app()->name . ' - ' . $this->pageCaption;
$this->pageDescription='';
$this->breadcrumbs=array(
	'Monitoring',//=>array('index','tahun'=>$tahun,'bulan'=>$bulan),
	'Rekap CKP'=>array('ckp_rekap','bulan'=>$model->kegiatan->bulan,'tahun'=>$model->kegiatan->tahun),
	$model->pegawai->nama_pegawai=>array('ckp','id'=>$model->id_pegawai,'bulan'=>$bulan,'tahun'=>$tahun),	
	'Update CKPD',
);

?>

<div class="form">

<?php $form=$this->beginWidget('BActiveForm', array(
	'id'=>'target-pegawai-form',
	'enableAjaxValidation'=>false,
)); ?>

	<div class="<?php echo $form->fieldClass($model, 'jml_target'); ?>">
		<?php echo $form->labelEx($model,'jml_target'); ?>
		<div style="padding-top:5px; font-weight:bold">
			<?php echo $model->target_satuan; ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'persen_kualitas'); ?>">
		<?php echo $form->labelEx($model,'persen_kualitas'); ?>
		<div class="input">
			<?php echo $form->textField($model,'persen_kualitas',array('style'=>'width:40px')).' %'; ?>
			<?php echo $form->error($model,'persen_kualitas'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'keterangan'); ?>">
		<?php echo $form->labelEx($model,'keterangan'); ?>
		<div class="input">
			<?php echo $form->textArea($model,'keterangan',array('rows'=>6, 'cols'=>50)); ?>
			<?php echo $form->error($model,'keterangan'); ?>
		</div>
	</div>

	<div class="actions">
		<?php echo CHtml::link('Cancel',Yii::app()->request->urlReferrer,array('class'=>'btn')); ?> 
		<?php echo BHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

	<?php echo CHtml::hiddenField('returnUrl',Yii::app()->request->urlReferrer); ?>

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php 
if(!$model->isNewRecord)
	echo CHtml::link(BHtml::icon('trash').'Delete',"#", array("submit"=>array('alokasi_delete', 'id'=>$model->id), 'confirm' => 'Are you sure?', 'csrf'=>true)); 
?>