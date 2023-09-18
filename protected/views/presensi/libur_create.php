<?php
$this->pageCaption='Tambah Hari Libur';
$this->pageTitle=Yii::app()->name . ' - ' . $this->pageCaption;
$this->pageDescription='';
$this->breadcrumbs=array(
	'Administrator',
	'Presensi',
	'Hari Libur'=>array('libur')
);
$this->widget('TbMenu', array(
	'type'=>'tabs',
	'items'=>array(
		array('label'=>'Presensi Harian','url'=>array('index')),
		array('label'=>'Proses','url'=>array('proses')),
		array('label'=>'Rekap Bulanan','url'=>array('rekap')),
		array('label'=>'Ketidakhadiran','url'=>array('absen')),
		array('label'=>'Perubahan Jam','url'=>array('jam')),
		array('label'=>'Hari Libur','url'=>array('libur'),'active'=>true),
	)
));

?>

<div class="form">

<?php $form=$this->beginWidget('BActiveForm', array('id'=>'personal-calendar-form', 'enableAjaxValidation'=>false )); ?>

	<div class="<?php echo $form->fieldClass($model, 'CalendarHolidayDate'); ?>">
		<?php echo $form->labelEx($model,'CalendarHolidayDate'); ?>
		<div class="input">
			<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array(
					'model'=>$model, 
					'attribute'=>'CalendarHolidayDate',
					'language'=>'id',
					'options'=>array('dateFormat'=>'yy-mm-dd','changeMonth'=>true),
					'htmlOptions'=>array('style'=>'width:70px; text-align:center;')
					)); ?>
			<?php echo $form->error($model,'CalendarHolidayDate'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'CalendarHolidayStatusDescription'); ?>">
		<?php echo $form->labelEx($model,'CalendarHolidayStatusDescription'); ?>
		<div class="input">
			<?php echo $form->textField($model,'CalendarHolidayStatusDescription',array('maxlength'=>35)); ?>
			<?php echo $form->error($model,'CalendarHolidayStatusDescription'); ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label"></label>
		<div class="input">
			<?php echo BHtml::submitButton('Create'); ?>
		</div>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
