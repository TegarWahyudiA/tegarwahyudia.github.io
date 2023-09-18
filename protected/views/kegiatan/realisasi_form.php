<?php
$arr_pegawai = array(''=>'-- Pilih --') + CHtml::listData(TabelTargetPegawai::model()->findAllByAttributes(array('id_kegiatan'=>$model->id_kegiatan)),'id_pegawai','pegawai.nama_pegawai');
?>
<div class="form">

	<?php $form=$this->beginWidget('BActiveForm', array(
		'id'=>'tabel-realisasi-form',
		'enableAjaxValidation'=>false,
	)); ?>

	<div class="<?php echo $form->fieldClass($model, 'id_pegawai'); ?>">
		<b><?php echo $form->labelEx($model,'id_pegawai'); ?></b>
		<div class="input">
			<?php echo $form->dropDownList($model,'id_pegawai',$arr_pegawai); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'tgl'); ?>">
		<?php echo $form->labelEx($model,'tgl'); ?>
		<div class="input">
			<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array(
					'model'=>$model, 
					'attribute'=>'tgl',
					'options'=>array('dateFormat'=>'yy-mm-dd','changeMonth'=>true),
					'htmlOptions'=>array('style'=>'width:70px; text-align:center;')
					)); ?>
			<?php echo $form->error($model,'tgl'); ?>
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

	<div class="actions">
		<?php echo CHtml::link('Cancel',Yii::app()->request->urlReferrer,array('class'=>'btn')); ?> 
		<?php echo BHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?> 
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php 
if(!$model->isNewRecord)
	echo CHtml::link(BHtml::icon('trash').'Delete',"#", array("submit"=>array('realisasi_delete', 'id'=>$model->id), 'confirm' => 'Are you sure?', 'csrf'=>true)); 
?>