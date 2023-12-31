<?php
Yii::import('zii.widgets.grid.CGridColumn');

class IndexColumn extends CGridColumn 
{
	public $sortable = false;
	public $header = '#';
	public $headerHtmlOptions = array('style'=>'width:20px');

	public function init()
	{
		parent::init();
	}

	protected function renderDataCellContent($row,$data)
	{
		$pagination = $this->grid->dataProvider->getPagination();
		$index = $pagination->pageSize * $pagination->currentPage + $row + 1;
		echo $index;
	}

}