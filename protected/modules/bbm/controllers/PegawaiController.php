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
				'users'=>array('@'),
			),
			array('deny',  
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($bulan=null, $tahun=null)
	{
		if(!$tahun) $tahun = date('Y');
		if(!$bulan || $bulan>12) $bulan = date('m');

		$dataProvider = new CActiveDataProvider('TabelBbm', array(
			'criteria'=>array(
				'condition'=>'id_pegawai='.Yii::app()->user->id.' AND year(tanggal)='.$tahun.' AND month(tanggal)='.$bulan,
				'order'=>'tanggal, id_jenis')
			));

		$this->render('index', array(
			'dataProvider'=>$dataProvider,
			'tahun'=>$tahun,
			'bulan'=>$bulan,
		));
	}

	public function actionRekap($tahun=null)
	{
		if(!$tahun) $tahun = date('Y');
		
		$this->render('rekap', array(
			'tahun'=>$tahun,
		));
	}

	public function actionAdd()
	{
		$pegawai = MasterPegawai::model()->findByPk(Yii::app()->user->id);
		$model = new TabelBbm();

		if(isset($_POST['TabelBbm'])){
			$model->attributes = $_POST['TabelBbm'];
			$model->id_pegawai = $pegawai->id;
			if(!$model->hasErrors() && $model->save())
				$this->redirect(array('index'));
		}

		$this->render('add', array(
			'model'=>$model
		));
	}

	public function actionEdit($id)
	{
		$model =  TabelBbm::model()->findByAttributes(array('id'=>$id,'id_pegawai'=>Yii::app()->user->id));

		$this->render('edit', array(
			'model'=>$model
		));
	}

	public function actionDelete($file)
	{
		if(Yii::app()->request->isPostRequest)
		{
			$filename = $this->module->basePath.'/data/'.$file;
			if(is_file($filename)) unlink($filename);

			$this->redirect(array('index'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

}