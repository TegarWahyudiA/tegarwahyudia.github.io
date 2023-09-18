<?php
$this->pageCaption='Presensi Pegawai';
$this->pageTitle=$this->pageCaption;

$prev = $mingguke<=1? CHtml::link('<i class="g g-chevron-left"></i>',array('index','tahun'=>$tahun-1,'mingguke'=>52)) : CHtml::link('<i class="g g-chevron-left"></i>',array('index','tahun'=>$tahun,'mingguke'=>((int)$mingguke<=10? '0'.($mingguke-1) : $mingguke-1)));
$next = $mingguke>=52? CHtml::link('<i class="g g-chevron-right"></i>',array('index','tahun'=>$tahun+1, 'mingguke'=>'01')) : CHtml::link('<i class="g g-chevron-right"></i>',array('index','tahun'=>$tahun, 'mingguke'=>((int)$mingguke<9?'0'.($mingguke+1) : $mingguke+1)));


//if($mingguke<10) $mingguke='0'.$mingguke;
$periode = strftime('%d %b',strtotime($tahun.'W'.$mingguke.'1')).' - '.strftime('%d %b %Y',strtotime($tahun.'W'.$mingguke.'5'));

$this->pageDescription='<span style="float:right">'.$prev.' '.$periode.' '.$next.'</span>';

$this->breadcrumbs=array(
	'Monitoring',
	'Presensi Pegawai'
);

$this->widget('TbMenu', array(
	'type'=>'tabs',
	'items'=>array(
		array('label'=>'Presensi Harian','url'=>'#','active'=>true),
		array('label'=>'Proses Presensi','url'=>array('proses')),
		array('label'=>'Rekap Bulanan','url'=>array('rekap')),
		array('label'=>'Ketidakhadiran','url'=>array('absen')),
		array('label'=>'Perubahan Jam','url'=>array('jam')),
		array('label'=>'Hari Libur','url'=>array('libur')),
	)
));

$tgl_1 = date('Y-m-d', strtotime($tahun.'W'.$mingguke.'1'));
$tgl_2 = date('Y-m-d', strtotime($tahun.'W'.$mingguke.'7'));

$bulan = strtotime($tahun.'W'.$mingguke.'1');

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
for($i=strtotime($tahun.'W'.$mingguke.'1'); $i<=strtotime($tahun.'W'.$mingguke.'7'); $i+=(3600*24)) {
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
	echo '<tr><td>'.($i++).'.</td><td>'.CHtml::link($pegawai->nama_pegawai,array('detail','id'=>$pegawai->id,'tahun'=>$tahun,'bulan'=>$bulan)).'</td>';
	for($h=0; $h<7; $h++){
		if(isset($arr_data[$pegawai->id_presensi][$arr_tanggal[$h]])){
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
//	echo CHtml::link('<i class="g g-menu-right"></i> Rekap Absensi',array('rekap_absensi'));
	echo $this->pageDescription;
?>

<style>
.libur {color:red;}
.tl, .psw {color: #f74014;}
</style>