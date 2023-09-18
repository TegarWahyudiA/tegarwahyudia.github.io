<?php

class AdminController extends Controller
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
/*			array('allow', 
				'actions'=>array('kredit_search','kredit_get'),
				'users'=>array('@'),
			),
*/			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('satuan','satuan_add','satuan_update','satuan_delete','satuan_auto'),
				'expression'=>'$user->isKasi',
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('pegawai','pegawai_add','pegawai_update','pegawai_delete','pegawai_import','pegawai_template',
					'kredit','kredit_view','proses_presensi','ckp_rekap'),
				'expression'=>'$user->isAdmin',
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionPegawai($filter=null)
	{ 
		if($filter=='mitra')
			$condition = 'id_unitkerja>9287';
		elseif($filter=='nonaktif')
			$condition = 'is_aktif=0';
		else
			$condition = 'is_aktif=1 and id_unitkerja<=9287';


		$dataProvider = new CActiveDataProvider('MasterPegawai', array(
			'pagination'=>array('pageSize'=>20),
			'criteria'=>array('condition'=>$condition, 'order'=>'case when id_eselon<2 then 9 else id_eselon end, id_unitkerja, nama_pegawai')
		));
		$this->render('pegawai', array(
			'dataProvider'=>$dataProvider,
			'filter'=>$filter
		));

/*		$model=new MasterPegawai('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['MasterPegawai']))
			$model->attributes=$_GET['MasterPegawai'];

		$this->render('pegawai',array(
			'model'=>$model,
		));
*/	}

	public function actionPegawai_add()
	{
		$model=new MasterPegawai;

		if(isset($_POST['MasterPegawai']))
		{
			$model->attributes=$_POST['MasterPegawai'];
			if($model->save())
				if(isset($_POST['returnUrl']))
					$this->redirect($_POST['returnUrl']);
				else
					$this->redirect(array('pegawai'));
		}

		$this->render('pegawai_add',array(
			'model'=>$model,
		));
	}

	public function actionPegawai_update($id)
	{
		$msg=null;
		$model=$this->loadPegawai($id);
		
		if(isset($_POST['MasterPegawai']))
		{
			$model->attributes=$_POST['MasterPegawai'];
			
			if($model->save())
				$this->redirect(array('pegawai'));
		}
		
		if(isset($_POST['password']) && $_POST['password']<>'' && isset($_POST['konfirmasi']) && $_POST['konfirmasi']<>'' && $_POST['password']== $_POST['konfirmasi']){

			$model->salt = uniqid(mt_rand(), true);
			$model->hash = sha1($model->salt.$_POST['password']);

			if(!$model->hasErrors() && $model->save())
				$msg = 'Password berhasil diganti';
			else
				$msg = 'Password gagal diganti';				
		}

		$this->render('pegawai_update',array(
			'model'=>$model,
			'msg'=>$msg,
		));
	}

	public function actionPegawai_import()
	{
		$arr_data = array();
		if($file = CUploadedFile::getInstanceByName('xlf')){
			require_once('protected/extensions/excel-reader/excel_reader2.php');
			$data = new Spreadsheet_Excel_Reader($file->tempName);
			
			$baris = 3;
			do {
				$model = new MasterPegawai;

				$model->nama_pegawai = $data->value($baris, 1);
				$model->nip = $data->value($baris, 2);
				$model->nipbaru = $data->value($baris, 3);
				$model->id_golongan = $data->value($baris, 4);
				$model->id_unitkerja = $data->value($baris, 5);
				$model->id_eselon = $data->value($baris, 6);
				$model->id_fungsional = $data->value($baris, 7);
				$model->username = $data->value($baris, 8);
				$model->hash = $data->value($baris, 9);
				$model->is_admin = $data->value($baris, 10)=='1'? '1':'0';
				
				$model->id_wilayah = $data->value($baris, 11);
				$model->id_presensi = $data->value($baris, 12);

//				if(substr($model->nip,0,4)=='3400')
//					$model->nip = (int)substr($model->nip, -5);

				$model->salt = uniqid(mt_rand(), true);
				$model->hash = sha1($model->salt.$model->hash);

				if(!$model->hasErrors() && $model->save()){
					$arr = array();
					for($kolom=1; $kolom<=2; $kolom++){
						$arr[] = $data->value($baris, $kolom);
					}
					$arr_data[] = $arr;
				} else {
					$arr_data[] = $model->errors;
				}
				
				$baris++;
			} while($data->value($baris,1)<>'');
		}
		
		$this->render('pegawai_import', array(
			'data'=>$arr_data,
		));
	}
	
	public function actionPegawai_template()
	{
		$filename="template_pegawai.xls";		
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=".$filename);
		readfile("protected/template/".$filename);
		exit();
	}
	
	public function actionPegawai_delete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			$this->loadPegawai($id)->delete();
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('pegawai'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	public function loadPegawai($id)
	{
		$model=MasterPegawai::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	public function actionSatuan()
	{
		$model=new MasterSatuan('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['MasterSatuan']))
			$model->attributes=$_GET['MasterSatuan'];

		$this->render('satuan',array(
			'model'=>$model,
		));
	}

	public function actionSatuan_add()
	{
		$model=new MasterSatuan;

		if(isset($_POST['MasterSatuan']))
		{
			$model->attributes=$_POST['MasterSatuan'];
			if($model->save())
				if(isset($_POST['returnUrl']))
					$this->redirect($_POST['returnUrl']);
				else
					$this->redirect(array('satuan'));
		}

		$this->render('satuan_add',array(
			'model'=>$model,
		));
	}

	public function actionSatuan_update($id)
	{
		$model=$this->loadSatuan($id);

		if(isset($_POST['MasterSatuan']))
		{
			$model->attributes=$_POST['MasterSatuan'];
			if($model->save())
				$this->redirect(array('satuan'));
		}

		$this->render('satuan_update',array(
			'model'=>$model,
		));
	}

	public function actionSatuan_delete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			$this->loadSatuan($id)->delete();

			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	public function actionSatuan_auto($term)
	{
//		if(!Yii::app()->request->isAjaxRequest || strlen($term)<3) exit();
		
		$arr = array();
		foreach(MasterSatuan::model()->findAll(array(
			'condition'=>'nama_satuan like \'%'.$term.'%\'',
			'order'=>'nama_satuan')) as $model){
			$arr[] = array(
				'label'=>$model->nama_satuan,
				'id'=>$model->id
			);
		}
		echo CJSON::encode($arr);
	}
	
	public function loadSatuan($id)
	{
		$model=MasterSatuan::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	public function loadKredit($id)
	{
		$model=MasterKredit::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	public function actionProses_presensi()
	{
		if(Yii::app()->request->isPostRequest){
			$tgl_mulai = $_POST['tgl_mulai'];
			$tgl_selesai = $_POST['tgl_selesai'];
			$jam_kerja = (int) $_POST['jam_kerja'];

			if(strtotime($tgl_mulai) && strtotime($tgl_selesai) && ($jam_kerja==1 || $jam_kerja==2)){
				if(strtotime($tgl_mulai) > strtotime($tgl_selesai)){
					list($tgl_mulai,$tgl_selesai) = array($tgl_selesai,$tgl_mulai);
				}

				$data[]="sdate=".$tgl_mulai;
				$data[]="edate=".$tgl_selesai;
				$data[]='period=1';

				for ($i=1;$i<200;$i++) {
				        $data[]="uid={$i}";
				}

				$process = curl_init();
				$options = array(
					CURLOPT_URL => "http://".Yii::app()->params['ip_presensi']."/form/Download",
					CURLOPT_HEADER => false,
					CURLOPT_POSTFIELDS => implode('&',$data),
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_FOLLOWLOCATION => TRUE,
					CURLOPT_POST => TRUE,
					CURLOPT_BINARYTRANSFER => TRUE
				);
				curl_setopt_array($process, $options);
				$result = curl_exec($process); 
				curl_close($process); 

				Yii::app()->db->createCommand()->truncateTable('a_log');
				
				if($rows = explode("\n", $result)) {
					foreach($rows as $row){
						if(($cols = explode("\t", $row)) && count($cols)==5){
							$id = $cols[0];	if(strlen($id)==8) $id='0'.$id;
							$nama = $cols[1];
							$tanggal = substr($cols[2],0,10);
							$jam = substr($cols[2],-8);
							$tombol = $cols[4];
							Yii::app()->db->createCommand()->insert('a_log', array('id_presensi'=>$id, 'tanggal'=>$tanggal, 'jam'=>$jam, 'tombol'=>$tombol));
						}
					}

					$msg .= Yii::app()->db->createCommand()->select('count(*)')->from('a_log')->queryScalar()." sidik jari.\n";

					$arr_libur = array();
					foreach(Yii::app()->db->createCommand()->select("*")->from("a_calendarholiday")->where("CalendarHolidayDate BETWEEN '".$tgl_mulai."' AND '".$tgl_selesai."'")->order("CalendarHolidayDate")->queryAll() as $row) {
						$arr_libur[] = $row['CalendarHolidayDate'];
					}

					$i = 0;
					foreach(Yii::app()->db->createCommand()->select("tanggal, id_presensi, case when min(jam)<'12:00' then min(jam) else null end as datang, case when max(jam)>='12:00' then max(jam) else null end as pulang")->from("a_log")->where("tanggal BETWEEN '".$tgl_mulai."' AND '".$tgl_selesai."'")->group("tanggal, id_presensi")->order("tanggal, id_presensi")->queryAll() as $row) {

						$hari = date('N', strtotime($row['tanggal']));
						if(in_array($row['tanggal'], $arr_libur) || $hari>=6)
							continue;

						$LateIn = null;
						$EarlyOut = null;

						if($jam_kerja==1){
							$jam_datang = '07:30:59'; $jam_pulang = '16:00'; $jam_pulang_2 = '16:30';
						} elseif($jam_kerja==2){
							$jam_datang = '08:00:59'; $jam_pulang = '15:30'; $jam_pulang_2 = '16:00';
						}

						if($hari>=1 && $hari<=5) {
							if(is_null($row['datang']))	$LateIn = (strtotime('12:00')-strtotime($jam_datang))/60;
							elseif($row['datang']>$jam_datang) $LateIn = (strtotime($row['datang'])-strtotime($jam_datang))/60;
						} 
						if($hari>=1 && $hari<=4){
							if(is_null($row['pulang'])) $EarlyOut = (strtotime($jam_pulang)-strtotime('12:00'))/60;
							elseif($row['pulang']<$jam_pulang) $EarlyOut = (strtotime($jam_pulang)-strtotime($row['pulang']))/60;
						} elseif($hari==5){
							if(is_null($row['pulang'])) $EarlyOut = (strtotime($jam_pulang_2)-strtotime('12:00'))/60;
							elseif($row['pulang']<$jam_pulang_2) $EarlyOut = (strtotime($jam_pulang_2)-strtotime($row['pulang']))/60;
						}


						if($log = Yii::app()->db->createCommand()->select('*')->from('a_personalcalendar')->where("FingerPrintID='".$row['id_presensi']."' AND PersonalCalendarDate='".$row['tanggal']."'")->queryRow()){
							Yii::app()->db->createCommand()->update('a_personalcalendar', array('TimeCome'=>$row['datang'], 'TimeHome'=>$row['pulang'], 'LateIn'=>$LateIn, 'EarlyOut'=>$EarlyOut),"FingerPrintID='".$row['id_presensi']."' AND PersonalCalendarDate='".$row['tanggal']."'");
						} else {
							Yii::app()->db->createCommand()->insert('a_personalcalendar', array('FingerPrintID'=>$row['id_presensi'], 'PersonalCalendarDate'=>$row['tanggal'], 'TimeCome'=>$row['datang'], 'TimeHome'=>$row['pulang'], 'LateIn'=>$LateIn, 'EarlyOut'=>$EarlyOut));
						}

						$i++;
					}

					$msg .= $i." Orang-Hari.\n";

				}
			}
		}

		$this->render('proses_presensi', array(
			'tgl_mulai'=>$tgl_mulai,
			'tgl_selesai'=>$tgl_selesai,
			'jam_kerja'=>$jam_kerja,
			'msg'=>$msg,
		));
	}

	public function actionKredit()
	{
		$model=new MasterKredit('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['MasterKredit']))
			$model->attributes=$_GET['MasterKredit'];
		
		if(isset($_GET['search']))
			$model->kegiatan=$_GET['search'];

		$this->render('kredit',array(
			'model'=>$model,
		));
	}

	public function actionKredit_view($id)
	{
		$this->render('kredit_view', array(
			'model'=>$this->loadKredit($id)
		));
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
}
