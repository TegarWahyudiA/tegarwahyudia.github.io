<div class="wide form">

<?php $form=$this->beginWidget('BActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="clearfix">
		<?php echo $form->label($model,'id'); ?>
		<div class="input">
			<?php echo $form->textField($model,'id'); ?>
		</div>
	</div>

	<div class="clearfix">
		<?php echo $form->label($model,'FingerPrintID'); ?>
		<div class="input">
			<?php echo $form->textField($model,'FingerPrintID',array('size'=>30,'maxlength'=>30)); ?>
		</div>
	</div>

	<div class="clearfix">
		<?php echo $form->label($model,'PersonalCalendarDate'); ?>
		<div class="input">
			<?php echo $form->textField($model,'PersonalCalendarDate'); ?>
		</div>
	</div>

	<div class="clearfix">
		<?php echo $form->label($model,'TimeCome'); ?>
		<div class="input">
			<?php echo $form->textField($model,'TimeCome'); ?>
		</div>
	</div>

	<div class="clearfix">
		<?php echo $form->label($model,'TimeHome'); ?>
		<div class="input">
			<?php echo $form->textField($model,'TimeHome'); ?>
		</div>
	</div>

	<div class="clearfix">
		<?php echo $form->label($model,'LateIn'); ?>
		<div class="input">
			<?php echo $form->textField($model,'LateIn'); ?>
		</div>
	</div>

	<div class="clearfix">
		<?php echo $form->label($model,'EarlyOut'); ?>
		<div class="input">
			<?php echo $form->textField($model,'EarlyOut'); ?>
		</div>
	</div>

	<div class="clearfix">
		<?php echo $form->label($model,'PersonalCalendarReason'); ?>
		<div class="input">
			<?php echo $form->textField($model,'PersonalCalendarReason',array('size'=>60,'maxlength'=>100)); ?>
		</div>
	</div>

	<div class="actions">
		<?php echo BHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->