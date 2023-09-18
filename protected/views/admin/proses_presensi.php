<?php
$this->pageTitle='Proses Presensi';
$this->pageCaption=$this->pageTitle;
$this->pageDescription='';
$this->breadcrumbs=array(
	'Administrator',
	'Proses Presensi',
);
$arr_jam_kerja = array(''=>'-- Pilih --','1'=>'Normal : 07.30 - 16.00/16.30','2'=>'Puasa : 08.00 - 15.00/15.30');
if(!$model->tgl_mulai) $tgl_mulai=Yii::app()->db->createCommand()->select('max(PersonalCalendarDate)')->from('a_PersonalCalendar')->where('TimeCome is not null')->queryScalar();
if(!$model->tgl_selesai) $tgl_selesai=date('Y-m-d');

$this->widget('TbMenu', array(
    'type'=>'tabs', // '', 'tabs', 'pills' (or 'list')
    'stacked'=>false, // whether this is a stacked menu
    'items'=>array(
        array('label'=>'Tarik Absen', 'url'=>'#', 'active'=>true),
        array('label'=>'Ketidakhadiran', 'url'=>array('/presensi/index')),
        array('label'=>'Ubah Jam', 'url'=>array('/presensi/jam')),
    ),
)); 
?>

<div class="form">
<?php $form=$this->beginWidget('BActiveForm'); ?>

	<div class="control-group">
		<label class="control-label">Periode : </label>
		<div class="input">
			<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array(
					'name'=>'tgl_mulai',
					'value'=>$tgl_mulai,
					'options'=>array('dateFormat'=>'yy-mm-dd','changeMonth'=>true),
					'htmlOptions'=>array('style'=>'width:70px; text-align:center;')
					)); ?> - 
			<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array(
					'name'=>'tgl_selesai',
					'value'=>$tgl_selesai,
					'options'=>array('dateFormat'=>'yy-mm-dd','changeMonth'=>true),
					'htmlOptions'=>array('style'=>'width:70px; text-align:center;')
					)); ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">Jam Kerja : </label>
		<div class="input">
			<?php echo CHtml::dropDownList('jam_kerja',$jam_kerja,$arr_jam_kerja);?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label"></label>
		<div class="input">
			<?php echo BHtml::submitButton('Proses'); ?>
		</div>
	</div>

<?php $this->endWidget(); ?>
</div>

<?php if($msg){
	echo '<pre>';
	echo $msg;
	echo '</pre>';
}