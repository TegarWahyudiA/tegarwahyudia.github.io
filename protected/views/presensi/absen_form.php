<div class="form">

<?php $form=$this->beginWidget('BActiveForm', array(
	'id'=>'personal-calendar-form',
	'enableAjaxValidation'=>false,
)); 

$arr_pegawai = array(''=>'-- Pilih --') + CHtml::listData(MasterPegawai::model()->findAll(array('order'=>'nama_pegawai', 'condition'=>'is_aktif=1')),'id_presensi','nama_pegawai');
$arr_status = array(''=>'-- Pilih --') + CHtml::listData(CalendarStatus::model()->findAll(array('order'=>'PersonalCalendarStatus')),'id','PersonalCalendarStatus');
?>

	<div class="<?php echo $form->fieldClass($model, 'FingerPrintID'); ?>">
		<label class="control-label required" for="CalendarPersonal_FingerPrintID">Nama Pegawai<span class="required">*</span></label>
		<div class="input">
			<?php echo $form->dropDownList($model,'FingerPrintID',$arr_pegawai); ?>
			<?php echo $form->error($model,'FingerPrintID'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'PersonalCalendarDate'); ?>">
		<label class="control-label required" for="CalendarPersonal_PersonalCalendarDate">Tanggal<span class="required">*</span></label>
		<div class="input">
			<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array(
					'model'=>$model, 
					'attribute'=>'PersonalCalendarDate',
					'language'=>'id',
					'options'=>array('dateFormat'=>'yy-mm-dd','changeMonth'=>true),
					'htmlOptions'=>array('style'=>'width:70px; text-align:center;')
					)); ?> s.d 
			<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array(
					'name'=>'PersonalCalendarDate',
					'language'=>'id',
					'options'=>array('dateFormat'=>'yy-mm-dd','changeMonth'=>true),
					'htmlOptions'=>array('style'=>'width:70px; text-align:center;')
					)); ?>
			<?php echo $form->error($model,'PersonalCalendarDate'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'PersonalCalendarStatus'); ?>">
		<label class="control-label required" for="CalendarPersonal_PersonalCalendarStatus">Status Absensi<span class="required">*</span></label>
		<div class="input">
			<?php echo $form->dropDownList($model,'PersonalCalendarStatus',$arr_status); ?>
			<?php echo $form->error($model,'PersonalCalendarStatus'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'PersonalCalendarReason'); ?>">
		<label class="control-label required" for="CalendarPersonal_PersonalCalendarReason">Keterangan</label>
		<div class="input">
			<?php echo $form->textField($model,'PersonalCalendarReason',array('size'=>60,'maxlength'=>100)); ?>
			<?php echo $form->error($model,'PersonalCalendarReason'); ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label"></label>
		<div class="input">
			<?php echo BHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?> 
			<?php echo CHtml::link('Cancel', array('absen'), array('class'=>'btn')); ?>
		</div>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<?php 
if(!$model->isNewRecord) 
	echo CHtml::link('<i class="g g-trash"></i> Delete','#', array('submit'=>array('delete', 'id'=>$model->id), 'confirm' => 'Are you sure?', 'csrf'=>true));