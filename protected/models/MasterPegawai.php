<?php
class MasterPegawai extends CActiveRecord
{
	public function tableName()
	{
		return 'm_pegawai';
	}

	public $password, $konfirmasi;
	
	public function rules()
	{
		return array(
			array('nip, nipbaru, nama_pegawai, id_unitkerja', 'required'),
			array('nip', 'length', 'max'=>9),
			array('nipbaru', 'length', 'max'=>18),
			array('nama_pegawai', 'length', 'max'=>32),
			array('username', 'length', 'max'=>64),
			array('id_golongan, id_wilayah, id_unitkerja, id_eselon, id_fungsional, is_aktif, is_admin, id_presensi', 'numerical', 'integerOnly'=>true),
			array('hash', 'length', 'max'=>1024),
			array('last_login', 'safe'),
			array('nip, nipbaru, username', 'unique'),

			array('id, nip, nipbaru, nama_pegawai, id_presensi, id_unitkerja, id_eselon, username', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'wilayah'=>array(self::BELONGS_TO,'MasterWilayah','id_wilayah'),
			'unitkerja'=>array(self::BELONGS_TO,'MasterUnitkerja','id_unitkerja'),
			'target'=>array(self::HAS_MANY,'TabelTargetPegawai','id_pegawai','order'=>'tgl_mulai,tgl_selesai'),
			'mingguan'=>array(self::HAS_MANY,'TabelTargetMingguan','id_pegawai'),
			'realisasi'=>array(self::HAS_MANY,'TabelRealisasi','id_pegawai'),
			'golongan'=>array(self::BELONGS_TO,'MasterGolongan','id_golongan'),
			'fungsional'=>array(self::BELONGS_TO,'MasterFungsional','id_fungsional'),
		);
	}

	public function getPresensi($tahun, $bulan)
	{
		return CalendarPersonal::model()->findAll(array(
			'condition'=>'FingerPrintID='.$this->id_presensi.' AND year(PersonalCalendarDate)='.$tahun.' AND month(PersonalCalendarDate)='.$bulan,
			'order'=>'PersonalCalendarDate'));
	}

	public function getSatuan_kerja()
	{
		if(substr($this->id_wilayah,-2)=='00')
			return 'BPS Prov. '.$this->wilayah->wilayah;
		elseif(substr($this->id_wilayah,3,1)>=7)
			return 'BPS '.$this->wilayah->wilayah;
		else
			return 'BPS Kab. '.$this->wilayah->wilayah;
	}

	public function getJabatan()
	{
		if($this->id_eselon == 3)
			//return $this->unitkerja->unitkerja;
			return 'Kepala '.$this->unitkerja->unitkerja;
		elseif($this->id_eselon == 4)
			if($this->id_unitkerja == 9281)
				return 'Kepala '.$this->unitkerja->unitkerja;
			else
				return 'Koordinator '.$this->unitkerja->unitkerja;
		elseif($this->id_unitkerja==9287)
			return $this->unitkerja->unitkerja;
		else
			return 'Staff '.$this->unitkerja->unitkerja;
	}

	public function getAtasan()
	{
		if($this->id_eselon==3)
			return $this->model()->findByAttributes(array('id_eselon'=>2, 'id_unitkerja'=>substr($this->id_unitkerja,0,2).'00'));
		elseif($this->id_eselon==4 || $this->id_unitkerja>=9287) 	
			return $this->model()->findByAttributes(array('id_eselon'=>3, 'id_unitkerja'=>substr($this->id_unitkerja,0,3).'0'));
		else 		
			return $this->model()->findByAttributes(array('id_eselon'=>4, 'id_unitkerja'=>$this->id_unitkerja));
	}

	public function getPenilai()
	{
		if($this->id_eselon==3)
			return $this->model()->findByAttributes(array('id_eselon'=>2, 'id_unitkerja'=>substr($this->id_unitkerja,0,2).'00'));
		elseif($this->id_eselon==4 || $this->id_unitkerja>=9287 || (in_array(substr($this->id_fungsional,0,1),array(1,2)) && $this->id_golongan>=33 )) 	
			return $this->model()->findByAttributes(array('id_eselon'=>3, 'id_unitkerja'=>substr($this->id_unitkerja,0,3).'0'));
		else 		
			return $this->model()->findByAttributes(array('id_eselon'=>4, 'id_unitkerja'=>$this->id_unitkerja));		
	}

	public function getCkp($tahun,$bulan)
	{
		return CKP::model()->findByAttributes(array(
			'tahun'=>$tahun,
			'bulan'=>$bulan,
			'id_pegawai'=>$this->id
		));
	}

/*	public function getTarget($tahun,$bulan)
	{
		return TabelTargetPegawai::model()->findAll(array(
			'join'=>'kegiatan',
			'condition'=>'year(tgl_mulai)='.$tahun.' and month(tgl_mulai)='.$bulan.' and id_pegawai='.$this->id.' AND is_ckp=1 and id_flag=0',
			'order'=>'kegiatan.nama_kegiatan',
		));
	}
*/
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'nip' => 'ID BPS',
			'nipbaru' => 'NIP',
			'nama_pegawai' => 'Nama Pegawai',
			'id_golongan' => 'Golongan',
			'id_wilayah' => 'Wilayah',
			'id_unitkerja' => 'Unitkerja',
			'id_eselon' => 'Eselon',
			'id_fungsional' => 'Fungsional',
			'id_presensi' => 'ID Presensi',
			'is_aktif' => 'Status',
			'is_admin' => 'Level',
			'username' => 'Username Login',
			'hash' => 'Password',
			'last_login' => 'Last Login',
			'jabatan'=>'Jabatan',
			'str_last_login' => 'Login Terakhir',
			'atasan' => 'Atasan',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('nip',$this->nip,true);
		$criteria->compare('nipbaru',$this->nipbaru,true);
		$criteria->compare('nama_pegawai',$this->nama_pegawai,true);
		$criteria->compare('id_w;',$this->id_wilayah);
		$criteria->compare('id_golongan',$this->id_golongan);
		$criteria->compare('id_eselon',$this->id_eselon);
		$criteria->compare('last_login',$this->last_login,true);

		$criteria->order='case when id_eselon<2 then 9 else id_eselon end, id_unitkerja, nama_pegawai';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function authenticate($username, $password)
	{ 
		$user = $this->model()->findByAttributes(array('username'=>$username));
//		if($user && $password === $user->hash) {
		if($user && sha1($user->salt.$password) === $user->hash) {
			//$user->last_login = strftime('%F %T');
			//$user->save();
			return $user;
		} /* elseif($user && function_exists('curl_init')) {
			$data_post = "uname=" . $username ."&pass=" . $password . "&appname=Front Page";
			$pages = array(
				'login'=>'https://community.bps.go.id/libs/clogin.php',
				'redirect'=>'https://community.bps.go.id/portal/index.php?id=0,0,0',
				'profil'=>'https://community.bps.go.id/portal/index.php?id=2,6,');

			$ch = curl_init();
			//Set options for curl session
			$options = array(
				CURLOPT_USERAGENT => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)',
				CURLOPT_VERBOSE => FALSE,
				CURLOPT_SSL_VERIFYPEER => FALSE,
				//CURLOPT_SSL_VERIFYHOST => 2,
				CURLOPT_HEADER => TRUE,
				CURLOPT_RETURNTRANSFER => TRUE,
				CURLOPT_COOKIEFILE => '.cookie',
				CURLOPT_COOKIEJAR => '.cookies'
			);

			//Login using POST
			$options[CURLOPT_URL] = $pages['login'];
			$options[CURLOPT_POST] = TRUE;
			$options[CURLOPT_POSTFIELDS] = $data_post;
			//$options[CURLOPT_FOLLOWLOCATION] = TRUE;
			curl_setopt_array($ch, $options);
			$login = $this->curl_exec_follow($ch);
			//echo htmlentities($login); die();
			if(strpos($login, 'Set-Cookie:'))
			{ 
				$user->salt = uniqid(mt_rand(), true);
				$user->hash = sha1($user->salt.$password);
				$user->last_login = time();
				if(!$user->hasErrors() && $user->save())
					return $user;
			} else
				return; */
		//} 
		else
			return;
	}

	private function curl_exec_follow($ch, &$maxredirect = null) {
		// we emulate a browser here since some websites detect 
		// us as a bot and don't let us do our job 
		$user_agent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0"; 
		curl_setopt($ch, CURLOPT_USERAGENT, $user_agent ); 
		$mr = $maxredirect === null ? 5 : intval($maxredirect); 
		if (filter_var(ini_get(‘open_basedir’), FILTER_VALIDATE_BOOLEAN) === false && filter_var(ini_get(‘safe_mode’), FILTER_VALIDATE_BOOLEAN) === false ) { 
			// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $mr > 0); 
			curl_setopt($ch, CURLOPT_MAXREDIRS, $mr); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
		} else { 
			// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); 
			if ($mr > 0) { 
				$original_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL); 
				$newurl = $original_url; 
				$rch = curl_copy_handle($ch); 
				curl_setopt($rch, CURLOPT_HEADER, true); 
				curl_setopt($rch, CURLOPT_NOBODY, true); 
				curl_setopt($rch, CURLOPT_FORBID_REUSE, false); 
				do { 
					curl_setopt($rch, CURLOPT_URL, $newurl); 
					$header = curl_exec($rch); 
					if (curl_errno($rch)) { 
						$code = 0; 
					} else { 
						$code = curl_getinfo($rch, CURLINFO_HTTP_CODE); 
						if ($code == 301 || $code == 302) { 
							preg_match('/Location:(.*?)\n/i', $header, $matches); 
							$newurl = trim(array_pop($matches)); 
							// if no scheme is present then the new url is a 
							// relative path and thus needs some extra care 
							if(!preg_match("/^https?:/i", $newurl)){ 
								$newurl = $original_url . $newurl; 
							} 
						} else { 
							$code = 0; 
						} 
					} 
				} while ($code && --$mr); 
				curl_close($rch); 
				if (!$mr) { 
					if ($maxredirect === null) 
						trigger_error('Too many redirects.', E_USER_WARNING); 
					else 
						$maxredirect = 0; 
					return false; 
				} 
				curl_setopt($ch, CURLOPT_URL, $newurl); 
			} 
		} 
		return curl_exec($ch);
	} 
}
