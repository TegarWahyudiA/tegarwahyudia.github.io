<?php
$this->pageTitle=$model->nama_kegiatan;
$this->pageCaption=(Yii::app()->user->isKasi && Yii::app()->user->id_unitkerja==$model->id_unitkerja? CHtml::link(BHtml::icon('edit'),array('update','id'=>$model->id),array('title'=>'Update')).' ': '').$model->nama_kegiatan;
$this->pageDescription='<span style="float:right">Progress : '.number_format($model->jml_realisasi/$model->jml_target*100,1).'%</span>'; 

$this->breadcrumbs=array(
	'SKP',
	$model->unitkerja->unitkerja=>array('index','unitkerja'=>$model->id_unitkerja,'tahun'=>$model->tahun),
	'Tahun '.$model->tahun,
);

$detail = Yii::app()->user->isAdmin || (Yii::app()->user->id_eselon==2) || (Yii::app()->user->id_unitkerja==$model->id_unitkerja) || (Yii::app()->user->id_eselon==3 && substr($model->id_unitkerja,0,3)==substr(Yii::app()->user->id_unitkerja,0,3));
$edit = Yii::app()->user->id_eselon || Yii::app()->user->isAdmin;
?>

<br>

<?php
$dataProvider = new CActiveDataProvider('SkpPegawai',array(
                    'pagination'=>array('pagesize'=>50),
                    'criteria'=>array(                          
                      'condition'=>'id_kegiatan='.$model->id,
                      'with'=>'pegawai',
                      'order'=>'case when id_eselon<2 then 9 else id_eselon end, id_unitkerja, nama_pegawai', 
                    )));

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'tabel-alokasi-grid',
	'dataProvider'=>$dataProvider,
	'cssFile'=>Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('ext.bootstrap-theme.widgets.assets')).'/gridview/styles.css',
	'itemsCssClass'=>'table',
	'template'=>'{items}',
	'rowHtmlOptionsExpression'=>'array("key"=>$data->id_pegawai)',
	'columns'=>array(
		array('class'=>'IndexColumn'),
		array('header'=>'Nama Pegawai','type'=>'raw','value'=>function($data)use($detail){$detail=false; return $detail?CHtml::link($data->pegawai->nama_pegawai,array('view_pegawai','id'=>$data->id_kegiatan,'pegawai'=>$data->id_pegawai)) : $data->pegawai->nama_pegawai;}),
		array('header'=>'Target','headerHtmlOptions'=>array('style'=>'width:80px'),'value'=>function($data){return $data->jml_target;}),
//		array('name'=>'jml_realisasi','header'=>'Realisasi','headerHtmlOptions'=>array('class'=>'desktop','style'=>'width:80px'),'htmlOptions'=>array('class'=>'desktop')),
		array('header'=>'Progress','headerHtmlOptions'=>array('style'=>'width:100px'),'type'=>'raw','htmlOptions'=>array('style'=>'padding:0; line-height:0px; vertical-align:middle;'),'value'=>function($data){
			$persen = (!$data->jml_realisasi)? '' :number_format($data->jml_realisasi/$data->jml_target*100,1);
			return Controller::createWidget('TbProgress',array('percent'=>$persen))->run();
		}),
		array('header'=>'Nilai','visible'=> $detail,'type'=>'raw','headerHtmlOptions'=>array('style'=>'width:40px; text-align: center;'),'htmlOptions'=>array('style'=>'text-align:center','class'=>'edit','col'=>'nilai'),'value'=>function($data){
			return '<span>'.($data->persen_kualitas? $data->persen_kualitas : '--').'</span><input id="t_nilai">';
		}),
//		'keterangan',
//		array('visible'=>$detail && $edit,'type'=>'raw','htmlOptions'=>array('style'=>'width:40px'),'value'=>function($data){ 
//			if((Yii::app()->user->isAdmin || Yii::app()->user->id_unitkerja==$data->kegiatan->id_unitkerja)) 
//				return CHtml::link(BHtml::icon('pencil'),array('alokasi_update','id'=>$data->id),array('title'=>'Update','style'=>'cursor:pointer'));
//			else return;
//		}),
	),
));


if($detail && $edit){ 
	echo CHtml::link(BHtml::icon('list').'Alokasi Target Pegawai',array('alokasi','id'=>$model->id),array('title'=>'Manage Target Pegawai'));
	if($model->id_flag==1 && $model->id_unitkerja==Yii::app()->user->id_unitkerja)
		echo CHtml::link(BHtml::icon('check').'Konfirmasi Kegiatan Ini',array('konfirmasi','id'=>$model->id),array('class'=>'btn btn-primary pull-right','confirm'=>'Konfirmasi kegiatan ini?'));

Yii::app()->clientScript->registerScript('js','
	$("td input").css("width","35px").css("text-align","center").attr("maxlength","3");
	$("input#t_nilai").css("display","none");

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

	$("input#t_nilai").blur(function(){
		var pegawai = $(this).closest("tr").attr("key");
		var nilai = $(this).val();
		var t_nilai = $(this);
		if(nilai!=""){
			$.post("'.Yii::app()->createUrl('skp/struktural/nilai_set',array('id'=>$model->id)).'",
				{pegawai:pegawai,nilai:nilai},
				function(data){
					if(data==nilai){
						t_nilai.css("display","none");
						if(nilai=="0") data="-";
						t_nilai.parent().css("padding-top","8px").find("span").text(data).css("display","block");
					} else 
						alert(data);								
				});
		} else {
			t_nilai.css("display","none");
			t_nilai.parent().css("padding-top","8px").find("span").css("display","block");
		}
	}).keyup(function(e){
		if(e.keyCode==27){
				t_nilai.css("display","none");
				t_nilai.parent().css("padding-top","8px").find("span").css("display","block");
		} else if(e.keyCode==13){
			$(this).blur();
		}
	});
');		
}


?>
<style>.page-header{margin-bottom:0px;}</style>