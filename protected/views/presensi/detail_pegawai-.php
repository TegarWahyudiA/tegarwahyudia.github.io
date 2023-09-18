<?php 
foreach($dataProvider->getData() as $pegawai){
?>
<table border=0 width=100%>
<tr><td width=110>FingerPrint ID</td><td>{{<?php echo $pegawai->id_presensi;?>}}</td></tr>
<tr><td>Nama Pegawai</td><td>{{nama_pegawai}}</td></tr>
<tr><td>Jabatan Pegawai</td><td>{{jabatan}}</td></tr>
<tr><td>Satuan Kerja</td><td>{{satuan_kerja}}</td></tr>
<tr>
	<td colspan=2>
	<table border=1>
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

		<tr>
			<td>{{hari}}</td>
			<td>{{tanggal}}</td>
			<td>{{datang_jam}}</td>
			<td>{{datang_menit}}</td>
			<td>{{pulang_jam}}</td>
			<td>{{pulang_menit}}</td>
			<td>{{telat_jam}}</td>
			<td>{{telat_menit}}</td>
			<td>{{psw_jam}}</td>
			<td>{{psw_menit}}</td>
			<td>{{lembur_jam}}</td>
			<td>{{lembur_menit}}</td>
			<td>{{total_jam}}</td>
			<td>{{total_menit}}</td>
			<td>{{keterangan}}</td>
		</tr>

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