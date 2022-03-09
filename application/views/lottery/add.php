<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div id="content">
	<form class="add-form" method="post">
    	<div class="form-group" style="margin-top: 20px;">
        	<input type="text" class="date-picker" name="import_date" value="<?php echo (isset($_GET['date']) && $_GET['date']) ? date('Y-m-d', strtotime($_GET['date'])) : date('Y-m-d'); ?>" style="width: 100px;" />
        	<select class="lottery_channel_id" name="lottery_channel_id">
        		<option value="">Chọn Tỉnh/Thành</option>
        		<?php if ($channels): ?>
        		<?php foreach ($channels as $channel): ?>
        			<option <?php echo isset($_GET['channel_id']) && $_GET['channel_id'] == $channel->id ? 'selected' : ''; ?> value="<?php echo $channel->id; ?>"><?php echo $channel->channel_name . '(' . ($channel->week_day == 1 ? 'Chủ Nhật' : 'Thứ ' . ($channel->week_day)) . ')'; ?></option>
        		<?php endforeach; ?>
        		<?php endif; ?>
        	</select>
    	</div>
    	<textarea rows="20" cols="50" name="numbers"></textarea>
    	<br/>
    	<button type="submit">Lưu</button>
	</form>
</div>

<script>
$(document).ready(function() {
	$('.date-picker').datepicker({ dateFormat: 'yy-mm-dd' });
	$('.lottery_channel_id').select2();
});
</script>