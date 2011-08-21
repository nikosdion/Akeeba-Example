<?php defined('_JEXEC') or die(); ?>

<?php if(!count($this->items)): ?>
<h2>No backup records</h2>
<p>Sorry, the site you specified indicates that there are no backup records available.</p>
<?php else: ?>

<table width="100%" border="1">
	<thead>
		<tr>
			<th rowspan="2">ID</th>
			<th rowspan="2">Description</th>
			<th colspan="2">Backup</th>
			<th rowspan="2">Status</th>
			<th rowspan="2">Origin</th>
			<th rowspan="2">Type</th>
			<th rowspan="2">Profile ID</th>
			<th rowspan="2">Total Size</th>
		</tr>
		<tr>
			<th>Start</th>
			<th>Duration (sec)</th>
		</tr>
	</thead>
	<tbody>
		<?php jimport('joomla.utilities.date'); ?>
		<?php foreach($this->items as $item): ?>
		<?php
			$dateFrom = new JDate($item->backupstart);
			$dateTo = new JDate($item->backupend);
			$duration = $dateTo->getTimestamp() - $dateFrom->getTimestamp();
		?>
		<tr>
			<td><?php echo $this->escape($item->id)?></td>
			<td><?php echo $this->escape($item->description)?></td>
			<td><?php echo $dateFrom->format('Y-m-d H:i:s');?></td>
			<td><?php echo $duration ?></td>
			<td><?php echo $item->status?></td>
			<td><?php echo $item->origin?></td>
			<td><?php echo $item->type?></td>
			<td><?php echo $item->profile_id?></td>
			<td><?php echo $item->total_size?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php endif; ?>

<p><a href="index.php">Go back to the first page</a></p>

