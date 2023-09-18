<?php

class SkpModule extends CWebModule
{
	public $defaultController = 'pegawai';

	public function init()
	{
		$this->setImport(array(
			'skp.models.*',
			'skp.components.*',
		));
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			return true;
		}
		else
			return false;
	}
}
