<div class="form">

<?php $form=$this->beginWidget('BActiveForm', array(
	'id'=>'spj-kegiatan-form',
	'enableAjaxValidation'=>false,
)); 

$arr_dokumen = CHtml::listData(SpjMaster::model()->findAll(array('order'=>'dokumen')),'id','dokumen');
$arr_jenis = array(''=>'-- Pilih --','GU'=>'GU','LS'=>'LS','TUP'=>'TUP','UP'=>'UP');
?>

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

	<div class="<?php echo $form->fieldClass($model, 'nomor'); ?>">
		<?php echo $form->labelEx($model,'nomor'); ?>
		<div class="input">
			<?php echo $form->textField($model,'nomor',array('size'=>16,'maxlength'=>16)); ?>
			<?php echo $form->error($model,'nomor'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'nominal'); ?>">
		<?php echo $form->labelEx($model,'nominal'); ?>
		<div class="input">
			<?php echo $form->textField($model,'nominal',array('size'=>16,'maxlength'=>16)); ?>
			<?php echo $form->error($model,'nominal'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'jenis'); ?>">
		<?php echo $form->labelEx($model,'jenis'); ?>
		<div class="input">
			<?php echo $form->dropDownList($model,'jenis',$arr_jenis); ?>
			<?php echo $form->error($model,'jenis'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'drpp_tanggal'); ?>">
		<?php echo $form->labelEx($model,'drpp_tanggal'); ?>
		<div class="input">
			<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array(
					'model'=>$model, 
					'attribute'=>'drpp_tanggal',
					'options'=>array('dateFormat'=>'yy-mm-dd','changeMonth'=>true),
					'htmlOptions'=>array('style'=>'width:70px; text-align:center;')
					)); ?>
			<?php echo $form->error($model,'drpp_tanggal'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'drpp_nomor'); ?>">
		<?php echo $form->labelEx($model,'drpp_nomor'); ?>
		<div class="input">
			<?php echo $form->textField($model,'drpp_nomor',array('size'=>16,'maxlength'=>16)); ?>
			<?php echo $form->error($model,'drpp_nomor'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'keterangan'); ?>">
		<?php echo $form->labelEx($model,'keterangan'); ?>
		<div class="input">
			<?php echo $form->textArea($model,'keterangan',array('rows'=>3, 'cols'=>50)); ?>
			<?php echo $form->error($model,'keterangan'); ?>
		</div>
	</div>

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
	
if($model->jenis!='GU')
Yii::app()->clientScript->registerScript('gu','
	$("#SpjSpm_drpp_nomor").closest("div.control-group").hide();
	$("#SpjSpm_drpp_tanggal").closest("div.control-group").hide();
');

Yii::app()->clientScript->registerScript('js','
	$("#SpjSpm_jenis").change(function(){
		if($(this).val()=="GU"){
			$("#SpjSpm_drpp_nomor").closest("div.control-group").show();
			$("#SpjSpm_drpp_tanggal").closest("div.control-group").show();

		} else {
			$("#SpjSpm_drpp_nomor").closest("div.control-group").hide();
			$("#SpjSpm_drpp_tanggal").closest("div.control-group").hide();
		}
	});
');