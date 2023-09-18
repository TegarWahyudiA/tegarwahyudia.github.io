<div class="form">

<?php $form=$this->beginWidget('BActiveForm', array(
	'id'=>'bbm-form',
	'enableAjaxValidation'=>false,
)); 

$arr_jenis = array(''=>'-- Pilih --','1'=>'Nota Bensin','2'=>'Nota Bengkel');
?>
	<div class="<?php echo $form->fieldClass($model, 'id_jenis'); ?>">
		<?php echo $form->labelEx($model,'id_jenis'); ?>
		<div class="input">
			<?php echo $form->dropDownList($model,'id_jenis',$arr_jenis); ?>
			<?php echo $form->error($model,'id_jenis'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'tanggal'); ?>">
		<?php echo $form->labelEx($model,'tanggal'); ?>
		<div class="input">
			<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array(
					'model'=>$model, 
					'attribute'=>'tanggal',
					'language'=>'id',
					'options'=>array('dateFormat'=>'yy-mm-dd','changeMonth'=>true),
					'htmlOptions'=>array('style'=>'width:70px; text-align:center;')
					)); ?>
			<?php echo $form->error($model,'tanggal'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'nilai'); ?>">
		<?php echo $form->labelEx($model,'nilai'); ?>
		<div class="input">
			<?php echo $form->textField($model,'nilai',array('size'=>60,'maxlength'=>10)); ?>
			<?php echo $form->error($model,'nilai'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'keterangan'); ?>">
		<?php echo $form->labelEx($model,'keterangan'); ?>
		<div class="input">
			<?php echo $form->textField($model,'keterangan',array('size'=>60,'maxlength'=>128)); ?>
			<?php echo $form->error($model,'keterangan'); ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label"></label>
		<div class="input">
			<?php echo BHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?> 
			<?php echo CHtml::link('Cancel', array('index'), array('class'=>'btn')); ?>
		</div>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<?php 
if(!$model->isNewRecord) 
	echo CHtml::link('<i class="g g-trash"></i> Delete','#', array('submit'=>array('delete', 'id'=>$model->id), 'confirm' => 'Are you sure?', 'csrf'=>true));