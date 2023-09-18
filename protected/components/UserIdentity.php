<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */

    protected $model;

	public function authenticate()
	{ 
		$model = MasterPegawai::model()->authenticate($this->username,$this->password);
		if(!$model)
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
		else {
			$this->errorCode = self::ERROR_NONE;
			$this->model = $model;

			$this->setState('nip',$model->nip);
			$this->setState('nipbaru',$model->nipbaru);
			$this->setState('id_wilayah',$model->id_wilayah);
			$this->setState('id_unitkerja',$model->id_unitkerja);
			$this->setState('id_eselon',$model->id_eselon);
			$this->setState('id_fungsional',$model->id_fungsional);
			$this->setState('unitkerja',isset($model->id_unitkerja)?$model->unitkerja->unitkerja:null);
			$this->setState('isAdmin',(boolean)$model->is_admin);

			if(substr($model->id_wilayah,-2)=='00'){
				$this->setState('satuankerja','BPS Prov. '.$model->wilayah->wilayah);
			} elseif(substr($model->id_wilayah,2,1)>=7) {
				$this->setState('satuankerja','BPS '.$model->wilayah->wilayah);
			} else {
				$this->setState('satuankerja','BPS Kab. '.$model->wilayah->wilayah);
			}

/*			if(SpjOperator::model()->findByAttributes(array('id_pegawai'=>$model->id)) || $this->getState('id_eselon')==3 || ($this->getState('id_eselon')==4 && $this->getState('id_unitkerja')=='9281'))
				$this->setState('isSpj',true);
*/
			$model->last_login = time();
			$model->save();
		}

		return !$this->errorCode;
	}

	public function getId()
	{
		return $this->model->id;
	} 

	public function getName()
	{
		return $this->model->nama_pegawai;
	}
}