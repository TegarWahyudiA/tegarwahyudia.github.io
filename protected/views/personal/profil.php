<?php
$this->pageTitle='Profil Saya';
$this->pageCaption=$this->pageTitle;
$this->pageDescription='';

$this->breadcrumbs=array(
	'Profil Saya',
);

$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'baseScriptUrl'=>false,
	'cssFile'=>false,
	'attributes'=>array(
		'nip',
		'nama_pegawai',
		'nipbaru',
		'golongan.golongan',
		'wilayah.wilayah',
		'unitkerja.unitkerja',
		'id_eselon',
		array('name'=>'id_fungsional','value'=>$model->id_fungsional? $model->fungsional->fungsional : '-'),
		'id_presensi',
		array('name'=>'is_admin','value'=>$model->is_admin? 'Administrator' : 'User'),
		'username',
//		'last_login',
		array('label'=>'','type'=>'raw','value'=>CHtml::link(BHtml::icon('lock').' Ganti Password','#',array('id'=>'link-ganti'))),
	),
));
?>

<div class="form" id="div-password" style="display:none">
	<hr>
	<?php $form=$this->beginWidget('BActiveForm', array(
		'id'=>'form-password',
		'enableAjaxValidation'=>false,
	)); ?>

		<div class="control-group">
			<label class="control-label">Password Baru </label>
			<div class="input"> 
				<?php echo CHtml::passwordField('password','',array());?> 
			</div>
		</div>

		<div class="control-group">
			<label class="control-label">Konfirmasi Password </label>
			<div class="input"> 
				<?php echo CHtml::passwordField('konfirmasi','',array());?> 
				<span id='cocok' style='color:red'></span>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label"></label>
			<div class="input"> 
				<?php echo CHtml::button('Cancel',array('class'=>'btn','id'=>'cancel'));?> 
				<?php echo CHtml::submitButton('Ganti Password',array('class'=>'btn','id'=>'submit','disabled'=>true));?>
			</div>
		</div>
	
	<?php $this->endWidget(); ?>
</div>

<?php 
if($msg)
	echo '<pre>'.$msg.'</pre>';

Yii::app()->clientScript->registerCss('css','.detail-view tbody tr {line-height:22px;}');
Yii::app()->clientScript->registerScript('js','
	$("#konfirmasi").keyup(function(){
		if($(this).val()!=$("#password").val()){
			$("#cocok").text("Password tidak sama");
			$("#submit").attr("disabled",true).attr("class","btn");
		} else {
			$("#cocok").text("");
			$("#submit").attr("disabled",false).attr("class","btn btn-primary");
		}
	});

	$("#form-password").submit(function(){return confirm("Are you sure?");});
	$("#link-ganti").click(function(){$("#div-password").css("display","block");return false;});
	$("#cancel").click(function(){$("#div-password").css("display","none")});
');