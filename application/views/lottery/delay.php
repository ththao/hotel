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
	    <thead>
        	<tr>
        	    <td>Ngày</td>
        	    <td>Số</td>
        	    <td>Đúng ngày</td>
        	    <td>Ra trễ</td>
        	    <td>Ra sớm</td>
        	</tr>
	    </thead>
	    <tbody>
        	<?php foreach ($data as $view_date => $item): ?>
        	<tr>
        	    <td><?php echo $view_date; ?></td>
        	    <td><?php echo $item['number']; ?></td>
        	    <td><?php echo $item['correct_date'] ? 'O' : 'X'; ?></td>
        	    <td><?php echo $item['next_date'] ? 'O' : 'X'; ?></td>
        	    <td><?php echo $item['prev_date'] ? 'O' : 'X'; ?></td>
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