<?php
$this->pageCaption = $model->dokumen->dokumen;
$this->pageDescription = '';

$this->breadcrumbs=array(
	'Monitoring SPJ'=>array('index'),
	$model->spj->nama_kegiatan=>array('view','id'=>$model->id_spj),
	'Edit Dokumen',
);

$arr_status = array(''=>'-- Pilih --', '2'=>'Sedang Diperiksa', '1'=>'Perlu Perbaikan', '4'=>'Disetujui');
?>

<div class="form">

<?php $form=$this->beginWidget('BActiveForm', array('id'=>'spj-dokumen-form', 'enableAjaxValidation'=>false)); ?>

	<div class="<?php echo $form->fieldClass($model, 'tanggal'); ?>">
		<?php echo $form->labelEx($model,'tanggal'); ?>
		<div class="input">
			<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array(
					'model'=>$model, 
					'attribute'=>'tanggal',
					'options'=>array('dateFormat'=>'yy-mm-dd','changeMonth'=>true),
					'htmlOptions'=>array('style'=>'width:70px; text-align:center;')
					)); ?>
			<?php echo $form->error($model,'tanggal'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'status'); ?>">
		<?php echo $form->labelEx($model,'status'); ?>
		<div class="input">
			<?php echo $form->dropDownList($model,'status',$arr_status); ?>
			<?php echo $form->error($model,'status'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'keterangan'); ?>">
		<?php echo $form->labelEx($model,'keterangan'); ?>
		<div class="input">
			<?php echo $form->textArea($model,'keterangan',array('rows'=>2, 'cols'=>50)); ?>
			<?php echo $form->error($model,'keterangan'); ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label"></label>
		<div class="input">
			<?php echo BHtml::submitButton('Save'); ?>
			<?php echo CHtml::link('Cancel', array('view','id'=>$model->id_spj), array('class'=>'btn')); ?> 
		</div>
	</div>

	<?php echo $form->errorSummary($model); ?>

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php
echo CHtml::link('<i class="g g-trash"></i> Delete',"#", array("submit"=>array('dokumen_delete', 'id'=>$model->id_spj, 'dokumen'=>$model->id_dokumen), 'confirm' => 'Are you sure?', 'csrf'=>true)); 
