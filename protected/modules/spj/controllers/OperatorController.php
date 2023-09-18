<?php

class OperatorController extends Controller
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
				'expression'=>'$user->isSpj',
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

	public function actionCreate_spm()
	{
		$model = new SpjSpm();

		if(isset($_POST['SpjSpm'])){
			$model->attributes = $_POST['SpjSpm'];
			if($model->save()){
				$this->redirect(array('spm','id'=>$model->id));
			}
		}

		$this->render('create_spm', array(
			'model'=>$model,
		));
	}

	public function actionUpdate_spm($id)
	{
		$model = SpjSpm::model()->findByPk($id);
		if(!$model) throw new CHttpException(404,'Page Not Found');

		if(isset($_POST['SpjSpm'])){
			$model->attributes = $_POST['SpjSpm'];
			if($model->save()){
				$this->redirect(array('spm','id'=>$id));
			}
		}

		$this->render('update_spm', array(
			'model'=>$model,
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

	public function actionUpdate($id)
	{
		$model = SpjKegiatan::model()->findByPk($id);
		if(!$model) throw new CHttpException(404,'Page Not Found');

		if(Yii::app()->request->isPostRequest && isset($_POST['SpjKegiatan']))
		{
			$model->attributes = $_POST['SpjKegiatan'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update', array(
			'model'=>$model
		));
	}

	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			$model = SpjKegiatan::model()->findByPk($id);
			if(!isset($_GET['ajax']) && $model->delete()){
				foreach ($model->dokumen as $dokumen) {
					$dokumen->delete();
				}
				$this->redirect(array('index'));
			}
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	public function actionDokumen($id,$dokumen)
	{
		$model = SpjDokumen::model()->findByAttributes(array('id_spj'=>$id, 'id_dokumen'=>$dokumen));
		if(!$model) throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
		
		if(isset($_POST['SpjDokumen'])){
			$model->attributes = $_POST['SpjDokumen'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id_spj));
		}

		$this->render('dokumen', array(
			'model'=>$model,
		));
	}

	public function actionDokumen_delete($id,$dokumen)
	{
		if(Yii::app()->request->isPostRequest)
		{
			$model = SpjDokumen::model()->findByAttributes(array('id_spj'=>$id,'id_dokumen'=>$dokumen));
			if(!isset($_GET['ajax']) && $model->delete())
				$this->redirect(array('view','id'=>$model->id_spj));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	public function actionDokumen_add($id)
	{
		$spj = SpjKegiatan::model()->findByPk($id);
		if(!$spj) throw new CHttpException(404,'Page not found.');

		$model = new SpjDokumen();
		$model->id_spj = $id;
		$model->id_dokumen = (int) $_POST['dokumen'];
		if($model->save())
			echo 'success';
		else
			print_r($model->errors);
	}

	public function actionCreate_spj($spm)
	{
		if(!SpjSpm::model()->findByPk($spm)) return;

		$model = new SpjSpj();
		$model->id_spm = $spm;

		if(isset($_POST['SpjSpj'])){
			$model->attributes = $_POST['SpjSpj'];
			$model->id_spm = $spm;
			if($model->save()){
				if(isset($_POST['dokumen'])) 
				foreach($_POST['dokumen'] as $key=>$val){
					$dokumen = new SpjDokumen();
					$dokumen->id_spj = $model->id;
					$dokumen->id_dokumen = $key;
					if(!$dokumen->save()) {print_r($dokumen);exit();}
				}
				$this->redirect(array('spj','id'=>$model->id));
			}

		}

		$this->render('create_spj', array(
			'model'=>$model
		));
	}

	public function actionUpdate_spj($id)
	{
		$model = SpjSpj::model()->findByPk($id);
		if(!$model) throw new CHttpException(404,'Page not found.');

		if(isset($_POST['SpjSpj'])){
			$model->attributes = $_POST['SpjSpj'];
			if($model->save()){
/*				foreach($_POST['dokumen'] as $key=>$val){
					$dokumen = new SpjDokumen();
					$dokumen->id_spj = $model->id;
					$dokumen->id_dokumen = $key;
					if(!$dokumen->save()) {print_r($dokumen);exit();}
				}
*/				$this->redirect(array('spj','id'=>$model->id));
			}

		}

		$this->render('update_spj', array(
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

	public function actionDokumen_tambah()
	{
		if(Yii::app()->request->isAjaxRequest){
			$spj = SpjSpj::model()->findByPk($_POST['spj']);
			if($spj && SpjMaster::model()->findByPk($_POST['dokumen'])){
				$model = new SpjDokumen();
				$model->id_spj = $spj->id;
				$model->id_dokumen = $_POST['dokumen'];
				$model->updated_on = time();
				if($model->save())
					echo $_POST['dokumen'];
				else
					echo 'e';
			} 
		}
	}

	public function actionDokumen_status()
	{
		if(Yii::app()->request->isAjaxRequest){
			$model = SpjDokumen::model()->findByPk($_POST['id']);
			if($model && in_array($_POST['status'],array(0,1,2,3,4))){
				$model->status = $_POST['status'];
				$model->updated_on = time();
				if($model->save())
					echo $_POST['status'];
				else
					echo 'e';
			}
		}
	}

	public function actionDokumen_keterangan()
	{
		if(Yii::app()->request->isAjaxRequest){
			$model = SpjDokumen::model()->findByPk($_POST['id']);
			if($model && isset($_POST['keterangan'])){
				$model->keterangan = $_POST['keterangan'];
				if($model->save())
					echo $_POST['keterangan'];
				else
					echo 'e';
			}
		}
	}

	public function actionDokumen_hapus($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			$model = SpjDokumen::model()->findByPk($id);
			if($model->delete())
				$this->redirect(array('spj','id'=>$model->id_spj));
		} else
			throw new CHttpException(404,'Page not found.');
	}

	public function actionTemplate_drpp()
	{
		$filename="template_drpp.xls";		
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=".$filename);
		readfile($this->module->basePath.'/'.$filename);
	}

	public function actionImport_drpp($spm)
	{
		$spm = SpjSpm::model()->findByPk($spm);
		if(!$spm) throw new CHttpException(404,'Page not found.');

		$arr_data = array();
		if($file = CUploadedFile::getInstanceByName('drpp')){
			require_once('protected/extensions/excel-reader/excel_reader2.php');
			$data = new Spreadsheet_Excel_Reader($file->tempName);
			$baris = 2;
			do {
				$model = new SpjSpj();
				$model->id_spm = $spm->id;
				$model->no_urut = $data->value($baris,1);
				$model->nomor = $data->value($baris,2);
				
				$model->tanggal = $data->value($baris,3);
				// jika format input  dd-mm-yyyy
				if(substr($model->tanggal,2,1)=="-" && substr($model->tanggal,2,1)=="-")
					$model->tanggal = substr($model->tanggal,-4).'-'.substr($model->tanggal,3,2).'-'.substr($model->tanggal,0,2);

				$model->keperluan = $data->value($baris,4);
				$model->akun = $data->value($baris,5);
				$model->jumlah_kotor = $data->value($baris,6);
				if(!$model->hasErrors() && $model->save()){
					for($i=1; $i<=6; $i++){
						if($data->value($baris, $i+6)<>""){
							$dok = new SpjDokumen();
							$dok->id_spj = $model->id;
							$dok->id_dokumen = $i;
							$dok->save();
						}
					}
				}
				$baris++;
			} while($data->value($baris,1)<>'');

			$this->redirect(array('spm','id'=>$spm->id));
		}

		$this->render('import_drpp', array(
			'spm'=>$spm,
		));
	}
}