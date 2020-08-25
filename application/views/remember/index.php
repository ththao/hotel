<div id="header">
    <?php $this->load->view('layout/partials/menu');?>
    
    <div class="row header-title">
        <div class="col-md-2 dropdown-select" style="float: left;">
            <a class="btn-default form-control btn-dropdown btn-back" href="/auth/manage">
                <span><img src="../../../../images/back.png"> Quay Lại</span>
            </a>
        </div>
        <div class="col-md-8 page-name" style="float: left;">
            <h1>QUẢN LÝ ĐĂNG NHẬP</h1>
        </div>
    </div>
</div>
<!--End header-->

<div class="clearfix"></div>

<div id="content">
    <div class="container-fluid">
        <div id="room-setting">
            <div class="manage-room">
                <table id="manage-room-table" class="table hotel-table">
                    <tr>
                        <th>ID</th>
                        <th>Tên</th>
                        <th>Browser</th>
                        <th>Hash</th>
                        <th>Lúc</th>
                        <th></th>
                    </tr>
					<?php foreach ($items as $item): ?>
                    <tr>
                        <td><p class="pad10px"><?php echo $item->user_id; ?></p></td>
                        <td><p class="pad10px"><?php echo $item->username; ?></p></td>
                        <td><p class="pad10px"><?php echo $item->browser; ?></p></td>
                        <td><p class="pad10px"><?php echo $item->remember_hash; ?></p></td>
                        <td><p class="pad10px"><?php echo date('d-m-Y H:i', $item->created_at); ?></p></td>
                        <td align="right">
                            <a class="btn btn-danger btn-remove" href="/remember/delete/<?php echo $item->user_id; ?>/<?php echo $item->remember_hash; ?>">Xóa</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <div class="clear-fix"></div>
        </div>
    </div>
</div>
<!--End content-->

<script type="text/javascript">
$(document).ready(function() {
	$(document).on('click', '.btn-remove', function(e) {
		e.preventDefault();

		if (confirm('Bạn có chắc chắn muốn xóa dữ liệu này?')) {
			window.location = $(this).attr('href');
		}
	});
});
</script>