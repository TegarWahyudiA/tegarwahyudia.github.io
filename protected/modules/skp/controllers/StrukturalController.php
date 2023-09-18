<?php

class StrukturalController extends Controller
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
//				'users'=>array('@'),
				'expression'=>'$user->isKasi',
			),
			array('deny',  
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($tahun=null,$unitkerja=null)
	{	
		if(!$tahun) $tahun = date('Y');
		if(!$unitkerja) $unitkerja = Yii::app()->user->id_unitkerja;

		$dataProvider = new CActiveDataProvider('SkpKegiatan', array(
				'pagination'=>array('pagesize'=>15),
				'criteria'=>array(
					'condition'=>'tahun='.$tahun.' AND id_unitkerja='.$unitkerja,
					'order'=>'nama_kegiatan',
				)));

		$this->render('index', array(
			'dataProvider'=>$dataProvider,
			'tahun'=>$tahun,
			'unitkerja'=>$unitkerja,
		));
	}

	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>SkpKegiatan::model()->findByPk($id),
		));
	}

	public function actionCreate($tahun=null)
	{
		if(!$tahun) $tahun = date('Y');

		$model = new SkpKegiatan();
		$model->tahun = $tahun;

		if(isset($_POST['SkpKegiatan']))
		{
			$model->attributes=$_POST['SkpKegiatan'];
			
			if(is_null($model->keterangan))
				$model->keterangan = '-';

			if(!Yii::app()->user->isAdmin && !strpos(Yii::app()->user->id_unitkerja,'0'))
				$model->id_unitkerja = Yii::app()->user->id_unitkerja;

			if(!$model->hasErrors() && $model->save())
				$this->redirect(array('alokasi','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));		
	}

	public function actionUpdate($id)
	{
		if($this->getState('isAdmin'))
			$model=$model=SkpKegiatan::model()->findByPk($id);
		else
			$model=SkpKegiatan::model()->findByAttributes(array(
				'id'=>$id,
				'id_unitkerja'=>Yii::app()->user->id_unitkerja));

		if(!$model)
			throw new CHttpException(403,'You are not authorized to perform this action.');

		if(isset($_POST['SkpKegiatan']))
		{
			$model->attributes=$_POST['SkpKegiatan'];
			
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			$model = SkpKegiatan::model()->findByPk($id);

			if(Yii::app()->user->isAdmin || (Yii::app()->user->isKasi && Yii::app()->user->id_unitkerja==$model->id_unitkerja)){
				foreach($model->pegawai as $pegawai)
					$pegawai->delete();
				$model->delete();
			}

			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index','unitkerja'=>$model->id_unitkerja, 'tahun'=>$model->tahun));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	public function actionAlokasi($id)
	{
		$this->render('alokasi',array(
			'model'=>SkpKegiatan::model()->findByPk($id),
		));
	}

	public function actionAlokasi_set($id)
	{
		if(!SkpKegiatan::model()->findByPk($id))
			return;

		if(Yii::app()->request->isPostRequest && Yii::app()->request->isAjaxRequest){
			$model = SkpPegawai::model()->find(array(
				'condition'=>'id_pegawai='.CHtml::encode($_POST['id_pegawai']).' AND id_kegiatan='.$id,
				));

			if(!$model) {
				$model = new SkpPegawai();
				$model->id_kegiatan = $id;
				$model->id_pegawai = CHtml::encode($_POST['id_pegawai']);
				$model->keterangan = '-';
			} 

//			if(Yii::app()->user->id_unitkerja<>$model->kegiatan->id_unitkerja  && !Yii::app()->user->isAdmin)
//				return;
//print_r($model); exit();

			if(isset($_POST['jml_target'])){
				$model->jml_target = (int) $_POST['jml_target'];

				if($model->jml_target==0 && !$model->persen_kualitas){
					$model->delete();
				}
				if(!$model->hasErrors() && $model->save())
					echo $model->jml_target;
				else 
					echo "-- error --";
			}
		}
	}

	public function actionNilai_set($id)
	{
		if(!SkpKegiatan::model()->findByPk($id)) return;

		if(Yii::app()->request->isPostRequest && Yii::app()->request->isAjaxRequest){
			$model = SkpPegawai::model()->find(array(
				'condition'=>'id_pegawai='.CHtml::encode($_POST['pegawai']).' AND id_kegiatan='.$id,
				));

			if(!$model) return;
//			if(Yii::app()->user->id_unitkerja<>$model->kegiatan->id_unitkerja  && !Yii::app()->user->isAdmin)
//				return;
//print_r($model); exit();

			if(isset($_POST['nilai'])){
				$model->persen_kualitas = (int) $_POST['nilai'];

				if(!$model->hasErrors() && $model->save())
					echo $model->persen_kualitas;
				else 
					echo "-- error --";
			}
		}
	}

}