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
    	<button class="view-statistics">Xem</button>
	</div>
	
	<?php if (isset($daily_data) && $daily_data): ?>
	<table>
		<thead>
			<tr>
				<?php foreach ($daily_data as $date => $numbers): ?>
				<th><?php echo date('d/m', strtotime($date)); ?></th>
				<?php endforeach; ?>
			</tr>
		</thead>
		<tbody>
			<tr>
				<?php foreach ($daily_data as $date => $numbers): ?>
				<td>
				    <?php if ($numbers): ?>
				    <?php sort($numbers); ?>
				    <?php foreach ($numbers as $number): ?>
				    <?php echo $number > 100 ? $number : ($number > 10 ? ('0' . $number) : ('00' . $number)) . '<br/>'; ?>
				    <?php endforeach; ?>
				    <?php endif; ?>
			    </td>
				<?php endforeach; ?>
			</tr>
			<tr>
			    <?php 
			    $all = [];
			    $count3 = [];
			    ?>
				<?php foreach ($daily_data as $date => $numbers): ?>
				<td>
				    <?php 
				    if ($numbers) {
				        $count2 = [];
    				    foreach ($numbers as $number) {
    				        if (!isset($all[$number%100])) {
    				            $all[$number%100] = [];
    				        }
    				        if (!isset($all[$number%100]['dates'])) {
    				            $all[$number%100]['dates'] = [];
    				        }
    				        $all[$number%100]['dates'][] = date('d/m', strtotime($date));
    				        if (!isset($all[$number%100]['numbers'])) {
    				            $all[$number%100]['numbers'] = [];
    				        }
    				        $all[$number%100]['numbers'][] = $number;
    				        
				            $center_d = floor(($number%100)/10);
				            $count2[$center_d] = (isset($count2[$center_d]) ? $count2[$center_d] : 0) + 1;
				            
				            if (!isset($count3[$date])) {
				                $count3[$date] = [];
				            }
				            $left_d = floor($number/100);
				            $count3[$date][$left_d] = (isset($count3[$date][$left_d]) ? $count3[$date][$left_d] : 0) + 1;
    				    }
    				    ksort($count2);
    				    foreach ($count2 as $digit => $c) {
    				        echo $digit . ': ' . $c . '<br/>';
    				    }
				    }
				    ?>
			    </td>
				<?php endforeach; ?>
			</tr>
			<tr style="display: none;">
				<?php foreach ($count3 as $date => $numbers): ?>
				<td>
				    <?php
				        ksort($numbers);
    				    foreach ($numbers as $digit => $c) {
    				        echo $digit . ': ' . $c . '<br/>';
    				    }
				    ?>
			    </td>
				<?php endforeach; ?>
			</tr>
		</tbody>
	</table>
	
	<table>
	    <?php
	    /*function cmp($a, $b) {
            return strcmp($a["date"], $b["date"]);
        }
        
        usort($all, "cmp");*/
	    ksort($all);
	    foreach ($all as $num => $data) {
	        echo '<tr>';
	        echo '<td>' . $num . '</td>';
	        echo '<td>' . implode(',', $data['numbers']) . '</td>';
	        echo '<td>' . implode(',', $data['dates']) . '</td>';
	        echo '</tr>';
	    }
	    ?>
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