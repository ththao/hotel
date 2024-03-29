<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
table {
    position: relative;
    border-collapse: collapse;
    height: 650px;
    display: block;
}
table thead th {
    border: 1px solid black !important;
    background: #00ffff;
    padding: 8px 3px;
    position: sticky;
    top: 0;
}
td {
    border: 1px solid black;
    padding: 3px;
    text-align: center;
}
.data-row.active {
    background: #80ff00 !important;
}
.data-row.deactive {
    background: #ff9999 !important;
}
</style>

<div id="content">
	<form class="view-form" method="get">
    	<div class="form-group" style="margin-top: 20px;">
        	<select class="lottery_channel_id" name="channel_id">
        		<option value="">Chọn Tỉnh/Thành</option>
        		<?php if ($channels): ?>
        		<?php foreach ($channels as $channel): ?>
        			<option <?php echo $channel_id == $channel->id ? 'selected' : ''; ?> value="<?php echo $channel->id; ?>"><?php echo $channel->channel_name . ($channel->id == 1 ? '' : (' (' . ($channel->week_day == 1 ? 'Chủ Nhật' : 'Thứ ' . ($channel->week_day)) . ')')); ?></option>
        		<?php endforeach; ?>
        		<?php endif; ?>
        	</select>
    	    <button type="submit">Xem</button>
    	</div>
	</form>
	
	<?php if ($data): ?>
	<table>
	    <thead><tr><td>Date</td><td>Care</td><td>Matched</td><td>Earn</td><td>Invert Care</td><td>Invert Matched</td><td>Invert Earn</td></ta><td>A</td><td>B</td></tr></thead>
	    <tbody>
            <?php $total = 0; ?>
            <?php $invert_total = 0; ?>
        	<?php foreach ($data as $item): ?>
        	<?php $total += $item['a'] ? ($channel_id == 1 ? ($item['count']*80 - 5*24) : ($item['count']*80 - 5*30)) : 0; ?>
        	<?php $invert_total += $item['a'] ? ($channel_id == 1 ? ($item['invert_count']*80 - 5*24) : ($item['invert_count']*80 - 5*30)) : 0; ?>
        	<tr>
        	    <td><?php echo $item['import_date']; ?></td>
        	    <td><?php echo $item['potential_numbers']; ?></td>
        	    <td><?php echo $item['count']; ?></td>
        	    <td><?php echo $item['a'] ? ($channel_id == 1 ? ($item['count']*80 - 5*24) : ($item['count']*80 - 5*30)) : 0; ?></td>
        	    <td><?php echo $item['invert_numbers']; ?></td>
        	    <td><?php echo $item['invert_count']; ?></td>
        	    <td><?php echo $item['a'] ? ($channel_id == 1 ? ($item['invert_count']*80 - 5*24) : ($item['invert_count']*80 - 5*30)) : 0; ?></td>
        	    <td><?php echo $item['a']; ?></td>
        	    <td><?php echo $item['b']; ?></td>
        	</tr>
        	<?php endforeach; ?>
        	<tr>
        	    <td>Total</td>
        	    <td></td>
        	    <td></td>
        	    <td><?php echo $total; ?></td>
        	    <td></td>
        	    <td></td>
        	    <td><?php echo $invert_total; ?></td>
        	    <td></td>
        	    <td></td>
        	</tr>
	    </tbody>
	</table>
	<?php endif; ?>
</div>

<script>
$(document).ready(function() {
	$('.lottery_channel_id').select2();
});
</script>