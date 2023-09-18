<?php
/* @var $this SiteController */
/* @var $error array */

$this->pageDescription='';
$this->pageCaption='Error '.$code;
?>

<div class="error">
<?php echo CHtml::encode($message); ?>
</div>