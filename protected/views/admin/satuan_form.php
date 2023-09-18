<div class="form">

<?php $form=$this->beginWidget('BActiveForm', array(
	'id'=>'master-satuan-form',
	'enableAjaxValidation'=>false,
)); ?>

	<div class="<?php echo $form->fieldClass($model, 'nama_satuan'); ?>">
		<?php echo $form->labelEx($model,'nama_satuan'); ?>
		<div class="input">
			<?php $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
				'model'=>$model,
				'attribute'=>'nama_satuan',
				'source'=>$this->createUrl('admin/satuan_auto'),
				'options'=>array(
					'minLength'=>'2',
					'showAnim'=>'fold',
					'select'=>'js:function(event,ui){
/*						$("#LoginForm_username").val(ui.item.id);
						$("#LoginForm_password").focus();
						alert(ui.item.id);
						$.post("'.Yii::app()->createUrl('admin/kredit_get').',{id:ui.item.id},function(data){alert(data);}");
*/					}'
				)
			)); ?>

			<?php echo $form->error($model,'nama_satuan'); ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label"></label>
		<div class="input">
			<?php echo BHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
			<?php echo CHtml::hiddenField('returnUrl',Yii::app()->request->urlReferrer);?>
		</div>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php 
if(!$model->isNewRecord)
	echo CHtml::link(BHtml::icon('trash').'Delete',"#", array("submit"=>array('delete', 'id'=>$model->id), 'confirm' => 'Are you sure?', 'csrf'=>true)); 
?>