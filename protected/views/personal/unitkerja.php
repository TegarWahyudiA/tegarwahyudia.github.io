<?php 
$this->pageTitle='';
$this->pageCaption=$this->pageTitle;

$prev_bulan = ($bulan==1)? array('unitkerja','id'=>$id,'tahun'=>$tahun-1,'bulan'=>12) : array('unitkerja','id'=>$id,'tahun'=>$tahun,'bulan'=>$bulan-1);
$next_bulan = ($bulan==12)? array('unitkerja','id'=>$id,'tahun'=>$tahun+1,'bulan'=>1) : array('unitkerja','id'=>$id,'tahun'=>$tahun,'bulan'=>$bulan+1);

$this->pageDescription = !$id? '' : '<span style="float:right">'.CHtml::link('<i class="g g-chevron-left"></i>',$prev_bulan).' '.strftime('%B %Y',mktime(0,0,0,$bulan,1,$tahun)).' '.CHtml::link('<i class="g g-chevron-right"></i>',$next_bulan).'</span>';

$this->breadcrumbs=array(
	'Indeks Kegiatan'=>array('unitkerja','tahun'=>$tahun,'bulan'=>$bulan),
);

if(!$id){
	$this->breadcrumbs[] = 	MasterUnitkerja::model()->findByPk(substr(Yii::app()->user->id_unitkerja,0,3).'0')->unitkerja;
	
	$filter = "id LIKE '".substr(Yii::app()->user->id_unitkerja, 0,3)."%' AND right(id,1)>0 AND right(id,1)<7";
	foreach(MasterUnitkerja::model()->findAll(array('condition'=>$filter,'order'=>$id)) as $unitkerja){
		echo BHtml::icon('menu-right').' '.CHtml::link($unitkerja->unitkerja,array('unitkerja','id'=>$unitkerja->id)).'<br><br>';
	}
} else {
	$this->pageCaption = MasterUnitkerja::model()->findByPk($id)->unitkerja;
	$this->widget('TbGridView', array(
		'id'=>'tabel-sub-kegiatan-grid',
		'dataProvider'=>$dataProvider,
		'cssFile'=>Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('ext.bootstrap-theme.widgets.assets')).'/gridview/styles.css',
		'itemsCssClass'=>'table',
		'template'=>'{items} {pager}',
		'columns'=>array(
			array('class'=>'IndexColumn'),
			array('header'=>'Nama Kegiatan','type'=>'raw','value'=>function($data){return $data->nama_kegiatan;}),
			array('name'=>'target_satuan','headerHtmlOptions'=>array('style'=>'max-width:150px')),
			array('name'=>'jadwal','headerHtmlOptions'=>array('class'=>'desktop','style'=>'width:100px'),'htmlOptions'=>array('class'=>'desktop')),
			array('header'=>'Progress','type'=>'raw','headerHtmlOptions'=>array('style'=>'width:100px'), 'htmlOptions'=>array('style'=>'padding:0; line-height:0px; vertical-align:middle;'),'value'=>function($data)use($excel){
				$persen = number_format($data->jml_realisasi/$data->jml_target*100,0);
				return $excel? $persen.' %' : Controller::createWidget('TbProgress',array('percent'=>$persen))->run();
			}),
		),
	));
}