<?php
/**
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<?php
echo "<?php\n";
$label=$this->class2name($this->modelClass);
echo "\$this->pageCaption='Manage $label';
\$this->pageTitle=\$this->pageCaption;
\$this->pageDescription=CHtml::link('<i class=\"icon icon-plus-sign\"></i>',array('create'),array('title'=>'New $label'));
\$this->breadcrumbs=array(
	'$label',
);\n";
?>

$this->widget('TbGridView', array(
	'id'=>'<?php echo $this->class2id($this->modelClass); ?>-grid',
	'dataProvider'=>$model->search(),
	'cssFile'=>Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('ext.bootstrap-theme.widgets.assets')).'/gridview/styles.css',
	'itemsCssClass'=>'table',
	'template'=>'{items} {pager}',
	'columns'=>array(
		array('header'=>'#','htmlOptions'=>array('style'=>'width:20px'),'value'=>'$this->grid->dataProvider->getPagination()->getOffset()+$row+1'),
<?php
$count=0;
foreach($this->tableSchema->columns as $column)
{
	if(++$count==7)
		echo "\t\t/*\n";
	echo "\t\t'".$column->name."',\n";
}
if($count>=7)
	echo "\t\t*/\n";
?>
		array('type'=>'raw','htmlOptions'=>array('style'=>'width:40px'),'value'=>function($data){ 
			return CHtml::link('<i class="icon icon-search" title="View"></i>',array('view','id'=>$data->id)).' '. CHtml::link('<i class="icon icon-pencil" title="Update"></i>',array('update','id'=>$data->id));
		}),
	),
)); ?>
