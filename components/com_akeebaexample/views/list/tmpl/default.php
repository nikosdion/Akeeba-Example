<?php defined('_JEXEC') or die(); ?>

<?php if(!count($this->items)): ?>
<h2><?php echo JText::_('COM_AKEEBAEXAMPLE_LIST_NORECORDS') ?></h2>
<p><?php echo JText::_('COM_AKEEBAEXAMPLE_LIST_NORECORDSEXPLANATION');?></p>
<?php else: ?>

<table width="100%" border="1">
	<thead>
		<tr>
			<th rowspan="2"><?php echo JText::_('COM_AKEEBAEXAMPLE_LIST_FIELD_ID');?></th>
			<th rowspan="2"><?php echo JText::_('COM_AKEEBAEXAMPLE_LIST_FIELD_DESCRIPTION');?></th>
			<th colspan="2"><?php echo JText::_('COM_AKEEBAEXAMPLE_LIST_FIELD_BACKUP');?></th>
			<th rowspan="2"><?php echo JText::_('COM_AKEEBAEXAMPLE_LIST_FIELD_STATUS');?></th>
			<th rowspan="2"><?php echo JText::_('COM_AKEEBAEXAMPLE_LIST_FIELD_ORIGIN');?></th>
			<th rowspan="2"><?php echo JText::_('COM_AKEEBAEXAMPLE_LIST_FIELD_TYPE');?></th>
			<th rowspan="2"><?php echo JText::_('COM_AKEEBAEXAMPLE_LIST_FIELD_PROFILEID');?></th>
			<th rowspan="2"><?php echo JText::_('COM_AKEEBAEXAMPLE_LIST_FIELD_TOTALSIZE');?></th>
		</tr>
		<tr>
			<th><?php echo JText::_('COM_AKEEBAEXAMPLE_LIST_FIELD_START');?></th>
			<th><?php echo JText::_('COM_AKEEBAEXAMPLE_LIST_FIELD_DURATION');?></th>
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

<p><a href="index.php"><?php echo JText::_('COM_AKEEBAEXAMPLE_LIST_GOBACK') ?></a></p>

