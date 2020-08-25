<div id="header">
    <?php $this->load->view('layout/partials/menu');?>
    
    <div class="row header-title page-name" align="center">
        <a class="btn btn-default btn-back pull-left top15" href="/auth/manage">
            <span><img src="../../../../images/back.png"> Quay Lại</span>
        </a>
        <h1>DS PHÒNG</h1>
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
                        <th>Phòng</th>
                        <th>Tầng</th>
                        <th>Giá</th>
                        <th>Tình trạng</th>
                        <th></th>
                    </tr>
					<?php foreach ($rooms as $room): ?>
                    <tr>
                        <td><p class="pad10px"><?php echo $room->name; ?></p></td>
                        <td><p><?php echo $room->floor; ?></p></td>
                        <td class="text-transform color-default text-weight">
                            <p><?php echo number_format($room->hourly_price, 0); ?>vnd / Giờ (Giờ tiếp theo <?php echo number_format($room->next_hourly_price, 0); ?>vnd / Giờ)</p>
                            <p class="no-marginbottom"><?php echo number_format($room->night_price, 0); ?>vnd / Đêm</p>
                            <p class="no-marginbottom"><?php echo number_format($room->daily_price, 0); ?>vnd / Ngày</p>
                            <p class="no-marginbottom">Phụ thu <?php echo number_format($room->extra_price, 0); ?>vnd</p>
                            <p class="no-marginbottom">Giảm giá <?php echo $room->discount ? number_format($room->discount, 0) : '0'; ?>vnd</p>
                        </td>
                        <td><?php echo $room->removed ? 'Bảo trì' : 'Đang hoạt động'; ?></td>
                        <td align="right">
                            <a href="/room/update/<?php echo $room->id; ?>">Cập nhật</a> | 
                            <?php if ($room->removed): ?>
                            	<a href="/room/getback/<?php echo $room->id; ?>">Bỏ Xóa</a>
                            <?php else: ?>
                            	<a href="/room/delete/<?php echo $room->id; ?>">Xóa</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                <div class="add-floor-button pull-right">
                	<a href="/room/create">
                        <img src="../../../../images/add.png" id="add-floor" class="pointer">
                        <p><strong>Thêm Phòng</strong></p>
                    </a>
                </div>
            </div>
            <div class="clear-fix"></div>
        </div>
    </div>
</div>
<!--End content-->