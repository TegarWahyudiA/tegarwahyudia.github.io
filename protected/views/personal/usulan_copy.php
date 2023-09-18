<?php
$this->pageCaption='Copy Usulan Kegiatan';
$this->pageTitle=Yii::app()->name . ' - ' . $this->pageCaption;
$this->pageDescription='';
$this->breadcrumbs=array(
	'Indeks Kegiatan'=>array('/kegiatan'),
	'Kegiatan Saya'=>array('/personal'),
	'Usulan Kegiatan'=>array('usulan'),
);
?>


<div class="form">

<?php $form=$this->beginWidget('BActiveForm', array(
	'id'=>'kegiatan-form',
	'enableAjaxValidation'=>false,
)); 

$filter = 'id LIKE \''.substr(Yii::app()->user->id_unitkerja,0,3).'%\' AND right(id,1)>0 AND right(id,1)<7';

$arr_jenis = array('1'=>'Kegiatan Utama','2'=>'Kegiatan Tambahan');
$arr_tampil = array('2'=>'Disembunyikan','1'=>'Ditampilkan');
$arr_unitkerja = array(''=>'-- Pilih --') + CHtml::listData(MasterUnitkerja::model()->findAll(array('condition'=>$filter)),'id','unitkerja');
$arr_satuan = array(''=>'-- Pilih --') + CHtml::listData(MasterSatuan::model()->findAll(array('order'=>'nama_satuan')),'id','nama_satuan');
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
			<?php //echo CHtml::link(BHtml::icon('plus-sign'),array('/admin/satuan_add'), array('title'=>'Master Satuan'));?> 
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
