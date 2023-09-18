<?php

class PegawaiController extends Controller
{
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
//				'actions'=>array('index'),
				'users'=>array('@'),
//				'expression'=>'$user->isAdmin',
			),
			array('deny',  
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($tahun=null)
	{
		if(!$tahun)
			$tahun = date('Y');

		$this->render('index', array(
			'model'=>MasterPegawai::model()->findByPk(Yii::app()->user->id),
			'tahun'=>$tahun,
		));
	}

	public function actionKegiatan($id)
	{
		$model = SkpPegawai::model()->findByAttributes(array('id_kegiatan'=>$id,'id_pegawai'=>Yii::app()->user->id));
		if(!$model)
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');

		$this->render('kegiatan', array(
			'model'=>$model
		));		
	}

	public function actionRealisasi_add($kegiatan)
	{
		$target = SkpPegawai::model()->findByAttributes(array(
			'id_kegiatan'=>$kegiatan, 'id_pegawai'=>Yii::app()->user->id));
		if(!$target)
			throw new CHttpException(404,'Tidak ada alokasi untuk pegawai tsb pada kegiatan ini.');

		$model = new SkpRealisasi();
		$model->id_kegiatan = $target->id_kegiatan;
		$model->id_pegawai = $target->id_pegawai;

		if(isset($_POST['SkpRealisasi']))
		{
			$model->attributes=$_POST['SkpRealisasi'];

			if(strtotime($model->tanggal)>time())
				$model->addError('tanggal','Tanggal tidak boleh melebihi hari ini');

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
		$model = SkpRealisasi::model()->findByPk($id);
		if(!$model)
			throw new CHttpException(404,'Page not found.');

		$target = SkpPegawai::model()->findByAttributes(array(
			'id_kegiatan'=>$model->id_kegiatan,
			'id_pegawai'=>Yii::app()->user->id,
			));

		if(isset($_POST['SkpRealisasi']))
		{
			$model->attributes=$_POST['SkpRealisasi'];
			$model->id_kegiatan = $target->id_kegiatan;
			$model->id_pegawai = $target->id_pegawai;

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
			$model = SkpRealisasi::model()->findByPk($id);
			$target = SkpPegawai::model()->findByAttributes(array('id_kegiatan'=>$model->id_kegiatan, 'id_pegawai'=>$model->id_pegawai));

			if((Yii::app()->user->isAdmin || $model->id_pegawai==Yii::app()->user->id || Yii::app()->user->id_seksi==$model->kegiatan->id_seksi))
				$model->delete();

			if(!isset($_GET['ajax']))
				$this->redirect(array('kegiatan','id'=>$target->id_kegiatan));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	public function actionSkp($tahun=null,$jenis=null,$download=null)
	{
		if(!$tahun)
			$tahun = date('Y');

		$model=MasterPegawai::model()->findByPk(Yii::app()->user->id);

		$filter = ' AND tahun='.$tahun;
		$target_pegawai = SkpPegawai::model()->findAll(array(
			'with'=>'kegiatan',
			'condition'=>'id_pegawai='.Yii::app()->user->id. $filter.' AND id_flag=0',
			'order'=>'nama_kegiatan ASC'
		));

		if($jenis=='realisasi'){
			$file = 'skp_realisasi';
			$dok = 'SKPR';
		} else {
			$file = 'skp_target';
			$dok = 'SKPT';
		}

		if(!$download)
			$this->renderNormal($file,array(
				'model'=>$model,
				'target_pegawai'=>$target_pegawai,
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
				'tahun'=>$tahun,
				'link'=>false,
				));
		}
	}

}