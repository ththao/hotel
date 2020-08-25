<div id="header">
    <?php $this->load->view('layout/partials/menu');?>
    
    <div class="row header-title page-name" align="center">
        <a class="btn btn-default btn-back pull-left top15" href="/rent">
            <span><img src="../../../../images/back.png"> Về Danh Sách</span>
        </a>
        <h1>PHÒNG <?php echo $room->name; ?></h1>
    </div>
</div>
<!--End header-->

<div class="clearfix"></div>

<div id="content">
    <div class="container-fluid">
        <div id="detail">
            <div class="detail-title">
                
            </div>
            <div class="col-md-12">
                
                <div class="row rent-item-detail">
                	<form method="post">
	                	<div class="row">
		                	<div class="col-md-3">Giờ vào</div>
		                	<div class="col-md-3"><input class="form-control" type="text" name="check_in" value="<?php echo date("d-m-Y H:i", $room->check_in); ?>"></div>
	                	</div>
	                	<div class="row">
		                	<div class="col-md-3">Giờ ra</div>
		                	<div class="col-md-3"><input class="form-control" type="text" name="check_out" value="<?php echo $room->check_out ? date("d-m-Y H:i", $room->check_out) : ''; ?>"></div>
	                	</div>
	                	<div class="row">
		                	<div class="col-md-3">Diễn giải</div>
		                	<div class="col-md-9">
		                		<textarea class="form-control" style="width: 100%; padding: 5px;" name="note"><?php echo $room->note; ?></textarea>
							</div>
	                	</div>
	                	<div class="row">
		                	<div class="col-md-3">Phụ thu</div>
		                	<div class="col-md-9">
		                		<input class="form-control" type="text" name="human" value="<?php echo $room->human; ?>">
							</div>
	                	</div>
	                	<div class="row">
		                	<div class="col-md-3">Số tiền</div>
		                	<div class="col-md-3"><input class="form-control" type="text" name="total_price" value="<?php echo $room->total_price; ?>"></div>
	                	</div>
	                	
	                	<?php if ($room->items_received): ?>
	                	<?php foreach ($room->items_received as $item): ?>
	                		<?php if ($item->early_return): ?>
	                			<div class="row">
		                			<div class="col-md-3"><?php echo $item->type == 'passport' ? 'Hộ chiếu' : ($item->type == 'id_card' ? 'CMND' : ($item->type == 'driving_license' ? 'Bằng lái' : ($item->type == 'bike' ? 'Xe máy' : ($item->type == 'cavet' ? 'Cà Vẹt': 'CMND')))); ?></div>
		                			
		                			<div class="col-md-6">
	                					<span><?php echo $item->name . ' ' . $item->number; ?> đã trả lúc <?php echo date('d-m-Y H:i', $item->early_return); ?></span>
		                			</div>
		                		</div>
	                		<?php else: ?>
		                		<div class="row">
		                			<div class="col-md-3"><?php echo $item->type == 'passport' ? 'Hộ chiếu' : ($item->type == 'id_card' ? 'CMND' : ($item->type == 'driving_license' ? 'Bằng lái' : ($item->type == 'bike' ? 'Xe máy' : ($item->type == 'cavet' ? 'Cà Vẹt': 'CMND')))); ?></div>
		                			<div class="col-md-3">
		                				<span><?php echo $item->number . ($item->name ? ' - ' . $item->name : ''); ?></span>
		                			</div>
		                		</div>
	                		<?php endif; ?>
	                	<?php endforeach; ?>
	                	<?php endif; ?>
	                	
	                	<?php if ($room->items_used): ?>
	                	<?php foreach ($room->items_used as $item): ?>
	                		<div class="row">
	                			<div class="col-md-3"><?php echo $item->name; ?></div>
	                			<div class="col-md-3"><span><?php echo $item->quantity; ?></span></div>
	                		</div>
	                	<?php endforeach; ?>
	                	<?php endif; ?>
	                
			        	<div class="row">
			        		<div class="col-md-3"></div>
			        		<div class="col-md-3">
				        		<button class="btn btn-success btn-save">Lưu</button>
				        		<a href="/rent/remove/<?php echo $room->rent_id; ?>" class="btn btn-danger btn-remove">Xóa</a>
			        		</div>
			        	</div>
			        </form>
                </div>
            </div>
        </div>
    </div>
</div>
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