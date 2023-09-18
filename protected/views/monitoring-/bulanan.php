<?php
$this->pageCaption='Kegiatan Bulanan';
$this->pageTitle=$this->pageCaption;

$prev_bulan = ($bulan==1)? array('bulanan','tahun'=>$tahun-1,'bulan'=>12) : array('bulanan','tahun'=>$tahun,'bulan'=>$bulan-1);
$next_bulan = ($bulan==12)? array('bulanan','tahun'=>$tahun+1,'bulan'=>1) : array('bulanan','tahun'=>$tahun,'bulan'=>$bulan+1);

$this->pageDescription = '<span style="float:right">'.CHtml::link('<i class="g g-chevron-left"></i>',$prev_bulan).' '.strftime('%B %Y',mktime(0,0,0,$bulan,1,$tahun)).' '.CHtml::link('<i class="g g-chevron-right"></i>',$next_bulan).'</span>';

$this->breadcrumbs=array(
	'Monitoring',
	'Kegiatan Bulanan'
);

$arr = array();
foreach($data as $target){
	if(!isset($arr[$target->id_pegawai][$target->kegiatan->id_unitkerja]))
		$arr[$target->id_pegawai][$target->kegiatan->id_unitkerja] = 1;
	else
		$arr[$target->id_pegawai][$target->kegiatan->id_unitkerja] = 	$arr[$target->id_pegawai][$target->kegiatan->id_unitkerja] + 1;
}

$arr_unitkerja = CHtml::listData(MasterUnitkerja::model()->findAll(array('order'=>'id','condition'=>'id LIKE \''.substr(Yii::app()->user->id_unitkerja,0,3).'%\' AND id<=9286')),'id','unitkerja');

$arr_pegawai = MasterPegawai::model()->findAll(array('order'=>'case when id_eselon<2 then 9 else id_eselon end, id_unitkerja, nama_pegawai','condition'=>'id_wilayah='.Yii::app()->user->id_wilayah.' AND id_unitkerja LIKE \''.substr(Yii::app()->user->id_unitkerja,0,3).'%\' AND is_aktif=1'));
?>

<table class="table">
<thead>
	<tr><th>#</th><th>Nama Pegawai</th>
	<?php foreach($arr_unitkerja as $unitkerja) echo '<th>'.$unitkerja.'</th>';?>
	<th>Jumlah</th></tr>
</thead>
<tbody>
	<?php 
		$i=1;
		foreach($arr_pegawai as $pegawai){
			echo '<tr><td>'.($i++).'</td><td>';
			if(($pegawai->id_eselon && $pegawai->id_eselon<Yii::app()->user->id_eselon) || ($pegawai->id_eselon==Yii::app()->user->id_eselon && $pegawai->id_unitkerja<>Yii::app()->user->id_unitkerja))
				echo $pegawai->nama_pegawai;
			else 
				echo CHtml::link($pegawai->nama_pegawai,array('pegawai','id'=>$pegawai->id,'tahun'=>$tahun,'bulan'=>$bulan));
			echo '</td>';

			$jml = 0;
			foreach(array_keys($arr_unitkerja) as $id_unitkerja){
				if(!isset($arr[$pegawai->id][$id_unitkerja]))
					echo '<td>-</td>';
				else{
					echo '<td>'.$arr[$pegawai->id][$id_unitkerja].'</td>';
					$jml += $arr[$pegawai->id][$id_unitkerja];
				}
			}
			echo '<td>'.$jml.'</td></tr>';
		}
	?>
</tbody>
</table>