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
	    <thead>
	        <?php for ($i = 0; $i <= 81; $i++): ?>
	        <td><?php echo $i; ?></td>
	        <?php endfor; ?>
	    </thead>
	    <tbody>
        	<?php foreach ($data as $item): ?>
        	<?php $digits = str_split(str_replace(' ', '', $item->numbers)); ?>
        	<tr>
        	    <?php foreach ($digits as $digit): ?>
        	    <td><?php echo $digit; ?></td>
        	    <?php endforeach; ?>
        	</tr>
        	<?php endforeach; ?>
	    </tbody>
	</table>
	<?php endif; ?>
</div>

<script>
$(document).ready(function() {
	$('.lottery_channel_id').select2();
});
</script>