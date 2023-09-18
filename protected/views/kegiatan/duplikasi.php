<?php
$this->pageCaption='Kegiatan '.$unitkerja->unitkerja;
$this->pageTitle=$this->pageCaption;
//$this->pageDescription=CHtml::link('<i class="icon icon-plus-sign"></i>',array('create'),array('title'=>'Create new Tabel Sub Kegiatans'));

$prev_bulan = ($bulan==1)? array('duplikasi','tahun'=>$tahun-1,'bulan'=>12) : array('duplikasi','tahun'=>$tahun,'bulan'=>$bulan-1);
$next_bulan = ($bulan==12)? array('duplikasi','tahun'=>$tahun+1,'bulan'=>1) : array('duplikasi','tahun'=>$tahun,'bulan'=>$bulan+1);

$this->pageDescription = '<span style="float:right">'.CHtml::link('<i class="g g-chevron-left"></i>',$prev_bulan).' '.strftime('%B %Y',mktime(0,0,0,$bulan,1,$tahun)).' '.CHtml::link('<i class="g g-chevron-right"></i>',$next_bulan).'</span>';

$this->breadcrumbs=array(
	'Duplikasi Kegiatan',
);

echo CHtml::beginForm();

$this->widget('TbGridView', array(
	'id'=>'tabel-sub-kegiatan-grid',
	'dataProvider'=>$dataProvider,
	'cssFile'=>Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('ext.bootstrap-theme.widgets.assets')).'/gridview/styles.css',
	'itemsCssClass'=>'table',
	'template'=>'{items}',
	'columns'=>array(
		array('class'=>'IndexColumn'),
		array('header'=>'Nama Kegiatan','value'=>function($data){return $data->nama_kegiatan;}),
		'jadwal',
		'target_satuan',
		array('header'=>'<input type="checkbox" id="pilih" val="none" title="All / None">','type'=>'raw','headerHtmlOptions'=>array('style'=>'width:20px'),'value'=>function($data){
			return CHtml::checkbox('key[]','',array('value'=>$data->id,'id'=>'_'.$data->id));
		}),
	),
)); 

if(count($dataProvider->getData())) {
?>
<div class='action'> Duplikasikan ke : <br>
	<select name='dst_bulan' style='width:100px'><option value=''>Bulan</option>
		<?php for($i=1; $i<=12; $i++){ echo '<option value="'.$i.'">'.strftime('%B',mktime(0,0,0,$i)).'</option>';} ?>
	</select> 
	<select name='dst_tahun' style='width:70px'><option value=''>Tahun</option>
		<?php for($i=date('Y');$i<=date('Y')+1;$i++){ echo '<option>'.$i.'</option>'; } ?>
	</select> 
	<?php echo CHtml::submitButton('Proses', array('class'=>'btn btn-primary','style'=>'margin-top:-10px'));?>
</div>

<?php
echo CHtml::endForm();
Yii::app()->clientScript->registerScript('pilih','
	$("#pilih").css("cursor","pointer")
		.click(function(){
			var val=$(this).attr("val");
			$("input[type=checkbox]").each(function(e){
				if(val=="all") $(this).prop("checked",false);
				else $(this).prop("checked",true);
			});
			if(val=="all") $(this).attr("val","none");
			else $(this).attr("val","all");
		});
');
}
