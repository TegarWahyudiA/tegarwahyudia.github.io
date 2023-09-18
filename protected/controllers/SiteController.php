<?php

class SiteController extends Controller
{
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	public function actionAuto($term)
	{
		if(!Yii::app()->request->isAjaxRequest || strlen($term)<3 || !Yii::app()->params['autocomplete']) exit();
		
		$arr = array();
		foreach(MasterPegawai::model()->findAll(array(
			'condition'=>'nama_pegawai like \'%'.$term.'%\' AND username<>\'\' AND is_aktif=1',
			'order'=>'nama_pegawai')) as $model){
			$arr[] = array(
				'label'=>trim($model->nama_pegawai),
				'id'=>$model->username
			);
		}
		echo CJSON::encode($arr);
	}
	
	public function actionIndex()
	{
		if(!Yii::app()->user->isGuest){
			if(Yii::app()->user->id_eselon)
				$this->redirect(array('/kegiatan'));
			else
				$this->redirect(array('/personal'));
		} else
			$this->actionLogin();
	}

	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-Type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	public function actionLogin()
	{
		if(!Yii::app()->user->isGuest)
			$this->redirect(array('index'));

		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			// tarik absen otomatis
//			Yii::import('application.controllers.PresensiController');
//			PresensiController::tarik_absen();
//			$this->tarik_absen();

			$model->attributes=$_POST['LoginForm'];
			/* $model->username=$_POST['LoginForm']['username'];
			$model->password=$_POST['LoginForm']['password']; */
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$login = Yii::app()->params['autocomplete']? 'login_autocomplete' : 'login';		
		$this->render('login',array('model'=>$model));
	}

	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
}