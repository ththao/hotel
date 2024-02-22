<div id="header">
    <?php $this->load->view('layout/partials/menu');?>
    
    <div class="row header-title page-name" align="center">
        <a class="btn btn-default btn-back pull-left top15" href="/site">
            <span><img src="../../../../images/back.png"> Về Trang Chủ</span>
        </a>
        <h1><?php echo $room->name; ?></h1>
    </div>
</div>
<!--End header-->

<div class="clearfix"></div>

<div id="content">
    <div class="container-fluid">
        <div id="detail">
            <div class="detail-title">
                
            </div>
            <div class="col-xs-12">
                
                <div class="row" style="border: 1px solid #348667; padding: 0px 10px 20px 30px; border-radius: 8px;">
                	<div class="col-xs-12 col-md-12">
                    	<h3><?php echo !empty($room->items_received) ? 'Đã nhận của khách:' : 'Không giữ giấy tờ hoặc xe'?></h3>
                        <?php foreach ($room->items_received as $item): ?>
                        	<div class="col-xs-12 col-md-12">
                        		<p><?php echo $item->type == 'passport' ? 'Hộ chiếu' : ($item->type == 'id_card' ? 'CMND' : ($item->type == 'driving_license' ? 'Bằng lái' : ($item->type == 'bike' ? 'Xe máy' : ($item->type == 'cavet' ? 'Cà Vẹt' : 'CMND')))); ?>
                        		<?php echo $item->type == 'bike' ? (' biển số <strong>' . $item->number . '</strong>') : (' số <strong>' . $item->number . '</strong> mang tên ' . $item->name); ?>. <?php echo $item->early_return ? '(đã trả lúc ' . date('d-m-Y H:i', $item->early_return) . ')' : ''; ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="row" style="border: 1px solid #348667; padding: 20px; border-radius: 8px; margin-top: 20px;">
                    <table id="report-table" class="table hotel-table" style="margin-bottom: 0px;">
                        <tr>
                            <td colspan="3" align="left" style="border-top: none;"><b>Vào</b>: <?php echo date('d-m-Y H:i', $room->check_in); ?> - <b>Ra</b>: <?php echo date('d-m-Y H:i', $room->check_out); ?></td>
                        </tr>
                        <?php
                            $note = $room->note;
                            $notes = explode(';', $note);
                            foreach ($notes as $note_item) {
                                $note_item = explode('=', $note_item);
                        ?>
                            <tr>
                                <td colspan="2"><b><?php echo isset($note_item[0]) ? $note_item[0] : ''; ?></b></td>
                                <td align="right"><?php echo isset($note_item[1]) ? $note_item[1] : ''; ?></td>
                            </tr>
                        <?php
                            } 
                        ?>
                        
                        <?php if ($room->human): ?>
                            <tr>
                                <td colspan="2"><b>Phụ Thu</b></td>
                                <td align="right"><?php echo number_format($room->human * $room->extra_price, 0); ?>vnd</td>
                            </tr>
						<?php endif; ?>
						
						<?php if ($room->prepaid): ?>
                            <tr>
                                <td colspan="2"><b>Tổng cộng:</b></td>
                                <td align="right"><?php echo number_format($room->total_price, 0); ?>vnd</td>
                            </tr>
                            <tr>
                                <td colspan="2"><b>Đã trả</b></td>
                                <td align="right"><?php echo number_format($room->prepaid, 0); ?>vnd</td>
                            </tr>
                            <?php if ($room->total_price - $room->prepaid > 0): ?>
                                <tr>
                                    <td colspan="2"><h4>Cần thu thêm:</h4></td>
                                    <td align="right"><h4><?php echo number_format($room->total_price - $room->prepaid, 0); ?>vnd</h4></td>
                                </tr>
                            <?php elseif ($room->total_price - $room->prepaid == 0): ?>
                                <tr>
                                    <td colspan="3"><h4>Đã đủ</h4></td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2"><h4>Cần trả lại:</h4></td>
                                    <td align="right"><h4><?php echo number_format($room->prepaid - $room->total_price, 0); ?>vnd</h4></td>
                                </tr>
                            <?php endif; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2"><h4>Tổng cộng:</h4></td>
                                <td align="right"><h4><?php echo number_format($room->total_price, 0); ?>vnd</h4></td>
                            </tr>
						<?php endif; ?>
                        <?php if ($room->notes): ?>
                            <tr>
                                <td colspan="3" style="color: red;"><h5><b><?php echo $room->notes; ?></b></h5></td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
                
                <div class="row">
                	<button class="btn btn-default btn-success btn-receipt pull-right hide" rent-id="<?php echo $room->rent_id; ?>">XUẤT HÓA ĐƠN</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
	$(".btn-receipt").click(function(e) {
		e.preventDefault();

		var win = window.open('/report/receipt?rent_id=' + $(this).attr('rent-id'), '_blank');
		if (win) {
		    //Browser has allowed it to be opened
		    win.focus();
		}
	});
});
</script>