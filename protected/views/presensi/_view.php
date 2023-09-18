<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('FingerPrintID')); ?>:</b>
	<?php echo CHtml::encode($data->FingerPrintID); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('PersonalCalendarDate')); ?>:</b>
	<?php echo CHtml::encode($data->PersonalCalendarDate); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('TimeCome')); ?>:</b>
	<?php echo CHtml::encode($data->TimeCome); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('TimeHome')); ?>:</b>
	<?php echo CHtml::encode($data->TimeHome); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('LateIn')); ?>:</b>
	<?php echo CHtml::encode($data->LateIn); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('EarlyOut')); ?>:</b>
	<?php echo CHtml::encode($data->EarlyOut); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('PersonalCalendarReason')); ?>:</b>
	<?php echo CHtml::encode($data->PersonalCalendarReason); ?>
	<br />

	*/ ?>

</div>