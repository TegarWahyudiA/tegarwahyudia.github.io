<?php
$this->pageCaption=$pegawai->nama_pegawai;
$this->pageTitle=$this->pageCaption;

$prev_bulan = ($bulan==1)? array('pegawai','id'=>$pegawai->id,'tahun'=>$tahun-1,'bulan'=>12) : array('pegawai','id'=>$pegawai->id,'tahun'=>$tahun,'bulan'=>$bulan-1);
$next_bulan = ($bulan==12)? array('pegawai','id'=>$pegawai->id,'tahun'=>$tahun+1,'bulan'=>1) : array('pegawai','id'=>$pegawai->id,'tahun'=>$tahun,'bulan'=>$bulan+1);

$this->pageDescription = '<span style="float:right">'.CHtml::link('<i class="g g-chevron-left"></i>',$prev_bulan).' '.strftime('%B %Y',mktime(0,0,0,$bulan,1,$tahun)).' '.CHtml::link('<i class="g g-chevron-right"></i>',$next_bulan).'</span>';

$this->breadcrumbs=array(
	'Monitoring',//=>array('index','tahun'=>$tahun,'bulan'=>$bulan),
	'Rekap CKP'=>array('ckp_rekap','bulan'=>$bulan,'tahun'=>$tahun),
);

$filter = ' AND year(tgl_mulai)='.$tahun.' AND month(tgl_mulai)<='.$bulan.' AND month(tgl_selesai)>='.$bulan;

$target_pegawai = TabelTargetPegawai::model()->findAll(array(
		'with'=>'kegiatan',
		'condition'=>"id_pegawai=".$pegawai->id. $filter,
		'order'=>'nama_kegiatan ASC, tgl_selesai DESC, tgl_mulai DESC'
	));
?>

<table class="table">
<thead>
	<tr><th style="width:20px">#</th><th>Nama Kegiatan</th><th>Jadwal</th><th>Seksi</th><th>Target</th><th style="width:80px">Progress</th>
	<?php if(substr($pegawai->id_unitkerja,0,Yii::app()->user->id_eselon) == substr(Yii::app()->user->id_unitkerja,0,Yii::app()->user->id_eselon))
		echo '<th style="width:40px;text-align:center">Nilai</th>';
	?>
	</tr>
</thead>
<tbody>
<?php 
$i = 1;
$arr_kuantitas = array();
$arr_kualitas = array();
$arr_angka_kredit = array();
$jml_kegiatan = 0;
foreach($target_pegawai as $target){
	$img_alokasi = ($target->child_target==$target->jml_target)? 'ok.png' : 'error.png';
	echo '<tr key="'.$target->id_kegiatan.'"><td>'.($i++).'</td>
	<td>'.$target->kegiatan->nama_kegiatan.'</td>
	<td>'.$target->kegiatan->jadwal.'</td>
	<td>'.$target->kegiatan->unitkerja->unitkerja.'</td>
	<td>'.$target->target_satuan.'</td><td style="padding:0; line-height:0px; vertical-align:middle">';
	Controller::createWidget('TbProgress', array('percent'=>number_format($target->jml_realisasi/$target->jml_target*100,0)))->run();
	echo '</td>';
	if(substr($pegawai->id_unitkerja,0,Yii::app()->user->id_eselon) == substr(Yii::app()->user->id_unitkerja,0,Yii::app()->user->id_eselon))
		echo '<td class="persen">'.(int)$target->persen_kualitas.'</td>';
	if($target->fungsional && $target->kredit){
		$angka_kredit = $target->kredit->{$target->fungsional->kolom_kredit} * $target->jml_realisasi;
		$arr_angka_kredit[] = $angka_kredit;
	}

	//echo '<td>'.(!$target->kegiatan->is_lock && ((Yii::app()->user->id_unitkerja==$target->kegiatan->id_unitkerja || Yii::app()->user->id_unitkerja==$pegawai->id_unitkerja 	|| Yii::app()->user->isKepala))?CHtml::link('<i class="g g-pencil"></i>',array('ckp_update','id'=>$target->id),array('title'=>'Update Penilaian')):'').'</td>';
	
	echo '</tr>';

	if($target->kegiatan->is_ckp==1){
		$arr_kuantitas[] = $target->jml_realisasi / $target->jml_target * 100;
		$arr_kualitas[] = $target->persen_kualitas;
		$jml_kegiatan++;
	}
} ?>
</tbody></table>

<?php 
if($target_pegawai){
	$r_kuantitas = number_format(array_sum($arr_kuantitas)/count($arr_kuantitas),2);
	$r_kualitas = number_format(array_sum($arr_kualitas)/count($arr_kualitas),2);
	$nilai_ckp = number_format(($r_kuantitas + $r_kualitas)/2,2);

//	echo '<span class="span2">Rata-Rata Kuantitas </span> : '.$r_kuantitas.'<br>';
//	echo '<span class="span2">Rata-Rata Kualitas </span> : '.$r_kualitas.'<br>';
//	echo '<span class="span2">Capaian Kinerja (CKP) </span> : '.$nilai_ckp.'<br>';
//	if(count($arr_angka_kredit))
//		echo '<span class="span2">Angka Kredit </span> : '.array_sum($arr_angka_kredit).'<br>';

	if(!$tahun) $tahun = date('Y');

	$t_ckp = CKP::model()->find(array('condition'=>'id_pegawai='.$pegawai->id.' AND bulan='.$bulan.' AND tahun='.$tahun));
	if(!$t_ckp) {
		$t_ckp = new CKP;
		$t_ckp->id_pegawai = $pegawai->id;
		$t_ckp->bulan = $bulan;
		$t_ckp->tahun = $tahun;
	}

	$t_ckp->jml_kegiatan = $jml_kegiatan;
	$t_ckp->r_kuantitas = $r_kuantitas;
	$t_ckp->r_kualitas = $r_kualitas;
	$t_ckp->nilai_ckp = $nilai_ckp;

	if(count($arr_angka_kredit))
		$t_ckp->angka_kredit = array_sum($arr_angka_kredit);

	$t_ckp->updated_on = date('Y-m-d H:i:s');
	$t_ckp->save();

	if(substr($pegawai->id_unitkerja,0,Yii::app()->user->id_eselon) == substr(Yii::app()->user->id_unitkerja,0,Yii::app()->user->id_eselon)){
		echo 'Rata-Rata Kuantitas: '.$r_kuantitas.'<br>';
		echo 'Rata-Rata Kualitas: '.$r_kualitas.'<br>';
		echo 'Capaian Kinerja: '.$nilai_ckp.'<br><br>';

		echo CHtml::link(BHtml::icon('circle-arrow-right').' CKP-R',array('ckp','id'=>$pegawai->id,'bulan'=>$bulan,'tahun'=>$tahun));
		Yii::app()->clientScript->registerScript('js','
			$(".persen").attr("style","cursor:pointer;color:#08c;text-align:center").click(function(){
				var e = $(this);
				var key = $(this).closest("tr").attr("key");
				var lama = parseInt($(this).text());
				var nilai = prompt("Masukkan nilai baru", lama);
				if(nilai && lama!=nilai){
					$.post("'.Yii::app()->createUrl('/monitoring/set_nilai',array('id'=>$pegawai->id)).'",{key:key,nilai:nilai},function(data){
						if(data==nilai){
							alert("Nilai telah diubah menjadi "+nilai);
							location.reload();
							e.text(data);
						} else
							alert(data);
					});
				}
			});
		');
	}
}


