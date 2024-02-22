<div id="header">
    <?php $this->load->view('layout/partials/menu');?>
    
    <div class="row header-title">
        <div class="col-md-2 dropdown-select" style="float: left; display: none;">
            <button class="btn-default form-control dropdown-toggle btn-dropdown" type="button" data-toggle="dropdown" aria-expanded="false">
                <span class="left color-default text-transform text-weight">Chọn Tầng</span><span class="right"><span class="caret"> </span></span>
            </button>
            <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
            	<?php foreach ($floors as $index => $floor): ?>
                	<li role="presentation"><a role="menuitem" tabindex="-1" href="#floor<?php echo $index; ?>">Tầng <?php echo $index; ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="col-md-10 page-name" style="float: left;">
            <h1 style="text-transform: uppercase;">KS <?php echo $this->session->userdata('fullname'); ?></h1>
        </div>
        <div class="col-md-2 dropdown-select" style="float: right;">
            <a class="btn-default form-control btn-dropdown btn-pay-request" href="#" title="<?php echo $expired ? ('Ngày hết hạn: ' . date('d-m-Y', $expired)) : ''; ?>">
                <span>Gia Hạn</span>
            </a>
        </div>
    </div>
</div>
<!--End header-->

<div class="clearfix"></div>

<div id="content">
    <div class="container-fluid">
    	<?php foreach ($floors as $index => $floor): ?>
            <div class="row floor" id="floor<?php echo $index; ?>">
                <h4 class="floor-title">Tầng <?php echo $index == 0 ? 'Trệt' : $index; ?></h4>
                <?php $col_class = count($floor)%3 == 0 ? 'col-md-4' : 'col-md-6'; ?>
                <?php foreach ($floor as $r_index => $room): ?>
                    <?php if ($col_class == 'col-md-6' && $r_index%2 == 0) { ?>
                    <div class="row">
                    <?php } ?>
                    <div class="<?php echo $col_class; ?> row room-item <?php echo isset($room->check_in) ? 'active' : ''; ?>">
                    
                    	<?php if (isset($room->rent_id) && $room->rent_id): ?>
                            <div class="room-info-header">
                                <div class="col-xs-3 room-left no-padding-left">
                                    <?php if ($room->human): ?>
                                        <div class="clearfix in-item">
                                            <span class="small-detail human"></span>
                                            <div class="orange-ellipse">
                                                <p data-toggle="tooltip" title="Phụ thu x <?php echo $room->human; ?>"><?php echo $room->human; ?></p>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php
                                        $id_card_cnt = 0;
                                        $id_cards = '';
                                        foreach ($room->items_received as $item) {
                                            if ($item->type == 'id_card' && !$item->early_return) {
                                                $id_card_cnt ++;
                                                $id_cards .= ($id_cards ? ', ' : '') . $item->name;
                                            }
                                        }
                                    ?>
                                    <?php if ($id_card_cnt): ?>
                                        <div class="clearfix in-item next">
                                            <span class="small-detail id-card"></span>
                                            <div class="orange-ellipse">
                                                <p data-toggle="tooltip" title="Nhận <?php echo $id_card_cnt; ?> CMND mang tên <?php echo $id_cards; ?>">
                                                	<?php echo $id_card_cnt; ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php
                                        $passport_cnt = 0;
                                        $passports = '';
                                        foreach ($room->items_received as $item) {
                                            if ($item->type == 'passport' && !$item->early_return) {
                                                $passport_cnt ++;
                                                $passports .= ($passports ? ', ' : '') . $item->name;
                                            }
                                        }
                                    ?>
                                    <?php if ($passport_cnt): ?>
                                        <div class="clearfix in-item next">
                                            <span class="small-detail passport"></span>
                                            <div class="orange-ellipse">
                                                <p data-toggle="tooltip" title="Nhận <?php echo $passport_cnt; ?> hộ chiếu mang tên <?php echo $passports; ?>">
                                                	<?php echo $passport_cnt; ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php
                                        $driving_license_cnt = 0;
                                        $driving_licenses = '';
                                        foreach ($room->items_received as $item) {
                                            if ($item->type == 'driving_license' && !$item->early_return) {
                                                $driving_license_cnt ++;
                                                $driving_licenses .= ($driving_licenses ? ', ' : '') . $item->name;
                                            }
                                        }
                                    ?>
                                    <?php if ($driving_license_cnt): ?>
                                        <div class="clearfix in-item next">
                                            <span class="small-detail drive"></span>
                                            <div class="orange-ellipse">
                                                <p data-toggle="tooltip" title="Nhận <?php echo $driving_license_cnt; ?> bằng lái xe mang tên: <?php echo $driving_licenses; ?>">
                                                   <?php echo $driving_license_cnt; ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php
                                        $cavet_cnt = 0;
                                        $cavet = '';
                                        foreach ($room->items_received as $item) {
                                            if ($item->type == 'cavet' && !$item->early_return) {
                                                $cavet_cnt ++;
                                                $cavet .= ($cavet ? ', ' : '') . $item->name;
                                            }
                                        }
                                    ?>
                                    <?php if ($cavet_cnt): ?>
                                        <div class="clearfix in-item next">
                                            <span class="small-detail cavet"></span>
                                            <div class="orange-ellipse">
                                                <p data-toggle="tooltip" title="Nhận <?php echo $cavet_cnt; ?> cà vẹt mang tên: <?php echo $cavet; ?>">
                                                   <?php echo $cavet_cnt; ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php
                                        $bike_cnt = 0;
                                        $bikes = '';
                                        foreach ($room->items_received as $item) {
                                            if ($item->type == 'bike' && !$item->early_return) {
                                                $bike_cnt ++;
                                                $bikes .= ($bikes ? ', ' : '') . $item->number;
                                            }
                                        }
                                    ?>
                                    <?php if ($bike_cnt): ?>
                                        <div class="clearfix in-item next">
                                            <span class="small-detail motobike"></span>
                                            <div class="orange-ellipse">
                                                <p data-toggle="tooltip" title="Giữ <?php echo $bike_cnt; ?> xe máy: <?php echo $bikes; ?>">
                                                	<?php echo $bike_cnt; ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-xs-6 room-center no-padding-left no-padding-right">
                                	<div class="col-xs-12">
                                        <p class="room-number" data-toggle="tooltip" title="<?php echo $room->description; ?>">
                                            <?php echo $room->name; ?>
                                        </p>
                                        <p><?php echo $room->description; ?></p>
                                    </div>
                                    <div class="col-xs-12 room-center room-status no-padding-left no-padding-right">
                                        <a class="view-room" href="/rent/view/<?php echo $room->rent_id; ?>">
                                            <button class="btn btn-room" style="padding-left: 20px; padding-right: 20px;"><?php echo $room->hourly ? 'GIỜ' : 'NGÀY'; ?></button>
                                        </a>
                                        <p><strong>Từ: </strong><?php echo date('d-m-Y H:i', $room->check_in); ?></p>
                                    </div>

                                </div>
                                <div class="col-xs-3 room-right no-padding-right">
                            		<?php if (isset($room->items_used) && !empty($room->items_used)): ?>
                            			<?php foreach ($room->items_used as $item): ?>
                                            <div class="clearfix out-item">
                                                <span class="small-detail <?php echo $item->icon_class; ?>"></span>
                                                <div class="orange-ellipse">
                                                	<p data-toggle="tooltip" title="Sử dụng <?php echo $item->quantity . ' ' . $item->name; ?>"><?php echo $item->quantity; ?></p>
                                                </div>
                                            </div>
                            			<?php endforeach; ?>
                            		<?php endif; ?>
                            	</div>
                            </div>
                            
                        	
                        <?php else: ?>
                        	<div class="col-xs-12 room-center">
                                <p class="room-number" data-toggle="tooltip" title="<?php echo $room->description; ?>">
                                	<?php echo $room->name; ?>
                                </p>
                                <p><?php echo $room->description; ?></p>
                                <a href="/site/checkin?room_id=<?php echo $room->id; ?>&hourly=1">
                                	<button class="btn btn-room" data-toggle="tooltip">Thuê Giờ</button>
                                </a>
								<a href="/site/checkin?room_id=<?php echo $room->id; ?>&hourly=0">
                                	<button class="btn btn-room" data-toggle="tooltip">Thuê Ngày</button>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($col_class == 'col-md-6' && $r_index%2 == 1) { ?>
                    </div>
                    <?php } ?>
                <?php endforeach; ?>
            </div>
    	<?php endforeach; ?>
    </div>
</div>

<script>
var room_status = "<?php echo $room_statuses; ?>";

$(document).ready(function() {
	setInterval(function(){
		checkRoomStatus()
	}, 60000);

	$(document).on('click', '.btn-pay-request', function(e) {
		e.preventDefault();

		if (confirm('Khi gửi yêu cầu gia hạn, chúng tôi sẽ liên hệ bạn ngay khi có thể. Bạn có đồng ý không?')) {
    		$.ajax({
    	        url: "/about/pay_request",
    	        type: 'POST',
    	        dataType: 'json',
    	        success: function (response) {
    	        	if (response.status) {
    	        		$.notify(response.message, {className: 'success', position: "right"});
    	        	}
    	        }
    	    });
		}
	});

	$(document).on('click', '.room-item.active', function(e) {
		window.location = $(this).find('a.view-room').attr('href');
	});
});

function checkRoomStatus() {
	$.ajax({
        url: "/site/check_status",
        type: 'POST',
        dataType: 'json',
        success: function (response) {
        	if (response.room_statuses != room_status) {
        		window.location.reload();
        	}
        }
    });
}
</script>