<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link href="<?php echo Yii::app()->theme->baseUrl; ?>/css/bootstrap.min.css" rel="stylesheet">
	<link href="<?php echo Yii::app()->theme->baseUrl; ?>/css/application.min.css" rel="stylesheet">
	<link href="<?php echo Yii::app()->theme->baseUrl; ?>/css/bootstrap-responsive.css" rel="stylesheet">
	<link href="<?php echo Yii::app()->theme->baseUrl; ?>/css/styles.css" rel="stylesheet">

	<!--[if lt IE 9]>
		<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/images/bps.png">
</head>

<body>
<?php 
$this->widget('TbNavbar',array(
    'brandOptions'=>array('style'=>'padding-left:40px'),
    'items'=>array(
        array(
            'class'=>'bootstrap.widgets.TbMenu',
            'encodeLabel'=>false,
            'htmlOptions'=>array('style'=>'float:right; margin-right:0; padding-right:0'),
            'items'=>array(
//                array('label'=>'=> Sensus Daring <=','url'=>'https://pwc.qualtrics.com/jfe/form/SV_39mUS8cDav1uNHn'),
              		array('label'=>BHtml::icon('calendar').'Struktural', 'visible'=>Yii::app()->user->isKasi, 'htmlOptions'=>array('style'=>'right:0; left:auto;'),'items'=>array(
                      array('label'=>'Kegiatan Unitkerja','url'=>array('/kegiatan/unitkerja','id'=>Yii::app()->user->id_unitkerja)),
                			array('label'=>'Verifikasi Realisasi', 'url'=>array('/kegiatan/verifikasi')),
                  		'---',
                  		array('label'=>'Monitoring Mingguan', 'url'=>array('/monitoring/mingguan'),'visible'=>Yii::app()->params['mingguan']),
                      array('label'=>'Monitoring Bulanan', 'url'=>array('/monitoring/bulanan')),
                      array('label'=>'Capaian Kinerja Pegawai', 'url'=>array('/monitoring/ckp_rekap')),
                      array('label'=>'Presensi Pegawai', 'url'=>array('/monitoring/presensi')),
/*                      '---',
                      array('label'=>'SKP Tahunan', 'url'=>array('/skp/struktural')),
*///                      array('label'=>'SPJ Kegiatan', 'url'=>array('/spj/monitoring')),
                   		'---',
                    array('label'=>'Copy Kegiatan', 'url'=>array('/kegiatan/duplikasi')),
                      array('label'=>'Master Satuan', 'url'=>array('/admin/satuan')),
               			)),
                  array('label'=>BHtml::icon('cog').'Administrator', 'visible'=>Yii::app()->user->isAdmin, 'htmlOptions'=>array('style'=>'right:0; left:auto;'),'items'=>array(
                      /*array('label'=>'Absensi Pegawai', 'url'=>array('/calendar/absensi')),
                      array('label'=>'Hari Libur', 'url'=>array('/calendar/holiday')),
                      '---',
                      */
                      array('label'=>'Proses Presensi', 'url'=>array('/presensi/proses')),
                      array('label'=>'Rekap CKP', 'url'=>array('/admin/ckp_rekap')),
                      array('label'=>'Master Pegawai', 'url'=>array('/admin/pegawai')),
                      array('label'=>'Master Angka Kredit', 'url'=>array('/admin/kredit')),
                      '---',
                     // array('label'=>'Orphan Records', 'url'=>array('/kegiatan/orphan'),'visible'=>Yii::app()->user->isKasi),
                      array('label'=>'Lock Data', 'url'=>array('/kegiatan/lock'),'visible'=>Yii::app()->user->isKasi),
                      //array('label'=>'Split Database', 'url'=>array('/kegiatan/split')),
                  )),
                  array('label'=>BHtml::icon('link').'Fungsional', 'visible'=>Yii::app()->user->isFungsional, 'htmlOptions'=>array('style'=>'right:0; left:auto;'),'items'=>array(
                      array('label'=>'Rincian Fungsional', 'url'=>array('/personal/fungsional')),
                      '---',
                      array('label'=>'Master Angka Kredit', 'url'=>array('/personal/kredit_master')),
                  )),
                  array('label'=>BHtml::icon('time').'Personal', 'visible'=>!Yii::app()->user->isGuest, 'htmlOptions'=>array('style'=>'right:0; left:auto;'),'items'=>array(
                      array('label'=>'Kegiatan Saya', 'url'=>array('/personal/index')),
                      array('label'=>'Kegiatan Mingguan', 'url'=>array('/personal/mingguan'),'visible'=>Yii::app()->params['mingguan']),
                      array('label'=>'Rincian Realisasi', 'url'=>array('/personal/realisasi')),
                      '---',
//                      array('label'=>'SKP Pegawai', 'url'=>array('/skp/pegawai')),
                      array('label'=>'Usulkan Kegiatan', 'url'=>array('/personal/usulan'), 'visible'=>!Yii::app()->user->id_eselon && substr(Yii::app()->user->id_unitkerja,-1)<8),
//                      array('label'=>'SPJ Kegiatan', 'url'=> array('/spj/operator'),'visible'=>Yii::app()->user->isSpj),
                  	array('label'=>BHtml::icon('search').'Pencarian Kegiatan', 'url'=>array('/personal/search')),
                  )),
                  array('label'=>BHtml::icon('user').Yii::app()->user->name, 'visible'=>!Yii::app()->user->isGuest, 'htmlOptions'=>array('style'=>'right:0; left:auto;'),'items'=>array(
                      array('label'=>'Presensi Saya', 'url'=>array('/personal/presensi')),
                      array('label'=>'Profil Saya', 'url'=>array('/personal/profil')),
                  	  '---',
                  	  array('label'=>BHtml::icon('log-out').'Log Out', 'url'=>array('/site/logout'),'linkOptions'=>array('confirm'=>'Are you sure?')),
                  )),
                  array('label'=>BHtml::icon('lock', array('title'=>'Log In')), 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
            ),
        ),
    ),
)); ?>

	<div class="container">
	<?php if(isset($this->breadcrumbs)): ?>
		<?php $this->widget('BBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
			'encodeLabel'=>false,
			'separator'=>' '.BHtml::icon('chevron-right small'),
		)); ?>
	<?php endif; ?>
	
	<?php echo $content; ?>
	
	<div class="container">
		<footer class="footer" style="font-size:0.8em">
			 BPS Kab. Banyumas
			<span style="float:right"><?php echo Yii::app()->params['versi'];?></span>
		</footer>
	</div>
	
</body>
</html>
<?php 
Yii::app()->clientScript->registerCoreScript('jquery');
Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl.'/js/script.js',CClientScript::POS_END);
