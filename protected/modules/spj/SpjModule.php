<?php

class SpjModule extends CWebModule
{
	public $defaultController = 'operator';

	public function init()
	{
		$this->setImport(array(
			'spj.models.*',
			'spj.components.*',
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
