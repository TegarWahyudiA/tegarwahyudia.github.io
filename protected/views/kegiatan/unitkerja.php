<?php
$this->pageTitle=strftime('%B %Y',mktime(0,0,0,$bulan,1,$tahun));

$prev_bulan = ($bulan==1)? array('unitkerja','id'=>$model->id,'tahun'=>$tahun-1,'bulan'=>12) : array('unitkerja','id'=>$model->id,'tahun'=>$tahun,'bulan'=>$bulan-1);
$next_bulan = ($bulan==12)? array('unitkerja','id'=>$model->id,'tahun'=>$tahun+1,'bulan'=>1) : array('unitkerja','id'=>$model->id,'tahun'=>$tahun,'bulan'=>$bulan+1);

$this->pageDescription = '<span style="float:right">'.CHtml::link(BHtml::icon('chevron-left'),$prev_bulan).' '.strftime('%B %Y',mktime(0,0,0,$bulan,1,$tahun)).' '.CHtml::link(BHtml::icon('chevron-right'),$next_bulan).'</span>';

$this->breadcrumbs=array(
	'Daftar Kegiatan'=>array('/kegiatan'),//,'tahun'=>$tahun,'bulan'=>$bulan),
	substr($model->id,-1)=='0'?'Kepala '.$model->unitkerja : $model->unitkerja,
//	$this->pageTitle,
);

$arr_persen = array();
$this->widget('TbGridView', array(
	'id'=>'tabel-sub-kegiatan-grid',
	'dataProvider'=>$dataProvider,
	'cssFile'=>Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('ext.bootstrap-theme.widgets.assets')).'/gridview/styles.css',
	'itemsCssClass'=>'table',
	'template'=>'{items} {pager}',
	'border'=>($excel? 1 : 0),
	//'htmlOptions'=>array('border'=>$excel?1:0),
	'columns'=>array(
		array('class'=>'IndexColumn'),
		array('header'=>'Nama Kegiatan','type'=>'raw','value'=>function($data)use($excel){
			return $excel? $data->nama_kegiatan : 
				($data->id_pegawai_usulan? BHtml::icon('question-sign', array('title'=>'Diusulkan oleh : '.$data->pegawai_usulan->nama_pegawai,'style'=>'color:'.($data->id_flag==1?'#bd362f':'#0088cc'))) : '').CHtml::link($data->nama_kegiatan,array('view','id'=>$data->id));
		}),
		array('name'=>'jadwal','headerHtmlOptions'=>array('class'=>'desktop','style'=>'width:80px'),'htmlOptions'=>array('class'=>'desktop')),
		array('name'=>'target_satuan','headerHtmlOptions'=>array('style'=>'max-width:150px')),
//		array('name'=>'jml_realisasi','header'=>'Realisasi','headerHtmlOptions'=>array('class'=>'desktop'),'htmlOptions'=>array('class'=>'desktop')),
		array('header'=>'Progress','type'=>'raw','headerHtmlOptions'=>array('style'=>'width:100px'), 'htmlOptions'=>array('style'=>'padding:0; line-height:0px; vertical-align:middle;'),'value'=>function($data)use($excel){
			$persen = number_format($data->jml_realisasi/$data->jml_target*100,0);
			return $excel? $persen.' %' : Controller::createWidget('TbProgress',array('percent'=>$persen))->run();
		}),
		array('name'=>'Nilai & ALokasi','visible'=>!$excel, 'type'=>'raw','headerHtmlOptions'=>array('class'=>'desktop','style'=>'width:100px'), 'htmlOptions'=>array('class'=>'desktop','style'=>'text-align:center'),'value'=>function($data){
			if($data->is_ckp==1){
				if($data->penilaian==count($data->pegawai) && count($data->pegawai))
					$return = CHtml::image('images/ok.png','Lengkap',array('title'=>'Penilaian Lengkap'));
				elseif($data->penilaian>0)
					$return = CHtml::image('images/warning.png','Kurang',array('title'=>'Belum Lengkap'));
				else
					$return = CHtml::image('images/error.png','Belum',array('title'=>'Belum Dinilai'));
			} else
				$return = '--';

			$return .= ' &nbsp; ';

			if($data->jml_target==$data->child_target)
				$return.= CHtml::image('images/ok.png','Lengkap',array('title'=>'Penilaian Lengkap'));
			elseif($data->child_target>0)
				$return.= CHtml::image('images/warning.png','Kurang',array('title'=>'Belum Lengkap'));
			else
				$return.= CHtml::image('images/error.png','Belum',array('title'=>'Belum Dinilai'));

			return $return;

		}),
	),
)); 

$persen_kuantitas = Yii::app()->db->createCommand("SELECT persen_kuantitas FROM v_persen_unitkerja WHERE id_unitkerja=".$model->id.' AND bulan='.$bulan.' AND tahun='.date('Y'))->queryScalar();
$this->pageCaption=	substr($model->id,-1)=='0'?'Kepala '.$model->unitkerja : $model->unitkerja;
$this->pageCaption.=' <small>Progress : '.number_format($persen_kuantitas,2).'%</small>';

if(Yii::app()->user->isKasi && !$excel){		// minimal kasi
	echo CHtml::link(BHtml::icon('plus-sign').'Add Kegiatan',array('add'));
	echo CHtml::link(BHtml::icon('download').'Export ke Excel',array('unitkerja','id'=>$model->id,'bulan'=>$bulan,'excel'=>'download'),array('class'=>'pull-right'));
}
?>

