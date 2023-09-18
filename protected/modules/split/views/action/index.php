<?php
$this->pageCaption = 'Arsip Split Database';
$this->pageDescription = CHtml::link(BHtml::icon('plus-sign'),array('split'),array('title'=>'Create new Split File'));

$this->breadcrumbs=array(
//	$this->module->id,
	'Split Database'
);
?>

<div class='list-view'>

<?php 
$dataPath = $this->module->basePath.'/data/';
$i = 1;
foreach(scandir($dataPath,1) as $file){
	if(substr($file,0,10)=='split_'.Yii::app()->user->id_wilayah){
		$bulan = mktime(0,0,0,substr($file,16,2),1,substr($file,11,4));
		$created = (int)substr($file,19,-3);
?>
		<div class="view">
			<div class="title"><?php echo ($i++).'. Bulan '.strftime('%B %Y',$bulan); ?></div>
			<div class="meta">
				<?php echo CHtml::link(BHtml::icon('download'),array('download','file'=>$file),array('title'=>'Download')); ?> &middot; 
				<?php echo strftime('%d %b %Y',$created); ?> &middot; <?php echo strftime('%H:%M:%S',$created);?> &middot; 
				<?php echo CHtml::link(BHtml::icon('erase'),'#',array('submit'=>array('delete','file'=>$file),'title'=>'Delete','style'=>'text-size:smaller;color:#ccc','confirm'=>'Are you sure?')); ?>
			</div>
		</div>
<?php
	}
}

Yii::app()->clientScript->registerCss('css','a:hover > i.g.g-erase{color:red}')
?>

</div>