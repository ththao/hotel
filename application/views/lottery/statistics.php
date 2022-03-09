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
	<div class="form-group" style="margin-top: 20px;">
		<select class="channel_area" name="channel_area">
    		<option value="MN" <?php echo !isset($_GET['channel_area']) || $_GET['channel_area'] == 'MN' ? 'selected' : ''; ?>>Miền Nam</option>
    		<option value="MT" <?php echo isset($_GET['channel_area']) && $_GET['channel_area'] == 'MT' ? 'selected' : ''; ?>>Miền Trung</option>
    		<option value="MB" <?php echo isset($_GET['channel_area']) && $_GET['channel_area'] == 'MB' ? 'selected' : ''; ?>>Miền Bắc</option>
    	</select>
    	<?php $channel_ids = isset($_GET['lottery_channel_id']) && $_GET['lottery_channel_id'] ? explode(',', $_GET['lottery_channel_id']) : []; ?>
    	<select class="lottery_channel_id" multiple="multiple" name="lottery_channel_id">
    		<option value="">Chọn Tỉnh/Thành</option>
    		<?php if ($channels): ?>
    		<?php foreach ($channels as $channel): ?>
    			<option channel_area="<?php echo $channel->channel_area; ?>" value="<?php echo $channel->id; ?>" <?php echo in_array($channel->id, $channel_ids) ? 'selected' : ''; ?>>
    			<?php echo $channel->channel_name . '(' . ($channel->week_day == 1 ? 'Chủ Nhật' : 'Thứ ' . ($channel->week_day)) . ')'; ?>
    			</option>
    		<?php endforeach; ?>
    		<?php endif; ?>
    	</select>
    	<button class="view-statistics">Xem</button>
	</div>
	
	<?php if (isset($daily_data) && $daily_data): ?>
	<table>
		<thead>
			<tr>
				<th></th>
				<th style="text-align: center; padding: 5px;" colspan="<?php echo count($weekly_week); ?>">Thống kê tuần</th>
				<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
				<th style="text-align: center; padding: 5px;" colspan="<?php echo count($daily_date); ?>">Thống kê ngày</th>
			</tr>
			<tr>
				<th></th>
				<?php if ($weekly_week): ?>
				<?php foreach ($weekly_week as $week): ?>
				<th style="padding: 5px;"><?php echo $week; ?></th>
				<?php endforeach; ?>
				<?php endif; ?>
				<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
				<?php foreach ($daily_date as $date): ?>
				<th><?php echo date('d/m', strtotime($date)); ?></th>
				<?php endforeach; ?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($daily_data as $number => $data): ?>
			    
			    <?php if ($number == 'total'): ?>
			        <tr style="border: 1px solid black !important; background: #00ffff;">
			            <td style="padding: 8px 3px; text-align: left;" colspan="<?php echo 1 + ($weekly_week ? count($weekly_week) : 0) + 1 + count($daily_date); ?>">Thống kê dàn</td>
			        </tr>
			    <?php else: ?>
    				<?php foreach ($data as $position => $item): ?>
            			<tr class="data-row" style="<?php echo is_numeric($number) && intval($number) % 2 == 1 ? 'background: #DEDEDE;' : ''; ?>">
            				<td style="padding: 5px;"><?php echo $number; ?></td>
    				        <?php if ($weekly_week): ?>
            				<?php foreach ($weekly_week as $week): ?>
            					<?php if (isset($weekly_data[$number]) && isset($weekly_data[$number][$position]) && isset($weekly_data[$number][$position][$week]) && $weekly_data[$number][$position][$week]['count']): ?>
            					<td style="padding: 5px;">
            					    <span title="<?php echo $weekly_data[$number][$position][$week]['notes']; ?>"><?php echo $weekly_data[$number][$position][$week]['count']; ?></span>
        					    </td>
            					<?php else: ?>
            					<td style="padding: 5px; color: red; font-weight: bold;">0</td>
            					<?php endif; ?>
            				<?php endforeach; ?>
    			            <?php endif; ?>
            				<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            				<?php foreach ($daily_date as $date): ?>
            					<td style="<?php echo (!isset($_GET['lottery_channel_id']) || !$_GET['lottery_channel_id']) && date('w', strtotime($date)) == date('w') ? 'background: #ffbf00;' : ''; ?>">
            					    <?php if (isset($item[$date]) && $item[$date]): ?>
                					    <?php if (is_numeric($number)): ?>
                					        <span style="color: #ff00ff; font-weight: bold;"><?php echo $item[$date]; ?></span>
                					    <?php else: ?>
                    					    <?php if ((isset($_GET['channel_area']) && $_GET['channel_area'] == 'MB' && $item[$date] > 3) || $item[$date] > 4): ?>
                    					        <span style="color: #ff00ff; font-weight: bold;"><?php echo $item[$date]; ?></span>
                    					    <?php else: ?>
                    					        <span><?php echo $item[$date]; ?></span>
                    					    <?php endif; ?>
                					    <?php endif; ?>
            					    <?php else: ?>
            					        <span>0</span>
            					    <?php endif; ?>
            					
            					</td>
    						<?php endforeach; ?>
            			</tr>
    				<?php endforeach; ?>
				<?php endif; ?>
			<?php endforeach; ?>
			
		</tbody>
	</table>
	<?php endif; ?>
</div>

<script>
$(document).ready(function() {
	$(document).on('change', '.channel_area', function() {
		$('.lottery_channel_id').val('');
		$('.lottery_channel_id').find('option').prop('disabled', true);
		$('.lottery_channel_id').find('option[channel_area="' + $(this).val() + '"]').prop('disabled', false);
	});
	
	$(document).on('click', '.data-row', function() {
		if ($(this).hasClass('active')) {
			$(this).removeClass('active').addClass('deactive');
		} else if ($(this).hasClass('deactive')) {
		    $(this).removeClass('deactive');
	    } else {
			$(this).addClass('active');
		}
	});
	
	$(document).on('click', '.view-statistics', function(e) {
	    e.preventDefault();
	    
	    if ($('.lottery_channel_id').val()) {
	        window.location = '/lottery/statistics?channel_area=' + $('.channel_area').val() + '&lottery_channel_id=' + $('.lottery_channel_id').val();
	    } else {
	        window.location = '/lottery/statistics?channel_area=' + $('.channel_area').val();
	    }
	});
	
	$('.channel_area, .lottery_channel_id').select2();
});
</script>