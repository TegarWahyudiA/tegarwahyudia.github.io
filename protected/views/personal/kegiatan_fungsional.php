<?php
$tahun = date('Y',strtotime($model->kegiatan->tgl_mulai));
$bulan = date('m',strtotime($model->kegiatan->tgl_mulai));
$kredit = $model->kredit;

$this->pageTitle=$model->kegiatan->nama_kegiatan;
$this->pageCaption='Link dengan Angka Kredit';
$this->pageDescription='';
$this->breadcrumbs=array(
	'Indeks Kegiatan'=>array('unitkerja'),
	'Kegiatan Saya'=>array('index','tahun'=>$tahun,'bulan'=>$bulan),
	$model->kegiatan->nama_kegiatan=>array('kegiatan','id'=>$model->id_kegiatan)
);

$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'baseScriptUrl'=>false,
	'cssFile'=>false,
	'attributes'=>array(
		'kegiatan.nama_kegiatan',
		'kegiatan.target_satuan',
		'kegiatan.jadwal',

		array('label'=>'Fungsional','value'=>$kredit->tingkat),
		array('label'=>'Kode','value'=>$kredit->kode),
		array('label'=>'Uraian Singkat','value'=>$kredit->uraian_singkat),
		array('label'=>'Kegiatan','value'=>$kredit->kegiatan),
		array('label'=>'Bukti Fisik','value'=>$kredit->bukti_fisik),
		array('label'=>'Pelaksana Kegiatan','value'=>$kredit->pelaksana_kegiatan),
		array('label'=>'Angka Kredit','value'=>$kredit->{$model->fungsional->kolom_kredit}),
		array('label'=>'Satuan Hasil','type'=>'raw','value'=>$kredit->satuan_hasil),

		array('label'=>'','type'=>'raw','value'=>
			'<form class="form-horizontal" id="kegiatan-fungsional-form" action="'.Yii::app()->createUrl('personal/kegiatan_fungsional', array('id'=>$model->id_kegiatan)).'" method="post">'. 
			CHtml::activeHiddenField($model,'kode_kredit').
			CHtml::submitButton('Update',array('class'=>'btn','style'=>'margin-bottom:-15px')).
			'</form>'),

		array('label'=>CHtml::link(BHtml::icon('search').' Update','#',array('id'=>'link-update')), 'type'=>'raw', 
			'value'=>CHtml::textField('pencarian','',array('class'=>'span10','placeholder'=>'Ketik keyword, tekan tombol ENTER untuk mencari. Tekan tombol ESC untuk berhenti.','style'=>'display:none')).
			'<div id="result"></div>'),
	),
)); 

Yii::app()->clientScript->registerCss('css','
	.detail-view tbody tr {line-height:22px;}
	li:hover {cursor:pointer; color:orangered}
');
Yii::app()->clientScript->registerScript('js','
	$(".detail-view tbody tr").eq(11).hide();

	$("#link-update").click(function(){
		$(this).hide();
		$(".detail-view tbody tr").eq(11).show();
		$("#pencarian").show().focus();
		$("#result").show();
		return false;
	});

	$("#pencarian").keyup(function(e){
		if(e.keyCode==27){
			$("#link-update").show();
			$("#pencarian").hide();
			$("#result").hide();
			$(".detail-view tbody tr").eq(11).hide();
		} else if(e.keyCode==13){
			$("#result").text("");
			$.post("'.Yii::app()->createUrl('personal/kredit_search').'",{term:$(this).val(),level:'.substr($model->id_fungsional,0,1).'},function(data){
				$.each(data,function(i,e){
					$("#result").append("<li key="+e.id+">"+e.label+"</li>");
				});
				$("li").click(function(){
					$.post("'.Yii::app()->createUrl('personal/kredit_get').'",{id:$(this).attr("key"),level:'.$model->id_fungsional.'},function(data){
						var e = $(".detail-view tbody tr");
						e.eq(4).find("td").text(data.kode_perka);
						e.eq(5).find("td").text(data.uraian_singkat);
						e.eq(6).find("td").text(data.kegiatan);
						e.eq(7).find("td").text(data.bukti_fisik);
						e.eq(8).find("td").text(data.pelaksana_kegiatan);
						e.eq(9).find("td").text(data.angka_kredit);
						e.eq(10).find("td").text(data.satuan_hasil);
						$("#TabelTargetPegawai_kode_kredit").val(data.kode);
					},"json");
				});
			},"json");
		}
	});

	$("input[type=submit]").click(function(){
		if(confirm("Anda akan menyimpan perubahan kode kegiatan fungsional?"))
			$("#kegiatan-fungsional-form").submit();
	})
');

if(!$kredit)
	Yii::app()->clientScript->registerScript('new','$("#link-update").click();');
