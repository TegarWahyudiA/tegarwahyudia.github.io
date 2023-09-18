<?php
$this->pageCaption='Rincian Fungsional';
$this->pageTitle=$this->pageCaption;

if(!$bulan_1 && !$bulan_2)
	$periode = null;
elseif($bulan_1==$bulan_2)
	$periode = 'Periode '.strftime('%B %Y', mktime(0,0,0,$bulan_1,1,$tahun));
elseif ($bulan_2>$bulan_1) 
	$periode = 'Periode '.strftime('%B', mktime(0,0,0,$bulan_1)).' - '.strftime('%B %Y', mktime(0,0,0,$bulan_2,1,$tahun));

$this->pageDescription = '<span style="float:right">'.$periode.'</span>';

$this->breadcrumbs=array(
	'Indeks Kegiatan'=>array('unitkerja'),
	'Kegiatan Saya'=>array('index'),
	'Rincian Fungsional',
);

$arr_bulan = array(''=>'-- Pilih --');
for($i=1; $i<=12; $i++)
	$arr_bulan[$i] = strftime('%m. %B',mktime(0,0,0,$i));

if(!$excel){ 
?>

<div class="form">
	<?php $form=$this->beginWidget('BActiveForm', array(
		'id'=>'tabel-fungsional-form',
		'enableAjaxValidation'=>false,
	));?>

	<div class="control-group">
		<label class="control-label">Periode</label>
		<div class="input">
			<?php echo BHtml::dropDownList('bulan_1',$bulan_1,$arr_bulan, array('style'=>'width:120px')); ?> - 	
			<?php echo BHtml::dropDownList('bulan_2',$bulan_2,$arr_bulan, array('style'=>'width:120px')); ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label">Tahun</label>
		<div class="input">
			<?php echo BHtml::textField('tahun',$tahun,array('maxlength'=>4,'style'=>'width:40px;text-align:center')); ?>
			<?php echo BHtml::submitButton('Tampilkan'); ?>
		</div>
	</div>

	<?php $this->endWidget(); ?>
</div>

<?php
}

if($data){ ?>
<table class="table" id="fungsional-table">
<thead><tr>
	<th style="width:20px">#</th>
	<th>Uraian Kegiatan</th>
	<th>Keterangan</th>
	<th style="width:50px">Kode</th>
	<th style="width:90px">Tanggal</th>
	<th style="width:90px">Satuan</th>
	<th style="width:40px">Volume</th>
	<th style="width:50px">Angka Kredit</th>
	<th style="width:50px">Jumlah Angka Kredit</th>
</tr></thead>
<tbody>
<?php
$i=1;
$angka_kredit = 0;
foreach($data as $model){
	$kegiatan = $model->kegiatan;
	$kredit = $model->kredit;
	$angka_kredit += ($model->jml_realisasi * $kredit->angka_kredit);
	echo '<tr><td>'.($i++).'</td><td>'.($excel? $kegiatan->nama_kegiatan : 
		CHtml::link($kegiatan->nama_kegiatan, array('kegiatan','id'=>$model->id_kegiatan))).'</td><td>'.
		$model->keterangan.'</td><td>'.
		$kredit->kode_perka.' '.$kredit->kode.'</td><td>'.
		$kegiatan->str_tgl_mulai_2.' - '.$kegiatan->str_tgl_selesai_2.'</td><td>'.
		$kredit->satuan_hasil.'</td><td>'.
		$model->jml_realisasi.'</td><td>'.
		$kredit->angka_kredit.'</td><td>'.
		($model->jml_realisasi * $kredit->angka_kredit).'</td></tr>';
}
?>
</tbody>
<thead><tr><th colspan=8 style="text-align:right">Jumlah Angka Kredit :</th><th><?php echo $angka_kredit;?></th></tr></thead>
</table>

<?php 
if(!$excel && $data)
	echo CHtml::link(BHtml::icon('download').' Export ke Excel','#',array('id'=>'export','onclick'=>'$("#fungsional-table").DataTable()'));
	Yii::app()->clientScript->registerScript('export','
		$("#export").click(function(){
			$("#bulan_1").val('.$bulan_1.');
			$("#bulan_2").val('.$bulan_2.');
			$("#tahun").val('.$tahun.');
			$("#tabel-fungsional-form").append("<input id=excel name=excel value=1 type=hidden />").submit();
			return;
		});
	');
} ?>

<?php 
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/datatables.min.js');
Yii::app()->clientScript->registerScript('form','
	$("select#bulan_2").change(function(){
		if($(this).val()>0); $(document).find("input[name=tahun]").focus();
		$("#excel").val(0);
	});
');