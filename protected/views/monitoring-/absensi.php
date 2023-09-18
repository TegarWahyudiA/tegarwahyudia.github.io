<?php
$this->pageCaption=$model->nama_pegawai;
$this->pageTitle=$this->pageCaption;


$prev_bulan = ($bulan==1)? array('absensi','id'=>$model->id,'tahun'=>$tahun-1,'bulan'=>12) : array('absensi','id'=>$model->id,'tahun'=>$tahun,'bulan'=>$bulan-1);
$next_bulan = ($bulan==12)? array('absensi','id'=>$model->id,'tahun'=>$tahun+1,'bulan'=>1) : array('absensi','id'=>$model->id,'tahun'=>$tahun,'bulan'=>$bulan+1);

$this->pageDescription = '<span style="float:right">'.CHtml::link('<i class="g g-chevron-left"></i>',$prev_bulan).' '.strftime('%B %Y',mktime(0,0,0,$bulan,1,$tahun)).' '.CHtml::link('<i class="g g-chevron-right"></i>',$next_bulan).'</span>';

$this->breadcrumbs=array(
	'Monitoring',// Presensi'=>array('rekap_absensi'),
	'Presensi Pegawai'=>array('presensi','tahun'=>$tahun,'bulan'=>$bulan),
);
?>

<table class="table">
<thead>
	<tr><th style='width:100px'>Tanggal</th><th style='width:70px'>Datang</th><th style='width:70px'>Pulang</th><th>Keterangan</th></th></tr>
</thead>
<tbody>

<?php 
$tgl_1 = date('Y-m-d', mktime(0,0,0,$bulan,1,$tahun));
$tgl_2 = date('Y-m-t', mktime(0,0,0,$bulan,1,$tahun));

$result = $db->query("select * from a_CalendarHoliday where CalendarHolidayStatusDescription<>'' AND CalendarHolidayDate>='".$tgl_1."' and CalendarHolidayDate<='".$tgl_2."' order by CalendarHolidayDate");
$arr_libur = array();
while ($row = $result->fetch()) {
	$arr_libur[date('Y-m-d',strtotime($row['CalendarHolidayDate']))] = $row['CalendarHolidayStatusDescription'];
}

$result = $db->query("select * from a_PersonalCalendar where FingerPrintID='".$model->id_presensi."' and PersonalCalendarDate>='".$tgl_1."' and PersonalCalendarDate<='".$tgl_2."' order by PersonalCalendarDate");
while ($row = $result->fetch()) {
	$libur = (array_key_exists(date('Y-m-d', strtotime($row['PersonalCalendarDate'])), $arr_libur) || date('N ',strtotime($row['PersonalCalendarDate']))>=6)? 'class=libur':'';
	echo '<tr '.$libur.'>
		<td>'.date('d M Y ',strtotime($row['PersonalCalendarDate'])).'</td>
		<td>'.$row['TimeCome'].'</td>
		<td>'.$row['TimeHome'].'</td>
		<td>'.get_tl($row['LateIn']).get_psw($row['EarlyOut']).' '.$row['PersonalCalendarReason'].' '.
		(array_key_exists(date('Y-m-d', strtotime($row['PersonalCalendarDate'])), $arr_libur)? $arr_libur[date('Y-m-d', strtotime($row['PersonalCalendarDate']))] :'').
		'</td></tr>';
}
?>
</tbody></table>
<style>
tr.libur, td span{
	color:red;
}
</style>

<?php 
echo CHtml::link(BHtml::icon('menu-left').'Kembali',array('presensi'));

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