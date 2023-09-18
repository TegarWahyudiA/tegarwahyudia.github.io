<?php
$this->pageCaption='Bulan '.strftime('%B %Y',mktime(0,0,0,$bulan,1,$tahun));
$this->pageTitle=$this->pageCaption;

$prev_bulan = ($bulan==1)? array('index','tahun'=>$tahun-1,'bulan'=>12) : array('index','tahun'=>$tahun,'bulan'=>$bulan-1);
$next_bulan = ($bulan==12)? array('index','tahun'=>$tahun+1,'bulan'=>1) : array('index','tahun'=>$tahun,'bulan'=>$bulan+1);
$this->pageDescription = '<span style="float:right">'.CHtml::link(BHtml::icon('chevron-left'),$prev_bulan).' '.strftime('%B %Y',mktime(0,0,0,$bulan,1,$tahun)).' '.CHtml::link(BHtml::icon('chevron-right'),$next_bulan).'</span>';

$this->breadcrumbs=array(
	'Progress Kegiatan'=>array('index','tahun'=>$tahun,'bulan'=>$bulan),
);
if(isset($unitkerja)) $this->breadcrumbs[] = MasterUnitkerja::model()->findByPk($unitkerja)->unitkerja;

if(!$unitkerja){
	if(Yii::app()->user->id_eselon==2)
		$filter = "id LIKE '%0' AND RIGHT(id,2)<80";
	elseif(Yii::app()->user->id_eselon==3)
		$filter = "id LIKE '".substr(Yii::app()->user->id_unitkerja,0,3)."%' AND RIGHT(id,1)<7";
	else
		$filter = "id LIKE '".substr(Yii::app()->user->id_unitkerja,0,3)."%' AND RIGHT(id,1)<7 AND RIGHT(id,1)>0";
} else {
	$filter = "id LIKE '".substr($unitkerja,0,3)."%' AND RIGHT(id,1)<7";
}

?>

<div class="row-fluid" style="text-align:center">
<?php 
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/jquery.easypiechart.css');
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/jquery.easypiechart.js');

$arr_persen = array();
foreach(MasterUnitkerja::model()->findAll(array('condition'=>$filter)) as $model){
	$arr_persen[] = $model->getPersen($tahun,$bulan);
?>
	<span style="margin:10px">
		<span id="seksi-<?php echo $model->id;?>" class="easychart" data-percent="<?php echo $model->getPersen($tahun,$bulan);?>">
			<span class="percent"><?php echo $model->getPersen($tahun,$bulan);?></span>
			<span class="title">
				<?php 
				if(Yii::app()->user->id_eselon==2){
					if(substr($model->id,-2)=='00')
						echo CHtml::link('Kepala '.$model->unitkerja,array('unitkerja','id'=>$model->id,'bulan'=>$bulan,'tahun'=>$tahun));
					elseif($unitkerja && substr($model->id,-1)=='0')
						echo CHtml::link('Kepala '.$model->unitkerja,array('unitkerja','id'=>$model->id,'bulan'=>$bulan,'tahun'=>$tahun));
					else
						echo CHtml::link($model->unitkerja,array('index','unitkerja'=>$model->id,'bulan'=>$bulan,'tahun'=>$tahun));
				} elseif(Yii::app()->user->id_eselon==3) {
					if(substr($model->id,-1)=='0')
						echo CHtml::link('Kepala '.$model->unitkerja,array('unitkerja','id'=>$model->id,'bulan'=>$bulan,'tahun'=>$tahun));
					else
						echo CHtml::link($model->unitkerja,array('index','unitkerja'=>$model->id,'bulan'=>$bulan,'tahun'=>$tahun));
				} else
					echo CHtml::link($model->unitkerja,array('index','unitkerja'=>$model->id,'bulan'=>$bulan,'tahun'=>$tahun));

/*
				if(substr($unitkerja,-2)=='00'){
					if(substr($model->id,-2)=='00')
						echo CHtml::link('Kepala '.$model->unitkerja,array('unitkerja','id'=>$model->id,'bulan'=>$bulan,'tahun'=>$tahun));
					else
						echo CHtml::link($model->unitkerja,array('index','unitkerja'=>$model->id,'bulan'=>$bulan,'tahun'=>$tahun));
				} else
					echo CHtml::link(substr($model->id,-1)==0?'Kepala '.$model->unitkerja:$model->unitkerja,array('unitkerja','id'=>$model->id,'bulan'=>$bulan,'tahun'=>$tahun));
*/
				?>
			</span>
		</span>
	</span>
<?php 
	Yii::app()->clientScript->registerScript('s-'.$model->id,'$("#seksi-'.$model->id.'").easyPieChart();');
} 
$this->pageCaption.=' <small>Progress : '.number_format(array_sum($arr_persen)/count($arr_persen),2).'%</small>';
?>
</div>
<?php echo CHtml::link(BHtml::icon('plus-sign').'Add Kegiatan',array('/kegiatan/add')); ?>