<?php

class PresensiController extends Controller
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
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
//				array('proses','absen','absen_create','absen_update'),
				'expression'=>'$user->isAdmin',
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($mingguke=null, $tahun=null)
	{
		if(!$mingguke || $mingguke<1 || $mingguke>53)
			$mingguke = date('W');
		if(!$tahun) $tahun=date('Y');

		$dataProvider = new CActiveDataProvider('MasterPegawai', array(
			'criteria'=>array(
				'condition'=>'id_unitkerja<=9287 AND is_aktif=1',
				'order'=>'case when id_eselon=0 then 9 else id_eselon end,id_unitkerja, nama_pegawai',
					//'nama_pegawai',
				),
			'pagination'=>array(
				'pageSize'=>150,
			)));

		$db = new PDO(Yii::app()->db->connectionString, Yii::app()->db->username, Yii::app()->db->password);

		$this->render('index', array(
			'tahun'=>$tahun,
			'mingguke'=>$mingguke,
			'dataProvider'=>$dataProvider,
			'db'=>$db,
		));
	}	

	public function actionAbsen($tahun=null, $bulan=null)
	{
		if(!$bulan || $bulan<1 || $bulan>12) $bulan = date('m');
		if(!$tahun) $tahun = date('Y');

		$dataProvider=new CActiveDataProvider('CalendarPersonal', array(
			'pagination'=>array('pageSize'=>20),
			'criteria'=>array(
				'with'=>array('pegawai'),
				'condition'=>"year(PersonalCalendarDate)=".$tahun." AND month(PersonalCalendarDate)=".$bulan." AND PersonalCalendarStatus<>''",
				'order'=>'PersonalCalendarDate ASC, pegawai.nama_pegawai'
				),
			));
		$this->render('absen',array(
			'dataProvider'=>$dataProvider,
			'tahun'=>$tahun,
			'bulan'=>$bulan,
		));
	}

	public function actionAbsen_pegawai($id, $tahun)
	{
		if(!$tahun) $tahun=date('Y');

		$model = MasterPegawai::model()->findByPk($id);

		$dataProvider = new CActiveDataProvider('CalendarPersonal', array(
			'pagination'=>array('pageSize'=>20),
			'criteria'=>array(
				'condition'=>'FingerPrintID='.$model->id_presensi.' AND PersonalCalendarDate LIKE \''.$tahun.'%\' AND PersonalCalendarReason<>\'\'',
				'order'=>'PersonalCalendarDate'
				)
		));

		$this->render('absen_pegawai', array(
			'model'=>$model,
			'dataProvider'=>$dataProvider,
			'tahun'=>$tahun
		));

	}

	public function actionAbsen_create()
	{
		$model = new CalendarPersonal();

		if(isset($_POST['CalendarPersonal']))
		{
			$model->attributes=$_POST['CalendarPersonal'];
			if($personal = CalendarPersonal::model()->findByAttributes(array('FingerPrintID'=>$model->FingerPrintID,'PersonalCalendarDate'=>$model->PersonalCalendarDate))){
				$personal->PersonalCalendarStatus = $model->PersonalCalendarStatus;
				$personal->PersonalCalendarReason = $model->PersonalCalendarReason;
				$model = $personal;
			}
			
			if($model->save()){
				$msg = 'Ditambahkan : '.$model->pegawai->nama_pegawai.' -> '.$model->status->PersonalCalendarStatus.' '.strftime('%d %b %Y',strtotime($model->PersonalCalendarDate));
				$model = new CalendarPersonal();
				$model->FingerPrintID = $_POST['CalendarPersonal']['FingerPrintID'];
				$model->PersonalCalendarDate = $_POST['CalendarPersonal']['PersonalCalendarDate'];
				$model->PersonalCalendarStatus = $_POST['CalendarPersonal']['PersonalCalendarStatus'];
				$model->PersonalCalendarReason = $_POST['CalendarPersonal']['PersonalCalendarReason'];
			}
		}

		$this->render('absen_create', array(
			'model'=>$model,
			'msg'=>isset($msg)? $msg : null,
		));
	}

	public function actionAbsen_update($id,$tanggal)
	{
		$model = CalendarPersonal::model()->find(array(
			'with'=>'pegawai',
			'condition'=>'pegawai.id='.$id.' AND PersonalCalendarDate=\''.$tanggal.'\''
			));

		if(!$model)
			$this->redirect(array('absen'));

		if(isset($_POST['CalendarPersonal']))
		{
			$model->PersonalCalendarStatus=$_POST['CalendarPersonal']['PersonalCalendarStatus'];
			$model->PersonalCalendarReason=$_POST['CalendarPersonal']['PersonalCalendarReason'];
			
			if($model->save()){
				$this->redirect(array('absen'));
			}
		}

		$this->render('absen_update', array(
			'model'=>$model,
		));
	}

	public function actionProses()
	{
		$tgl_mulai = null;
		$tgl_selesai = null;
		$jam_kerja = null;
		$msg = null;

		if(Yii::app()->request->isPostRequest){
			$tgl_mulai = $_POST['tgl_mulai'];
			$tgl_selesai = $_POST['tgl_selesai'];
			$jam_kerja = (int) $_POST['jam_kerja'];

			if(strtotime($tgl_mulai) && strtotime($tgl_selesai) && ($jam_kerja==1 || $jam_kerja==2)){
				if(strtotime($tgl_mulai) > strtotime($tgl_selesai)){
					list($tgl_mulai,$tgl_selesai) = array($tgl_selesai,$tgl_mulai);
				}

				$msg = self::proses_absen($tgl_mulai,$tgl_selesai,$jam_kerja);
			}
		}

		$this->render('proses', array(
			'tgl_mulai'=>$tgl_mulai,
			'tgl_selesai'=>$tgl_selesai,
			'jam_kerja'=>$jam_kerja,
			'msg'=>$msg,
		));
	}


	private static function proses_absen($tgl_mulai,$tgl_selesai,$jam_kerja)
	{
		$msg = null;
		$mitra = array();
		$arr_libur = array();

		if($rows = Yii::app()->tad->getArray(null, $tgl_mulai, $tgl_selesai, Yii::app()->params['ip_presensi'])) {

			foreach(MasterPegawai::model()->findAll(array('condition'=>'is_aktif=1 and id_presensi is not null')) as $pegawai){
				$date = $tgl_mulai;	
				if($pegawai->id_unitkerja>9287 && $pegawai->id_presensi<>'') 
					$mitra[] = $pegawai->id_presensi;
				if(strlen($pegawai->id_presensi)>1)
					while (strtotime($date) <= strtotime($tgl_selesai)) {
						Yii::app()->db->createCommand("insert ignore into ".CalendarPersonal::model()->tableName()." (FingerPrintID,PersonalCalendarDate) values ('".$pegawai->id_presensi."','".$date."')")->query();
	            		$date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
					}
			}

			foreach($rows as $f){
				$id = $f['PIN'];
				if(strlen($id)==8) $id='0'.$id;
				$tanggal = substr($f['DateTime'],0,10);
				$jam = substr($f['DateTime'],-8);
				$tombol = $f['Status'];

				Yii::app()->db->createCommand("insert ignore into a_log (id_presensi, tanggal, jam, tombol) values('".$id."','".$tanggal."','".$jam."','".$tombol."')")->query();

				if($tombol==2 || $tombol==3)
					Yii::app()->db->createCommand("insert ignore into a_keluar (id_presensi, tanggal, jam, tombol) values('".$id."','".$tanggal."','".$jam."','".$tombol."')")->query();
			}

			foreach(Yii::app()->db->createCommand()->select("*")->from("a_calendarholiday")->where("CalendarHolidayDate BETWEEN '".$tgl_mulai."' AND '".$tgl_selesai."'")->order("CalendarHolidayDate")->queryAll() as $row) {
				$arr_libur[] = $row['CalendarHolidayDate'];
			}

			$i = 0;
			foreach(Yii::app()->db->createCommand()->select("tanggal, id_presensi, case when min(jam)<'12:00' then min(jam) else null end as datang, case when max(jam)>='12:00' then max(jam) else null end as pulang")->from("a_log")->where("tanggal BETWEEN '".$tgl_mulai."' AND '".$tgl_selesai."'")->group("tanggal, id_presensi")->order("tanggal, id_presensi")->queryAll() as $row) {

				$hari = date('N', strtotime($row['tanggal']));
				$kode = Yii::app()->db->createCommand()->select('PersonalCalendarStatus')->from('a_personalcalendar')->where("FingerPrintID='".$row['id_presensi']."' AND PersonalCalendarDate='".$row['tanggal']."'")->queryScalar();

				if((in_array($row['tanggal'], $arr_libur) || $hari>=6) && !in_array($row['id_presensi'], $mitra) && ($kode<>'99'))
					continue;

				$LateIn = null;
				$EarlyOut = null;

				if($jam_kerja==1){
					$jam_datang = '07:30:59'; $jam_pulang = '16:00'; $jam_pulang_2 = '16:30';
				} elseif($jam_kerja==2){
					$jam_datang = '08:00:59'; $jam_pulang = '15:00'; $jam_pulang_2 = '15:30';
				}

				if($hari>=1 && $hari<=5) {
					if(is_null($row['datang']))	$LateIn = (strtotime('12:00')-strtotime($jam_datang))/60;
					elseif($row['datang']>$jam_datang) $LateIn = (strtotime($row['datang'])-strtotime($jam_datang))/60+1;
				} 
				if($hari>=1 && $hari<=4){
					if(is_null($row['pulang'])) $EarlyOut = (strtotime($jam_pulang)-strtotime('12:00'))/60;
					elseif($row['pulang']<$jam_pulang) $EarlyOut = (strtotime($jam_pulang)-strtotime($row['pulang']))/60;
				} elseif($hari==5){
					if(is_null($row['pulang'])) $EarlyOut = (strtotime($jam_pulang_2)-strtotime('12:00'))/60;
					elseif($row['pulang']<$jam_pulang_2) $EarlyOut = (strtotime($jam_pulang_2)-strtotime($row['pulang']))/60;
				}


				if($log = Yii::app()->db->createCommand()->select('*')->from('a_personalcalendar')->where("FingerPrintID='".$row['id_presensi']."' AND PersonalCalendarDate='".$row['tanggal']."'")->queryRow()){

					if(substr($row['pulang'],0,5) <> substr($row['datang'],0,5))
						Yii::app()->db->createCommand()->update('a_personalcalendar', array('TimeCome'=>substr($row['datang'],0,5), 'TimeHome'=>substr($row['pulang'],0,5), 'LateIn'=>$LateIn, 'EarlyOut'=>$EarlyOut),"FingerPrintID='".$row['id_presensi']."' AND PersonalCalendarDate='".$row['tanggal']."'");
					else
						Yii::app()->db->createCommand()->update('a_personalcalendar', array('TimeCome'=>substr($row['datang'],0,5), 'LateIn'=>$LateIn, 'EarlyOut'=>$EarlyOut),"FingerPrintID='".$row['id_presensi']."' AND PersonalCalendarDate='".$row['tanggal']."'");

				} else {
					if(substr($row['pulang'],0,5) <> substr($row['datang'],0,5))
						Yii::app()->db->createCommand()->insert('a_personalcalendar', array('FingerPrintID'=>$row['id_presensi'], 'PersonalCalendarDate'=>$row['tanggal'], 'TimeCome'=>substr($row['datang'],0,5), 'TimeHome'=>substr($row['pulang'],0,5), 'LateIn'=>$LateIn, 'EarlyOut'=>$EarlyOut));
					else
						Yii::app()->db->createCommand()->insert('a_personalcalendar', array('FingerPrintID'=>$row['id_presensi'], 'PersonalCalendarDate'=>$row['tanggal'], 'TimeCome'=>substr($row['datang'],0,5), 'LateIn'=>$LateIn, 'EarlyOut'=>$EarlyOut));
				}

				$i++;
			}

			$msg .= $i." Orang-Hari.\n";

			$arr_jam = array('07:35', '08:05', '15:35', '16:05', '16:35');
			for($i=0; $i<count($arr_jam); $i++){
				if(strtotime($arr_jam[$i])>time()){
					$jam = $arr_jam[$i];
					break;
				} 
				if($i>=count($arr_jam)-1)
					$jam = $arr_jam[0];
			}

			$cfg_file = 'protected/runtime/presensi.cfg';
			file_put_contents($cfg_file,"tanggal=".date('Y-m-d')."\njam=".$jam."\njam_kerja=".$jam_kerja);			
		}

		return $msg;	
	}

	public function actionProses_pegawai($pin,$mulai,$sampai=null)
	{
		$msg = null;
		$arr_libur = array();
		$mitra = array();

		if(!$sampai) $sampai = date('Y-m-d');
		$tgl_mulai = $mulai;
		$tgl_selesai = $sampai;
		$jam_kerja = 1;

		if($rows = Yii::app()->tad->getArray($pin, $tgl_mulai, $tgl_selesai, Yii::app()->params['ip_presensi'])) {

			foreach(MasterPegawai::model()->findAll(array('condition'=>"is_aktif=1 and id_presensi='".$pin."'")) as $pegawai){
				$date = $tgl_mulai;	
				if($pegawai->id_unitkerja>9287 && $pegawai->id_presensi<>'') 
					$mitra[] = $pegawai->id_presensi;
				if(strlen($pegawai->id_presensi)>1)
					while (strtotime($date) <= strtotime($tgl_selesai)) {
						Yii::app()->db->createCommand("insert ignore into ".CalendarPersonal::model()->tableName()." (FingerPrintID,PersonalCalendarDate) values ('".$pegawai->id_presensi."','".$date."')")->query();
	            		$date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
					}
			}

			foreach($rows as $f){
				$id = $f['PIN'];
				if(strlen($id)==8) $id='0'.$id;
				$tanggal = substr($f['DateTime'],0,10);
				$jam = substr($f['DateTime'],-8);
				$tombol = $f['Status'];

				Yii::app()->db->createCommand("insert ignore into a_log (id_presensi, tanggal, jam, tombol) values('".$id."','".$tanggal."','".$jam."','".$tombol."')")->query();

				if($tombol==2 || $tombol==3)
					Yii::app()->db->createCommand("insert ignore into a_keluar (id_presensi, tanggal, jam, tombol) values('".$id."','".$tanggal."','".$jam."','".$tombol."')")->query();
			}

			foreach(Yii::app()->db->createCommand()->select("*")->from("a_calendarholiday")->where("CalendarHolidayDate BETWEEN '".$tgl_mulai."' AND '".$tgl_selesai."'")->order("CalendarHolidayDate")->queryAll() as $row) {
				$arr_libur[] = $row['CalendarHolidayDate'];
			}

			$i = 0;
			foreach(Yii::app()->db->createCommand()->select("tanggal, id_presensi, case when min(jam)<'12:00' then min(jam) else null end as datang, case when max(jam)>='12:00' then max(jam) else null end as pulang")->from("a_log")->where("tanggal BETWEEN '".$tgl_mulai."' AND '".$tgl_selesai."' and id_presensi='".$pin."'")->group("tanggal, id_presensi")->order("tanggal, id_presensi")->queryAll() as $row) {

				$hari = date('N', strtotime($row['tanggal']));
				$kode = Yii::app()->db->createCommand()->select('PersonalCalendarStatus')->from('a_personalcalendar')->where("FingerPrintID='".$row['id_presensi']."' AND PersonalCalendarDate='".$row['tanggal']."'")->queryScalar();

				if((in_array($row['tanggal'], $arr_libur) || $hari>=6) && !in_array($row['id_presensi'], $mitra) && ($kode<>'99'))
					continue;

				$LateIn = null;
				$EarlyOut = null;

				if($jam_kerja==1){
					$jam_datang = '07:30:59'; $jam_pulang = '16:00'; $jam_pulang_2 = '16:30';
				} elseif($jam_kerja==2){
					$jam_datang = '08:00:59'; $jam_pulang = '15:00'; $jam_pulang_2 = '15:30';
				}

				if($hari>=1 && $hari<=5) {
					if(is_null($row['datang']))	$LateIn = (strtotime('12:00')-strtotime($jam_datang))/60;
					elseif($row['datang']>$jam_datang) $LateIn = (strtotime($row['datang'])-strtotime($jam_datang))/60+1;
				} 
				if($hari>=1 && $hari<=4){
					if(is_null($row['pulang'])) $EarlyOut = (strtotime($jam_pulang)-strtotime('12:00'))/60;
					elseif($row['pulang']<$jam_pulang) $EarlyOut = (strtotime($jam_pulang)-strtotime($row['pulang']))/60;
				} elseif($hari==5){
					if(is_null($row['pulang'])) $EarlyOut = (strtotime($jam_pulang_2)-strtotime('12:00'))/60;
					elseif($row['pulang']<$jam_pulang_2) $EarlyOut = (strtotime($jam_pulang_2)-strtotime($row['pulang']))/60;
				}


				if($log = Yii::app()->db->createCommand()->select('*')->from('a_personalcalendar')->where("FingerPrintID='".$row['id_presensi']."' AND PersonalCalendarDate='".$row['tanggal']."'")->queryRow()){

					if(substr($row['pulang'],0,5) <> substr($row['datang'],0,5))
						Yii::app()->db->createCommand()->update('a_personalcalendar', array('TimeCome'=>substr($row['datang'],0,5), 'TimeHome'=>substr($row['pulang'],0,5), 'LateIn'=>$LateIn, 'EarlyOut'=>$EarlyOut),"FingerPrintID='".$row['id_presensi']."' AND PersonalCalendarDate='".$row['tanggal']."'");
					else
						Yii::app()->db->createCommand()->update('a_personalcalendar', array('TimeCome'=>substr($row['datang'],0,5), 'LateIn'=>$LateIn, 'EarlyOut'=>$EarlyOut),"FingerPrintID='".$row['id_presensi']."' AND PersonalCalendarDate='".$row['tanggal']."'");

				} else {
					if(substr($row['pulang'],0,5) <> substr($row['datang'],0,5))
						Yii::app()->db->createCommand()->insert('a_personalcalendar', array('FingerPrintID'=>$row['id_presensi'], 'PersonalCalendarDate'=>$row['tanggal'], 'TimeCome'=>substr($row['datang'],0,5), 'TimeHome'=>substr($row['pulang'],0,5), 'LateIn'=>$LateIn, 'EarlyOut'=>$EarlyOut));
					else
						Yii::app()->db->createCommand()->insert('a_personalcalendar', array('FingerPrintID'=>$row['id_presensi'], 'PersonalCalendarDate'=>$row['tanggal'], 'TimeCome'=>substr($row['datang'],0,5), 'LateIn'=>$LateIn, 'EarlyOut'=>$EarlyOut));
				}

				$i++;
			}

			$msg .= $i." Orang-Hari.\n";

			$arr_jam = array('07:35', '08:05', '15:35', '16:05', '16:35');
			for($i=0; $i<count($arr_jam); $i++){
				if(strtotime($arr_jam[$i])>time()){
					$jam = $arr_jam[$i];
					break;
				} 
				if($i>=count($arr_jam)-1)
					$jam = $arr_jam[0];
			}

			$cfg_file = 'protected/runtime/presensi.cfg';
			file_put_contents($cfg_file,"tanggal=".date('Y-m-d')."\njam=".$jam."\njam_kerja=".$jam_kerja);			
		}

		echo $msg;					
	}

	private static function proses_absen_($tgl_mulai,$tgl_selesai,$jam_kerja)
	{
		$msg = null;

				foreach(MasterPegawai::model()->findAll(array('condition'=>'is_aktif=1 AND id_unitkerja<=9287')) as $pegawai){
					$date = $tgl_mulai;
					while (strtotime($date) <= strtotime($tgl_selesai)) {
						Yii::app()->db->createCommand("insert ignore into ".CalendarPersonal::model()->tableName()." (FingerPrintID,PersonalCalendarDate) values ('".$pegawai->id_presensi."','".$date."')")->query();
                		$date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
					}
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
							$jam_datang = '08:00:59'; $jam_pulang = '15:00'; $jam_pulang_2 = '15:30';
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

					$arr_jam = array('07:35', '08:05', '15:35', '16:05', '16:35');
					for($i=0; $i<count($arr_jam); $i++){
						if(strtotime($arr_jam[$i])>time()){
							$jam = $arr_jam[$i];
							break;
						} 
						if($i>=count($arr_jam)-1)
							$jam = $arr_jam[0];
					}

					$cfg_file = 'protected/runtime/presensi.cfg';
					file_put_contents($cfg_file,"jam=".$jam."\njam_kerja=".$jam_kerja);			
				}
		return $msg;
	}

	public function actionLibur($tahun=null)
	{
		if(!$tahun) $tahun = date('Y');

		$dataProvider = new CActiveDataProvider('CalendarHoliday', array(
			'pagination'=>array('pageSize'=>15),
			'criteria'=>array('condition'=>'year(CalendarHolidayDate)='.$tahun, 'order'=>'CalendarHolidayDate asc')
		));

		$this->render('libur', array(
			'dataProvider'=>$dataProvider,
			'tahun'=>$tahun,
		));
	}

	public function actionLibur_create()
	{
		$model = new CalendarHoliday();

		if(isset($_POST['CalendarHoliday']))
		{
			$model->attributes=$_POST['CalendarHoliday'];
			if($model->save()){
				$this->redirect(array('libur', 'tahun'=>substr($model->CalendarHolidayDate,0,4)));
			}
		}

		$this->render('libur_create', array(
			'model'=>$model,
		));
	}

	public function actionLibur_delete($tanggal)
	{
		if(Yii::app()->request->isPostRequest)
		{
			$model = CalendarHoliday::model()->findByAttributes(array('CalendarHolidayDate'=>$tanggal));
			if($model && $model->delete())
				$this->redirect(array('libur', 'tahun'=>substr($tanggal,0,4)));
			
		} else
			throw new CHttpException(404,'Page not found.');
	}

	public function actionJam()
	{
		$this->render('jam', array(
			'model'=>new CalendarPersonal(),
		));
	}

	public function actionJam_ajax()
	{
		if(Yii::app()->request->isPostRequest && Yii::app()->request->isAjaxRequest
			&& isset($_POST['id'])  && isset($_POST['tanggal'])) {
			if($model = CalendarPersonal::model()->findByAttributes(array(
				'FingerPrintID'=>$_POST['id'],
				'PersonalCalendarDate'=>$_POST['tanggal']))){
				echo json_encode(array('datang'=>$model->TimeCome, 'pulang'=>$model->TimeHome, 'keterangan'=>$model->PersonalCalendarReason? $model->PersonalCalendarReason:''));
			}
		}
	}

	public function actionJam_update()
	{
		if(Yii::app()->request->isPostRequest && Yii::app()->request->isAjaxRequest
			&& isset($_POST['id'])  && isset($_POST['tanggal']) && isset($_POST['datang']) && isset($_POST['pulang']) && isset($_POST['jam_kerja'])) {

			$model = CalendarPersonal::model()->findByAttributes(array(
				'FingerPrintID'=>$_POST['id'],
				'PersonalCalendarDate'=>$_POST['tanggal']));

			if(!$model){
				if(MasterPegawai::model()->findByAttributes(array('id_presensi'=>$_POST['id']))){
					$model = new CalendarPersonal();
					$model->FingerPrintID = $_POST['id'];
					$model->PersonalCalendarDate = $_POST['tanggal'];
				} else{
					echo '404 Page not found.';
					exit();
				}
			}

			$model->TimeCome = $_POST['datang'];
			$model->TimeHome = $_POST['pulang'];
			$model->PersonalCalendarStatus = null;
			$model->PersonalCalendarReason = null;

			$model->LateIn = ceil($this->hitung_telat($_POST['tanggal'], $_POST['datang'], $_POST['jam_kerja']));
			$model->EarlyOut = ceil($this->hitung_psw($_POST['tanggal'], $_POST['pulang'], $_POST['jam_kerja']));

			if($model->save())
				echo 'success';
			else
				print_r($model->errors);
		}
	}

	private function hitung_telat($tanggal, $datang, $jam_kerja)
	{
		$hari = date('N', strtotime($tanggal));
		if($hari>=6) return 0;

		if($jam_kerja==1){
			$jam_datang = '07:30:59';
		} elseif($jam_kerja==2){
			$jam_datang = '08:00:59';
		}

		if(!$datang) 
			return (strtotime('12:00')-strtotime($jam_datang))/60;
		elseif($datang>$jam_datang) 
			return (strtotime($datang)-strtotime($jam_datang))/60;
	}

	private function hitung_psw($tanggal, $pulang, $jam_kerja)
	{
		$hari = date('N', strtotime($tanggal));
		if($hari>=6) return 0;

		if($jam_kerja==1){
			$jam_pulang = '16:00'; $jam_pulang_2 = '16:30';
		} elseif($jam_kerja==2){
			$jam_pulang = '15:00'; $jam_pulang_2 = '15:30';
		}

		if($tanggal==date('Y-m-d') && $hari>=1 && $hari<=4 && time()<strtotime($tanggal.' '.$jam_pulang))
			return;
		if($tanggal==date('Y-m-d') && $hari==5 && time()<strtotime($tanggal.' '.$jam_pulang_2))
			return;

		if($hari>=1 && $hari<=4){
			if(!$pulang) 
				return (strtotime($jam_pulang)-strtotime('12:00'))/60;
			elseif($pulang<$jam_pulang)
				return (strtotime($jam_pulang)-strtotime($pulang))/60;
		} elseif($hari==5){
			if(!$pulang) 
				return (strtotime($jam_pulang_2)-strtotime('12:00'))/60;
			elseif($pulang<$jam_pulang_2) 
				return (strtotime($jam_pulang_2)-strtotime($pulang))/60;
		}

	}

	public function actionRekap($tahun=null, $bulan=null, $excel=null)
	{
		if(!$tahun) $tahun=date('Y');
		if(!$bulan || $bulan<1 || $bulan>12) $bulan=date('m');

		$db = new PDO(Yii::app()->db->connectionString, Yii::app()->db->username, Yii::app()->db->password);

		if(Yii::app()->user->id_eselon>=3)
			$filter = " AND id_unitkerja LIKE '".substr(Yii::app()->user->id_unitkerja,0,3)."%' AND id_unitkerja <= 9287";

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
			$this->render('rekap', array(
				'dataProvider' => $dataProvider,
				'bulan'=>$bulan,
				'tahun'=>$tahun,
				'excel'=>$excel,
				'db'=>$db,
			));
		}
	}

	public function actionDetail($id=null, $bulan=null,$tahun=null)
	{
		if((!$bulan || $bulan<1 || $bulan>12))
			$bulan = date('m');
		if(!$tahun) 
			$tahun=date('Y');

		$db = new PDO(Yii::app()->db->connectionString, Yii::app()->db->username, Yii::app()->db->password);

		$this->render('detail', array(
			'bulan'=>$bulan,
			'tahun'=>$tahun,
			'model'=>MasterPegawai::model()->findByPk($id),
			'db'=>$db,
		));
	}	

	public function actionHarian($tahun=null, $bulan=null, $excel=null)
	{
		if(!$tahun) $tahun=date('Y');
		if(!$bulan || $bulan<1 || $bulan>12) $bulan=date('m');

		$db = new PDO(Yii::app()->db->connectionString, Yii::app()->db->username, Yii::app()->db->password);

		if(Yii::app()->user->id_eselon>=3)
			$filter = " AND id_unitkerja LIKE '".substr(Yii::app()->user->id_unitkerja,0,3)."%' AND id_unitkerja <= 9287";

		$dataProvider = new CActiveDataProvider('MasterPegawai', array(
				'pagination'=>array('pageSize'=>50),
				'criteria'=>array(
					'condition'=>'is_aktif=1' . $filter,
					'order'=>'case when id_eselon=0 then 9 else id_eselon end,id_unitkerja, nama_pegawai',
						//'nama_pegawai',
					)
				));

		if($excel){
			$filename=date('m',mktime(0,0,0,$bulan,1,$tahun)).'. Absen '.strftime('%B %Y',mktime(0,0,0,$bulan,1,$tahun));	
			header("Content-type: application/vnd-ms-excel");
			header("Content-Disposition: attachment; filename='".$filename.".xls'");
//			echo "Detail Presensi ".Yii::app()->params['unitkerja']."<br>Bulan ".strftime('%B %Y',mktime(0,0,0,$bulan,1,$tahun))."<br><br>Eksport Tanggal: ".date('d-M-Y')."<br>";

			$this->renderPartial('excel_pegawai', array(
				'dataProvider' => $dataProvider,
				'bulan'=>$bulan,
				'tahun'=>$tahun,
				'excel'=>$excel,
				'db'=>$db,
			));
		} else {
			$this->render('excel_pegawai', array(
				'dataProvider' => $dataProvider,
				'bulan'=>$bulan,
				'tahun'=>$tahun,
				'excel'=>$excel,
				'db'=>$db,
			));
		}
	}

	public static function tarik_absen()
	{	
		$jam = null; $jam_kerja=null;
		$cfg_file = 'protected/runtime/presensi.cfg';
		foreach(explode("\n", file_get_contents($cfg_file)) as $row){
			if(strpos($row,'jam=')===0) $jam = substr($row, -5);
			elseif(strpos($row,'jam_kerja=')===0) $jam_kerja = substr($row, -1);
		}

		if(time() > strtotime($jam)){		
			$tgl_mulai = Yii::app()->db->createCommand()->select('max(PersonalCalendarDate)')->from('a_PersonalCalendar')->where('TimeCome is not null')->queryScalar();
			$tgl_selesai = date('Y-m-d');

			if(strtotime($tgl_mulai) && strtotime($tgl_selesai) && ($jam_kerja==1 || $jam_kerja==2)){
				if(strtotime($tgl_mulai) > strtotime($tgl_selesai)){
					list($tgl_mulai,$tgl_selesai) = array($tgl_selesai,$tgl_mulai);
				}

				self::proses_absen($tgl_mulai,$tgl_selesai,$jam_kerja);
			}
		}		
	}

	public function actionLembur($bulan=null,$tahun=null)
	{
		if(!$tahun) $tahun = date('Y');
		$dataProvider = new CActiveDataProvider('CalendarPersonal', array(
			'criteria'=>array(
				'with'=>'pegawai',
				'condition'=>'PersonalCalendarStatus=99',
				'order'=>'PersonalCalendarDate, pegawai.nama_pegawai'
		)));

		$this->render('lembur', array(
			'tahun'=>$tahun,
			'bulan'=>$bulan,
			'dataProvider'=>$dataProvider,
		));
	}
}
