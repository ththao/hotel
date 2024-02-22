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
	<?php if ($data): ?>
	<table>
	    <tbody>
        	<?php foreach ($data as $item): ?>
        	<tr>
        	    <td><?php echo $item['view_date']; ?></td>
        	    <td><?php echo $item['mn_o_1']; ?></td>
        	    <td><?php echo $item['mn_w_1']; ?></td>
        	    <td><?php echo $item['mn_m_1']; ?></td>
        	    <td><?php echo $item['mn_y_1']; ?></td>
        	    <td>&nbsp;</td>
        	    <td><?php echo $item['mt_o_1']; ?></td>
        	    <td><?php echo $item['mt_w_1']; ?></td>
        	    <td><?php echo $item['mt_m_1']; ?></td>
        	    <td><?php echo $item['mt_y_1']; ?></td>
        	    <td>&nbsp;</td>
        	    <td><?php echo $item['mb_o_1']; ?></td>
        	    <td><?php echo $item['mb_w_1']; ?></td>
        	    <td><?php echo $item['mb_m_1']; ?></td>
        	    <td><?php echo $item['mb_y_1']; ?></td>
        	</tr>
        	<tr>
        	    <td><?php echo date('w', strtotime($item['view_date'])) + 1; ?></td>
        	    <td><?php echo $item['mn_o_2']; ?></td>
        	    <td><?php echo $item['mn_w_2']; ?></td>
        	    <td><?php echo $item['mn_m_2']; ?></td>
        	    <td><?php echo $item['mn_y_2']; ?></td>
        	    <td>&nbsp;</td>
        	    <td><?php echo $item['mt_o_2']; ?></td>
        	    <td><?php echo $item['mt_w_2']; ?></td>
        	    <td><?php echo $item['mt_m_2']; ?></td>
        	    <td><?php echo $item['mt_y_2']; ?></td>
        	    <td>&nbsp;</td>
        	    <td>&nbsp;</td>
        	    <td>&nbsp;</td>
        	    <td>&nbsp;</td>
        	    <td>&nbsp;</td>
        	</tr>
        	<tr style="background: <?php echo $item['view_date'] == date('Y-m-d') ? 'yellow' : '#80ff00'; ?>;">
        	    <td></td>
        	    <td><?php echo $item['mn_o_r']; ?></td>
        	    <td><?php echo $item['mn_w_r']; ?></td>
        	    <td><?php echo $item['mn_m_r']; ?></td>
        	    <td><?php echo $item['mn_y_r']; ?></td>
        	    <td>&nbsp;</td>
        	    <td><?php echo $item['mt_o_r']; ?></td>
        	    <td><?php echo $item['mt_w_r']; ?></td>
        	    <td><?php echo $item['mt_m_r']; ?></td>
        	    <td><?php echo $item['mt_y_r']; ?></td>
        	    <td>&nbsp;</td>
        	    <td><?php echo $item['mb_o_r']; ?></td>
        	    <td><?php echo $item['mb_w_r']; ?></td>
        	    <td><?php echo $item['mb_m_r']; ?></td>
        	    <td><?php echo $item['mb_y_r']; ?></td>
        	</tr>
        	<tr>
        	    <td>&nbsp;</td>
        	    <td>&nbsp;</td>
        	    <td>&nbsp;</td>
        	    <td>&nbsp;</td>
        	    <td>&nbsp;</td>
        	    <td>&nbsp;</td>
        	    <td>&nbsp;</td>
        	    <td>&nbsp;</td>
        	    <td>&nbsp;</td>
        	    <td>&nbsp;</td>
        	    <td>&nbsp;</td>
        	    <td>&nbsp;</td>
        	    <td>&nbsp;</td>
        	    <td>&nbsp;</td>
        	    <td>&nbsp;</td>
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