<?php

class KegiatanController extends Controller
{
	public $layout='//layouts/column2';

	public function filters()
	{
		return array(
			'accessControl', 
		);
	}

	public function accessRules()
	{
		return array(
			array('allow',
				'actions'=>array('index','unitkerja','view','view_pegawai','mingguan','search','realisasi'),
				'users'=>array('@'),
			),
			array('allow',
				'actions'=>array('add','update','delete','duplikasi','konfirmasi','verifikasi','verifikasi_acc',
					'alokasi','alokasi_set','alokasi_update','alokasi_delete','realisasi_add','realisasi_update','realisasi_delete'),
				'expression'=>'$user->isKasi',
			),
			array('allow',
				'actions'=>array('lock','lock_update'),
				'expression'=>'$user->isKepala',
			),
			array('allow',
				'actions'=>array('orphan','split','lock','lock_update'),
				'expression'=>'$user->isAdmin',
			),
			array('deny',  
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($unitkerja=null,$bulan=null, $tahun=null)
	{
		if(!$bulan || $bulan<1 || $bulan>12)
			$bulan = date('m');

		if(!$tahun)
			$tahun = date('Y');

		if(!Yii::app()->user->id_eselon)
			$this->redirect(array('/personal','tahun'=>$tahun, 'bulan'=>$bulan));

		if($unitkerja && !MasterUnitkerja::model()->findByPk($unitkerja))
			$this->redirect(array('index','tahun'=>$tahun, 'bulan'=>$bulan));

		if(($unitkerja && substr($unitkerja,-1)<>'0') || $unitkerja==Yii::app()->user->id_unitkerja)
			$this->redirect(array('unitkerja','id'=>$unitkerja,'tahun'=>$tahun, 'bulan'=>$bulan));

		$this->render('index',array(
			'unitkerja'=>$unitkerja,
			'bulan'=>$bulan,
			'tahun'=>$tahun,
		));
	}

	public function actionUnitkerja($id,$bulan=null,$tahun=null,$excel=null)
	{
		if(!$bulan || $bulan<1 || $bulan>12)
			$bulan = date('m');

		if(!$tahun)
			$tahun = date('Y');

		$model = MasterUnitkerja::model()->findByPk($id);
		if(!$model)
			throw new CHttpException(404,'Page not found');

		if(Yii::app()->user->id_eselon==3 && substr(Yii::app()->user->id_unitkerja,0,3)<>substr($id,0,3))
			throw new CHttpException(400,'Process terminated');

		elseif((!Yii::app()->user->id_eselon || Yii::app()->user->id_eselon>=4) && (substr(Yii::app()->user->id_unitkerja,0,3)<>substr($id,0,3) || substr($id,-1)=='0'))
			throw new CHttpException(400,'Process terminated');
		
		$dataProvider=new CActiveDataProvider('TabelKegiatan', array(
			'pagination'=>array(
					'pageSize'=>99,
			),
			'criteria'=>array(
				'condition'=>' id_unitkerja='.$model->id.' AND year(tgl_mulai)='.$tahun.' AND month(tgl_mulai)<='.$bulan.' AND month(tgl_selesai)>='.$bulan,
				'order'=>'nama_kegiatan ASC'
			)));

		if($excel){
			$filename='Laporan '.$model->unitkerja.'-'.strftime('%m. %B_%Y',mktime(0,0,0,$bulan));	
			header("Content-type: application/vnd-ms-excel");
			header("Content-Disposition: attachment; filename=".$filename.".xls");
			echo "Kegiatan Seksi ".$model->unitkerja."<br>Bulan ".strftime('%B %Y',mktime(0,0,0,$bulan,1,$tahun))."<br><br>Cetak Tanggal: ".date('d-m-Y')."<br>";
			$this->renderPartial('unitkerja',array(
				'model'=>$model,
				'dataProvider'=>$dataProvider,
				'bulan'=>$bulan,
				'tahun'=>$tahun,
				'excel'=>true,
			));
		} else
			$this->render('unitkerja',array(
				'model'=>$model,
				'dataProvider'=>$dataProvider,
				'bulan'=>$bulan,
				'tahun'=>$tahun,
				'excel'=>false,
			));
	}

	public function actionAdd()
	{
		$model=new TabelKegiatan();

		if(isset($_POST['TabelKegiatan']))
		{
			$model->attributes=$_POST['TabelKegiatan'];
			
			if(is_null($model->keterangan))
				$model->keterangan = '-';

			if(!Yii::app()->user->isAdmin && !strpos(Yii::app()->user->id_unitkerja,'0'))
				$model->id_unitkerja = Yii::app()->user->id_unitkerja;

			if($model->is_lock)
				$model->addError('tgl_mulai','Bulan sudah dikunci');

			if(substr($model->tgl_selesai,0,8)<>substr($model->tgl_mulai,0,8))
				$model->addError('tgl_selesai','Kegiatan tidak boleh > 1 bulan');

			if(!$model->hasErrors() && $model->save())
				$this->redirect(array('/kegiatan/alokasi','id'=>$model->id));
		}

		$this->render('add',array(
			'model'=>$model,
		));
	}

	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	public function actionView_pegawai($id,$pegawai){
		$model = TabelTargetPegawai::model()->findByAttributes(array('id_kegiatan'=>$id, 'id_pegawai'=>$pegawai));
		if(!$model || (Yii::app()->user->id_unitkerja<>$model->kegiatan->id_unitkerja && !Yii::app()->user->isAdmin && !Yii::app()->user->isKepala ))
			throw new CHttpException(403,'You are not authorized to perform this action.');

		$this->render('view_pegawai', array(
			'model'=>$model
		));
	}

	public function actionUpdate($id)
	{
		if($this->getState('isAdmin'))
			$model=$this->loadModel($id);
		else
			$model=TabelKegiatan::model()->findByAttributes(array(
				'id'=>$id,
				'id_unitkerja'=>Yii::app()->user->id_unitkerja));

		if(!$model)
			throw new CHttpException(403,'You are not authorized to perform this action.');

		if(isset($_POST['TabelKegiatan']))
		{
			$model->attributes=$_POST['TabelKegiatan'];
			
			if($model->is_lock)
				$model->addError('tgl_mulai','Bulan sudah dikunci');

			if(substr($model->tgl_selesai,0,8)<>substr($model->tgl_mulai,0,8))
				$model->addError('tgl_selesai','Kegiatan tidak boleh > 1 bulan');

			if($model->save())
				$this->redirect(array('/kegiatan/view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	public function actionAlokasi($id,$target=false)
	{
		$mitra= $target=='mitra'? true : false;

		$this->render('alokasi',array(
			'model'=>$this->loadModel($id),
			'mitra'=>$mitra
		));
	}

	public function actionAlokasi_set($id)
	{
		if(!TabelKegiatan::model()->findByPk($id))
			return;

		if(Yii::app()->request->isPostRequest && Yii::app()->request->isAjaxRequest){
			$model = TabelTargetPegawai::model()->find(array(
				'condition'=>'id_pegawai='.CHtml::encode($_POST['id_pegawai']).' AND id_kegiatan='.$id,
				));

			if(!$model) {
				$model = new TabelTargetPegawai();
				$model->id_kegiatan = $id;
				$model->id_pegawai = CHtml::encode($_POST['id_pegawai']);
				$model->keterangan = '-';
			} 

			if(Yii::app()->user->id_unitkerja<>$model->kegiatan->id_unitkerja  && !Yii::app()->user->isAdmin)
				return;

			if(isset($_POST['jml_target'])){
				$model->jml_target = (int) $_POST['jml_target'];

				if($model->jml_target==0 && !$model->persen_kualitas){
foreach($model->mingguan as $mingguan) $mingguan->delete();					
$model->delete();
				
				}
if(!$model->hasErrors() && $model->save())
					echo $model->jml_target;
				else 
					echo "-- error --";
			}
		}
	}

	public function actionAlokasi_update($id)
	{
		$model = TabelTargetPegawai::model()->findByPk($id);
		if(!$model)
			return;

		if(isset($_POST['TabelTargetPegawai'])) {
			$model->attributes = $_POST['TabelTargetPegawai'];
			if(!$model->hasErrors() && $model->save())
				$this->redirect(array('view','id'=>$model->id_kegiatan));
		}

		$this->render('alokasi_update', array(
			'model'=>$model
		));
	}

	public function actionAlokasi_delete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			$model = TabelTargetPegawai::model()->findByPk($id);
			if(!$model || $model->kegiatan->id_unitkerja<>Yii::app()->user->id_unitkerja)
				return;

			if($model->delete()){
foreach($model->mingguan as $mingguan) $mingguan->delete();
				$this->redirect(array('view','id'=>$model->id_kegiatan));
}
		}
		else
			throw new CHttpException(404,'Page not found.');
	}


	public function actionRealisasi($id)
	{
		$model = TabelKegiatan::model()->find(array(
			'condition'=>'id='.$id.(Yii::app()->user->isAdmin?'' : ' AND id_unitkerja='.Yii::app()->user->id_unitkerja),
			));
		if(!$model)
//			return;
			throw new CHttpException(404,'Page not found');

		$dataProvider = new CActiveDataProvider('TabelRealisasi', array(
			'criteria'=>array(
				'condition'=>'id_kegiatan='.$model->id,
				'order'=>'tgl,id_pegawai',
			)));

		$this->render('realisasi',array(
			'model'=>$model,
			'dataProvider'=>$dataProvider
		));
	}

	public function actionRealisasi_add($id)
	{
		if(!TabelKegiatan::model()->find(array(
			'condition'=>'id='.$id.' AND id_unitkerja='.Yii::app()->user->id_unitkerja,
			)))
			return;

		$model = new TabelRealisasi;
		$model->id_kegiatan = $id;

		if(isset($_POST['TabelRealisasi'])) {
			$model->attributes = $_POST['TabelRealisasi'];
			$model->id_kegiatan = $id;
			if(!$model->hasErrors() && $model->save())
				$this->redirect(array('realisasi','id'=>$model->id_kegiatan));
		}

		$this->render('realisasi_add', array(
			'model'=>$model
		));
	}

	public function actionRealisasi_update($id)
	{
		$model = TabelRealisasi::model()->findByPk($id);
		if(!$model)
			return;

		if(isset($_POST['TabelRealisasi'])) {
			$model->attributes = $_POST['TabelRealisasi'];
			if(!$model->hasErrors() && $model->save())
				$this->redirect(array('realisasi','id'=>$model->id_kegiatan));
		}

		$this->render('realisasi_update', array(
			'model'=>$model
		));
	}

	public function actionRealisasi_delete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			$model = TabelRealisasi::model()->findByPk($id);
			if(!$model || $model->kegiatan->id_unitkerja<>Yii::app()->user->id_unitkerja)
				return;

			if($model->delete())
				$this->redirect(array('realisasi','id'=>$model->id_kegiatan));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}


	public function actionVerifikasi()
	{
		$dataProvider = new CActiveDataProvider('TabelRealisasi', array(
			'criteria'=> array(
				'with'=>'kegiatan',
				'condition'=>'id_unitkerja='.Yii::app()->user->id_unitkerja.' and acc_on is null',
				'order'=>'id_kegiatan, id_pegawai, tgl',
			)));
		$this->render('verifikasi', array(
			'dataProvider'=>$dataProvider,
			));
	}

	public function actionVerifikasi_acc()
	{
		if(Yii::app()->request->isPostRequest)
		{
			$model = TabelRealisasi::model()->findByPk($_POST['id']);
			if($model && $model->kegiatan->id_unitkerja == Yii::app()->user->id_unitkerja){
				$model->acc_on = date('Y-m-d H:i:s');
				if($model->save())
					echo 'OK';
				else 
					echo 'Gagal menyimpan verifikasi';
			} else
				echo 'Anda tidak berhak melakukan verifikasi kegiatan ini';
		}
	}

	public function actionProgress($bulan=null,$tahun=null)
	{
		if(!$bulan || $bulan<1 || $bulan>12)
			$bulan = date('m');

		if(!$tahun || $tahun>date('Y'))
			$tahun = date('Y');

		$this->render('seksi', array(
				'bulan'=>$bulan,
				'tahun'=>$tahun,
		));
	}
	
	public function actionDuplikasi($unitkerja=null, $bulan=null, $tahun=null)
		{
			if(!$unitkerja) 
				$unitkerja=Yii::app()->user->id_unitkerja;

			$unitkerja = MasterUnitkerja::model()->findByPk($unitkerja);
			if(!$unitkerja)
				throw new CHttpException(404,'Page not found.');

			if(!$bulan || $bulan<1 || $bulan>12)
				$bulan = date('m');

			if(!$tahun || $tahun>date('Y'))
				$tahun = date('Y');

			if(isset($_POST['dst_bulan']) && $_POST['dst_bulan'] && isset($_POST['dst_tahun']) && $_POST['dst_tahun'] && isset($_POST['key']) && count($_POST['key'])>0){
				foreach($_POST['key'] as $id_kegiatan){
					$model = TabelKegiatan::model()->findByPk($id_kegiatan);
					if($model){
						$new = new TabelKegiatan;
						$new->nama_kegiatan = $model->nama_kegiatan;
						$new->id_unitkerja = $model->id_unitkerja;
						$new->id_satuan = $model->id_satuan;
						$new->jml_target = $model->jml_target;
						$new->keterangan = $model->keterangan;
						$new->tgl_mulai = $this->ubah_tanggal($model->tgl_mulai, $_POST['dst_bulan'], $_POST['dst_tahun']);
						$new->tgl_selesai = $this->ubah_tanggal($model->tgl_selesai, $_POST['dst_bulan'], $_POST['dst_tahun']);
						
						if($new->save()){
							foreach($model->pegawai as $pegawai){
								$target = new TabelTargetPegawai;
								$target->id_kegiatan = $new->id;
								$target->id_pegawai = $pegawai->id_pegawai;
								$target->jml_target = $pegawai->jml_target;
								$target->keterangan = $pegawai->keterangan;
								$target->save();
							}
						}
					}
				}
				$this->redirect(array('/kegiatan/unitkerja','id'=>$unitkerja->id,'bulan'=>$_POST['dst_bulan'],'tahun'=>$_POST['dst_tahun']));
			}

			$dataProvider = new CActiveDataProvider('TabelKegiatan', array(
				'pagination'=>array('pageSize'=>999),
				'criteria'=>array(
					'condition'=>'id_unitkerja='.$unitkerja->id.' AND month(tgl_mulai)='.$bulan.' and year(tgl_mulai)='.$tahun,
					'order'=>'nama_kegiatan',
				)));

			$this->render('duplikasi', array(
				'unitkerja'=>$unitkerja,
				'bulan'=>$bulan,
				'tahun'=>$tahun,
				'dataProvider'=>$dataProvider,
			));
		}

	private function ubah_tanggal($input, $bulan, $tahun)
	{
		if(substr($input,-2)==date('t',strtotime($input)))
			$tanggal = date('t', mktime(0,0,0,$bulan,1,$tahun));
		else
			$tanggal = substr($input, -2);

		return $tahun.'-'.$bulan.'-'.$tanggal;
	}

	public function actionMingguan($ke=null)
	{
		if(!$ke || $ke<1 || $ke>53)
			$ke = date("W");

		$this->render("mingguan", array(
			"mingguke"=>$ke,
		));
	}

	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			$model = $this->loadModel($id);

			if(TabelRealisasi::model()->findByAttributes(array('id_kegiatan'=>$model->id)) || TabelTargetPegawai::model()->findByAttributes(array('id_kegiatan'=>$model->id)) || TabelTargetMingguan::model()->findByAttributes(array('id_kegiatan'=>$model->id)))
				throw new CHttpException(400,'Hapus terlebih dahulu semua realisasi dan alokasi pada kegiatan ini.');

			if(Yii::app()->user->isAdmin || (Yii::app()->user->isKasi && Yii::app()->user->id_unitkerja==$model->id_unitkerja))
				$model->delete();

			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('/kegiatan/unitkerja','id'=>$model->id_unitkerja,'tahun'=>substr($model->tgl_mulai,0,4),'bulan'=>substr($model->tgl_mulai,5,2)));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	public function actionAdmin()
	{
		$model=new TabelKegiatan('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['TabelKegiatan']))
			$model->attributes=$_GET['TabelKegiatan'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	public function loadModel($id)
	{
		$model=TabelKegiatan::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='tabel-sub-kegiatan-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	public function actionOrphan()
	{
		$sql = "";
		if(isset($_POST['r']) && count($_POST['r'])){
			$sql .= 'DELETE FROM '.TabelRealisasi::model()->tableName().' WHERE id IN ('.implode(',',$_POST['r']).');';
		}
		$realisasi = new CActiveDataProvider('TabelRealisasi', array(
			'pagination'=>array('pageSize'=>25),
			'criteria'=>array(
				'with'=>'kegiatan',
				'condition'=>'kegiatan.nama_kegiatan is null',
				'order'=>'id_kegiatan,tgl'
			)));

if(Yii::app()->params['mingguan']){
		if(isset($_POST['m']) && count($_POST['m'])){
			$sql .= 'DELETE FROM '.TabelTargetMingguan::model()->tableName().' WHERE id IN ('.implode(',',$_POST['m']).');';
		}
		$mingguan = new CActiveDataProvider('TabelTargetMingguan', array(
			'pagination'=>array('pageSize'=>25),
			'criteria'=>array(
				'with'=>'kegiatan',
				'condition'=>'kegiatan.nama_kegiatan is null',
				'order'=>'id_kegiatan,mingguke'
			)));
}
		if(isset($_POST['t']) && count($_POST['t'])){
			$sql .= 'DELETE FROM '.TabelTargetPegawai::model()->tableName().' WHERE id IN ('.implode(',',$_POST['t']).')';
		}

		if($sql)
			Yii::app()->db->createCommand($sql)->execute();

		$target = new CActiveDataProvider('TabelTargetPegawai', array(
			'pagination'=>array('pageSize'=>25),
			'criteria'=>array(
				'with'=>'kegiatan',
				'condition'=>'kegiatan.nama_kegiatan is null',
				'order'=>'id_kegiatan'
			)));

		$this->render('orphan', array(
			'realisasi'=>$realisasi,
			'mingguan'=>$mingguan,
			'target'=>$target,
		));
	}

	public function actionLock()
	{
		$dataProvider = new CActiveDataProvider('LockData', array(
			'pagination'=>array('pageSize'=>12),
			'criteria'=>array('order'=>'tahun desc, bulan desc')
		));

		$this->render('lock', array(
			'dataProvider'=>$dataProvider
		));
	}

	public function actionLock_update($tahun=null,$bulan=null)
	{
		if($tahun && $bulan && $model = LockData::model()->findByAttributes(array('tahun'=>$tahun,'bulan'=>$bulan))){
		} else
			$model = new LockData;

		if(isset($_POST['LockData'])){
			$model->attributes = $_POST['LockData'];

			if($model->isNewRecord && LockData::model()->findByAttributes(array('tahun'=>$model->tahun,'bulan'=>$model->bulan)))
				$model->addError('bulan','Data bulan ini sudah ada');

			if(!$model->isNewRecord && $model->id<>LockData::model()->findByAttributes(array('tahun'=>$model->tahun,'bulan'=>$model->bulan))->id)
				$model->addError('bulan','Data bulan ini sudah ada');

			if(!$model->hasErrors() && $model->save())
				$this->redirect(array('lock'));
		}

		$this->render('lock_update', array(
			'model'=>$model
		));
	}

	public function actionSplit()
	{
		$this->redirect(array('/split'));
	}

	public function actionKonfirmasi($id,$unlock=null)
	{
		$model = TabelKegiatan::model()->findByAttributes(array('id'=>$id,'id_unitkerja'=>Yii::app()->user->id_unitkerja),'id_pegawai_usulan is not null');
		if(!$model)
			throw new CHttpException(404,'The requested page does not exist.');

		if($unlock=='unlock')
			$model->id_flag = 1;
		else
			$model->id_flag = 0;

		$model->save();

		$this->redirect(array('view','id'=>$id));
	}
}
