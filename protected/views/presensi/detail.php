<?php
$this->pageCaption=$model->nama_pegawai;
$this->pageTitle=$this->pageCaption;


$prev_bulan = ($bulan==1)? array('detail','id'=>$model->id,'tahun'=>$tahun-1,'bulan'=>12) : array('detail','id'=>$model->id,'tahun'=>$tahun,'bulan'=>$bulan-1);
$next_bulan = ($bulan==12)? array('detail','id'=>$model->id,'tahun'=>$tahun+1,'bulan'=>1) : array('detail','id'=>$model->id,'tahun'=>$tahun,'bulan'=>$bulan+1);

$this->pageDescription = '<span style="float:right">'.CHtml::link('<i class="g g-chevron-left"></i>',$prev_bulan).' '.strftime('%B %Y',mktime(0,0,0,$bulan,1,$tahun)).' '.CHtml::link('<i class="g g-chevron-right"></i>',$next_bulan).'</span>';

$this->breadcrumbs=array(
	'Administrator',
//	'Presensi',//=>array('index'),
	'Presensi',
	'Rekap Bulanan'=>array('rekap','tahun'=>$tahun,'bulan'=>$bulan),
);

$this->widget('TbMenu', array(
	'type'=>'tabs',
	'items'=>array(
		array('label'=>'Presensi Harian','url'=>array('index')),
		array('label'=>'Proses','url'=>array('proses')),
		array('label'=>'Rekap Bulanan','url'=>array('rekap'),'active'=>true),
		array('label'=>'Ketidakhadiran','url'=>array('absen')),
		array('label'=>'Perubahan Jam','url'=>array('jam')),
		array('label'=>'Hari Libur','url'=>array('libur')),
	)
));

?>

<table class="table">
<thead>
	<tr><th style='width:100px'>Tanggal</th><th style='width:70px'>Datang</th><th style='width:70px'>Pulang</th><th>Keterangan</th></th></tr>
</thead>
<tbody>

<?php 
$tgl_1 = date('Y-m-d', mktime(0,0,0,$bulan,1,$tahun));
$tgl_2 = date('Y-m-t', mktime(0,0,0,$bulan,1,$tahun));


function tgldari($mulai, $selesai){
	$result = array();
	$dari = mktime(1,0,0,substr($mulai,5,2),substr($mulai,8,2),substr($mulai,0,4));
	$sampai = mktime(1,0,0,substr($selesai,5,2),substr($selesai,8,2),substr($selesai,0,4));
	if($sampai>=$dari){
		array_push($result, date('Y-m-d',$dari));
		while($dari<$sampai){
			$dari+=86400;
			array_push($result, date('Y-m-d', $dari));
		}
	}
	return $result;
}

$tgl_1 = date('Y-m-d', mktime(0,0,0,$bulan,1,$tahun));
$tgl_2 = date('Y-m-t', mktime(0,0,0,$bulan,1,$tahun));

$list=tgldari($tgl_1,$tgl_2);
$no=1;


$result = $db->query("select * from a_CalendarHoliday where CalendarHolidayStatusDescription<>'' AND CalendarHolidayDate>='".$tgl_1."' and CalendarHolidayDate<='".$tgl_2."' order by CalendarHolidayDate");
$arr_libur = array();
while ($row = $result->fetch()) {
	$arr_libur[date('Y-m-d',strtotime($row['CalendarHolidayDate']))] = $row['CalendarHolidayStatusDescription'];
}

$result = $db->query("select * from a_PersonalCalendar where FingerPrintID='".$model->id_presensi."' and PersonalCalendarDate>='".$tgl_1."' and PersonalCalendarDate<='".$tgl_2."' order by PersonalCalendarDate");
$arr_data = array();
	while ($row = $result->fetch()) {
	$arr_data[date('Y-m-d',strtotime($row['PersonalCalendarDate']))] = array (
		'tgl' => $row['PersonalCalendarDate'],
		'TimeCome' => $row['TimeCome'],
		'TimeHome' => $row['TimeHome'],
		'PersonalCalendarReason' => $row['PersonalCalendarReason'],
		'LateIn' => $row['LateIn'],
		'EarlyOut' => $row['EarlyOut'],
		);
} 

foreach($list as $x){
    $no++;
	$libur = (array_key_exists(date('Y-m-d', strtotime($x)), $arr_libur) || date('N ',strtotime($x))>=6)? 'class=libur':'';
	echo '<tr '.$libur.'>
	<td>'.date('d M Y ',strtotime($x)).'</td>';
	
	$data = $arr_data[$x];
	$dlibur = $arr_libur[$x];
			
	if(array_key_exists(date('Y-m-d', strtotime($x)), $arr_data)){
		echo '<td>'.$data['TimeCome'].'</td>
		<td>'.$data['TimeHome'].'</td>
		<td>'.get_tl($data['LateIn']).get_psw($data['EarlyOut']).' '.$data['PersonalCalendarReason'].' '.$dlibur.'</td></tr>';
	}
	elseif(array_key_exists(date('Y-m-d', strtotime($x)), $arr_libur)){
		echo '<td>-</td>
		<td>-</td>
		<td>'.$dlibur.'</td>
		</tr>';
	}
	else{
		echo '<td>-</td>
		<td>-</td>
		<td>-</td>
		</tr>';
	}

}

function get_tl($latein) {
	if(!$latein)
		return;
	else
		return " <span title='$latein menit'>TL $latein menit</span> ";
}

function get_psw($earlyhome) {
	if(!$earlyhome)
		return;
	else
		return " <span title='$earlyhome menit'>PSW $earlyhome menit</span> ";
}
?>
</tbody></table>
<style>
tr.libur{
	color:red;
}
</style>