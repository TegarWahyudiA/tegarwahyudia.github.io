<?php
$this->pageCaption='Alokasi Target Pegawai';
$this->pageTitle=$this->pageCaption;
$this->pageDescription=$model->nama_kegiatan;
$this->breadcrumbs=array(
	'Daftar Kegiatan'=>array('/kegiatan'),
	$model->unitkerja->unitkerja=>array('unitkerja','id'=>$model->id_unitkerja),
	$model->nama_kegiatan=>array('view','id'=>$model->id),
);

$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'baseScriptUrl'=>false,
	'cssFile'=>false,
	'attributes'=>array(
		'jadwal',
		'target_satuan',
		'keterangan',
	),
)); 
//				'order'=>'case when id_eselon=0 then 9 else id_eselon end,id_unitkerja, nama_pegawai',
					//'nama_pegawai',

$filter = $mitra?' and id_unitkerja=9288 and id_wilayah='.Yii::app()->user->id_wilayah :' and id_unitkerja LIKE \''.substr(Yii::app()->user->id_unitkerja,0,Yii::app()->user->id_eselon - 1).'%\' and id_unitkerja<=9287';

$arr_pegawai = array();
foreach(MasterPegawai::model()->findAll(array('condition'=>'is_aktif=1 AND id_wilayah=\''.Yii::app()->user->id_wilayah.'\' '.$filter,'order'=>'case when id_eselon=0 then 9 else id_eselon end,id_unitkerja, nama_pegawai ')) as $pegawai){
	$arr_pegawai[$pegawai->id]['nama_pegawai'] = $pegawai->nama_pegawai;
	$arr_pegawai[$pegawai->id]['jabatan'] = $pegawai->jabatan;
}

foreach(TabelTargetPegawai::model()->findAll(array('with'=>'pegawai','condition'=>'id_kegiatan='.$model->id.$filter)) as $target){
	$arr_pegawai[$target->id_pegawai]['jml_target'] = $target->jml_target;
//	$arr_pegawai[$target->id_pegawai]['jml_realisasi'] = $target->jml_realisasi;
	$arr_pegawai[$target->id_pegawai]['persen_kualitas'] = $target->persen_kualitas;
}

echo '<br><table class="table">
<thead><tr><th style="width:20px">#</th><th>Nama Pegawai</th><th>Jabatan</th><th style="width:70px">Target</th><th style="width:70px">% Kualitas</th><th>Keterangan</th></tr></thead>
<tbody>';
$i=1;
foreach($arr_pegawai as $id=>$data){
	echo '<tr key='.$id.'><td>'.($i++).'</td>
	<td>'.$data['nama_pegawai'].'</td>
	<td>'.$data['jabatan'].'</td>
	<td class="edit" col="Target"><span>'.(isset($data['jml_target'])?$data['jml_target']:'-').'</span><input id="t_target"></td>
	<td><span>'.(isset($data['persen_kualitas'])?$data['persen_kualitas']:'-').'</span></td>
	<td>'.$data->keterangan.'</td>
	</tr>';
}

echo '</tbody></table>';

echo CHtml::link('Selesai',array('view','id'=>$model->id),array('class'=>'btn btn-primary'));
if(!$mitra)
	echo CHtml::link('Mitra',array('alokasi','id'=>$model->id,'target'=>'mitra'),array('class'=>'btn','style'=>'float:right'));
else
	echo CHtml::link('Pegawai',array('alokasi','id'=>$model->id,'target'=>'pegawai'),array('class'=>'btn','style'=>'float:right'));

Yii::app()->clientScript->registerScript('admin-edit','
	$("td input").css("width","35px");
	$("input#t_target").css("display","none");
	$("input#t_kualitas").css("display","none");

	$("td.edit")
		.css("cursor","pointer")
		.hover(function(){
			$(this).attr("title","Edit "+$(this).attr("col"));
			if($(this).find("input").css("display")!="block") $(this).append(" <i class=\"g g-pencil\"></i>");
		}, function(){
			$(this).find("i.g").remove();
		})
		.click(function(){
			$(this).css("padding","3px 0 0 7px");
			$(this).find("span").css("display","none");
			$(this).find("input").css("display","block").css("margin","0").focus();
			$(this).find("input").attr("title","Untuk update data, tekan ENTER.\nUntuk membatalkan, kosongkan isian");
			$(this).find("i.g").remove();
		});

	$("input#t_target").blur(function(){
		var id_pegawai = $(this).closest("tr").attr("key");
		var jml_target = $(this).val();
		var t_target = $(this);
		if(jml_target!=""){
			$.post("'.Yii::app()->createUrl('/kegiatan/alokasi_set',array('id'=>$model->id)).'",
				{id_pegawai:id_pegawai,jml_target:jml_target},
				function(data){
					if(data==jml_target){
						t_target.css("display","none");
						if(jml_target=="0") data="-";
						t_target.parent().css("padding-top","8px").find("span").text(data).css("display","block");
					} else 
						alert(data);								
				});
		} else {
			t_target.css("display","none");
			t_target.parent().css("padding-top","8px").find("span").css("display","block");
		}
	}).keyup(function(e){
		if(e.keyCode==27){
				t_target.css("display","none");
				t_target.parent().css("padding-top","8px").find("span").css("display","block");
		} else if(e.keyCode==13){
			$(this).blur();
		}
	});
');	