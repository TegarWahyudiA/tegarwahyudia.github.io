<?php

class PersonalController extends Controller
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
				'actions'=>array('index','mingguan','mingguan_set','kegiatan','kegiatan_fungsional','set_keterangan','realisasi_add','realisasi_update','realisasi_delete','realisasi','ckp','presensi','profil','fungsional','unitkerja','search','usulan','usulan_add','usulan_edit','usulan_copy'),
				'users'=>array('@'),
			),
			array('allow',
				'actions'=>array('kredit_master','kredit_view','kredit_search','kredit_get'),
				'expression'=>'$user->isFungsional',
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionSearch()
	{
		if(isset($_POST['q']) && $_POST['q']<>''){
			$q = trim($_POST['q']);
			if(strlen($q)>2) {
				$split = explode(" ", $q);

				if(count($split)>1){
					$filter = 'nama_kegiatan LIKE \'%'. array_shift($split).'%\'';
					foreach($split as $val){
						$filter.=' AND nama_kegiatan LIKE \'%'.$val.'%\' ';
					}
				} else {
					$filter = 'nama_kegiatan LIKE \'%'.$q.'%\'';
				}

				$dataProvider = new CActiveDataProvider('TabelKegiatan',array(
					'pagination'=>array('pageSize'=>20),
					'criteria'=>array('condition'=>$filter,'order'=>'tgl_selesai desc, tgl_mulai desc, nama_kegiatan')));
			}
		} else {
			$q = null;
			$dataProvider = null;		
		}

		$this->render('search', array(
			'q'=>$q,
			'dataProvider'=>$dataProvider
		));
	}

	public function actionRealisasi($bulan=null,$tahun=null)
	{
		if(!$bulan || $bulan<1 || $bulan>12)
			$bulan = date('m');
		if(!$tahun)
			$tahun = date('Y');

		$this->render('realisasi', array(
			'model'=>MasterPegawai::model()->findByPk(Yii::app()->user->id),
			'bulan'=>$bulan,
			'tahun'=>$tahun,
		));
	}

	public function actionPresensi($bulan=null,$tahun=null)
	{
		if(!$bulan || $bulan<1 || $bulan>12)
			$bulan = date('m');
		if(!$tahun || $tahun>date('Y'))
			$tahun = date('Y');

/*		$dbName = Yii::app()->params['db_presensi'];
		if (!file_exists($dbName)) //die("Could not find database file.");
			throw new CHttpException(404,'Could not find database file.');
		$db = new PDO("odbc:DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=$dbName; Uid=; Pwd=ithITtECH;") or die('error loading pdo');
*/

$db = new PDO(Yii::app()->db->connectionString, Yii::app()->db->username, Yii::app()->db->password);
		
		$this->render('presensi', array(
			'model'=>MasterPegawai::model()->findByPk(Yii::app()->user->id),
			'bulan'=>$bulan,
			'tahun'=>$tahun,
			'db'=>$db,
		));
	}

	public function actionIndex($bulan=null,$tahun=null)
	{
		if(!$bulan || $bulan<1 || $bulan>12)
			$bulan = date('m');
		if(!$tahun)
			$tahun = date('Y');

		$this->render('index', array(
			'model'=>MasterPegawai::model()->findByPk(Yii::app()->user->id),
			'bulan'=>$bulan,
			'tahun'=>$tahun,
		));
	}

	public function actionKegiatan($id)
	{
		$model = TabelTargetPegawai::model()->findByAttributes(array('id_kegiatan'=>$id,'id_pegawai'=>Yii::app()->user->id));
		if(!$model)
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');

		$this->render('kegiatan', array(
			'model'=>$model
		));		
	}

	public function actionMingguan_set($kegiatan)
	{
		if(Yii::app()->request->isPostRequest && Yii::app()->request->isAjaxRequest) {
			$pegawai = Yii::app()->user->id;
			$target = TabelTargetPegawai::model()->findByAttributes(array(
				'id_pegawai'=>$pegawai,
				'id_kegiatan'=>$kegiatan,
				));
			if(!$target) return;

			$model = TabelTargetMingguan::model()->findByAttributes(array(
				'id_pegawai'=>$pegawai,
				'id_kegiatan'=>$kegiatan,
				'mingguke'=>$_POST['mingguke'],
				));

			if(!$model && TabelTargetPegawai::model()->find(array('condition'=>'id_kegiatan='.$kegiatan.' AND id_pegawai='.$pegawai))) {
				$model = new TabelTargetMingguan();
				$model->id_kegiatan = $kegiatan;
				$model->id_pegawai = $pegawai;
				$model->mingguke = (int) $_POST['mingguke'];
				$model->keterangan = '-';
				$prev = 0;
			} else
				$prev = $model->jml_target;

			$model->jml_target = (int)$_POST['jml_target'];

			if($model->target->jml_target < $model->target->child_target - $prev + $model->jml_target){
				$model->addError('jml_target','Melebihi target yang telah ditentukan');
				echo 'ERROR :: Total input melebihi target yang telah ditentukan';
			} else if(!$model->hasErrors() && $model->save())
				echo $model->jml_target;
		} 
	}

	public function actionMingguan($ke=null)
	{
		if(!$ke || $ke<1 || $ke>53)
			$ke = date('W');

		$this->render('mingguan', array(
			'model'=>MasterPegawai::model()->findByPk(Yii::app()->user->id),
			'mingguke'=>$ke,
		));
	}

	public function actionRealisasi_add($kegiatan)
	{
		$target = TabelTargetPegawai::model()->findByAttributes(array(
			'id_kegiatan'=>$kegiatan, 'id_pegawai'=>Yii::app()->user->id));
		if(!$target)
			throw new CHttpException(404,'Tidak ada alokasi untuk pegawai tsb pada kegiatan ini.');

		if($target->kegiatan->is_lock)
			throw new CHttpException(400,'Data sudah dikunci');

		$model = new TabelRealisasi();
		$model->id_kegiatan = $target->id_kegiatan;
		$model->id_pegawai = $target->id_pegawai;

		if(isset($_POST['TabelRealisasi']))
		{
			$model->attributes=$_POST['TabelRealisasi'];

			if(strtotime($model->tgl)< strtotime($model->kegiatan->tgl_mulai))
				$model->addError('tgl','Tanggal di luar range jadwal');

			if(strtotime($model->tgl)>time())
				$model->addError('tgl','Tanggal tidak boleh melebihi hari ini');

			if(!(int)$model->jml_realisasi || $model->jml_realisasi<0)
				$model->addError('jml_realisasi','Isikan nilai > 0');

			if($model->jml_realisasi + $target->jml_realisasi > $target->jml_target)
				$model->addError('jml_realisasi','Total realisasi ('.$target->jml_realisasi.'+'.$model->jml_realisasi .') melebihi target ('.$target->jml_target.')');

			if(!$model->hasErrors() && $model->save())
				$this->redirect(array('kegiatan','id'=>$model->id_kegiatan));
		}

		$this->render('realisasi_add', array(
			'model'=>$model,
			'target'=>$target,
		));
	}

	public function actionRealisasi_update($id)
	{
		$model = TabelRealisasi::model()->findByPk($id);
		if(!$model)
			throw new CHttpException(404,'Page not found.');

		$target = TabelTargetPegawai::model()->findByAttributes(array(
			'id_kegiatan'=>$model->id_kegiatan,
			'id_pegawai'=>Yii::app()->user->id,
			));

		if($target->kegiatan->is_lock)
			throw new CHttpException(400,'Data sudah dikunci');

		if(isset($_POST['TabelRealisasi']))
		{
			$model->attributes=$_POST['TabelRealisasi'];
			$model->id_kegiatan = $target->id_kegiatan;
			$model->id_pegawai = $target->id_pegawai;

			if(strtotime($model->tgl)< strtotime($model->kegiatan->tgl_mulai))
				$model->addError('tgl','Tanggal di luar range jadwal');

			if(strtotime($model->tgl)>time())
				$model->addError('tgl','Tanggal tidak boleh melebihi hari ini');

			if(!(int)$model->jml_realisasi || $model->jml_realisasi<0)
				$model->addError('jml_realisasi','Isikan nilai > 0');

			if($model->jml_realisasi + $target->jml_realisasi > $target->jml_target)
				$model->addError('jml_realisasi','Total realisasi ('.$target->jml_realisasi.'+'.$model->jml_realisasi .') melebihi target ('.$target->jml_target.')');

			if(!$model->hasErrors() && $model->save())
				$this->redirect(array('kegiatan','id'=>$model->id_kegiatan));
		}

		$this->render('realisasi_update', array(
			'model'=>$model,
			'target'=>$target
			));
	}

	public function actionRealisasi_delete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			$model = TabelRealisasi::model()->findByPk($id);
			$target = TabelTargetPegawai::model()->findByAttributes(array('id_kegiatan'=>$model->id_kegiatan, 'id_pegawai'=>$model->id_pegawai));

			if(!$target->kegiatan->is_lock && (Yii::app()->user->isAdmin || $model->id_pegawai==Yii::app()->user->id || Yii::app()->user->id_seksi==$model->kegiatan->id_seksi))
				$model->delete();

			if(!isset($_GET['ajax']))
				$this->redirect(array('kegiatan','id'=>$target->id_kegiatan));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}


	public function actionCkp($bulan=null,$tahun=null,$jenis=null,$download=null)
	{
		if(!$bulan || $bulan<1 || $bulan>12)
			$bulan = date('m');
		if(!$tahun || $tahun>date('Y'))
			$tahun = date('Y');

		$model=MasterPegawai::model()->findByPk(Yii::app()->user->id);

		$filter = ' AND year(tgl_mulai)='.$tahun.' AND month(tgl_mulai)<='.$bulan.' AND month(tgl_selesai)>='.$bulan;
		$target_pegawai = TabelTargetPegawai::model()->findAll(array(
			'with'=>'kegiatan',
			'condition'=>'id_pegawai='.Yii::app()->user->id. $filter.' AND is_ckp=1',// and id_flag=0',
			'order'=>'nama_kegiatan ASC'
		));

		if($jenis=='realisasi'){
			$file = 'ckp_realisasi';
			$dok = 'CKPR';
		} else {
			$file = 'ckp_target';
			$dok = 'CKPT';
		}

		if(!$download)
			$this->renderNormal($file,array(
				'model'=>$model,
				'target_pegawai'=>$target_pegawai,
				'bulan'=>$bulan,
				'tahun'=>$tahun,
				'link'=>true,
				));
		else {
			$filename=$dok.'-'.str_ireplace(' ', '_', $model->nama_pegawai).'-'.strftime('%B_%Y',mktime(0,0,0,$bulan,1,$tahun));	
			header('Content-type: application/vnd-ms-excel');
			header("Content-Disposition: attachment; filename='".$filename.".xls'");

			$this->renderPartial($file,array(
				'model'=>$model,
				'target_pegawai'=>$target_pegawai,
				'bulan'=>$bulan,
				'tahun'=>$tahun,
				'link'=>false,
				));
		}
	}

	public function actionProfil()
	{
		$msg = null;
		$model = MasterPegawai::model()->findByPk(Yii::app()->user->id);
		
		if(isset($_POST['password']) && $_POST['password']<>'' && isset($_POST['konfirmasi']) && $_POST['konfirmasi']<>'' && $_POST['password']== $_POST['konfirmasi']){

			$model->salt = uniqid(mt_rand(), true);
			$model->hash = sha1($model->salt.$_POST['password']);

			if(!$model->hasErrors() && $model->save())
				$msg = 'Password berhasil diganti';
			else
				$msg = 'Password gagal diganti';				
		}

		$this->render('profil', array(
			'model'=>$model,
			'msg'=>$msg,
		));
	}

	public function actionKegiatan_fungsional($id,$fungsional=null)
	{
		if(!Yii::app()->user->id_fungsional && !Yii::app()->user->isAdmin)
			throw new CHttpException(400,'Anda tidak berhak membuka halaman ini.');

		$kegiatan = TabelKegiatan::model()->findBYPk($id);
		if(!$kegiatan)
			throw new CHttpException(404,'Kegiatan tidak ditemukan.');

		$model = TabelTargetPegawai::model()->findByAttributes(array('id_kegiatan'=>$id,'id_pegawai'=>Yii::app()->user->id));
		if(!$model && !Yii::app()->user->isAdmin)
			throw new CHttpException(404,'Tidak ada alokasi kegiatan ini untuk Anda.');

		$model->id_fungsional = Yii::app()->user->id_fungsional;

		if(isset($_POST['TabelTargetPegawai'])){
			$model->kode_kredit = $_POST['TabelTargetPegawai']['kode_kredit'];

			if(!$model->hasErrors() && $model->save()){
				foreach($model->kegiatan->pegawai as $target){
					if($target->pegawai->id_fungsional && substr($target->pegawai->id_fungsional,0,1)==substr($model->id_fungsional,0,1) && !$target->kredit){
						$target->id_fungsional = $target->pegawai->id_fungsional;
						$target->kode_kredit = $model->kode_kredit;
						$target->save();
					}
				}

				$this->redirect(array('kegiatan','id'=>$model->id_kegiatan));
			}
		}

		$this->render('kegiatan_fungsional', array(
			'model'=>$model
		));
	}

	public function actionFungsional()
	{
		$tahun = date('Y');

		if(isset($_POST['tahun']) && isset($_POST['bulan_1']) && isset($_POST['bulan_2'])){
			$bulan_1 = (int) $_POST['bulan_1'];
			$bulan_2 = (int) $_POST['bulan_2'];
			$tahun = (int) $_POST['tahun'];

			if($bulan_1>$bulan_2){
				$tmp = $bulan_1;
				$bulan_1 = $bulan_2;
				$bulan_2 = $tmp;
			}

			if($bulan_1 && $bulan_2 && $tahun && Yii::app()->user->id_fungsional)
			$data = TabelTargetPegawai::model()->findAllBySql("select t.* 
				from t_target_pegawai t
				join t_kegiatan k on k.id=t.id_kegiatan 
				where id_pegawai=".Yii::app()->user->id." AND year(k.tgl_mulai)=".$tahun." AND month(k.tgl_mulai)>=".$bulan_1." AND month(k.tgl_selesai)<=".$bulan_2." AND t.id_fungsional is not null AND t.kode_kredit is not null AND k.id_flag=0 ORDER BY k.tgl_mulai,k.tgl_selesai,k.nama_kegiatan");
		}

		if(isset($_POST['excel']) && $_POST['excel']==1){
			header("Content-type: application/vnd-ms-excel");
			header("Content-Disposition: attachment; filename=rincian_fungsional.xls");
			echo "Rincian Kegiatan Fungsional<br>";

			$this->renderPartial('fungsional', array(
				'bulan_1'=>$bulan_1,
				'bulan_2'=>$bulan_2,
				'tahun'=>$tahun,
				'data'=>$data,
				'excel'=>true
			));	

		} else
			$this->render('fungsional', array(
				'bulan_1'=>$bulan_1,
				'bulan_2'=>$bulan_2,
				'tahun'=>$tahun,
				'data'=>$data
			));	
	}

	public function actionSet_keterangan()
	{
		if(Yii::app()->request->isPostRequest && Yii::app()->request->isAjaxRequest) {
			$model = TabelTargetPegawai::model()->findByAttributes(array(
				'id_pegawai'=>Yii::app()->user->id,
				'id_kegiatan'=>(int)$_POST['id']
			));

			if($model && $model->keterangan<>trim($_POST['keterangan'])){
				$model->keterangan=trim($_POST['keterangan']);
				if($model->save())
					echo 'OK';
			}
		}
	}

	public function actionUnitkerja($id=null,$bulan=null,$tahun=null)
	{
		if(!$bulan || $bulan<1 || $bulan>12)
			$bulan = date('m');
		if(!$tahun || $tahun>date('Y'))
			$tahun = date('Y');

		if($id<=9286 && MasterUnitkerja::model()->findByPk($id))
			$dataProvider = new CActiveDataProvider('TabelKegiatan',array(
				'pagination'=>array('pageSize'=>25),
				'criteria'=>array('condition'=>'id_unitkerja='.$id.' and year(tgl_mulai)='.$tahun.' and month(tgl_mulai)='.$bulan, 'order'=>'nama_kegiatan')));
		elseif(!$id)
			$dataProvider = null;
		else
			throw new CHttpException(404,'Page not found.');

		$this->render('unitkerja', array(
			'id'=>$id,
			'bulan'=>$bulan,
			'tahun'=>$tahun,
			'dataProvider'=>$dataProvider,
			'excel'=>false
		));
	}

	public function actionKredit_master()
	{
		$term = isset($_POST['term'])? trim($_POST['term']) : null;
		if($term){
			$split = explode(" ", $term);

			if(count($split)>1){
				$filter = ' AND CONCAT(uraian_singkat,kegiatan) LIKE \'%'. array_shift($split).'%\'';
				foreach($split as $val){
					$filter.=' AND CONCAT(uraian_singkat,kegiatan) LIKE \'%'.$val.'%\' ';
				}
			} else {
				$filter = ' AND CONCAT(uraian_singkat,kegiatan) LIKE \'%'.$term.'%\' OR kode=\''.$term.'\'';
			}
			$this->setState('term', $term);
			$this->setState('filter', $filter);
		} elseif (isset($_POST['term'])) {// reset pencarian
			$this->setState('term', '');
			$this->setState('filter', '');
		} elseif($this->getState('filter')){
			$term = $this->getState('term');
			$filter = $this->getState('filter');
		}

		$dataProvider = new CActiveDataProvider('MasterKredit', array(
			'criteria'=>array('condition'=>'kode_tingkat='.substr(Yii::app()->user->id_fungsional,0,1).$filter)));

		$this->render('kredit_master',array(
			'dataProvider'=>$dataProvider,
			'term'=>$term,
		));
	}

	public function actionKredit_view($id=null,$kode=null,$kode_tingkat=null)
	{
		if($id){
			$model = $this->loadKredit($id);
		} elseif ($kode && $kode_tingkat) {
			$model = MasterKredit::model()->findByAttributes(array('kode'=>$kode, 'kode_tingkat'=>$kode_tingkat));
		}

		$this->render('kredit_view', array(
			'model'=>$model
		));
	}

	public function actionKredit_search()
	{
		$term = isset($_POST['term'])? trim($_POST['term']) : null;
		if(!Yii::app()->request->isAjaxRequest || strlen($term)<3) exit();

		$split = explode(" ", $term);

		if(count($split)>1){
			$filter = 'CONCAT(uraian_singkat,kegiatan) LIKE \'%'. array_shift($split).'%\'';
			foreach($split as $val){
				$filter.=' AND CONCAT(uraian_singkat,kegiatan) LIKE \'%'.$val.'%\' ';
			}
		} else {
			$filter = 'CONCAT(uraian_singkat,kegiatan) LIKE \'%'.$term.'%\' OR kode LIKE \'%'.$term.'%\'';
		}

		$arr = array();
		foreach(MasterKredit::model()->findAll(array(
			'condition'=>$filter.' AND kode_tingkat='.(int)substr($_POST['level'],0,1),
			'order'=>'uraian_singkat,kegiatan')) as $model){
			$arr[] = array(
				'label'=>trim($model->uraian_singkat).' :: '.trim($model->kegiatan),
				'id'=>$model->id
			);
		}
		echo CJSON::encode($arr);
	}
	
	public function actionKredit_get()
	{	
		if(Yii::app()->request->isPostRequest)
		{
			$id = (int) $_POST['id'];
			echo CJSON::encode(MasterKredit::model()->findByPk($id));
		}
	}

	public function loadKredit($id)
	{
		$model=MasterKredit::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	public function actionUsulan($tahun=null,$bulan=null)
	{
		if(!$tahun) $tahun = date('Y');
		if(!$bulan || $bulan>12) $bulan = date('m');

		$dataProvider = new CActiveDataProvider('TabelKegiatan', array(
			'pagination'=>array('pageSize'=>15),
			'criteria'=>array(
				'condition'=>'id_pegawai_usulan='.Yii::app()->user->id.' and year(tgl_mulai)='.$tahun.' and month(tgl_mulai)='.$bulan,
				'order'=>'nama_kegiatan'
			)));

		$this->render('usulan', array(
			'dataProvider'=>$dataProvider,
			'bulan'=>$bulan,
			'tahun'=>$tahun
		));
	}

	public function actionUsulan_add()
	{
		$model = new TabelKegiatan();

		if(isset($_POST['TabelKegiatan']))
		{
			$model->attributes=$_POST['TabelKegiatan'];
			
			if(is_null($model->keterangan))
				$model->keterangan = '-';

			$model->id_flag = 1; // flag kegiatan usulan
			$model->id_pegawai_usulan = Yii::app()->user->id; // id_pegawai yang mengusulkan kegiatan

			if($model->is_lock)
				$model->addError('tgl_mulai','Bulan sudah dikunci');

			if(substr($model->tgl_selesai,0,8)<>substr($model->tgl_mulai,0,8))
				$model->addError('tgl_selesai','Kegiatan tidak boleh > 1 bulan');

			if(!$model->hasErrors() && $model->save()){
				$target = new TabelTargetPegawai();
				$target->id_kegiatan = $model->id;
				$target->id_pegawai = Yii::app()->user->id;
				$target->jml_target = $model->jml_target;
				$target->keterangan = '-';
				if(!$target->hasErrors() && $target->save())
					$this->redirect(array('/personal/kegiatan','id'=>$model->id));
				else {
					$model->delete();
					$model->addError('nama_kegiatan','Gagal ditambahkan');
				}
			}
		}

		$this->render('usulan_add', array(
			'model'=>$model,
		));
	}

	public function actionUsulan_edit($id)
	{
		$model = TabelKegiatan::model()->findByAttributes(array(
			'id'=>$id,
//			'id_flag'=>1,
			'id_pegawai_usulan'=>Yii::app()->user->id));

		if(!$model)
			throw new CHttpException(404,'The requested page does not exist.');

		if(isset($_POST['TabelKegiatan']))
		{
			$model->attributes=$_POST['TabelKegiatan'];
			
			if(is_null($model->keterangan))
				$model->keterangan = '-';

			$model->id_flag = 1; // flag kegiatan usulan
			$model->id_pegawai_usulan = Yii::app()->user->id; // id_pegawai yang mengusulkan kegiatan

			if($model->is_lock)
				$model->addError('tgl_mulai','Bulan sudah dikunci');

			if(!$model->hasErrors() && $model->save()){
				$this->redirect(array('/personal/kegiatan','id'=>$model->id));
			}
		}

		$this->render('usulan_edit', array(
			'model'=>$model
		));
	}
	public function actionUsulan_copy($id)
	{
		$model = TabelKegiatan::model()->findByAttributes(array(
			'id'=>$id,
			'id_pegawai_usulan'=>Yii::app()->user->id));

		if(!$model)	throw new CHttpException(404,'The requested page does not exist.');

		$model->tgl_mulai = null;
		$model->tgl_selesai = null;

		if(isset($_POST['TabelKegiatan']))
		{
			$new = new TabelKegiatan();
			$new->attributes=$_POST['TabelKegiatan'];
			
			if(is_null($new->keterangan))
				$new->keterangan = '-';

			$new->id_flag = 1; // flag kegiatan usulan
			$new->id_pegawai_usulan = Yii::app()->user->id; // id_pegawai yang mengusulkan kegiatan

			if($new->is_lock)
				$new->addError('tgl_mulai','Bulan sudah dikunci');

			if(substr($new->tgl_selesai,0,8)<>substr($new->tgl_mulai,0,8))
				$new->addError('tgl_selesai','Kegiatan tidak boleh > 1 bulan');

			if(!$new->hasErrors() && $new->save()){
				$target = new TabelTargetPegawai();
				$target->id_kegiatan = $new->id;
				$target->id_pegawai = Yii::app()->user->id;
				$target->jml_target = $new->jml_target;
				$target->keterangan = '-';
				if(!$target->hasErrors() && $target->save())
					$this->redirect(array('/personal/kegiatan','id'=>$new->id));
				else {
					$new->delete();
					$new->addError('nama_kegiatan','Gagal ditambahkan');
				}
			}
		}

		$this->render('usulan_copy', array(
			'model'=>$model,
		));
	}

}
