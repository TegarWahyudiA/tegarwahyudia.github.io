<div class="form">

<?php $form=$this->beginWidget('BActiveForm', array(
	'id'=>'spj-kegiatan-form',
	'enableAjaxValidation'=>false,
)); 

$arr_dokumen = CHtml::listData(SpjMaster::model()->findAll(array('order'=>'dokumen')),'id','dokumen');
?>

	<div class="<?php echo $form->fieldClass($model, 'no_urut'); ?>">
		<?php echo $form->labelEx($model,'no_urut'); ?>
		<div class="input">
			<?php echo $form->textField($model,'no_urut',array('size'=>2,'maxlength'=>2,'style'=>'width:30px')); ?>
			<?php echo $form->error($model,'no_urut'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'keperluan'); ?>">
		<?php echo $form->labelEx($model,'keperluan'); ?>
		<div class="input">
			<?php echo $form->textArea($model,'keperluan',array('rows'=>5, 'cols'=>50)); ?>
			<?php echo $form->error($model,'keperluan'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'nomor'); ?>">
		<?php echo $form->labelEx($model,'nomor'); ?>
		<div class="input">
			<?php echo $form->textField($model,'nomor',array('size'=>16,'maxlength'=>16)); ?>
			<?php echo $form->error($model,'nomor'); ?>
		</div>
	</div>

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

	<div class="<?php echo $form->fieldClass($model, 'akun'); ?>">
		<?php echo $form->labelEx($model,'akun'); ?>
		<div class="input">
			<?php echo $form->textField($model,'akun',array('maxlength'=>6,'style'=>'width:70px')); ?>
			<?php echo $form->error($model,'akun'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'jumlah_kotor'); ?>">
		<?php echo $form->labelEx($model,'jumlah_kotor'); ?>
		<div class="input">
			<?php echo $form->textField($model,'jumlah_kotor',array('size'=>16,'maxlength'=>16)); ?>
			<?php echo $form->error($model,'jumlah_kotor'); ?>
		</div>
	</div>


	<?php if($model->isNewRecord){ ?>
	<div class="control-group">
		<label class="control-label">Dokumen</label>
		<div class="input" style='padding-left:145px'>
			<?php 
				foreach($arr_dokumen as $id=>$value)
					echo CHtml::checkbox('dokumen['.$id.']').' '.$value.'<br>';
			?>
		</div>
	</div>
	<?php } ?>

	<div class="control-group">
		<label class="control-label"></label>
		<div class="input">
			<?php echo BHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?> 
			<?php echo CHtml::link('Cancel', Yii::app()->request->urlReferrer, array('class'=>'btn')); ?> 
		</div>
	</div>

	<?php echo $form->errorSummary($model); ?>

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php 
if(!$model->isNewRecord)
	echo CHtml::link('<i class="g g-trash"></i> Delete',"#", array("submit"=>array('delete', 'id'=>$model->id), 'confirm' => 'Hapus SPJ ini beserta seluruh dokumen terkait?', 'csrf'=>true)); 
	
?>