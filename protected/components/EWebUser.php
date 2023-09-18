<?php
class EWebUser extends CWebUser{
 
    protected $_model;

    protected function loadUser()
    {
        if ( $this->_model === null ) {
                $model = MasterPegawai::model()->findByPk(Yii::app()->user->id);
                $this->_model = $model;
        }
        return $this->_model;
    }

    public function getId_wilayah()
    {   
        return $this->getState('id_wilayah');
    }

    public function getWilayah()
    {   
        return $this->getState('kode_wilayah');
    }

    public function getId_unitkerja()
    {   
        return $this->getState('id_unitkerja');
    }

    public function getUnitkerja()
    {   
        return $this->getState('unitkerja');
    }

    public function getSatuankerja()
    {   
        return $this->getState('satuankerja');
    }

     public function getId_eselon()
    {   
        return $this->getState('id_eselon');
    }

      public function getId_fungsional()
    {   
        return $this->getState('id_fungsional');
    }

    public function getIsKasi()
    {   
        return !Yii::app()->user->isGuest && $this->getState('id_eselon')<=4 && $this->getState('id_eselon')>0;
    }

    public function getIsKepala()
    {   
        return !Yii::app()->user->isGuest && $this->getState('id_eselon')<=3 && $this->getState('id_eselon')>0 && substr($this->getState('id_unitkerja'),-1)=='0';
    }

    public function getIsFungsional()
    {   
        return !Yii::app()->user->isGuest && $this->getState('id_fungsional')>10 && $this->getState('id_fungsional')<30;
    }

    public function getIsAdmin()
    {
    	return !Yii::app()->user->isGuest && $this->getState('isAdmin');
    }

/*    public function getIsSpj()
    {
        return !Yii::app()->user->isGuest && $this->getState('isSpj');
    }
*/
}