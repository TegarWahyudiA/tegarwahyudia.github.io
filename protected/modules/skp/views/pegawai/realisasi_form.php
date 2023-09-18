<div class="form">

	<?php $form=$this->beginWidget('BActiveForm', array(
		'id'=>'tabel-realisasi-form',
		'enableAjaxValidation'=>false,
	)); ?>

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

	<div class="<?php echo $form->fieldClass($model, 'jml_realisasi'); ?>">
		<?php echo $form->labelEx($model,'jml_realisasi'); ?>
		<div class="input">
			<?php echo $form->textField($model,'jml_realisasi',array('style'=>'width:70px')).' '.$model->kegiatan->satuan->nama_satuan; ?>
			<?php echo $form->error($model,'jml_realisasi'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'keterangan'); ?>">
		<?php echo $form->labelEx($model,'keterangan'); ?>
		<div class="input">
			<?php echo $form->textArea($model,'keterangan',array('rows'=>6, 'cols'=>50)); ?>
			<?php echo $form->error($model,'keterangan'); ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label"></label>
		<div class="input">
			<?php echo BHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?> 
			<?php echo CHtml::link('Cancel',Yii::app()->request->urlReferrer,array('class'=>'btn')); ?> 
		</div>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php 
if(!$model->isNewRecord)
	echo CHtml::link(BHtml::icon('trash').'Delete',"#", array("submit"=>array('realisasi_delete', 'id'=>$model->id), 'confirm' => 'Are you sure?', 'csrf'=>true)); 
?>