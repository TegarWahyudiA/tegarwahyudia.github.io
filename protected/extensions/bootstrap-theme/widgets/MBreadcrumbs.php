<?php

Yii::import('zii.widgets.CBreadcrumbs');

class MBreadcrumbs extends CBreadcrumbs {
	public function run()
	{
		if(empty($this->links))
			return;
		
		//if(!isset($this->links) || $this->links[0]==='') {
		//	echo '<div class="breadcrumb" style="padding:0; border:none;"></div>';
		//	return;
		//}
		
		if(isset($this->htmlOptions['class']))
			$this->htmlOptions['class'] .= ' small';
		else
			$this->htmlOptions['class'] = 'small';

		echo CHtml::openTag('nav',$this->htmlOptions);
		echo CHtml::openTag('ul')."\n";

		$links=array();
		if($this->homeLink===null)
			$links[]=CHtml::link(Yii::t('zii','<i class="icon-home"></i>'),Yii::app()->homeUrl);
		else if($this->homeLink!==false)
			$links[]=$this->homeLink;
		foreach($this->links as $label=>$url)
		{
			if(is_string($label) || is_array($url))
				$links[]=CHtml::link($this->encodeLabel ? CHtml::encode($label) : $label, $url);
			else
				$links[]=($this->encodeLabel ? CHtml::encode($url) : $url);
		}
		$separator = CHtml::closeTag('li')."\n<li></li>\n" . CHtml::openTag('li');
		echo CHtml::openTag('li').implode($separator,$links).CHtml::closeTag('li')."\n";
		echo CHtml::closeTag('ul');
		echo CHtml::closeTag('nav')."\n";
	}
}