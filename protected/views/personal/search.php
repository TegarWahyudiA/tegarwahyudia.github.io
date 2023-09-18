<?php
$this->pageCaption='Pencarian Kegiatan';
$this->pageTitle=$this->pageCaption;
$this->pageDescription = '';

$this->breadcrumbs=array(
	'Pencarian Kegiatan',
);

if(!$q) Yii::app()->clientScript->registerScript('q','$("#q").focus()');

?>

<div class="form">
<?php $form=$this->beginWidget('BActiveForm');?>
	Kata Kunci : <?php echo CHtml::textField('q',$q,array('placeholder'=>'Minimal 3 huruf'));?> <input type="submit" value="Go" class="btn btn-primary"/>
<?php $this->endWidget(); ?>
</div>

<?php 
if($dataProvider){
	$this->widget('TbListView', array(
		'dataProvider'=>$dataProvider,
		'itemView'=>'search_item',
		'template'=>'{summary} {items}',
	));
}
