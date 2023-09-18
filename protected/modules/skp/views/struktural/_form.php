<div class="form">

<?php $form=$this->beginWidget('BActiveForm', array(
	'id'=>'tabel-kegiatan-form',
	'enableAjaxValidation'=>false,
)); 

if(Yii::app()->user->id_eselon==2)
	$filter = 'id like \''.substr(Yii::app()->user->id_unitkerja,0,2).'%0\' AND id<>9280';
elseif(Yii::app()->user->id_eselon==3)
	$filter = 'id like \''.substr(Yii::app()->user->id_unitkerja,0,3).'%\'';
else
	$filter = 'id='.Yii::app()->user->id_unitkerja;

//echo $filter;

$arr_unitkerja = array(''=>'-- Pilih --') + CHtml::listData(MasterUnitkerja::model()->findAll(array('condition'=>$filter)),'id','unitkerja');
$arr_satuan = array(''=>'-- Pilih --') + CHtml::listData(MasterSatuan::model()->findAll(array('order'=>'nama_satuan')),'id','nama_satuan');
?>

	<div class="<?php echo $form->fieldClass($model, 'tahun'); ?>">
		<?php echo $form->labelEx($model,'tahun'); ?>
		<div class="input">
			<?php echo $form->textField($model,'tahun',array('size'=>4,'maxlength'=>4, 'style'=>'width:40px; text-align: center')); ?>
			<?php echo $form->error($model,'tahun'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'nama_kegiatan'); ?>">
		<?php echo $form->labelEx($model,'nama_kegiatan'); ?>
		<div class="input">
			<?php echo $form->textField($model,'nama_kegiatan',array('size'=>60,'maxlength'=>128)); ?>
			<?php echo $form->error($model,'nama_kegiatan'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'id_unitkerja'); ?>">
		<?php echo $form->labelEx($model,'id_unitkerja'); ?>
		<div class="input">
			<?php echo $form->dropDownList($model,'id_unitkerja',$arr_unitkerja); ?>
			<?php echo $form->error($model,'id_unitkerja'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'id_satuan'); ?>">
		<?php echo $form->labelEx($model,'id_satuan'); ?>
		<div class="input">
			<?php echo $form->dropDownList($model,'id_satuan',$arr_satuan); ?> 
			<?php echo CHtml::link(BHtml::icon('plus-sign'),array('/admin/satuan_add'), array('title'=>'Master Satuan'));?> 
			<?php echo $form->error($model,'id_satuan'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'jml_target'); ?>">
		<?php echo $form->labelEx($model,'jml_target'); ?>
		<div class="input">
			<?php echo $form->textField($model,'jml_target', array('style'=>'width:40px; text-align: center;')); ?>
			<?php echo $form->error($model,'jml_target'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'jml_bulan'); ?>">
		<?php echo $form->labelEx($model,'jml_bulan'); ?>
		<div class="input">
			<?php echo $form->textField($model,'jml_bulan', array('style'=>'width:40px; text-align: center;')); ?>
			<?php echo $form->error($model,'jml_bulan'); ?>
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
			<?php echo CHtml::link('Cancel', Yii::app()->request->urlReferrer, array('class'=>'btn')); ?> 
		</div>
	</div>

	<?php echo $form->errorSummary($model); ?>

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php 
if(!$model->isNewRecord)
	echo CHtml::link('<i class="g g-trash"></i> Delete',"#", array("submit"=>array('delete', 'id'=>$model->id), 'confirm' => 'Are you sure?', 'csrf'=>true)); 
	
?>