<?php
$this->pageCaption='Realisasi Kegiatan '.strftime('%B %Y',mktime(0,0,0,$bulan,1,$tahun));
$this->pageTitle=$this->pageCaption;

$prev_bulan = ($bulan==1)? array('realisasi','tahun'=>$tahun-1,'bulan'=>12) : array('realisasi','tahun'=>$tahun,'bulan'=>$bulan-1);
$next_bulan = ($bulan==12)? array('realisasi','tahun'=>$tahun+1,'bulan'=>1) : array('realisasi','tahun'=>$tahun,'bulan'=>$bulan+1);

$this->pageDescription = '<span style="float:right">'.CHtml::link('<i class="g g-chevron-left"></i>',$prev_bulan).' '.strftime('%B %Y',mktime(0,0,0,$bulan,1,$tahun)).' '.CHtml::link('<i class="g g-chevron-right"></i>',$next_bulan).'</span>';

$this->breadcrumbs=array(
	'Kegiatan Saya'=>array('/personal','tahun'=>$tahun,'bulan'=>$bulan),
	'Realisasi Kegiatan',
);

$arr_data = array();
foreach(TabelRealisasi::model()->findAll(array('order'=>'tgl','condition'=>'id_pegawai='.Yii::app()->user->id.' AND YEAR(tgl)='.$tahun.' AND MONTH(tgl)='.$bulan)) as $realisasi) {
	
	if(isset($realisasi->kegiatan))
		$arr_data[$realisasi->tgl][] = array(
			'id'=>$realisasi->id, 
			'kegiatan'=>$realisasi->kegiatan->nama_kegiatan, 
			'jml'=>$realisasi->jml_realisasi.' '.$realisasi->kegiatan->satuan->nama_satuan, 
			'keterangan'=>$realisasi->keterangan, 
			'validasi'=>$realisasi->acc_on? $realisasi->str_acc : null,
		); 
}

?>

<table class="table">
<thead>
	<tr><th style='width:80px'>Tanggal</th><th style='min-width:90px'>Jml Realisasi</th><th>Nama Kegiatan</th><th class="desktop">Keterangan</th></th><th style="width:20px"></th></tr>
</thead>
<tbody>
<?php 
for($tgl=1; $tgl<=date('t',mktime(0,0,0,$bulan));$tgl++){
	$mktime = mktime(0,0,0,$bulan,$tgl,$tahun);
	$libur = date('N',$mktime)>=6? ' class=libur' : '';
	$data = isset($arr_data[date('Y-m-d',$mktime)])?$arr_data[date('Y-m-d',$mktime)] : null;
	
	if(!$data)
		echo '<tr'.$libur.'><td>'.strftime('%d - %A',$mktime).'</td><td colspan=4>-</td></tr>'."\n";
	else {
		echo '<tr'.$libur.'><td rowspan='.count($data).'>'.strftime('%d - %A',$mktime).'</td>';
		foreach($arr_data[date('Y-m-d',$mktime)] as $key=>$data){
			if($key>0) echo '<tr>';
			echo '<td>'.$data['jml'].'</td><td>'.$data['kegiatan'].'</td><td class="desktop">'.$data['keterangan'].'</td><td>';
			echo $data['validasi']? CHtml::image('images/ok.png','V',array('title'=>'Validasi OK')) : ''; //CHtml::link('<i class="g g-pencil"></i>',array('update','id'=>$data['id']));
			echo '</td></tr>'."\n";
		}
	}
}
?>
</tbody></table>

<?php
Yii::app()->clientScript->registerCss('libur','tr.libur{color:red;}');
?>