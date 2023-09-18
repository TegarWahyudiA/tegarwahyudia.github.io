<?php
$this->pageTitle='Perubahan Jam Datang dan Pulang';
$this->pageCaption=$this->pageTitle;
$this->pageDescription='';
$this->breadcrumbs=array(
	'Administrator',
	'Presensi',
);

$this->widget('TbMenu', array(
	'type'=>'tabs',
	'items'=>array(
		array('label'=>'Presensi Harian','url'=>array('index')),
		array('label'=>'Proses','url'=>array('proses')),
		array('label'=>'Rekap Bulanan','url'=>array('rekap')),
		array('label'=>'Ketidakhadiran','url'=>array('absen')),
		array('label'=>'Perubahan Jam','url'=>'#','active'=>true),
		array('label'=>'Hari Libur','url'=>array('libur')),
	)
));

$arr_pegawai = array(''=>'-- Pilih --') + CHtml::listData(MasterPegawai::model()->findAll(array('order'=>'nama_pegawai', 'condition'=>'is_aktif=1')),'id_presensi','nama_pegawai');
$arr_jam_kerja = array(''=>'-- Pilih --','1'=>'Normal : 07.30 - 16.00/16.30','2'=>'Puasa : 08.00 - 15.00/15.30');
?>

<div class="form form-horizontal">

	<div class="control-group">
		<label class="control-label">Nama Pegawai</label>
		<div class="input">
			<?php echo CHtml::dropDownList('id','',$arr_pegawai); ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">Tanggal</label>
		<div class="input">
			<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array(
					'name'=>'tanggal',
					'language'=>'id',
					'options'=>array('dateFormat'=>'yy-mm-dd','changeMonth'=>true),
					'htmlOptions'=>array('style'=>'width:70px; text-align:center;')
					)); ?>
			<span id="keterangan" style="color:red; font-weight:bold;margin-left:5px;"></span>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">Jam Datang</label>
		<div class="input">
			<?php echo CHtml::textField('datang','',array('maxlength'=>5, 'style'=>'width:40px; text-align:center')); ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">Jam Pulang</label>
		<div class="input">
			<?php echo CHtml::textField('pulang','',array('maxlength'=>5, 'style'=>'width:40px; text-align:center')); ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">Jam Kerja</label>
		<div class="input">
			<?php echo CHtml::dropDownList('jam_kerja','',$arr_jam_kerja); ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label"></label>
		<div class="input">
			<?php echo BHtml::button('Update', array('id'=>'update','class'=>'btn btn-primary')); ?> 
			<?php echo CHtml::resetButton('Cancel', array('class'=>'btn')); ?>
		</div>
	</div>

</div>

<?php
Yii::app()->clientScript->registerScript('js','
	$("#id").change(function(){
		get_jam($("#id").val(),$("#tanggal").val());
	});

	$("#tanggal").change(function(){
		get_jam($("#id").val(),$("#tanggal").val());
	});

	$("#update").click(function(){
		$.post("'.Yii::app()->createUrl('presensi/jam_update').'",{
				id : $("#id").val(),
				tanggal : $("#tanggal").val(),
				datang : $("#datang").val(),
				pulang : $("#pulang").val(),
				jam_kerja : $("#jam_kerja").val(),
			}, function(data){
				alert(data);
			});
	});

	function get_jam(id,tanggal){
		$("#datang").val(""); $("#pulang").val(""); $("#keterangan").text("");
		$.post("'.Yii::app()->createUrl('presensi/jam_ajax').'",{id:id,tanggal:tanggal},function(data){
			$("#datang").val(data.datang);
			$("#pulang").val(data.pulang);
			$("#keterangan").text(data.keterangan);
		},"json");
	} 
');