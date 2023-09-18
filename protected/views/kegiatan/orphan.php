<?php
$this->pageCaption='Orphan Records';
$this->pageTitle=$this->pageCaption;
$this->pageDescription='';

$this->breadcrumbs=array(
	'Orphan Records',
);

if(!count($realisasi->getData()) && ($mingguan && !count($mingguan->getData())) && !count($target->getData())) {
	echo 'No records found';
} else {
	echo CHtml::beginForm();

	if(count($realisasi->getData())) {
		$this->widget('TbGridView', array(
			'id'=>'tabel-realisasi',
			'dataProvider'=>$realisasi,
			'cssFile'=>Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('ext.bootstrap-theme.widgets.assets')).'/gridview/styles.css',
			'itemsCssClass'=>'table',
			'template'=>'{items}',
			'columns'=>array(
				array('class'=>'IndexColumn'),
				array('name'=>'pegawai.nama_pegawai','headerHtmlOptions'=>array('style'=>'width:170px')),
				array('header'=>'Realisasi','value'=>function($data){return $data->jml_realisasi;},'headerHtmlOptions'=>array('style'=>'width:80px')),
				array('name'=>'str_tgl','headerHtmlOptions'=>array('style'=>'width:80px')),
				array('header'=>'Keterangan','value'=>function($data){return $data->keterangan;}),
				array('header'=>'Kegiatan','value'=>function($data){return $data->id_kegiatan;},'headerHtmlOptions'=>array('style'=>'width:60px')),
				array('header'=>'<input type="checkbox" id="pilih_r" val="none" title="All / None">','type'=>'raw','headerHtmlOptions'=>array('style'=>'width:20px'),'value'=>function($data){
					return CHtml::checkbox('r[]','',array('class'=>'r','value'=>$data->id,'id'=>'r_'.$data->id));
				}),
			),
		)); 
		echo CHtml::submitButton('Delete Realisasi', array('class'=>'btn btn-primary','style'=>'margin:-15px 0 20px;float:right'));
	}

	if(Yii::app()->params['mingguan'] && $mingguan && count($mingguan->getData())) {
		$this->widget('TbGridView', array(
			'id'=>'tabel-mingguan',
			'dataProvider'=>$mingguan,
			'cssFile'=>Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('ext.bootstrap-theme.widgets.assets')).'/gridview/styles.css',
			'itemsCssClass'=>'table',
			'template'=>'{items}',
			'columns'=>array(
				array('class'=>'IndexColumn'),
				array('name'=>'pegawai.nama_pegawai','headerHtmlOptions'=>array('style'=>'width:170px')),
				array('header'=>'Minggu Ke','value'=>function($data){return $data->mingguke;},'headerHtmlOptions'=>array('style'=>'width:100px')),
				array('name'=>'target_satuan','headerHtmlOptions'=>array('style'=>'width:100px')),
				array('header'=>'Keterangan','value'=>function($data){return $data->keterangan;}),
				array('header'=>'Kegiatan','value'=>function($data){return $data->id_kegiatan;},'headerHtmlOptions'=>array('style'=>'width:60px')),
				array('header'=>'<input type="checkbox" id="pilih_m" val="none" title="All / None">','type'=>'raw','headerHtmlOptions'=>array('style'=>'width:20px'),'value'=>function($data){
					return CHtml::checkbox('m[]','',array('class'=>'m','value'=>$data->id,'id'=>'m_'.$data->id));
				}),
			),
		)); 
		echo CHtml::submitButton('Delete Mingguan', array('class'=>'btn btn-primary','style'=>'margin:-15px 0 20px;float:right'));
	}

	if(count($target->getData())){
		$this->widget('TbGridView', array(
			'id'=>'tabel-target',
			'dataProvider'=>$target,
			'cssFile'=>Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('ext.bootstrap-theme.widgets.assets')).'/gridview/styles.css',
			'itemsCssClass'=>'table',
			'template'=>'{items}',
			'columns'=>array(
				array('class'=>'IndexColumn'),
				array('name'=>'pegawai.nama_pegawai','headerHtmlOptions'=>array('style'=>'width:170px')),
				array('name'=>'target_satuan','headerHtmlOptions'=>array('style'=>'width:100px')),
				array('header'=>'Keterangan','value'=>function($data){return $data->keterangan;}),
				array('header'=>'Kegiatan','value'=>function($data){return $data->id_kegiatan;},'headerHtmlOptions'=>array('style'=>'width:60px')),
				array('header'=>'<input type="checkbox" id="pilih_t" val="none" title="All / None">','type'=>'raw','headerHtmlOptions'=>array('style'=>'width:20px'),'value'=>function($data){
					return CHtml::checkbox('t[]','',array('class'=>'t','value'=>$data->id,'id'=>'t_'.$data->id));
				}),
			),
		)); 
		echo CHtml::submitButton('Delete Target', array('class'=>'btn btn-primary','style'=>'margin:-15px 0 20px;float:right'));
	}

	echo CHtml::endForm();
	Yii::app()->clientScript->registerScript('pilih','
		$("#pilih_r").css("cursor","pointer")
			.click(function(){
				var val=$(this).attr("val");
				$("input[type=checkbox].r").each(function(e){
					if(val=="all") $(this).prop("checked",false);
					else $(this).prop("checked",true);
				});
				if(val=="all") $(this).attr("val","none");
				else $(this).attr("val","all");
			});
		$("#pilih_m").css("cursor","pointer")
			.click(function(){
				var val=$(this).attr("val");
				$("input[type=checkbox].m").each(function(e){
					if(val=="all") $(this).prop("checked",false);
					else $(this).prop("checked",true);
				});
				if(val=="all") $(this).attr("val","none");
				else $(this).attr("val","all");
			});
		$("#pilih_t").css("cursor","pointer")
			.click(function(){
				var val=$(this).attr("val");
				$("input[type=checkbox].t").each(function(e){
					if(val=="all") $(this).prop("checked",false);
					else $(this).prop("checked",true);
				});
				if(val=="all") $(this).attr("val","none");
				else $(this).attr("val","all");
			});

		$("form").submit(function(){
			return confirm("Are you sure?");
		});

		$(".summary").attr("style","float:left;font-size:small;font-style:italic;");
	');
}