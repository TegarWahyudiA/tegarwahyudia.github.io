<?php 
$this->pageCaption='Rekap Absensi '.strftime('%B %Y',mktime(0,0,0,$bulan,1,$tahun));
$this->pageTitle=$this->pageCaption;

$prev_bulan = ($bulan==1)? array('rekap_absensi','tahun'=>$tahun-1,'bulan'=>12) : array('rekap_absensi','tahun'=>$tahun,'bulan'=>$bulan-1);
$next_bulan = ($bulan==12)? array('rekap_absensi','tahun'=>$tahun+1,'bulan'=>1) : array('rekap_absensi','tahun'=>$tahun,'bulan'=>$bulan+1);

$this->pageDescription = '<span style="float:right">'.CHtml::link('<i class="g g-chevron-left"></i>',$prev_bulan).' '.strftime('%B %Y',mktime(0,0,0,$bulan,1,$tahun)).' '.CHtml::link('<i class="g g-chevron-right"></i>',$next_bulan).'</span>';

$this->breadcrumbs=array(
	'Monitoring',//=>array('index'),
	'Presensi Pegawai'=>array('presensi','tahun'=>$tahun,'bulan'=>$bulan),
	'Rekap Absensi',
);

$arr_data = array();
foreach($dataProvider->getData() as $pegawai){
	$arr_data[$pegawai->id_presensi] = array(
		's'=>array(), 'i'=>array(), 'tk'=>array(), 'cb'=>array(), 'cp'=>array(), 'cm'=>array(), 'ct'=>array(), 'tl1'=>array(), 'tl2'=>array(), 'tl3'=>array(), 'tl4'=>array(),
		'psw1'=>array(), 'psw2'=>array(), 'psw3'=>array(), 'psw4'=>array(), 'tb'=>array(), 'cltn'=>array(), 'bmp'=>array(), 'hd'=>array(), 'ke'=>array(), 'ckp'=>array(),
		'dl'=>array(),'hadir'=>array()
	);
}

$tgl_1 = date('Y-m-d', mktime(0,0,0,$bulan,1,$tahun));
//if($bulan<date('m'))
	$tgl_2 = date('Y-m-t', mktime(0,0,0,$bulan,1,$tahun));
//else
//	$tgl_2 = date('Y-m-d');

$result = $db->query("select * from a_CalendarHoliday where CalendarHolidayStatusDescription<>'' AND CalendarHolidayDate>='".$tgl_1."' and CalendarHolidayDate<='".$tgl_2."' order by CalendarHolidayDate");
$arr_libur = array();
while ($row = $result->fetch()) {
	$arr_libur[] = $row['CalendarHolidayDate'];// = $row['CalendarHolidayStatusDescription'];
}

