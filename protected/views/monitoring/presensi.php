<?php
$this->pageCaption='Presensi Pegawai';
$this->pageTitle=$this->pageCaption;

$periode = strftime('%d %b',strtotime(date('Y').'W'.$mingguke.'1')).' - '.strftime('%d %b %Y',strtotime(date('Y').'W'.$mingguke.'5'));

$this->pageDescription='<span style="float:right">'.
	CHtml::link('<i class="g g-chevron-left"></i>',array('presensi','mingguke'=>$mingguke-1)).' '.$periode.' '.
	CHtml::link('<i class="g g-chevron-right"></i>',array('presensi','mingguke'=>$mingguke+1)).'</span>';

$this->breadcrumbs=array(
	'Monitoring',
	'Presensi Pegawai'
);

$tgl_1 = date('Y-m-d', strtotime(date('Y').'W'.$mingguke.'1'));
$tgl_2 = date('Y-m-d', strtotime(date('Y').'W'.$mingguke.'7'));


$result = $db->query("select * from a_CalendarHoliday where CalendarHolidayStatusDescription<>'' AND CalendarHolidayDate>='".$tgl_1."' and CalendarHolidayDate<='".$tgl_2."' order by CalendarHolidayDate");
$arr_libur = array();
while ($row = $result->fetch()) {
	$arr_libur[date('Y-m-d',strtotime($row['CalendarHolidayDate']))] = $row['CalendarHolidayStatusDescription'];
}

$result = $db->query("select * from a_PersonalCalendar where PersonalCalendarDate>='".$tgl_1."' and PersonalCalendarDate<='".$tgl_2."' order by FingerPrintID,PersonalCalendarDate");
$arr_data = array();
while ($row = $result->fetch()) {
	$arr_data[$row['FingerPrintID']][date('Y-m-d',strtotime($row['PersonalCalendarDate']))] = array (
		'TimeCome' => $row['TimeCome'],
		'TimeHome' => $row['TimeHome'],
		'PersonalCalendarReason' => $row['PersonalCalendarReason'],
		'LateIn' => $row['LateIn'],
		'EarlyOut' => $row['EarlyOut'],
		);
}
?>
<table class='table'>
<thead><tr><th style='width:20px'>#</th><th>Nama Pegawai</th>
<?php
$arr_tanggal = array(); 
for($i=strtotime(date('Y').'W'.$mingguke.'1'); $i<=strtotime(date('Y').'W'.$mingguke.'7'); $i+=(3600*24)) {
	$tanggal = date('Y-m-d',$i);
	$arr_tanggal[] = $tanggal;
	if(in_array($tanggal, array_keys($arr_libur))){
		echo "<th class=libur title='".$arr_libur[$tanggal]."''>".strftime('%d %b',$i).'</th>';		
	} elseif(date('N',$i)>=6) {
		echo "<th class=libur title='".date('l',$i)."''>".strftime('%d %b',$i).'</th>';
	} else {
		echo "<th title='".date('l',$i)."''>".strftime('%d %b',$i).'</th>';
	}
}
?>
</tr>
<tbody>

<?php 
$i=1;
foreach($dataProvider->getData() as $pegawai){
	echo '<tr><td>'.($i++).'.</td><td>'.CHtml::link($pegawai->nama_pegawai,array('absensi','id'=>$pegawai->id,'tahun'=>$tahun,'bulan'=>$bulan)).'</td>';
	for($h=0; $h<7; $h++){
		if(isset($arr_data[$pegawai->id_presensi])){
			$data = $arr_data[$pegawai->id_presensi][$arr_tanggal[$h]];

			$tl=($data['LateIn'])?" title='TL - ".hitung_jam($data['LateIn'])."' class='tl'" : "";
			$psw=($data['EarlyOut'])?" title='PSW - ".hitung_jam($data['EarlyOut'])."' class='psw'" : "";

			if(!$data['PersonalCalendarReason'])
				echo '<td title="'.$data['PersonalCalendarReason'].'">
				<span'.$tl.'>'.$data['TimeCome'].'</span> - 
				<span'.$psw.'>'.$data['TimeHome'].'</span></td>';
			else
				echo '<td class=libur title="'.$data['PersonalCalendarReason'].'">'.$data['PersonalCalendarReason'].'</td>';
		} else 
			echo '<td>-</td>';
	}
	echo '</tr>';
}

function hitung_jam($menit) {
	$j = date('G',mktime(0,$menit));
	$m = date('i',mktime(0,$menit));
	return ($j>0?$j.' jam ':'').(int)$m.' menit';
}

?>
</tbody>
</table>
<?php 
	echo CHtml::link('<i class="g g-menu-right"></i> Rekap Absensi',array('rekap_absensi'));
	echo $this->pageDescription;
?>

<style>
.libur {color:red;}
.tl, .psw {color: #f74014;}
</style>