<?php

class ActionController extends Controller
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
				'actions'=>array('index','split','download','delete'),
				'expression'=>'$user->isAdmin',
			),
			array('deny',  
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex()
	{
		$this->render('index');
	}

	public function actionSplit()
	{
		$tahun = date('Y');

		if(isset($_POST['tahun']) && isset($_POST['bulan']) && (int) $_POST['tahun'] && (int) $_POST['bulan']){
			$tahun = (int) $_POST['tahun'];
			$bulan = (int) $_POST['bulan'];

			if($bulan<10) $bulan='0'.$bulan;

			if($tahun>=date('Y')-1 && mktime(0,0,0,$bulan,1,$tahun)<=time()){

				if(is_file($this->module->basePath.'/data/split.db')) unlink($this->module->basePath.'/data/split.db');
				copy($this->module->basePath.'/data/template.db', $this->module->basePath.'/data/split.db');

				$filename = 'split_'.Yii::app()->user->id_wilayah.'_'.$tahun.'.'.$bulan.'_'.time().'.db';
				$log = "Rekap Jumlah Record\n\n";

				$arr_nip = array();

				foreach(MasterPegawai::model()->findAllByAttributes(array('id_wilayah'=>Yii::app()->user->id_wilayah,'is_aktif'=>1)) as $pegawai){
					$model = new SplitPegawai();
					$model->id = substr($pegawai->nip,0,4)=='3400'? (int)substr($pegawai->nip,-5) : $pegawai->nip;
					$model->nama_pegawai = $pegawai->nama_pegawai;
					$model->nipbaru = $pegawai->nipbaru;
					$model->id_golongan = $pegawai->id_golongan;
					$model->id_wilayah = $pegawai->id_wilayah;
					$model->id_unitkerja = $pegawai->id_unitkerja;
					$model->id_eselon = $pegawai->id_eselon;
					$model->id_fungsional = $pegawai->id_fungsional;
					$model->username = $pegawai->username;

					if(!$model->hasErrors() && $model->save()){
						$arr_nip[$pegawai->id] = $model->id;
					}
				}
				$log .= 'Pegawai : '.SplitPegawai::model()->count()." orang\n";

				$arr_kegiatan = array();
				$arr_ckp = array();
				$arr_kredit = array();

				foreach(TabelKegiatan::model()->findAll(array(
					'condition'=>'left(tgl_mulai,4)='.$tahun.' AND mid(tgl_mulai,6,2)='.$bulan.' AND is_ckp=1',
					'order'=>'id_unitkerja'
				)) as $kegiatan){
					$model = new SplitKegiatan();
					$model->tahun = $tahun;
					$model->bulan = $bulan;
					$model->id_wilayah = Yii::app()->user->id_wilayah;
					$model->id_kegiatan = $kegiatan->id;
					$model->id_unitkerja = $kegiatan->id_unitkerja;
					$model->nama_kegiatan = $kegiatan->nama_kegiatan;
					$model->satuan = $kegiatan->satuan->nama_satuan;
					$model->jml_target = $kegiatan->jml_target;
					$model->tgl_mulai = $kegiatan->tgl_mulai;
					$model->tgl_selesai = $kegiatan->tgl_selesai;
					$model->is_ckp = $kegiatan->is_ckp;
					$model->keterangan = $kegiatan->keterangan;

					if($terampil=$kegiatan->getFungsional(1)) {
						$model->kode_terampil = $terampil->kredit->kode;
						$arr_kredit[$kegiatan->id][1] = $terampil->kredit;
					}
					if($ahli=$kegiatan->getFungsional(2)) {
						$model->kode_ahli = $ahli->kredit->kode;
						$arr_kredit[$kegiatan->id][2] = $ahli->kredit;
					}

					if(!$model->hasErrors() && $model->save()){
						$arr_kegiatan[$model->id_kegiatan] = $model->id;
					}
				}
				$log .= 'Kegiatan : '.SplitKegiatan::model()->count()." kegiatan\n";

				foreach(TabelTargetPegawai::model()->findAll(array(
					'condition'=>'id_kegiatan in ('.implode(',', array_keys($arr_kegiatan)).') AND jml_target>0',
					'order'=>'id_kegiatan, id_pegawai'
				)) as $target){
					$model = new SplitTarget();
					$model->id_wilayah = Yii::app()->user->id_wilayah;
					$model->id_kegiatan = $arr_kegiatan[$target->id_kegiatan];
					$model->id_pegawai = $arr_nip[$target->id_pegawai]; 
					$model->jml_target = $target->jml_target;
					$model->jml_realisasi = $target->jml_realisasi;
					$model->persen_kualitas = $target->persen_kualitas;
					$model->keterangan = $target->keterangan;

					if(!$model->hasErrors() && $model->save()){
						if($target->kegiatan->is_ckp) {
							$arr_ckp[$model->id_pegawai]['persen_kuantitas'][] = $model->jml_realisasi / $model->jml_target * 100;
							$arr_ckp[$model->id_pegawai]['persen_kualitas'][] = $model->persen_kualitas;
							if($target->pegawai->id_fungsional)
								$arr_ckp[$model->id_pegawai]['angka_kredit'][] = $arr_kredit[$arr_kegiatan[$target->id_kegiatan]][substr($target->pegawai->id_fungsional,0,1)]->{$model->pegawai->fungsional->kolom_kredit};
						}
					}
				}
				$log .= 'Alokasi : '.SplitTarget::model()->count()." O-K\n";

				foreach ($arr_ckp as $id => $value) {
					$model = new SplitCkp();
					$model->tahun = $tahun;
					$model->bulan = $bulan;
					$model->id_wilayah = Yii::app()->user->id_wilayah;
					$model->id_pegawai = $id;
					$model->id_unitkerja = $model->pegawai->id_unitkerja;
					$model->id_penilai = $model->pegawai->penilai->id;
					$model->jml_kegiatan = count($value['persen_kuantitas']);
					$model->r_kuantitas = array_sum($value['persen_kuantitas']) / count($value['persen_kuantitas']);
					$model->r_kualitas = array_sum($value['persen_kualitas']) / count($value['persen_kualitas']);
					$model->nilai_ckp = ($model->r_kuantitas + $model->r_kualitas) / 2; 
					if(count($value['angka_kredit']))
						$model->angka_kredit = array_sum($value['angka_kredit']);

					if(!$model->hasErrors()) $model->save();
				}


				copy($this->module->basePath.'/data/split.db', $this->module->basePath.'/data/'.$filename);
				$log .= "\nFilename : ".$filename;
			}
		}

		$this->render('split', array(
			'tahun'=>$tahun,
			'bulan'=>$bulan,
			'log'=>$log,
			'filename'=>$filename,
		));
	}

	public function actionDownload($file)
	{
		$filename = $this->module->basePath.'/data/'.$file;
		if(is_file($filename)){
//			header("Content-type: application/vnd-ms-excel");
			header("Content-Disposition: attachment; filename=".$file);
			readfile($filename);
			exit();	
		}
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