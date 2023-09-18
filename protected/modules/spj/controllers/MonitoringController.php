<?php

class MonitoringController extends Controller
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
				'expression'=>'$user->isKasi',
			),
			array('deny',  
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($status=null,$tahun=null)
	{	
		if(!$status || !in_array($status,array('belum','selesai')))
			$status = 'belum';
		if(!$tahun)
			$tahun = date('Y');

		$arr_id = array();
		$where = $status=='selesai'?'persen=100' : 'persen<100 or persen is null';
		foreach(Yii::app()->db->createCommand("select id_spm from v_persen_spm where ".$where)->queryAll() as $row){
			$arr_id[] = $row['id_spm'];
		}
		$filter = $arr_id ? 'year(tanggal)='.$tahun.' and id in ('.implode(",", $arr_id).')' : '1=2';

		$dataProvider = new CActiveDataProvider('SpjSpm', array(
				'pagination'=>array('pagesize'=>15),
				'criteria'=>array(
					'condition'=>$filter,
					'order'=>'tanggal'),
				));

		$this->render('index', array(
			'dataProvider'=>$dataProvider,
			'status'=>$status,
			'tahun'=>$tahun,
		));
	}


	public function actionSpm($id)
	{
		$model = SpjSpm::model()->findByPk($id);
		if(!$model) throw new CHttpException(404,'Page Not Found');

		$this->render('spm', array(
			'model'=>$model
		));		
	}


	public function actionSpj($id)
	{
		$model = SpjSpj::model()->findByPk($id);
		if(!$model) return;

		$this->render('spj', array(
			'model'=>$model
		));
	}
}