$result = $db->query("select * from a_PersonalCalendar where PersonalCalendarDate>='".$tgl_1."' and PersonalCalendarDate<='".$tgl_2."' order by FingerPrintID,PersonalCalendarDate");
while ($row = $result->fetch()) {
	if(
		in_array($row['FingerPrintID'], array_keys($arr_data)) && 
		!in_array($row['PersonalCalendarDate'], $arr_libur) &&
		!in_array(date('w',strtotime($row['PersonalCalendarDate'])), array(0,6))
	){
if($row['TimeCome'] || $row['TimeHome']) $arr_data[$row['FingerPrintID']]['hadir'][] =  substr($row['PersonalCalendarDate'],8,2);

		if($row['PersonalCalendarStatus']){
			$field = '';
			switch($row['PersonalCalendarStatus']){
				// sakit
				case 45: case 48: $field= 's'; break;
				case 46: $field= 'cm'; break;
				case 47: $field= 'cp'; break;
				case 49: $field= 'dl';break; // DL
				case 50: $field= 'ct'; break;
				case 51: $field= 'i'; break;
				case 57: $field= 'tk'; break;
			}
			if($field) $arr_data[$row['FingerPrintID']][$field][] = substr($row['PersonalCalendarDate'],8,2);

		} else {
			//hitung tk
			if(!$row['TimeCome'] && !$row['TimeHome'] && !$row['PersonalCalendarReason'])
				$arr_data[$row['FingerPrintID']]['tk'][] =  substr($row['PersonalCalendarDate'],8,2);

			//hitung terlambat
			if($row['LateIn']) {
				$field = '';
				if($row['LateIn']<=30) $field = 'tl1';
				elseif($row['LateIn']<=60) $field = 'tl2';
				elseif($row['LateIn']<=90) $field = 'tl3';
				elseif($row['LateIn']>90) $field = 'tl4';
				$arr_data[$row['FingerPrintID']][$field][] =  substr($row['PersonalCalendarDate'],8,2);
			} elseif(!$row['TimeCome'] && !$row['PersonalCalendarReason'])
				$arr_data[$row['FingerPrintID']]['tl4'][] =  substr($row['PersonalCalendarDate'],8,2);

			//hitung pulang cepat
			if($row['EarlyOut']) {
				$field = '';
				if($row['EarlyOut']<=30) $field = 'psw1';
				elseif($row['EarlyOut']<=60) $field = 'psw2';
				elseif($row['EarlyOut']<=90) $field = 'psw3';
				elseif($row['EarlyOut']>90) $field = 'psw4';
				$arr_data[$row['FingerPrintID']][$field][] =  substr($row['PersonalCalendarDate'],8,2);
			} elseif(!$row['TimeHome'] && !$row['PersonalCalendarReason'])
				$arr_data[$row['FingerPrintID']]['psw4'][] =  substr($row['PersonalCalendarDate'],8,2);
		}
	}
}
?>

<table class='table' border='1'>
<thead>
	<tr>
		<th rowspan=2>No</th>
		<th rowspan=2>Nama</th>
		<th rowspan=2>Gol</th>		
		<th colspan=18>RINCIAN ABSENSI/KETIDAKHADIRAN</th>
		<th rowspan=2>HD</th>
		<th rowspan=2>KE</th>
		<th rowspan=2>CKP</th>
		<th rowspan=2>DL</th>
		<th rowspan=2>Hadir</th>
	</tr>
	<tr>
		<th>S</th><th>i</th><th>TK</th><th>CB</th><th>CP</th><th>CM</th><th>CT/I</th>
		<th>TL1</th><th>TL2</th><th>TL3</th><th>TL4</th><th>PSW1</th><th>PSW2</th><th>PSW3</th><th>PSW4</th>
		<th>TB</th><th>CLTN</th><th>BMP</th>
	</tr>
	<tr>
		<?php 
			if(!$excel) for($i=1;$i<=26;$i++) echo '<th><small>('.$i.')</small></th>'; 
			else for($i=1;$i<=26;$i++) echo '<th><small>\'('.$i.')</small></th>'; 
		?>
	</tr>
</thead>
<tbody>
<?php
$i=1;
foreach($dataProvider->getData() as $pegawai){
	$class=$i%2? 'odd':'even';
	echo '<tr class='.$class.' title='.$pegawai->id_presensi.'><td align=center>'.($i++).'</td>
		<td>'.CHtml::link($pegawai->nama_pegawai,array('absensi','id'=>$pegawai->id,'bulan'=>$bulan,'tahun'=>$tahun)).'</td>
		<td>'.$pegawai->golongan->golongan.'</td>';
	foreach($arr_data[$pegawai->id_presensi] as $key=>$val) {
		if($key=='hadir')
			echo '<td align=center>'.count($val).'</td>';
		elseif(count($val))
			echo '<td align=center title="'.implode(", ",$val).'">'.count($val).'</td>';
		else
			echo '<td></td>';
	}
	echo '</tr>';
}
?>
</tbody>
</table>
<?php
if(!$excel){
	echo CHtml::link('<i class="g g-download"></i> Export Excel',array('rekap_absensi','bulan'=>$bulan,'tahun'=>$tahun,'excel'=>'yes'));
}
?>

<style>
thead {border-bottom: 3px solid black; }
th {font-family:arial; font-size:8pt;}
td {font-family:arial; font-size:9pt;}
td[align=center] {text-align:center;}
table, tr, th, td { border-collapse: collapse; padding:1px 3px;}
tr.odd {background:#efefef;}
tbody tr:hover {background:#ff8844;}
</style>