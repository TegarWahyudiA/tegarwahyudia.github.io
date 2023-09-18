<?php
require 'lib/TADFactory.php';
require 'lib/TAD.php';
require 'lib/TADResponse.php';
require 'lib/Providers/TADSoap.php';
require 'lib/Providers/TADZKLib.php';
require 'lib/Exceptions/ConnectionError.php';
require 'lib/Exceptions/FilterArgumentError.php';
require 'lib/Exceptions/UnrecognizedArgument.php';
require 'lib/Exceptions/UnrecognizedCommand.php';

use TADPHP\TADFactory;
use TADPHP\TAD;

class Presensi extends CWidget
{
    public function init()
    {
        return parent::init();
    }

    public function run()
    {
	
    }
	
	function getArray($id_presensi, $tgl_mulai, $tgl_selesai, $ip_presensi){
		$return = array();

		$tad_factory = new TADFactory(array('ip'=>$ip_presensi));
		$tad = $tad_factory->get_instance();
		$logs = $tad->get_att_log(array('pin'=>$id_presensi));

		if($tgl_mulai || $tgl_selesai){
			$logs = $logs->filter_by_date(
			  array(
				'start'=>$tgl_mulai,
				'end'=>$tgl_selesai,
			  )
			);
		}

		if($array = $logs->to_array()){
/*			foreach($array['Row'] as $row){
			  if(isset($row['PIN']))
				$return[] = array(
					'id' => $row['PIN'],
					'tanggal' => substr($row['DateTime'],0,10),
					'jam' => substr($row['DateTime'],-8),
					'tombol' => $row['Status'],
				);
			}
*/
		return $array['Row'];
		}
	}

	function test()
	{
		return array(1,2,3);
	}
}