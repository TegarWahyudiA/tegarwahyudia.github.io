<?php 
$arr_libur = CHtml::listData(CalendarHoliday::model()->findAll(array('condition'=>'year(CalendarHolidayDate)='.$tahun.' AND month(CalendarHolidayDate)='.$bulan)),'CalendarHolidayDate','CalendarHolidayStatusDescription');
foreach($dataProvider->getData() as $pegawai){
?>
<table border=0 width=100%>
<tr><td width=110 colspan=2>FingerPrint ID</td><td colspan=13 align=left style='text-align:left'><b><?php echo $pegawai->id_presensi;?></b></td></tr>
<tr><td colspan=2>Nama Pegawai</td><td colspan=13><b><?php echo $pegawai->nama_pegawai;?></b></td></tr>
<tr><td colspan=2>Jabatan Pegawai</td><td colspan=13><b><?php echo $pegawai->jabatan;?></b></td></tr>
<tr><td colspan=2>Satuan Kerja</td><td colspan=13><b><?php echo $pegawai->satuan_kerja;?></b></td></tr>
<tr>
	<td colspan=15>
	<table border=1 cellpadding=3>
		<tr>
			<th rowspan=2>Hari</th>
			<th rowspan=2>Tanggal</th>
			<th colspan=2>Jam Masuk</th>
			<th colspan=2>Jam Pulang</th>
			<th colspan=2>Terlambat</th>
			<th colspan=2>Pulang Cepat</th>
			<th colspan=2>Lembur</th>
			<th colspan=2>Total Hour</th>
			<th rowspan=2>Catatan</th>
		</tr>
		<tr>
			<th>Jam</th><th>Menit</th>
			<th>Jam</th><th>Menit</th>
			<th>Jam</th><th>Menit</th>
			<th>Jam</th><th>Menit</th>
			<th>Jam</th><th>Menit</th>
			<th>Jam</th><th>Menit</th>
		</tr>
<?php foreach($pegawai->getPresensi($tahun, $bulan) as $presensi){ 
	$waktu = null;
	$lembur_jam = null;
	$lembur_menit = null;
	$total_jam = null;
	$total_menit = null;

	if($presensi->TimeCome && $presensi->TimeHome){
		$waktu = (strtotime($presensi->TimeHome) - strtotime($presensi->TimeCome))/60;
		if($presensi->PersonalCalendarStatus==99){
			$lembur_jam = floor($waktu/60);
			$lembur_menit = $waktu % 60;
		} elseif(date('N',strtotime($presensi->PersonalCalendarDate)<=5) && !in_array($presensi->PersonalCalendarDate, $arr_libur)){
			$total_jam = floor($waktu/60);
			$total_menit = $waktu % 60;
		}
	}
?>
		<tr>
			<td><?php echo $presensi->hari;?></td>
			<td><?php echo $presensi->tanggal;?></td>
<?php
	if((date('N',strtotime($presensi->PersonalCalendarDate))>5 || in_array($presensi->PersonalCalendarDate, $arr_libur)) && $presensi->PersonalCalendarStatus<>99){
		for($i=1;$i<=13;$i++) echo '<td></td>';
	} else {
?>			
			<td><?php echo $presensi->datang_jam;?></td>
			<td><?php echo $presensi->datang_menit;?></td>
			<td><?php echo $presensi->pulang_jam;?></td>
			<td><?php echo $presensi->pulang_menit;?></td>
			<td><?php echo $presensi->telat_jam;?></td>
			<td><?php echo $presensi->telat_menit;?></td>
			<td><?php echo $presensi->psw_jam;?></td>
			<td><?php echo $presensi->psw_menit;?></td>
			<td><?php echo $lembur_jam;?></td>
			<td><?php echo $lembur_menit;?></td>
			<td><?php echo $total_jam;?></td>
			<td><?php echo $total_menit;?></td>
			<td><?php echo $presensi->PersonalCalendarStatus<>99 && in_array($presensi->PersonalCalendarDate, array_keys($arr_libur))? $arr_libur[$presensi->PersonalCalendarDate] : $presensi->keterangan;?></td>
<?php } ?>
		</tr>
<?php } ?>
		<tr>
			<th colspan=6>Total</th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>
	</table>
	</td>
</tr>
</table>
<br>
<br>
<?php } ?>