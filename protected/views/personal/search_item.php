<?php $isTarget = $data->getTarget_pegawai(Yii::app()->user->id); ?>
<div class="view">
	<div class="title"><?php echo CHtml::link(CHtml::encode($data->nama_kegiatan), array($isTarget? 'kegiatan' : '/kegiatan/view', 'id'=>$data->id),array('target'=>'_blank')); ?></div>
	<div class="meta">
		<?php echo CHtml::encode($isTarget? 'Kegiatan Saya' : $data->unitkerja->unitkerja); ?> &middot; 
		Target <?php echo CHtml::encode($isTarget? $isTarget->target_satuan : $data->target_satuan); ?> &middot; 
		Jadwal <?php echo CHtml::encode($data->jadwal_tahun); ?> &middot; 
		Progress <?php echo CHtml::encode($data->progress); ?>%  
	</div>
</div>