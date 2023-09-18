<?php
$this->pageTitle='Import Master Pegawai';
$this->pageCaption=$this->pageTitle;
$this->pageDescription='';
$this->breadcrumbs=array(
	'Master Pegawai'=>array('pegawai'),
	'Import',
);
?>
<div class="form">
	<form class="form-horizontal" id="import-form" action="" method="post" enctype="multipart/form-data">

		<div class="control-group">
			<label class="control-label required" for="xlf">Excel Pegawai *</label>
			<div class="input">
				<input name="xlf" id="xlf" type="file" />
			</div>
		</div>

		<div class="control-group">
			<label class="control-label required" for="file_template_xls"></label>
			<div class="input">
				<?php echo CHtml::link(BHtml::icon('download').'Download Template', array('pegawai_template'));?>
			</div>
		</div>

		<hr>

		<div class="control-group">
			<label class="control-label required" for="submit"></label>
			<div class="input">
				<input type="submit" class="btn-primary btn" name="submit" value="Upload" />
			</div>
		</div>

	</form>
</div><!-- form -->

<?php
if($data){
	echo '<ol>';
	foreach($data as $key=>$val)
		echo '<li>'.json_encode($val).'</li>';
	echo '</ol>';
}
	