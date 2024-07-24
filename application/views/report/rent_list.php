<div id="header">
    <?php $this->load->view('layout/partials/menu');?>
    
    <div class="row header-title">
        <div class="col-md-2 dropdown-select" style="float: left;">
        	<?php if (!$this->session->has_userdata('logged_in') || !$this->session->userdata('logged_in')) { ?>
                <a class="btn btn-default btn-back" href="/site">
                    <span><img src="../../../../images/back.png"> Quay Lại</span>
                </a>
            <?php } else { ?>
            	<a class="btn btn-default btn-back" href="/auth/manage">
                    <span><img src="../../../../images/back.png"> Quay Lại</span>
                </a>
            <?php } ?>
            
            <a href="#" class="btn btn-success btn-download-excel" style="">Tải Excel</a>
        </div>
    	<form method="get" action="/report/rent_list" class="submit-form">
            <div class="dropdown-select" style="float: right; width: 85px;">
                <select name="year" id="years" class="form-control ymd-select">
                    <option value="">Year</option>
                    <?php $cy = intval(date('Y')); ?>
                    <?php for ($y=$cy-5; $y<=$cy; $y++): ?>
                    	<option value="<?php echo $y; ?>" <?php echo $y == $year ? 'selected' : ''; ?>><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="dropdown-select" style="float: right; width: 65px;">
                <select name="month" id="month" class="form-control ymd-select">
                    <option value="">Month</option>
                    <?php for ($m=1; $m<=12; $m++): ?>
                    	<option value="<?php echo $m; ?>" <?php echo $m == $month ? 'selected' : ''; ?>><?php echo $m; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="dropdown-select" style="float: right; width: 65px;">
                <select name="date" id="date" class="form-control ymd-select">
                    <option value="">Day</option>
                    <?php for ($d=1; $d<=31; $d++): ?>
                    	<option value="<?php echo $d; ?>" <?php echo $d == $date ? 'selected' : ''; ?>><?php echo $d; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </form>
    </div>
</div>
<!--End header-->

<div class="clearfix"></div>

<div id="content">
    <div class="container-fluid">
    	<h2 align="center"><b>LƯU TRÚ GIỜ</b></h2>
        <table class="table report-table">
            <tr>
            	<th style="width: 2%;">STT</th>
                <th style="width: 20%;">Họ Tên</th>
                <th style="width: 5%;">Nam/Nữ</th>
                <th style="width: 11%;">Năm Sinh</th>
                <th style="width: 8%;">Dân Tộc</th>
                <th style="width: 10%;">CMND</th>
                <th style="width: 30%;">Địa Chỉ</th>
                <th style="width: 4%;">Phòng</th>
            	<?php if (false): ?>
                	<th style="width: 5%;"></th>
            	<?php endif; ?>
            </tr>
            <?php foreach ($hourly_list as $index => $item): ?>
                <tr <?php echo $item->remove ? 'class="removed-item"' : ''; ?>>
                    <td><strong><?php echo $index + 1; ?></strong></td>
                    <td><?php echo $item->name; ?></td>
                    <td><?php echo $item->gender == 1 ? 'Nam' : 'Nữ'; ?></td>
                    <td><?php echo $item->birthday; ?></td>
                    <td><?php echo $item->nation; ?></td>
                    <td><?php echo $item->number; ?></td>
                    <td><?php echo $item->address; ?></td>
                    <td><?php echo $item->room; ?></td>
                	<?php if (false): ?>
                        <td>
                        	<a class="btn-remove" href="/report/remove/<?php echo $item->id; ?>">Xóa</a>
                    		<a class="btn-change" href="/report/change/<?php echo $item->id; ?>">Đổi</a>
                    	</td>
                	<?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </table>

		<br/>
    	<h2 align="center"><b>LƯU TRÚ QUA ĐÊM</b></h2>
        <table class="table report-table">
            <tr>
            	<th style="width: 2%;">STT</th>
                <th style="width: 20%;">Họ Tên</th>
                <th style="width: 5%;">Nam/Nữ</th>
                <th style="width: 11%;">Năm Sinh</th>
                <th style="width: 8%;">Dân Tộc</th>
                <th style="width: 10%;">CMND</th>
                <th style="width: 30%;">Địa Chỉ</th>
                <th style="width: 4%;">Phòng</th>
            	<?php if (false): ?>
                	<th style="width: 5%;"></th>
            	<?php endif; ?>
            </tr>
            <?php foreach ($nightly_list as $index => $item): ?>
                <tr <?php echo $item->remove ? 'class="removed-item"' : ''; ?>>
                    <td><strong><?php echo $index + 1; ?></strong></td>
                    <td><?php echo $item->name; ?></td>
                    <td><?php echo $item->gender == 1 ? 'Nam' : 'Nữ'; ?></td>
                    <td><?php echo $item->birthday; ?></td>
                    <td><?php echo $item->nation; ?></td>
                    <td><?php echo $item->number; ?></td>
                    <td><?php echo $item->address; ?></td>
                    <td><?php echo $item->room; ?></td>
                	<?php if (false): ?>
                        <td>
                        	<a class="btn-remove" href="/report/remove/<?php echo $item->id; ?>">Xóa</a>
                    		<a class="btn-change" href="/report/change/<?php echo $item->id; ?>">Đổi</a>
                    	</td>
                	<?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<!--End content-->

<script>
$(document).ready(function() {
	$(".ymd-select").change(function() {
		$(".submit-form").submit();
	});

	$(document).on('click', '.btn-remove', function(e) {
		e.preventDefault();
		
		var selected = $(this);

		$.ajax({
            url: $(selected).attr('href'),
            type: 'GET',
            dataType: 'json',
            success: function (response) {
            	window.location.reload();
            }
        });
	});

	$(document).on('click', '.btn-change', function(e) {
		e.preventDefault();
		
		var selected = $(this);

		$.ajax({
            url: $(selected).attr('href'),
            type: 'GET',
            dataType: 'json',
            success: function (response) {
            	window.location.reload();
            }
        });
	});
	
	$(".btn-download-excel").click(function(e) {
		e.preventDefault();

		var win = window.open('/report/download_excel?y=' + $('#years').val() + '&m=' + $('#month').val(), '_blank');
		if (win) {
		    //Browser has allowed it to be opened
		    win.focus();
		}
	});
});
</script>