<?php $this->beginContent('//layouts/main'); ?>
<div class="container">
	<div class="appcontent">
<?php if($this->pageCaption !== '') : ?>
		<div class="page-header">
			<h1><?php echo $this->pageCaption; ?> <small><?php echo $this->pageDescription;?></small></h1>
		</div>
<?php endif; ?>
		<div class="row">
			<div class="span12">
				<?php echo $content; ?>
			</div>
		</div>
	</div>
</div> <!-- /container -->
<?php $this->endContent(); ?>