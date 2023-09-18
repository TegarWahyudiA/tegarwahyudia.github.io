<div class="form">

<?php $form=$this->beginWidget('BActiveForm', array(
	'id'=>'tabel-sub-kegiatan-form',
	'enableAjaxValidation'=>false,
)); 

if(Yii::app()->user->id_eselon==2)
	$filter = 'id like \''.substr(Yii::app()->user->id_unitkerja,0,2).'%0\' AND id<>9280';
elseif(Yii::app()->user->id_eselon==3 || Yii::app()->user->isAdmin)
	$filter = 'id like \''.substr(Yii::app()->user->id_unitkerja,0,3).'%\' AND id<=9286';
else
	$filter = 'id='.Yii::app()->user->id_unitkerja;

//echo $filter;

$arr_tampil = array('2'=>'Disembunyikan','1'=>'Ditampilkan');
$arr_unitkerja = array(''=>'-- Pilih --') + CHtml::listData(MasterUnitkerja::model()->findAll(array('condition'=>$filter)),'id','unitkerja');
$arr_satuan = array(''=>'-- Pilih --') + CHtml::listData(MasterSatuan::model()->findAll(array('order'=>'nama_satuan')),'id','nama_satuan');
$arr_jenis = array('1'=>'Kegiatan Utama','2'=>'Kegiatan Tambahan');
?>

	<div class="<?php echo $form->fieldClass($model, 'nama_kegiatan'); ?>">
		<?php echo $form->labelEx($model,'nama_kegiatan'); ?>
		<div class="input">
			<?php echo $form->textField($model,'nama_kegiatan',array('size'=>60,'maxlength'=>128)); ?>
			<?php echo $form->error($model,'nama_kegiatan'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'id_jenis'); ?>">
		<?php echo $form->labelEx($model,'id_jenis'); ?>
		<div class="input">
			<?php echo $form->dropDownList($model,'id_jenis',$arr_jenis); ?>
			<?php echo $form->error($model,'id_jenis'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'id_unitkerja'); ?>">
		<?php echo $form->labelEx($model,'id_unitkerja'); ?>
		<div class="input">
			<?php echo $form->dropDownList($model,'id_unitkerja',$arr_unitkerja); ?>
			<?php echo $form->error($model,'id_unitkerja'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'tgl_mulai'); ?>">
		<?php echo $form->labelEx($model,'tgl_mulai'); ?>
		<div class="input">
			<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array(
					'model'=>$model, 
					'attribute'=>'tgl_mulai',
					'options'=>array('dateFormat'=>'yy-mm-dd','changeMonth'=>true),
					'htmlOptions'=>array('style'=>'width:70px; text-align:center;')
					)); ?>
			<?php echo $form->error($model,'tgl_mulai'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'tgl_selesai'); ?>">
		<?php echo $form->labelEx($model,'tgl_selesai'); ?>
		<div class="input">
			<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array(
					'model'=>$model, 
					'attribute'=>'tgl_selesai',
					'options'=>array('dateFormat'=>'yy-mm-dd','changeMonth'=>true),
					'htmlOptions'=>array('style'=>'width:70px; text-align:center;')
					)); ?>
			<?php echo $form->error($model,'tgl_selesai'); ?>
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
			<?php echo $form->textField($model,'jml_target'); ?>
			<?php echo $form->error($model,'jml_target'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'keterangan'); ?>">
		<?php echo $form->labelEx($model,'keterangan'); ?>
		<div class="input">
			<?php echo $form->textArea($model,'keterangan',array('rows'=>6, 'cols'=>50)); ?>
			<?php echo $form->error($model,'keterangan'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'is_ckp'); ?>">
		<?php echo $form->labelEx($model,'is_ckp'); ?>
		<div class="input">
			<?php echo $form->dropDownList($model,'is_ckp',$arr_tampil); ?>
			<?php echo $form->error($model,'is_ckp'); ?>
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