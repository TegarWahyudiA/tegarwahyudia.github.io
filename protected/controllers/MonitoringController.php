<?php

class MonitoringController extends Controller
{
	public $layout='//layouts/column2';

	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	public function accessRules()
	{
		return array(
			array('allow',
				'actions'=>array('mingguan','mingguan_view','bulanan','pegawai','presensi','absensi','ckp','ckp_rekap','ckp_update','rekap_absensi','set_nilai'),
				'expression'=>'$user->isKasi',
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionRekap_absensi($bulan=null,$tahun=null,$excel=null)
	{
		if(!$bulan || $bulan<1 || $bulan>12) $bulan = date('m');
		if(!$tahun) $tahun=date('Y');
/*
		$dbName = Yii::app()->params['db_presensi'];
		if (!file_exists($dbName)) die("Could not find database file.");
		$db = new PDO("odbc:DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=$dbName; Uid=; Pwd=ithITtECH;") or die('error loading pdo');
*/

$db = new PDO(Yii::app()->db->connectionString, Yii::app()->db->username, Yii::app()->db->password);

		if(Yii::app()->user->id_eselon>=3)
			$filter = " AND id_unitkerja LIKE '".substr(Yii::app()->user->id_unitkerja,0,3)."%'";

		$dataProvider = new CActiveDataProvider('MasterPegawai', array(
				'pagination'=>array('pageSize'=>50),
				'criteria'=>array(
					'condition'=>'is_aktif=1' . $filter,
					'order'=>'case when id_eselon=0 then 9 else id_eselon end,id_unitkerja, nama_pegawai',
						//'nama_pegawai',
					)
				));
		if($excel){
			$filename='Rekap Absensi-'.strftime('%B %Y',mktime(0,0,0,$bulan,1,$tahun));	
			header("Content-type: application/vnd-ms-excel");
			header("Content-Disposition: attachment; filename='".$filename.".xls'");
			echo "Rekap Absensi ".Yii::app()->params['unitkerja']."<br>Bulan ".strftime('%B %Y',mktime(0,0,0,$bulan,1,$tahun))."<br><br>Eksport Tanggal: ".date('d-M-Y')."<br>";

			$this->renderPartial('rekap_absensi', array(
				'dataProvider' => $dataProvider,
				'bulan'=>$bulan,
				'tahun'=>$tahun,
				'excel'=>$excel,
				'db'=>$db,
			));
		} else {
			$this->render('rekap_absensi', array(
				'dataProvider' => $dataProvider,
				'bulan'=>$bulan,
				'tahun'=>$tahun,
				'excel'=>$excel,
				'db'=>$db,
			));
		}
	}

	public function actionCkp_rekap($bulan=null,$tahun=null,$excel=false)
	{
		if(!$bulan || $bulan<1 || $bulan>12)
				$bulan = date('m');

		if(!$tahun) $tahun=date('Y');

//		if(Yii::app()->user->id_eselon>=3 || !Yii::app()->user->id_eselon)
			$filter = " AND id_unitkerja LIKE '".substr(Yii::app()->user->id_unitkerja,0,Yii::app()->user->id_eselon)."%'";

		$dataProvider = new CActiveDataProvider('CKP', array(
				'pagination'=>array('pageSize'=>50),
				'criteria'=>array(
					'condition'=>'tahun='.$tahun.' AND bulan=' . $bulan . $filter,
					'with'=>'pegawai',
					'order'=>'case when id_eselon<2 then 9 else id_eselon end, id_unitkerja, nama_pegawai',
					)
				));
if(!$excel){
	foreach(MasterPegawai::model()->findAll(array('condition'=>'is_aktif=1 '.$filter)) as $pegawai){
		$arr_kualitas = array();
		$arr_kuantitas = array();
		$jml_kegiatan = 0;
		foreach (TabelTargetPegawai::model()->findAll(array('with'=>'kegiatan','condition'=>'is_ckp=1 AND year(tgl_mulai)='.$tahun.' AND month(tgl_mulai)='.$bulan.' AND id_pegawai='.$pegawai->id)) as $target) {
			if($target->jml_target){
				$arr_kuantitas[] = $target->jml_realisasi/$target->jml_target * 100;
				$arr_kualitas[] = $target->persen_kualitas;
				$jml_kegiatan++;
			}
		}
		$ckp = CKP::model()->findByAttributes(array('tahun'=>$tahun,'bulan'=>$bulan,'id_pegawai'=>$pegawai->id));
		if(!$ckp){
			$ckp = new CKP();
			$ckp->id_pegawai = $pegawai->id;
			$ckp->tahun = $tahun;
			$ckp->bulan = $bulan;
		}
		$ckp->jml_kegiatan = $jml_kegiatan;
		$ckp->r_kuantitas =  $jml_kegiatan? number_format(array_sum($arr_kuantitas) / $jml_kegiatan,2) : null;
		$ckp->r_kualitas =  $jml_kegiatan? number_format(array_sum($arr_kualitas) / $jml_kegiatan,2) : null;
		$ckp->nilai_ckp = number_format(($ckp->r_kuantitas + $ckp->r_kualitas)/2,2);
		$ckp->save();
	}
}

		$dataProvider = new CActiveDataProvider('CKP', array(
				'pagination'=>array('pageSize'=>50),
				'criteria'=>array(
					'condition'=>'tahun='.$tahun.' AND bulan=' . $bulan . $filter,
					'with'=>'pegawai',
					'order'=>'case when id_eselon<2 then 9 else id_eselon end, id_unitkerja, nama_pegawai',
					)
				));

		if($excel){
			$filename=$bulan.'. Rekap CKP-'.strftime('%b %Y',mktime(0,0,0,$bulan,1,$tahun));	
			header("Content-type: application/vnd-ms-excel");
			header("Content-Disposition: attachment; filename='".$filename.".xls'");

//			$kepala = MasterPegawai::model()->findByPk(Yii::app()->user->id);
//			$unitkerja = Yii::app()->user->isKepala? Yii::app()->params['unitkerja'] : Yii::app()->user->seksi;

			echo "<table border=0><tr><td colspan=5 align=center><b>Rekap CKP ".$unitkerja."</b></td></tr>";
			echo "<tr><td colspan=5 align=center><b>Bulan ".strftime('%B %Y',mktime(0,0,0,$bulan,1,$tahun))."</b></td></tr>";
			echo '<tr><td colspan=5></td></tr></table>';

			$this->renderPartial('ckp_rekap', array(
				'dataProvider' => $dataProvider,
				'bulan'=>$bulan,
				'tahun'=>$tahun,
				'excel'=>true
			)); 

//			echo '<table border=0><tr><td colspan=5></td></tr><tr><td style="width:20px"></td><td></td><td colspan=3 align=center>'.Yii::app()->params['namaibukota'].', '.strftime('%d %B %Y').'</td></tr>';
//			echo '<tr><td></td><td></td><td colspan=3 align=center>Kepala '.$unitkerja.',</td></tr>';		
//			echo '<tr><td colspan=5></td></tr><tr><td colspan=5></td></tr><tr><td colspan=5></td></tr>';
//			echo '<tr><td></td><td></td><td colspan=3 align=center>'.$kepala->nama_pegawai.'</td></tr>';		
//			echo '<tr><td></td><td></td><td colspan=3 align=center>NIP. '.$kepala->nipbaru.'</td></tr></table>';		
		} else {
			$this->render('ckp_rekap', array(
				'dataProvider' => $dataProvider,
				'bulan'=>$bulan,
				'tahun'=>$tahun,
				'excel'=>false
			));
		}
	}

	public function actionCkp($id,$bulan=null,$tahun=null,$download=null)
	{
		if(!$bulan || $bulan<1 || $bulan>12)
			$bulan = date('m');
		if(!$tahun)
			$tahun = date('Y');

		$pegawai = MasterPegawai::model()->findByPk($id);

		if(!Yii::app()->user->id_eselon || (Yii::app()->user->id_eselon && $pegawai->id_eselon && Yii::app()->user->id_eselon > $pegawai->id_eselon) ||
			(Yii::app()->user->id_eselon == $pegawai->id_eselon && Yii::app()->user->id_unitkerja <> $pegawai->id_unitkerja ) || 
			(substr($pegawai->id_unitkerja,0,Yii::app()->user->id_eselon)<>substr(Yii::app()->user->id_unitkerja,0,Yii::app()->user->id_eselon)))
			throw new CHttpException(400,'Anda tidak berhak melihat halaman ini.');

		$filter = ' AND year(tgl_mulai)='.$tahun.' AND month(tgl_mulai)<='.$bulan.' AND month(tgl_selesai)>='.$bulan;
		$target_pegawai = TabelTargetPegawai::model()->findAll(array(
			'with'=>'kegiatan',
			'condition'=>'id_pegawai='.$pegawai->id. $filter.' AND is_ckp=1 and id_flag=0',
			'order'=>'nama_kegiatan ASC'
		));

		if($download=='now'){
			$filename='CKPR-'.str_ireplace(' ', '_', $pegawai->nama_pegawai).'-'.strftime('%B_%Y',mktime(0,0,0,$bulan,1,$tahun));	
			header('Content-type: application/vnd-ms-excel');
			header("Content-Disposition: attachment; filename=".$filename.".xls");

			$this->renderPartial('ckp_realisasi', array(
				'bulan'=>$bulan,
				'tahun'=>$tahun,
				'pegawai'=>$pegawai,
				'target_pegawai'=>$target_pegawai,
				'link'=>false,
			));
		} else {
			$this->render('ckp_realisasi', array(
				'bulan'=>$bulan,
				'tahun'=>$tahun,
				'pegawai'=>$pegawai,
				'target_pegawai'=>$target_pegawai,
				'link'=>true,
			));
		}
	}

	public function actionSet_nilai($id)
	{	
		$pegawai = MasterPegawai::model()->findByPk($id);
		if($pegawai && (substr($pegawai->id_unitkerja,0,Yii::app()->user->id_eselon) == substr(Yii::app()->user->id_unitkerja,0,Yii::app()->user->id_eselon))){
			$target = TabelTargetPegawai::model()->findByAttributes(array('id_pegawai'=>$pegawai->id, 'id_kegiatan'=>(int)$_POST['key']));
			if($target){
				$target->persen_kualitas = (int) $_POST['nilai'];
				if(!$target->hasErrors() && $target->save())
					echo $target->persen_kualitas;
				else
					echo 'GAGAL menyimpan nilai';
			} else
				echo 'Data not found';
		} else 
			echo 'Insufficent privilege';
	}

	public function actionPegawai($id,$bulan=null,$tahun=null)
	{
		if(!$bulan || $bulan<1 || $bulan>12)
			$bulan = date('m');
		if(!$tahun)
			$tahun = date('Y');

		$pegawai = MasterPegawai::model()->findByPk($id);

		if(!Yii::app()->user->id_eselon || (Yii::app()->user->id_eselon && $pegawai->id_eselon && Yii::app()->user->id_eselon > $pegawai->id_eselon) ||
			(Yii::app()->user->id_eselon == $pegawai->id_eselon && Yii::app()->user->id_unitkerja <> $pegawai->id_unitkerja ))
			throw new CHttpException(400,'Anda tidak berhak melihat halaman ini.');

		$filter = ' AND year(tgl_mulai)='.$tahun.' AND month(tgl_mulai)<='.$bulan.' AND month(tgl_selesai)>='.$bulan;
		$target_pegawai = TabelTargetPegawai::model()->findAll(array(
			'with'=>'kegiatan',
			'condition'=>'id_pegawai='.$pegawai->id. $filter.' AND is_ckp=1 and id_flag=0',
			'order'=>'nama_kegiatan ASC'
		));

		$this->render('pegawai', array(
			'bulan'=>$bulan,
			'tahun'=>$tahun,
			'pegawai'=>$pegawai,
			'target_pegawai'=>$target_pegawai,
		));
	}

	public function actionCkp_update($id)
	{
		$model = TabelTargetPegawai::model()->findByPk($id);
		if(!$model || (!Yii::app()->user->isKepala && Yii::app()->user->id_unitkerja<>$model->kegiatan->id_unitkerja) || $model->kegiatan->is_lock)
			throw new CHttpException(400,'Anda tidak berhak melihat halaman ini.');

		$this->render('ckp_update', array(
			'model'=>$model
		));
	}

	public function actionAbsensi($id=null, $bulan=null,$tahun=null)
	{
		if((!$bulan || $bulan<1 || $bulan>12) & $bulan<>'all')
			$bulan = date('m');
		if(!$tahun) 
			$tahun=date('Y');

/*
		$dbName = Yii::app()->params['db_presensi'];
		if (!file_exists($dbName)) 
			throw new CHttpException(404,'Database not found');
		$db = new PDO("odbc:DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=$dbName; Uid=; Pwd=ithITtECH;") or die('error loading pdo');
*/


$db = new PDO(Yii::app()->db->connectionString, Yii::app()->db->username, Yii::app()->db->password);

		$this->render('absensi', array(
			'bulan'=>$bulan,
			'tahun'=>$tahun,
			'model'=>MasterPegawai::model()->findByPk($id),
			'db'=>$db,
		));
	}	

	public function actionPresensi($mingguke=null)
	{
		if(!$mingguke || $mingguke<1 || $mingguke>53)
			$mingguke = date('W');

		$dataProvider = new CActiveDataProvider('MasterPegawai', array(
			'criteria'=>array(
				'condition'=>'id_unitkerja<=9287 AND is_aktif=1',
				'order'=>'case when id_eselon=0 then 9 else id_eselon end,id_unitkerja, nama_pegawai',
					//'nama_pegawai',
				),
			'pagination'=>array(
				'pageSize'=>100,
			)));

/*
		$dbName = Yii::app()->params['db_presensi'];
		if (!file_exists($dbName))
			throw new CHttpException(404,'Database not found');
		$db = new PDO("odbc:DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=$dbName; Uid=; Pwd=ithITtECH;") or die('error loading pdo');
*/


$db = new PDO(Yii::app()->db->connectionString, Yii::app()->db->username, Yii::app()->db->password);

		$this->render('presensi', array(
			'mingguke'=>$mingguke,
			'dataProvider'=>$dataProvider,
			'db'=>$db,
		));
	}	

	public function actionMingguan($mingguke=null)
	{
		if(!$mingguke || $mingguke<1 || $mingguke>53)
			$mingguke = date('W');

		if(Yii::app()->user->id_eselon>=3 || !Yii::app()->user->id_eselon)
			$filter = " AND id_unitkerja LIKE '".substr(Yii::app()->user->id_unitkerja,0,3)."%'";

		$dataProvider = new CActiveDataProvider('MasterPegawai', array(
			'criteria'=>array(
				'condition'=>'is_aktif=1 '.$filter,
				'order'=>'case when id_eselon=0 then 9 else id_eselon end,id_unitkerja, nama_pegawai',
					//'nama_pegawai',
				),
			'pagination'=>array(
				'pageSize'=>50,
			)));

		$arr_data = CHtml::listData(Yii::app()->db->createCommand("SELECT id_pegawai, COUNT(*) as c FROM ".TabelTargetMingguan::model()->tableName()." WHERE mingguke=".$mingguke.' GROUP BY id_pegawai')->queryAll(),'id_pegawai','c');
		
		$this->render('mingguan', array(
			'dataProvider'=>$dataProvider,
			'arr_data'=>$arr_data,
			'mingguke'=>$mingguke,
		));
	}

	public function actionMingguan_view($pegawai,$mingguke)
	{
		$dataProvider = new CActiveDataProvider('TabelTargetMingguan', array(
			'criteria'=>array(
				'condition'=>'id_pegawai='.$pegawai.' AND mingguke='.$mingguke.' AND jml_target>0',
				),
			'pagination'=>array(
				'pageSize'=>25,
				),
			));

		$this->render('mingguan_view',array(
			'pegawai'=>MasterPegawai::model()->findByPk($pegawai),
			'mingguke'=>$mingguke,
			'dataProvider'=>$dataProvider,
			));
	}

	public function actionBulanan($tahun=null,$bulan=null)
	{
		if((!$bulan || $bulan<1 || $bulan>12) & $bulan<>'all')
			$bulan = date('m');
		if(!$tahun) 
			$tahun=date('Y');

		$data = TabelTargetPegawai::model()->findAll(array(
			'with'=>'kegiatan',
			'condition'=>'year(tgl_mulai)='.$tahun.' AND month(tgl_mulai)='.$bulan.' AND id_unitkerja LIKE \''.substr(Yii::app()->user->id_unitkerja,0,3).'%\'',
			));

//echo '<pre>'; print_r($data); exit();

		$this->render('bulanan', array(
			'tahun'=>$tahun,
			'bulan'=>$bulan,
			'data'=>$data
		));
	}
}