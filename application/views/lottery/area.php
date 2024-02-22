<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">

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
    min-width: 30px;
}
.data-row.active {
    background: #80ff00 !important;
}
.data-row.active td {
    background: #80ff00 !important;
}
.data-row.deactive {
    background: #ff9999 !important;
}
.data-row.deactive td {
    background: #ff9999 !important;
}
</style>

<div id="content">
	<?php if ($fields): ?>
	<table>
	    <thead>
	        <tr>
	            <th style="background: yellow;"></th>
	            <th style="background: #CDCDCD;">N</th><th style="background: #CDCDCD;">T</th><th style="background: #CDCDCD;">B</th>
	            <th style="background: #EFEFEF;">N</th><th style="background: #EFEFEF;">T</th><th style="background: #EFEFEF;">B</th>
	            <th>N</th><th>T</th><th>B</th>
	        </tr>
	    </thead>
	    <tbody>
        	<?php foreach ($fields as $field): ?>
        	<tr class="data-row">
        	    <td style="background: yellow; padding: 5px; font-size: 14px; font-weight: bold;"><?php echo $field; ?></td>
        	    <td style="background: #CDCDCD;<?php echo isset($mn_2[$field]) && $mn_2[$field] ? 'color: #ff00ff; font-size: 14px; font-weight: bold;' : ''; ?>"><?php echo isset($mn_2[$field]) ? $mn_2[$field] : 0; ?></td>
        	    <td style="background: #CDCDCD;<?php echo isset($mt_2[$field]) && $mt_2[$field] ? 'color: #ff00ff; font-size: 14px; font-weight: bold;' : ''; ?>"><?php echo isset($mt_2[$field]) ? $mt_2[$field] : 0; ?></td>
        	    <td style="background: #CDCDCD;<?php echo isset($mb_2[$field]) && $mb_2[$field] ? 'color: #ff00ff; font-size: 14px; font-weight: bold;' : ''; ?>"><?php echo isset($mb_2[$field]) ? $mb_2[$field] : 0; ?></td>
        	    <td style="background: #EFEFEF;<?php echo isset($mn_1[$field]) && $mn_1[$field] ? 'color: #ff00ff; font-size: 14px; font-weight: bold;' : ''; ?>"><?php echo isset($mn_1[$field]) ? $mn_1[$field] : 0; ?></td>
        	    <td style="background: #EFEFEF;<?php echo isset($mt_1[$field]) && $mt_1[$field] ? 'color: #ff00ff; font-size: 14px; font-weight: bold;' : ''; ?>"><?php echo isset($mt_1[$field]) ? $mt_1[$field] : 0; ?></td>
        	    <td style="background: #EFEFEF;<?php echo isset($mb_1[$field]) && $mb_1[$field] ? 'color: #ff00ff; font-size: 14px; font-weight: bold;' : ''; ?>"><?php echo isset($mb_1[$field]) ? $mb_1[$field] : 0; ?></td>
        	    <td <?php echo isset($mn[$field]) && $mn[$field] ? 'style="color: #ff00ff; font-size: 14px; font-weight: bold;"' : ''; ?>><?php echo isset($mn[$field]) ? $mn[$field] : 0; ?></td>
        	    <td <?php echo isset($mt[$field]) && $mt[$field] ? 'style="color: #ff00ff; font-size: 14px; font-weight: bold;"' : ''; ?>><?php echo isset($mt[$field]) ? $mt[$field] : 0; ?></td>
        	    <td <?php echo isset($mb[$field]) && $mb[$field] ? 'style="color: #ff00ff; font-size: 14px; font-weight: bold;"' : ''; ?>><?php echo isset($mb[$field]) ? $mb[$field] : 0; ?></td>
        	</tr>
        	<?php endforeach; ?>
	    </tbody>
	</table>
	<?php endif; ?>
</div>

<script>
$(document).ready(function() {
	$(document).on('click', '.data-row', function() {
		if ($(this).hasClass('active')) {
			$(this).removeClass('active').addClass('deactive');
		} else if ($(this).hasClass('deactive')) {
		    $(this).removeClass('deactive');
	    } else {
			$(this).addClass('active');
		}
	});
});
</script>