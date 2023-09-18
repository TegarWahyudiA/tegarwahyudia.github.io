<?php
$this->pageCaption='Login';
$this->pageTitle=Yii::app()->name . ' - ' . $this->pageCaption;
$this->pageDescription="You've been here before, haven't you?";
$this->breadcrumbs=array(
	'Login',
);
?>

<div class="form">
<?php $form=$this->beginWidget('BActiveForm', array(
	'id'=>'login-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); 
?>
	<div class="<?php echo $form->fieldClass($model, 'username'); ?>">
		<?php echo $form->labelEx($model,'username'); ?>
		<div class="controls">
			<?php echo $form->textField($model,'username'); ?>
			<?php echo $form->error($model,'username'); ?>
		</div>
	</div>

	<div class="<?php echo $form->fieldClass($model, 'password'); ?>">
		<?php echo $form->labelEx($model,'password'); ?>
		<div class="controls">
			<?php echo $form->passwordField($model,'password'); ?>
			<?php echo $form->error($model,'password'); ?>
		</div>
	</div>
<!--
	<div class="<?php echo $form->fieldClass($model, 'rememberMe'); ?>">
		<div class="controls">
			<?php echo $form->checkBox($model,'rememberMe'); ?>
			<?php echo $form->error($model,'rememberMe'); ?>
		</div>
	</div>
-->
	<div class="form-actions">
		<?php echo BHtml::submitButton('Login'); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->

<?php 
if(is_file('images/banner.jpg')) echo CHtml::image('images/banner.jpg','',array('style'=>'margin-bottom:-20px'));
Yii::app()->clientScript->registerScript('','
	$("#LoginForm_username").focus();
',CClientScript::POS_END);
?>