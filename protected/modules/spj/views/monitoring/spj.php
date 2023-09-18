<?php
$this->pageCaption = 'Kelengkapan Dokumen';
$this->pageDescription = '';//'Rp. '.number_format($model->jumlah_kotor);

$this->breadcrumbs=array(
	'Monitoring SPJ'=>array('index'),
	'SPM Nomor: '.$model->spm->nomor.', '.$model->spm->str_tanggal=>array('spm','id'=>$model->id_spm),
	'SPJ '.$model->keperluan,
	'View SPJ',
);

$dataProvider = new CActiveDataProvider('SpjDokumen', array('criteria'=>array('with'=>'dokumen','condition'=>'id_spj='.$model->id, 'order'=>'dokumen.dokumen')));
$arr_status = array(0=>'-', 1=>'Sedang Diperiksa', 2=>'Perlu Perbaikan', 3=>'Pihak Ketiga', 4=>'Disetujui');

$this->widget('TbGridView', array(
	'dataProvider'=>$dataProvider,
	'template'=>'{items} {pager}',
	'rowHtmlOptionsExpression'=>'array("key"=>$data->id)',
	'columns'=>array(
		array('class'=>'IndexColumn'),
		array('name'=>'dokumen.dokumen','headerHtmlOptions'=>array('style'=>'width:150px')),
		array('header'=>'Status','type'=>'raw','htmlOptions'=>array('style'=>'padding:0; line-height:0px; vertical-align:middle;width:150px'),'value'=>function($data)use($arr_status){
			$title = $data->updated_on? 'Update '.strftime('%d %b',$data->updated_on) : '';
			return CHtml::dropDownList('status',$data->status,$arr_status,array('class'=>'status','style'=>'margin-bottom:0;width:150px','title'=>$title));
		}),
		'keterangan',
/*		array('header'=>'','type'=>'raw','headerHtmlOptions'=>array('style'=>'width:20px'),'value'=>function($data){
			return CHtml::link(BHtml::icon('trash'),'#',array('title'=>'Delete',"submit"=>array('dokumen_hapus', 'id'=>$data->id),'confirm'=>'Hapus dokumen ini?'));
		}),
*/	)
));
/*
$arr_dokumen = array(''=>'-- Tambah Dokumen --')+CHtml::listData(SpjMaster::model()->findAll(array('order'=>'dokumen')),'id','dokumen');
foreach($dataProvider->getData() as $dok){
	unset($arr_dokumen[$dok->id_dokumen]);
}
echo CHtml::dropDownList('dokumen','',$arr_dokumen,array('style'=>'margin-bottom:0')).' '.CHtml::link(BHtml::icon('plus-sign'),'#',array('id'=>'add','class'=>'btn btn-primary'));
*/
Yii::app()->clientScript->registerScript('js','
	$("select.status").each(function(){
		if($(this).val()==1) bg = "#fdef83"; 
		else if($(this).val()==2) bg = "#fdef83"; 
		else if($(this).val()==3) bg = "#f89406"; 
		else if($(this).val()==4) bg = "#90e090"; 
		else bg = "#fb8080";
		$(this).css("background",bg);

	});
');

echo CHtml::link(BHtml::icon('chevron-left').'Kembali',array('spm','id'=>$model->id_spm));