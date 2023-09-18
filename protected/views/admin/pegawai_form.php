<div class="form">

<?php $form=$this->beginWidget('BActiveForm', array(
	'enableAjaxValidation'=>false,
)); 

$arr_wilayah = substr(Yii::app()->user->id_wilayah,-2)=='00'? array(''=>'-- Pilih --') + CHtml::listData(MasterWilayah::model()->findAll(),'id','kode_wilayah') : array('3300'=>'3300 - Jawa Tengah') + CHtml::listData(MasterWilayah::model()->findAllByAttributes(array('id'=>Yii::app()->user->id_wilayah)),'id','kode_wilayah');
$arr_unitkerja = array(''=>'-- n.a --') + CHtml::listData(MasterUnitkerja::model()->findAll(),'id','unitkerja');
$arr_golongan = array(''=>'-- n.a --') + CHtml::listData(MasterGolongan::model()->findAll(),'id','golongan');
$arr_eselon = array(''=>'-- n.a --'); for($i=4; $i>=2; $i--) $arr_eselon[$i]='Eselon '.$i;
$arr_fungsional = array(''=>'-- n.a --') + CHtml::listData(MasterFungsional::model()->findAll(),'id','fungsional');

?>

	<div class="<?php echo $form->fieldClass($model, 'nip'); ?>">
		<?php echo $form->labelEx($model,'nip'); ?>
		<div class="input">
			<?php echo $form->textField($model,'nip',array('size'=>9,'maxlength'=>9)); ?>
			<?php echo $form->error($model,'nip'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'nama_pegawai'); ?>">
		<?php echo $form->labelEx($model,'nama_pegawai'); ?>
		<div class="input">
			<?php echo $form->textField($model,'nama_pegawai',array('size'=>32,'maxlength'=>32)); ?>
			<?php echo $form->error($model,'nama_pegawai'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'nipbaru'); ?>">
		<?php echo $form->labelEx($model,'nipbaru'); ?>
		<div class="input">
			<?php echo $form->textField($model,'nipbaru',array('size'=>18,'maxlength'=>18)); ?>
			<?php echo $form->error($model,'nipbaru'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'id_golongan'); ?>">
		<?php echo $form->labelEx($model,'id_golongan'); ?>
		<div class="input">
			<?php echo $form->dropDownList($model,'id_golongan',$arr_golongan); ?>
			<?php echo $form->error($model,'id_golongan'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'id_wilayah'); ?>">
		<?php echo $form->labelEx($model,'id_wilayah'); ?>
		<div class="input">
			<?php echo $form->dropDownList($model,'id_wilayah',$arr_wilayah); ?>
			<?php echo $form->error($model,'id_wilayah'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'id_unitkerja'); ?>">
		<?php echo $form->labelEx($model,'id_unitkerja'); ?>
		<div class="input">
			<?php echo $form->dropDownList($model,'id_unitkerja',$arr_unitkerja); ?>
			<?php echo $form->error($model,'id_unitkerja'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'id_eselon'); ?>">
		<?php echo $form->labelEx($model,'id_eselon'); ?>
		<div class="input">
			<?php echo $form->dropDownList($model,'id_eselon',$arr_eselon); ?>
			<?php echo $form->error($model,'id_eselon'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'id_fungsional'); ?>">
		<?php echo $form->labelEx($model,'id_fungsional'); ?>
		<div class="input">
			<?php echo $form->dropDownList($model,'id_fungsional',$arr_fungsional); ?>
			<?php echo $form->error($model,'id_fungsional'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'id_presensi'); ?>">
		<?php echo $form->labelEx($model,'id_presensi'); ?>
		<div class="input">
			<?php echo $form->textField($model,'id_presensi'); ?>
			<?php echo $form->error($model,'id_presensi'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'username'); ?>">
		<?php echo $form->labelEx($model,'username'); ?>
		<div class="input">
			<?php echo $form->textField($model,'username'); ?>
			<?php echo $form->error($model,'username'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'is_admin'); ?>">
		<?php echo $form->labelEx($model,'is_admin'); ?>
		<div class="input">
			<?php echo $form->dropDownList($model,'is_admin',array(''=>'User','1'=>'Administrator')); ?>
			<?php echo $form->error($model,'is_admin'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'is_aktif'); ?>">
		<?php echo $form->labelEx($model,'is_aktif'); ?>
		<div class="input">
			<?php echo $form->dropDownList($model,'is_aktif',array(''=>'Tidak Aktif','1'=>'Aktif')); ?>
			<?php echo $form->error($model,'is_aktif'); ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label"></label>
		<div class="input">
			<?php echo BHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
			<?php if(!$model->isNewRecord) echo CHtml::link(BHtml::icon('lock').' Ganti Password','#',array('id'=>'link-ganti','style'=>'margin-left:50px'));?>
		</div>
	</div>

<?php $this->endWidget(); ?>
	
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
	</div><!-- form -->

<?php 
if(!$model->isNewRecord)
	echo CHtml::link(BHtml::icon('trash').' Delete',"#", array("submit"=>array('pegawai_delete', 'id'=>$model->id), 'confirm' => 'Are you sure?', 'csrf'=>true)); 
?>

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
 ?>