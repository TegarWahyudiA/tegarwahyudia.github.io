<?php
$tahun = date('Y',strtotime($model->tgl_mulai));
$bulan = date('m',strtotime($model->tgl_mulai));

$this->pageTitle=$model->nama_kegiatan;
$this->pageCaption=(!$model->is_lock && Yii::app()->user->isKasi && Yii::app()->user->id_unitkerja==$model->id_unitkerja? CHtml::link(BHtml::icon('edit'),array('update','id'=>$model->id),array('title'=>'Update')).' ': '').$model->nama_kegiatan;
$this->pageDescription='<span style="float:right">Progress : '.number_format($model->jml_realisasi/$model->jml_target*100,1).'%</span>'; 

$this->breadcrumbs=array(
	'Daftar Kegiatan'=>array('/kegiatan','tahun'=>$tahun,'bulan'=>$bulan),
	$model->unitkerja->unitkerja=>array('/kegiatan/unitkerja','id'=>$model->id_unitkerja,'tahun'=>$tahun,'bulan'=>$bulan),
);

$detail = Yii::app()->user->isAdmin || (Yii::app()->user->id_eselon==2) || (Yii::app()->user->id_unitkerja==$model->id_unitkerja) || (Yii::app()->user->id_eselon==3 && substr($model->id_unitkerja,0,3)==substr(Yii::app()->user->id_unitkerja,0,3));
$edit = Yii::app()->user->id_eselon || Yii::app()->user->isAdmin;

$terampil = $model->kode_terampil? $model->getFungsional('1')->kredit : null;
$ahli = $model->kode_ahli? $model->getFungsional('2')->kredit : null;

$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'baseScriptUrl'=>false,
	'cssFile'=>false,
	'attributes'=>array(
		'jadwal',
		'target_satuan',
		array('visible'=>$detail,'label'=>'Dialokasikan','value'=>($model->child_target)),
		array('visible'=>$detail,'label'=>'Realisasi','type'=>'raw','value'=>(($model->jml_realisasi)?($model->jml_realisasi):'0').' &nbsp; '.($detail?CHtml::link(BHtml::icon('plus'),array('realisasi','id'=>$model->id),array('title'=>'Detail Realisasi','style'=>'font-size:smaller')):'')),
		'keterangan',
/*		array('label'=>'Statistisi Terampil','type'=>'raw','value'=>
			!$terampil? '-' : $terampil->kode_perka.' - '.$terampil->uraian_singkat.' '.$terampil->kegiatan
			),
		array('label'=>'Statistisi Ahli','type'=>'raw','value'=>
			!$ahli? '-' : $ahli->kode_perka.' - '.$ahli->uraian_singkat.' '.$ahli->kegiatan
			)
*/	),
)); ?>

<br>

<?php
$dataProvider = new CActiveDataProvider('TabelTargetPegawai',array(
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
	'columns'=>array(
		array('class'=>'IndexColumn'),
		array('header'=>'Nama Pegawai','type'=>'raw','value'=>function($data)use($detail){return $detail?CHtml::link($data->pegawai->nama_pegawai,array('view_pegawai','id'=>$data->id_kegiatan,'pegawai'=>$data->id_pegawai)) : $data->pegawai->nama_pegawai;}),
		array('header'=>'Target','headerHtmlOptions'=>array('style'=>'width:80px'),'value'=>function($data){return $data->jml_target;}),
		array('name'=>'jml_realisasi','header'=>'Realisasi','headerHtmlOptions'=>array('class'=>'desktop','style'=>'width:80px'),'htmlOptions'=>array('class'=>'desktop')),
		array('header'=>'Progress','headerHtmlOptions'=>array('style'=>'width:100px'),'type'=>'raw','htmlOptions'=>array('style'=>'padding:0; line-height:0px; vertical-align:middle;'),'value'=>function($data){
			$persen = (!$data->jml_realisasi)? '' :number_format($data->jml_realisasi/$data->jml_target*100,1);
			return Controller::createWidget('TbProgress',array('percent'=>$persen))->run();
		}),
		array('header'=>'Nilai & Alokasi','visible'=> $detail,'type'=>'raw','headerHtmlOptions'=>array('style'=>'width:100px'),'htmlOptions'=>array('style'=>'text-align:center'),'value'=>function($data){
			$nilai = $data->persen_kualitas? $data->persen_kualitas : '--';
			if(!$data->child_target)
				return $nilai.' &nbsp; '.CHtml::image('images/error.png','BELUM',array('title'=>'Belum mengisi target mingguan'));
			elseif($data->child_target < $data->jml_target)
				return $nilai.' &nbsp; '.CHtml::image('images/warning.png','KURANG',array('title'=>'Target mingguan belum lengkap ('.$data->child_target.')'));
			elseif($data->child_target == $data->jml_target)
				return $nilai.' &nbsp; '.CHtml::image('images/ok.png','LENGKAP',array('title'=>'Target mingguan lengkap'));
		}),
		'keterangan',
		array('visible'=>$detail && $edit && !$model->is_lock,'type'=>'raw','htmlOptions'=>array('style'=>'width:40px'),'value'=>function($data){ 
			if(!Yii::app()->user->isGuest && (Yii::app()->user->isAdmin || Yii::app()->user->id_unitkerja==$data->kegiatan->id_unitkerja)) 
				return CHtml::link(BHtml::icon('pencil'),array('alokasi_update','id'=>$data->id),array('title'=>'Update','style'=>'cursor:pointer'));
			else return;
		}),
	),
));


if($detail && $edit && !$model->is_lock){ 
	echo CHtml::link(BHtml::icon('list').'Alokasi Target Pegawai',array('alokasi','id'=>$model->id),array('title'=>'Manage Target Pegawai'));
	if($model->id_flag==1 && $model->id_unitkerja==Yii::app()->user->id_unitkerja)
		echo CHtml::link(BHtml::icon('check').'Konfirmasi',array('konfirmasi','id'=>$model->id),array('class'=>'btn btn-primary pull-right','confirm'=>'Konfirmasi kegiatan ini?'));
	elseif($model->id_pegawai_usulan && $model->id_unitkerja==Yii::app()->user->id_unitkerja)
		echo CHtml::link(BHtml::icon('lock').'Unlock',array('konfirmasi','id'=>$model->id,'unlock'=>'unlock'),array('class'=>'btn pull-right','confirm'=>'Unlock kegiatan ini?'));
}

