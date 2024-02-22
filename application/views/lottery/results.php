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
thead th { position: sticky; top: 0; }
table thead th {
    border: 1px solid black !important;
    background: #dedede;
    padding: 8px 3px;
    position: sticky;
    top: 0;
}
td {
    border: 1px solid black;
    padding: 3px;
    text-align: center;
}
</style>

<div id="content">
	
	<?php if ($data): ?>
	<?php
	    $week_w_earn = 0;
	    $month_w_earn = 0;
	    $year_w_earn = 0;
	    $same_w_earm = 0;
	    $week_month_w_earn = 0;
	    $month_year_w_earn = 0;
	    $week_year_w_earn = 0;
	    $week_month_year_earn = 0;
	    
	    $week_w_earn_t = 0;
	    $month_w_earn_t = 0;
	    $year_w_earn_t = 0;
	    $same_w_earm_t = 0;
	    $week_month_w_earn_t = 0;
	    $month_year_w_earn_t = 0;
	    $week_year_w_earn_t = 0;
	    $week_month_year_earn_t = 0;
	?>
	<table>
	    <thead><tr>
	        <th>Date</th><th>Thứ</th>
	        <th>Tuần M</th><th>Tháng M</th><th>Năm M</th><th>Trùng M</th><th>Tuần Tháng M</th><th>Tháng Năm M</th><th>Tuần Năm M</th><th>Tuần Tháng Năm M</th>
	        <th></th>
	        <th>Tuần M</th><th>Tháng M</th><th>Năm M</th><th>Trùng M</th><th>Tuần Tháng M</th><th>Tháng Năm M</th><th>Tuần Năm M</th><th>Tuần Tháng Năm M</th>
        </tr></thead>
	    <tbody>
        	<?php foreach ($data as $date => $item): ?>
        	<?php
        	    $week_w_earn += ($item['mn']['w_earn'] + $item['mn']['w_cost']);
        	    $month_w_earn += ($item['mn']['m_earn'] + $item['mn']['m_cost']);
        	    $year_w_earn += ($item['mn']['y_earn'] + $item['mn']['y_cost']);
        	    $same_w_earm += $item['mn']['all_cost'] + $item['mn']['all_earn'];
        	    $week_month_w_earn += $item['mn']['w_earn'] + $item['mn']['w_cost'] + $item['mn']['m_earn'] + $item['mn']['m_cost'];
        	    $month_year_w_earn += $item['mn']['y_earn'] + $item['mn']['y_cost'] + $item['mn']['m_earn'] + $item['mn']['m_cost'];
        	    $week_year_w_earn += $item['mn']['w_earn'] + $item['mn']['w_cost'] + $item['mn']['y_earn'] + $item['mn']['y_cost'];
        	    $week_month_year_earn += $item['mn']['w_earn'] + $item['mn']['w_cost'] + $item['mn']['m_earn'] + $item['mn']['m_cost'] + $item['mn']['y_earn'] + $item['mn']['y_cost'];
        	    
        	    $week_w_earn_t += ($item['mt']['w_earn'] + $item['mt']['w_cost']);
        	    $month_w_earn_t += ($item['mt']['m_earn'] + $item['mt']['m_cost']);
        	    $year_w_earn_t += ($item['mt']['y_earn'] + $item['mt']['y_cost']);
        	    $same_w_earm_t += $item['mt']['all_cost'] + $item['mt']['all_earn'];
        	    $week_month_w_earn_t += $item['mt']['w_earn'] + $item['mt']['w_cost'] + $item['mt']['m_earn'] + $item['mt']['m_cost'];
        	    $month_year_w_earn_t += $item['mt']['y_earn'] + $item['mt']['y_cost'] + $item['mt']['m_earn'] + $item['mt']['m_cost'];
        	    $week_year_w_earn_t += $item['mt']['w_earn'] + $item['mt']['w_cost'] + $item['mt']['y_earn'] + $item['mt']['y_cost'];
        	    $week_month_year_earn_t += $item['mt']['w_earn'] + $item['mt']['w_cost'] + $item['mt']['m_earn'] + $item['mt']['m_cost'] + $item['mt']['y_earn'] + $item['mt']['y_cost'];
        	?>
        	
        	<tr>
        	    <td><?php echo $date; ?></td>
        	    <td><?php echo date('l', strtotime($date)); ?></td>
        	    <td><?php echo $item['mn']['w_earn'] + $item['mn']['w_cost']; ?></td>
        	    <td><?php echo $item['mn']['m_earn'] + $item['mn']['m_cost']; ?></td>
        	    <td><?php echo $item['mn']['y_earn'] + $item['mn']['y_cost']; ?></td>
        	    <td><?php echo $item['mn']['all_cost'] + $item['mn']['all_earn']; ?></td>
        	    <td><?php echo $item['mn']['w_earn'] + $item['mn']['w_cost'] + $item['mn']['m_earn'] + $item['mn']['m_cost']; ?></td>
        	    <td><?php echo $item['mn']['y_earn'] + $item['mn']['y_cost'] + $item['mn']['m_earn'] + $item['mn']['m_cost']; ?></td>
        	    <td><?php echo $item['mn']['w_earn'] + $item['mn']['w_cost'] + $item['mn']['y_earn'] + $item['mn']['y_cost']; ?></td>
        	    <td><?php echo $item['mn']['w_earn'] + $item['mn']['w_cost'] + $item['mn']['m_earn'] + $item['mn']['m_cost'] + $item['mn']['y_earn'] + $item['mn']['y_cost']; ?></td>
        	    <td></td>
        	    <td><?php echo $item['mt']['w_earn'] + $item['mt']['w_cost']; ?></td>
        	    <td><?php echo $item['mt']['m_earn'] + $item['mt']['m_cost']; ?></td>
        	    <td><?php echo $item['mt']['y_earn'] + $item['mt']['y_cost']; ?></td>
        	    <td><?php echo $item['mt']['all_cost'] + $item['mt']['all_earn']; ?></td>
        	    <td><?php echo $item['mt']['w_earn'] + $item['mt']['w_cost'] + $item['mt']['m_earn'] + $item['mt']['m_cost']; ?></td>
        	    <td><?php echo $item['mt']['y_earn'] + $item['mt']['y_cost'] + $item['mt']['m_earn'] + $item['mt']['m_cost']; ?></td>
        	    <td><?php echo $item['mt']['w_earn'] + $item['mt']['w_cost'] + $item['mt']['y_earn'] + $item['mt']['y_cost']; ?></td>
        	    <td><?php echo $item['mt']['w_earn'] + $item['mt']['w_cost'] + $item['mt']['m_earn'] + $item['mt']['m_cost'] + $item['mt']['y_earn'] + $item['mt']['y_cost']; ?></td>
        	</tr>
        	<?php if (date('l', strtotime($date)) == 'Sunday') { ?>
    	    <tr>
        	    <td><b>Tổng kết tuần</b></td>
        	    <td></td>
        	    <td><b><?php echo $week_w_earn; ?></b></td>
        	    <td><b><?php echo $month_w_earn; ?></b></td>
        	    <td><b><?php echo $year_w_earn; ?></b></td>
        	    <td><b><?php echo $same_w_earm; ?></b></td>
        	    <td><b><?php echo $week_month_w_earn; ?></b></td>
        	    <td><b><?php echo $month_year_w_earn; ?></b></td>
        	    <td><b><?php echo $week_year_w_earn; ?></b></td>
        	    <td><b><?php echo $week_month_year_earn; ?></b></td>
        	    <td></td>
        	    <td><b><?php echo $week_w_earn_t; ?></b></td>
        	    <td><b><?php echo $month_w_earn_t; ?></b></td>
        	    <td><b><?php echo $year_w_earn_t; ?></b></td>
        	    <td><b><?php echo $same_w_earm_t; ?></b></td>
        	    <td><b><?php echo $week_month_w_earn_t; ?></b></td>
        	    <td><b><?php echo $month_year_w_earn_t; ?></b></td>
        	    <td><b><?php echo $week_year_w_earn_t; ?></b></td>
        	    <td><b><?php echo $week_month_year_earn_t; ?></b></td>
        	</tr>
        	<?php
        	    $week_w_earn = 0;
        	    $month_w_earn = 0;
        	    $year_w_earn = 0;
        	    $same_w_earm = 0;
        	    $week_month_w_earn = 0;
        	    $month_year_w_earn = 0;
        	    $week_year_w_earn = 0;
        	    $week_month_year_earn = 0;
        	    
        	    $week_w_earn_t = 0;
        	    $month_w_earn_t = 0;
        	    $year_w_earn_t = 0;
        	    $same_w_earm_t = 0;
        	    $week_month_w_earn_t = 0;
        	    $month_year_w_earn_t = 0;
        	    $week_year_w_earn_t = 0;
        	    $week_month_year_earn_t = 0;
        	?>
        	<?php } ?>
        	<?php endforeach; ?>
	    </tbody>
	</table>
	<?php endif; ?>
</div>