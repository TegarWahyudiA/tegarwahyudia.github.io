<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();

	public function init() {
	    $this->attachBehavior('bootstrap', new BController($this));
	}

	public function setState($state,$value)
	{
		return Yii::app()->user->setState($state,$value);
	}
	
	public function getState($state)
	{
		return Yii::app()->user->getState($state);
	}
	
	public function renderNormal($view, $data=null, $return=false, $options=null)
	{
		$output = parent::render($view,$data,true);
/*		
		$compactor = Yii::app()->contentCompactor;
		if($compactor==null)
			throw new CHttpException(500, Yii::t('message','Missing component Content Compactor in configuration'));
		
		$output = $compactor->compact($output, $options);
*/		
		if($return)
			return $output;
		else
			echo $output;
	}

/*	public function render($view, $data=null, $return=false, $options=null)
	{
		$output = parent::render($view,$data,true);
		
		$compactor = Yii::app()->contentCompactor;
		if($compactor==null)
			throw new CHttpException(500, Yii::t('message','Missing component Content Compactor in configuration'));
		
		$output = $compactor->compact($output, $options);
		
		if($return)
			return $output;
		else
			echo $output;
	}
*/
}