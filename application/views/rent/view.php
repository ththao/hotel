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

<div id="content" style="margin-top: 10px;">
    <div class="container-fluid">
        <div id="detail">
            <!-- <div class="detail-title">
                <label>Thông tin thuê phòng <?php echo $room->name; ?></label>
            </div> -->
            <div class="col-xs-12 col-md-8">
                <div class="row paid-detail">
                    <div class="row top-detail">
                        <div class="col-xs-12 col-sm-6 col-md-6 room-start">
                            <span>Thuê theo <a href="#" class="btn btn-success btn-changeType" rent-id="<?php echo $room->rent_id; ?>"><?php echo $room->hourly == 1 ? 'giờ' : ($room->hourly == 2 ? 'Đêm' : 'ngày'); ?></a> lúc:</span><br>
                            <strong><?php echo date('d-m-Y H:i', $room->check_in); ?></strong>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-6" align="center">
                            <a href="#" class="btn btn-warning btn-notes hide" style="margin-bottom: 5px; margin-top: 5px;">Ghi chú</a>
                            <a href="#" class="btn btn-warning btn-additional-fee" style="margin-bottom: 5px; margin-top: 5px;">Phụ Thu</a>
                            <a href="#" class="btn btn-warning btn-negotiate-price" style="margin-bottom: 5px; margin-top: 5px;">Ghi chú</a>
                        </div>
                    </div>
                    
                    <div class="clearfix"></div>
                    
                    <div class="row middle-detail">
                    	<div class="col-md-12 received-group">
                            <?php echo $this->load->view('rent/items', array('room' => $room), true); ?>
                        </div>
                    </div>
                    
                    <div class="clearfix"></div>
                    
                    <div class="row middle-detail used-item-details">
                    	<?php if (isset($room->rent_id) && $room->rent_id): ?>
                    		<?php if (isset($room->items_used) && !empty($room->items_used)): ?>
                    			<?php foreach ($room->items_used as $item): ?>
                                    <div class="paid-icon used-item" rent-id="<?php echo $room->rent_id; ?>" item-id="<?php echo $item->id; ?>">
                                        <span class="small-detail <?php echo $item->icon_class; ?>"></span>
                                        <div class="ellipse yellow"><p class="center text-weight white"><?php echo $item->quantity; ?></p></div>
                                        <br/><br/><p style="font-size: 16px;"><?php echo $item->name; ?></p>
                                    </div>
                    			<?php endforeach; ?>
                    		<?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="clear-fix"></div>
                
                <div class="row end-detail">
                    <button class="btn btn-default btn-danger btn-cancel" rent-id="<?php echo $room->rent_id; ?>">Hủy Phòng</button>
                    <button class="btn btn-default btn-warning btn-changeroom" rent-id="<?php echo $room->rent_id; ?>">Đổi Phòng</button>
                    <button class="btn btn-default btn-primary btn-checkout pull-right" rent-id="<?php echo $room->rent_id; ?>">Trả Phòng</button>
                </div>
            </div>
            
            <div class="clear-fix"></div>
            
            <div class="col-xs-12 col-md-4 no-padding-left">
                <div class="top-definition">
                	<div class="row">
                    	<div class="col-xs-3 icon add-human hide" rent-id="<?php echo $room->rent_id; ?>">
                            <div class="small-detail human"></div>
                            <div class="clearfix"></div>
                            <div>Phụ Thu</div>
                        </div>
                        <div class="col-xs-3 icon add-id-card">
                            <span class="small-detail id-card"></span>
                            <div class="clearfix"></div>
                            <div>Giấy tờ</div>
                        </div>
                        <div class="col-xs-3 icon add-bike">
                            <span class="small-detail motobike"></span>
                            <div class="clearfix"></div>
                            <div>Xe</div>
                        </div>
                    </div>
                </div>
                
                <div class="top-definition top10">
                	<div class="row">
                		<?php foreach ($items as $item): ?>
                            <div class="col-xs-3 icon use-item" rent-id="<?php echo $room->rent_id; ?>" item-id="<?php echo $item->id; ?>">
                                <span class="small-detail <?php echo $item->icon_class?>"></span>
                                <div class="clearfix"></div>
                                <div><?php echo $item->name; ?></div>
                            </div>
                        <?php endforeach; ?>
					</div>
                </div>
                
                <div class="top-definition top10 temp-total">
                	<?php echo $this->load->view('rent/total', array('room' => $room, 'data' => $data), true); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="idCardModal" class="modal fade" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Nhận giấy tờ</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-3 no-padding-left">
    					<select name="card_type" class="card_type form-control">
    						<option value="id_card">CMND</option>
    						<option value="passport">Hộ Chiếu</option>
    						<option value="driving_license">Bằng Lái</option>
    						<option value="cavet">Cà Vẹt</option>
    					</select>
					</div>
					<div class="col-xs-6">
						<input class="txt-number form-control" type="text" name="number" placeholder="Số" />
					</div>
					<div class="col-xs-3 no-padding-right"><a href="#" class="open-scan-btn">Scan</a></div>
				</div>
				<br/>
				<div class="row">
					<input class="txt-name form-control" type="text" name="name" placeholder="Họ Tên" />
				</div>
				<br/>
				<div class="row">
					<input class="txt-address form-control" type="text" name="address" placeholder="Địa Chỉ" />
				</div>
				<br/>
				<div class="row">
					<div class="col-xs-4 no-padding-left">
						<input class="txt-birthday form-control" type="text" name="birthday" placeholder="Ngày Sinh" />
					</div>
					<div class="col-xs-4">
                		<select class="txt-gender form-control" name="gender">
                			<option value="1">Nam</option>
                			<option value="2">Nữ</option>
                		</select>
					</div>
					<div class="col-xs-4 no-padding-right">
						<input class="txt-nation form-control" type="text" name="nation" placeholder="Dân Tộc" />
					</div>
				</div>
				<div clas="row">
                    <div id="scanner-widget" style="padding-top: 10px;">
                        <div class="scanner-body">
                            <div id="datasymbol-barcode-viewport"></div>
                        </div>
                    </div>
				</div>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn btn-success btn-save" type="card" rent-id="<?php echo $room->rent_id; ?>">Lưu</a>
				<button type="button" class="btn btn-default" data-dismiss="modal">Thôi</button>
			</div>
		</div>
	</div>
</div>

<div id="idCardReturnModal" class="modal fade" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Cập nhật</h4>
			</div>
			<div class="modal-body">
				
			</div>
			<div class="modal-footer">
				<a href="#" class="btn btn-success btn-save" rent-id="<?php echo $room->rent_id; ?>" type="card">Cập Nhật</a>
				<a href="#" class="btn btn-warning btn-return" rent-id="<?php echo $room->rent_id; ?>">Trả</a>
				<a href="#" class="btn btn-danger btn-remove" rent-id="<?php echo $room->rent_id; ?>">Xóa</a>
				<button type="button" class="btn btn-default" data-dismiss="modal">Thôi</button>
			</div>
		</div>
	</div>
</div>

<div id="idBikeModal" class="modal fade" role="dialog">
	<div class="modal-dialog" style="width: 350px;">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Nhận xe</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<input class="txt-number form-control" type="text" name="number" placeholder="Biển số" />
				</div>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn btn-success btn-save" type="bike" rent-id="<?php echo $room->rent_id; ?>">Lưu</a>
				<button type="button" class="btn btn-default" data-dismiss="modal">Thôi</button>
			</div>
		</div>
	</div>
</div>

<div id="prepaidModal" class="modal fade" role="dialog">
	<div class="modal-dialog" style="width: 350px;">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Trả trước</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<input class="txt-number form-control" type="text" name="amount" placeholder="Số tiền trả trước"/>
				</div>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn btn-success btn-save" rent-id="<?php echo $room->rent_id; ?>">Lưu</a>
				<button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button>
			</div>
		</div>
	</div>
</div>

<div id="negotiatePriceModal" class="modal fade" role="dialog">
	<div class="modal-dialog" style="width: 350px;">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Ghi chú</h4>
			</div>
			<div class="modal-body">
				<div class="row" style="padding-bottom: 10px;">
					<textarea class="txt-note form-control" name="notes" placeholder="Ghi chú" rows="3"><?php echo $room->notes; ?></textarea>
				</div>
				<div class="row" style="padding-bottom: 10px;">
					<input class="txt-number form-control" type="number" name="prepaid" placeholder="Số tiền trả trước/thêm"/>
				</div>
				<div class="row" style="padding-bottom: 10px;">
					<input class="txt-number form-control" type="number" name="negotiate_price" placeholder="Giá thỏa thuận" value="<?php echo $room->negotiate_price ? $room->negotiate_price : ''; ?>"/>
				</div>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn btn-success btn-save" rent-id="<?php echo $room->rent_id; ?>">Lưu</a>
				<button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button>
			</div>
		</div>
	</div>
</div>

<div id="additionalFeeModal" class="modal fade" role="dialog">
	<div class="modal-dialog" style="width: 350px;">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Phụ Thu</h4>
			</div>
			<div class="modal-body">
				<table class="additional-fee-list" style="width: 100%;">
					<tr>
					<td style="width: 60%;"><input class="txt-note form-control" name="notes" placeholder="Nội dung" style="width: 95%;"/></td>
					<td style="width: 30%;"><input class="txt-number form-control" name="amount" placeholder="Số tiền"/></td>
					<td style="width: 10%;"><a href="#" style="float: right;" class="btn-save" rent-id="<?php echo $room->rent_id; ?>">Lưu</a></td>
					</tr>
					<?php if ($data['fee_list']): ?>
    					<?php foreach ($data['fee_list'] as $fee): ?>
    					<tr>
    					<td style="width: 60%; padding-top: 10px;"><?php echo $fee->notes ? $fee->notes : 'Phụ Thu'; ?></td>
    					<td style="width: 30%; padding-top: 10px;"><?php echo number_format($fee->amount, 0); ?></td>
    					<td style="width: 10%; padding-top: 10px;"><a href="#" style="float: right; color: red;" class="btn-remove-fee" fee-id="<?php echo $fee->id; ?>" rent-id="<?php echo $room->rent_id; ?>">Xóa</a></td>
    					</tr>
    					<?php endforeach; ?>
					<?php endif; ?>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button>
			</div>
		</div>
	</div>
</div>

<div id="notesModal" class="modal fade" role="dialog">
	<div class="modal-dialog" style="width: 350px;">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Ghi chú</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<textarea class="txt-note form-control" name="note" ><?php echo $room->notes; ?></textarea>
				</div>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn btn-success btn-save" rent-id="<?php echo $room->rent_id; ?>">Lưu</a>
				<button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button>
			</div>
		</div>
	</div>
</div>

<div id="changeRoomModal" class="modal fade" role="dialog">
	<div class="modal-dialog" style="width: 350px;">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Đổi phòng</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<select class="room form-control">
					<?php foreach ($free_rooms as $free_room): ?>
						<?php if (!$free_room->rent_id): ?>
							<option value="<?php echo $free_room->id; ?>"><?php echo $free_room->name; ?></option>
						<?php endif; ?>
					<?php endforeach;?>
					</select>
				</div>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn btn-success btn-save" type="bike" rent-id="<?php echo $room->rent_id; ?>">Lưu</a>
				<button type="button" class="btn btn-default" data-dismiss="modal">Thôi</button>
			</div>
		</div>
	</div>
</div>

<script>
var using = <?php echo $room->check_out ? 0 : 1; ?>;

function setEnable(bool) {
	if (bool) {
    	$('.add-human, .remove-human, .use-item, .used-item').removeClass("disabled");
    	$(".add-passport, .add-driving-license, .add-id-card, .add-bike").removeClass("disabled");
    	$(".btn-checkout, .btn-prepaid, .btn-negotiate-price").removeClass("disabled");
    	$("#prepaidModal .btn-save, #prepaidModal button").removeClass("disabled");
    	$("#negotiatePriceModal .btn-save, #negotiatePriceModal button").removeClass("disabled");
    	$("#idBikeModal .btn-save, #idBikeModal button").removeClass("disabled");
    	$("#idCardReturnModal .btn-save, #idCardReturnModal .btn-return, #idCardReturnModal .btn-remove, #idCardReturnModal button").removeClass("disabled");
    	$("#idCardModal .btn-save, #idCardModal button").removeClass("disabled");
	} else {
    	$('.add-human, .remove-human, .use-item, .used-item').addClass("disabled");
    	$(".add-passport, .add-driving-license, .add-id-card, .add-bike").addClass("disabled");
    	$(".btn-checkout, .btn-prepaid, .btn-negotiate-price").addClass("disabled");
    	$("#prepaidModal .btn-save, #prepaidModal button").addClass("disabled");
    	$("#negotiatePriceModal .btn-save, #negotiatePriceModal button").addClass("disabled");
    	$("#idBikeModal .btn-save, #idBikeModal button").addClass("disabled");
    	$("#idCardReturnModal .btn-save, #idCardReturnModal .btn-return, #idCardReturnModal .btn-remove, #idCardReturnModal button").addClass("disabled");
    	$("#idCardModal .btn-save, #idCardModal button").addClass("disabled");
	}
}

function checkRoomStatus() {
	$.ajax({
        url: "/rent/check_status",
        data: {
            rent_id: <?php echo $room->rent_id; ?>
        },
        type: 'POST',
        dataType: 'json',
        success: function (response) {
        	if (response.using != using) {
        		window.location.reload();
        	}
        }
    });
}

$(document).ready(function() {
	setInterval(function(){
		checkRoomStatus()
	}, 60000);
	
	
    $("#additionalFeeModal .txt-note").autocomplete({
    	source: [
    		'Thêm người',
    		'Sau 23h',
    		'Giặt quần áo',
    		'Phụ thu dịch vụ'
    	]
    });
    $("#additionalFeeModal .txt-number").autocomplete({
    	source: [
    		'10000',
    		'20000',
    		'30000',
    		'40000',
    		'50000',
    		'60000',
    		'70000',
    		'80000',
    		'90000',
    		'100000',
    	]
    });

	$(document).on('click', '.btn-changeType', function(e) {
		e.preventDefault();

		var selected = $(this);
		if ($(selected).hasClass("disabled")) {
			return false;
		}

		$.ajax({
            url: "/rent/changeType",
            data: {
                rent_id: $(selected).attr("rent-id")
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
            	setEnable(false);
            },
            success: function (response) {
                if (response.status == 1) {
                	if (response.hourly == 1) {
                    	$(selected).html('giờ');
                	} else if (response.hourly == 0) {
                		$(selected).html('ngày');
                	} else {
                		$(selected).html('đêm');
                	}
                } else {
                	window.location.reload();
                }
            },
            complete: function() {
            	setEnable(true);
            }
        });
	});
	
	$(document).on('click', '.add-human, .remove-human', function() {
		var selected = $(this);
		if ($(selected).hasClass("disabled")) {
			return false;
		}

		$.ajax({
            url: "/rent/add_remove_human",
            data: {
                rent_id: $(selected).attr("rent-id"),
                add: $(selected).hasClass("add-human") ? 1 : -1
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
            	setEnable(false);
            },
            success: function (response) {
                if (response.status == 1) {
                    $('.received-group').html(response.html);
                    $('.temp-total').html(response.total_html);
                } else {
                	window.location.reload();
                }
            },
            complete: function() {
            	setEnable(true);
            }
        });
	});
	
	$(document).on('click', '.use-item', function() {
		var selected = $(this);
		if ($(selected).hasClass("disabled")) {
			return false;
		}

		$.ajax({
            url: "/rent/add_item",
            data: {
                rent_id: $(selected).attr("rent-id"),
                item_id: $(selected).attr("item-id")
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
            	setEnable(false);
            },
            success: function (response) {
            	if (response.status) {
                	var existed = 0;
                	$('.used-item').each(function() {
                    	if ($(this).attr('item-id') == $(selected).attr("item-id")) {
                        	var cnt = parseInt($(this).find('.text-weight').html()) + 1;
                        	$(this).find('.text-weight').html(cnt);
                        	existed = 1;
                    	}
                	});
                	if (existed == 0) {
                		var html = '<div class="paid-icon used-item" rent-id="' + response.rent_id + '" item-id="' + response.item_id + '">';
                    	html += '<span class="small-detail ' + response.icon_class + '"></span>';
                    	html += '<div class="ellipse yellow"><p class="center text-weight white">1</p></div>';
                    	html += '<br/><br/><p style="font-size: 16px;">' + response.name + '</p></div>';
                    	$('.used-item-details').append(html);
                	}
                	$('.temp-total').html(response.total_html);
            	} else {
            		window.location.reload();
            	}
            },
            complete: function() {
            	setEnable(true);
            }
        });
	});

	$(document).on('click', '.used-item', function() {
		var selected = $(this);
		if ($(selected).hasClass("disabled")) {
			return false;
		}

		$.ajax({
            url: "/rent/remove_item",
            data: {
                rent_id: $(selected).attr("rent-id"),
                item_id: $(selected).attr("item-id")
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
            	setEnable(false);
            },
            success: function (response) {
                if (response.status) {
                	$('.used-item').each(function() {
                    	if ($(this).attr('item-id') == $(selected).attr("item-id")) {
                        	var cnt = parseInt($(this).find('.text-weight').html()) - 1;
                        	if (cnt) {
                        		$(this).find('.text-weight').html(cnt);
                        	} else {
                            	$(this).remove();
                        	}
                    	}
                	});
                	$('.temp-total').html(response.total_html);
                } else { 
            		window.location.reload();
                }
            },
            complete: function() {
            	setEnable(true);
            }
        });
	});

	$(document).on('click', '.add-id-card', function() {
		if ($(this).hasClass("disabled")) {
			return false;
		}
		
		$("#idCardModal input[type='text']").val("");
		$("#idCardModal").modal("show");
	});

	$(document).on('click', '.btn-changeroom', function() {
		if ($(this).hasClass("disabled")) {
			return false;
		}

		$("#changeRoomModal").modal("show");
	});

	$(document).on('click', '.add-bike', function() {
		if ($(this).hasClass("disabled")) {
			return false;
		}
		$("#idBikeModal input[type='text']").val("");
		$("#idBikeModal").modal("show");
	});
	
	$('#additionalFeeModal, #negotiatePriceModal, #idCardModal, #idBikeModal').on('shown.bs.modal', function () {
		if ($(this).attr('id') == 'idCardModal') {
			$(this).find('.txt-number').focus();
		} else {
			$(this).find('input').first().focus();
		}
	});
	
	$(document).on('blur', '#idCardModal .txt-number', function() {
		var selected = $(this);

		var number = $(selected).val();
		if (number.length >= 7) {
    		$.ajax({
                url: "/rent/complete_card",
                data: {
                    number: number,
                    type: $('#idCardModal .card_type').val()
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                	setEnable(false);
                },
                success: function (response) {
                    if (response.status == 1) {
                    	$('#idCardModal .card_type').val(response.type);
                    	$('#idCardModal .txt-number').val(response.number);
                    	$('#idCardModal .txt-name').val(response.name);
                    	$('#idCardModal .txt-nation').val(response.nation);
                    	$('#idCardModal .txt-address').val(response.address);
                    	$('#idCardModal .txt-gender').val(response.gender);
                    	$('#idCardModal .txt-birthday').val(response.birthday);
                    }
                },
                complete: function() {
                	setEnable(true);
                }
            });
		}
	});

	$(document).on("keypress", "#idCardModal input[type='text'], #idBikeModal input[type='text'], #additionalFeeModal input[type='text'], #negotiatePriceModal input[type='text'], #idCardReturnModal input[type='text']", function(e) {
		if (e.which == 13) {
			$(this).parents(".modal-dialog").find(".btn-save").trigger("click");
		}
	});

	$(document).on('click', '#idCardModal .btn-save, #idBikeModal .btn-save', function(e) {
		e.preventDefault();
		
		var selected = $(this);
		if ($(selected).hasClass("disabled")) {
			return false;
		}
		
		if ($(selected).attr('type') == 'bike') {
			if ($.trim($('#idBikeModal .txt-number').val()) == '') {
				$('#idBikeModal .txt-number').parents(".row").addClass('has-error');
				$('#idBikeModal .txt-number').focus();
				return false;
			}
		} else {
			if ($.trim($('#idCardModal .txt-name').val()) == '') {
				$('#idCardModal .txt-name').parents(".row").addClass('has-error');
				$('#idCardModal .txt-name').focus();
				return false;
			}
		}

		var card_type = $(selected).parents('.modal-dialog').find('.card_type');
		
		$.ajax({
            url: "/rent/receive_item",
            data: {
                rent_id: $(selected).attr("rent-id"),
                type: $(card_type).length > 0 ? $(card_type).val() : 'bike',
                name: $(selected).parents(".modal-dialog").find(".txt-name").val(),
                number: $(selected).parents(".modal-dialog").find(".txt-number").val(),
                nation: $(selected).parents(".modal-dialog").find(".txt-nation").val(),
                birthday: $(selected).parents(".modal-dialog").find(".txt-birthday").val(),
                gender: $(selected).parents(".modal-dialog").find(".txt-gender").val(),
                address: $(selected).parents(".modal-dialog").find(".txt-address").val()
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                $(selected).html("Đang lưu ...");
                setEnable(false);
            },
            success: function(response) {
                if (response.status) {
                	$('.received-group').html(response.html);
                	$(selected).html("Lưu");
                	$(selected).parents('.modal-dialog').find('.close').trigger('click');
                } else {
                	$.notify(response.message, {className: 'error', position: "right"});
                	$(selected).html("Lưu");
                	$(input).parents(".row").addClass('has-error');
        			$(input).focus();
        			return false;
                }
            },
            complete: function() {
            	setEnable(true);
            }
        });
	});

	$(document).on('click', '#changeRoomModal .btn-save', function(e) {
		e.preventDefault();
		
		var selected = $(this);
		if ($(selected).hasClass("disabled")) {
			return false;
		}

		$.ajax({
            url: "/rent/change_room",
            data: {
                rent_id: $(selected).attr("rent-id"),
                room_id: $('#changeRoomModal .room').val()
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                $(selected).html("Đang lưu ...");
                setEnable(false);
            },
            success: function(response) {
                if (response.status) {
                	window.location = '/site';
                } else {
                	$.notify(response.message, {className: 'error', position: "right"});
                	$(selected).html("Lưu");
        			return false;
                }
            },
            complete: function() {
            	setEnable(true);
            }
        });
	});

	$(document).on('click', '.received-item', function() {
		var selected = $(this);
		if ($(selected).hasClass("disabled")) {
			return false;
		}

		$.ajax({
            url: "/rent/get_received_items",
            data: {
                rent_id: <?php echo $room->rent_id; ?>,
                type: $(selected).attr("type")
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
            	setEnable(false);
            },
            success: function (response) {
                if (response.status == 1) {
	            	$('#idCardReturnModal .modal-body').html(response.html);
	            	if ($(selected).attr("type") == 'bike') {
	            		$('#idCardReturnModal .btn-save').attr('type', 'bike');
	            	} else {
	            		$('#idCardReturnModal .btn-save').attr('type', 'card');
	            	}
	            	$('#idCardReturnModal select[name="item_receive_id"]').trigger('change');
	        		$('#idCardReturnModal').modal("show");
                }
            },
            complete: function() {
            	setEnable(true);
            }
        });
	});

	$(document).on('change', '#idCardReturnModal select[name="item_receive_id"]', function(e) {
		var selected = $(this);

		if ($(this).val() == '') {
			$('#idCardReturnModal input').val('');
			$('.edit-card-field').slideUp('medium');
			$('.edit-bike-field').slideUp('medium');
			return false;
		}
		
		$.ajax({
            url: "/rent/receive_item_detail",
            data: {
            	item_receive_id: $('#idCardReturnModal').find('select[name="item_receive_id"]').val()
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                setEnable(false);
            },
            success: function(data) {
                if (data.status == 1) {
                	$('#idCardReturnModal .txt-number').val(data.item.number);
                    if (data.item.type == 'bike') {
                    	$('.edit-card-field').slideUp('medium');
                    	$('#idCardReturnModal .btn-save').attr('type', 'bike');
                    } else {
                    	$('#idCardReturnModal .edit-card-field .txt-name').val(data.item.name);
                    	$('#idCardReturnModal .edit-card-field .txt-nation').val(data.item.nation);
                    	$('#idCardReturnModal .edit-card-field .txt-birthday').val(data.item.birthday);
                    	$('#idCardReturnModal .edit-card-field .txt-address').val(data.item.address);
                    	$('#idCardReturnModal .edit-card-field .txt-gender').val(data.item.address);
                    	$('.edit-card-field').slideDown('medium');
                    	$('#idCardReturnModal .btn-save').attr('type', 'card');
                    }
                } else {
                	$('#idCardReturnModal').find('select[name="item_receive_id"]').val('').focus();
                }
            },
            complete: function (response) {
            	setEnable(true);
            }
        });
	});

	$(document).on('click', '#idCardReturnModal .btn-save', function(e) {
		e.preventDefault();
		
		var selected = $(this);
		if ($(selected).hasClass("disabled")) {
			return false;
		}
		
		if ($(selected).attr('type') == 'bike') {
			if ($.trim($('#idCardReturnModal .txt-number').val()) == '') {
				$('#idCardReturnModal .txt-number').parents(".row").addClass('has-error');
				$('#idCardReturnModal .txt-number').focus();
				return false;
			}
			var data = {
                rent_id: $(selected).attr("rent-id"),
            	item_receive_id: $('#idCardReturnModal').find('select[name="item_receive_id"]').val(),
                number: $(selected).parents(".modal-dialog").find(".txt-number").val()
			};
		} else {
			if ($.trim($('#idCardReturnModal .txt-name').val()) == '') {
				$('#idCardReturnModal .txt-name').parents(".row").addClass('has-error');
				$('#idCardReturnModal .txt-name').focus();
				return false;
			}
			var data = {
                rent_id: $(selected).attr("rent-id"),
            	item_receive_id: $('#idCardReturnModal').find('select[name="item_receive_id"]').val(),
            	name: $(selected).parents(".modal-dialog").find(".txt-name").val(),
                number: $(selected).parents(".modal-dialog").find(".txt-number").val(),
                nation: $(selected).parents(".modal-dialog").find(".txt-nation").val(),
                birthday: $(selected).parents(".modal-dialog").find(".txt-birthday").val(),
                address: $(selected).parents(".modal-dialog").find(".txt-address").val(),
                gender: $(selected).parents(".modal-dialog").find(".txt-gender").val()
			};
		}
		
		$.ajax({
            url: "/rent/receive_item",
            data: data,
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                $(selected).html("Đang lưu ...");
                setEnable(false);
            },
            success: function(response) {
                if (response.status) {
                	$('.received-group').html(response.html);
                	$(selected).html("Cập Nhật");
                	$(selected).parents('.modal-dialog').find('.close').trigger('click');
                } else {
                	$.notify(response.message, {className: 'error', position: "right"});
                	$(selected).html("Cập Nhật");
                	$(input).parents(".row").addClass('has-error');
        			$(input).focus();
        			return false;
                }
            },
            complete: function() {
            	setEnable(true);
            }
        });
	});

	$(document).on('click', '#idCardReturnModal .btn-return', function(e) {
		e.preventDefault();
		var selected = $(this);

		if ($('#idCardReturnModal').find('select[name="item_receive_id"]').val() == '') {
			$('#idCardReturnModal').find('select[name="item_receive_id"]').focus();
			return false;
		}
		
		$.ajax({
            url: "/rent/return_receive_item",
            data: {
            	item_receive_id: $('#idCardReturnModal').find('select[name="item_receive_id"]').val()
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                $(selected).html("Đang lưu ...");
                setEnable(false);
            },
            complete: function (response) {
            	window.location.reload();
            }
        });
	});

	$(document).on('click', '#idCardReturnModal .btn-remove', function(e) {
		e.preventDefault();
		var selected = $(this);

		if ($('#idCardReturnModal').find('select[name="item_receive_id"]').val() == '') {
			$('#idCardReturnModal').find('select[name="item_receive_id"]').focus();
			return false;
		}

		if (!confirm('Bạn muốn xóa?')) {
			return false;
		}
		
		$.ajax({
            url: "/rent/remove_receive_item",
            data: {
            	item_receive_id: $('#idCardReturnModal').find('select[name="item_receive_id"]').val()
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                $(selected).html("Đang lưu ...");
                setEnable(false);
            },
            complete: function (response) {
            	window.location.reload();
            }
        });
	});

	$(".btn-additional-fee").click(function(e) {
		e.preventDefault();
		
		if ($(this).hasClass("disabled")) {
			return false;
		}
		
		$('#additionalFeeModal input[name="amount"]').val('');
		$('#additionalFeeModal input[name="notes"]').val('');
		$("#additionalFeeModal").modal("show");
	});

	$(document).on('click', '#additionalFeeModal .btn-save', function(e) {
		e.preventDefault();
		var selected = $(this);
		
		var rent_id = $(selected).attr("rent-id");
		$.ajax({
            url: "/rent/add_fee",
            data: {
            	rent_id: rent_id,
            	amount: $('#additionalFeeModal input[name="amount"]').val(),
            	notes: $('#additionalFeeModal input[name="notes"]').val()
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                $(selected).html("...");
                setEnable(false);
            },
            success: function (data) {
                if (data.status == 1) {
                	$('.temp-total').html(data.total_html);
                	var html = '<tr>';
                	html += '<td style="width: 60%; padding-top: 10px;">' + (data.notes ? data.notes : 'Phụ Thu') + '</td>';
                	html += '<td style="width: 30%; padding-top: 10px;">' + data.amount + '</td>';
                	html += '<td style="width: 10%; padding-top: 10px;"><a href="#" style="float: right; color: red;" class="btn-remove-fee" rent-id="' + rent_id + '" fee-id="' + data.fee_id + '">Xóa</a></td>';
                	html += '</tr>';
                	$('.additional-fee-list').append(html);
            		$('#additionalFeeModal input[name="amount"]').val('');
            		$('#additionalFeeModal input[name="notes"]').val('');
                } else {
            		window.location.reload();
                }
            },
            complete: function() {
            	$(selected).html("Lưu");
            	setEnable(true);
            }
        });
	});

	$(document).on('click', '#additionalFeeModal .btn-remove-fee', function(e) {
		e.preventDefault();
		var selected = $(this);
		
		$.ajax({
            url: "/rent/remove_fee",
            data: {
            	rent_id: $(selected).attr("rent-id"),
            	fee_id: $(selected).attr("fee-id")
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                $(selected).html("...");
                setEnable(false);
            },
            success: function (response) {
                if (response.status == 1) {
                	$('.temp-total').html(response.total_html);
                	$(selected).parents('tr').remove();
                } else {
            		window.location.reload();
                }
            },
            complete: function() {
            	$(selected).html("Xóa");
            	setEnable(true);
            }
        });
	});

	$(".btn-negotiate-price").click(function(e) {
		e.preventDefault();
		
		if ($(this).hasClass("disabled")) {
			return false;
		}
		
		$("#negotiatePriceModal input[name='prepaid']").val("");
		$("#negotiatePriceModal").modal("show");
	});

	$(document).on('click', '#negotiatePriceModal .btn-save', function(e) {
		e.preventDefault();
		var selected = $(this);
		
		$.ajax({
            url: "/rent/save_negotiate_price",
            data: {
            	rent_id: $(selected).attr("rent-id"),
            	negotiate_price: $('#negotiatePriceModal input[name="negotiate_price"]').val(),
            	prepaid: $('#negotiatePriceModal input[name="prepaid"]').val(),
            	notes: $('#negotiatePriceModal textarea[name="notes"]').val()
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                $(selected).html("Đang lưu ...");
                setEnable(false);
            },
            success: function (response) {
                if (response.status == 1) {
                	$('.temp-total').html(response.total_html);
                	$(selected).parents('.modal-dialog').find('.close').trigger('click');
                } else {
            		window.location.reload();
                }
            },
            complete: function() {
            	$(selected).html("Lưu");
            	setEnable(true);
            }
        });
	});

	$(".btn-notes").click(function(e) {
		e.preventDefault();
		
		if ($(this).hasClass("disabled")) {
			return false;
		}
		
		$("#notesModal").modal("show");
	});

	$(document).on('click', '#notesModal .btn-save', function(e) {
		e.preventDefault();
		var selected = $(this);
		
		$.ajax({
            url: "/rent/update_notes",
            data: {
            	rent_id: $(selected).attr("rent-id"),
            	notes: $('#notesModal textarea').val()
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                $(selected).html("Đang lưu ...");
                setEnable(false);
            },
            success: function (response) {
                if (response.status == 1) {
                	$('.temp-total').html(response.total_html);
                	$(selected).parents('.modal-dialog').find('.close').trigger('click');
                } else {
            		window.location.reload();
                }
            },
            complete: function() {
            	$(selected).html("Lưu");
            	setEnable(true);
            }
        });
	});

	$(document).on('click', '.btn-checkout', function() {
		var selected = $(this);
		if ($(selected).hasClass("disabled")) {
			return false;
		}

		$.ajax({
            url: "/rent/checkout",
            data: {
                rent_id: $(selected).attr("rent-id")
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                $(selected).html("Vui lòng chờ xử lý ...");
                setEnable(false);
            },
            success: function (response) {
            	if (response.status == 1) {
            		window.location.reload();
            	} else {
            		window.location = "/site/index";
            	}
            }
        });
	});

	$(document).on('click', '.btn-cancel', function() {
		var selected = $(this);
		if ($(selected).hasClass("disabled")) {
			return false;
		}

		if (!confirm('Bạn muốn hủy phòng đã chọn?')) {
			return false;
		}

		$.ajax({
            url: "/rent/cancel",
            data: {
                rent_id: $(selected).attr("rent-id")
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                $(selected).html("Vui lòng chờ xử lý ...");
                setEnable(false);
            },
            success: function (response) {
            	window.location = "/site/index";
            }
        });
	});
	
	$(document).on('click', '.open-scan-btn', function(e) {
		$('#scanner-widget').show();
		CreateScanner();
	});
	
	$("#idCardModal").on('hide.bs.modal', function(){
        DSScanner.Destroy();
    });
});

function onBarcodeReady (barcodeResult) {
    
	for (var i = 0; i < barcodeResult.length; ++i) {
        var sBarcode = DSScanner.bin2String(barcodeResult[i]);
        if (sBarcode) {
        	var cccd = sBarcode.split('|');
        	
        	$('#idCardModal .txt-number').val(cccd[0]);
        	$('#idCardModal .txt-name').val(cccd[2]);
        	$('#idCardModal .txt-address').val(cccd[5]);
        	var birthday = cccd[3];
        	$('#idCardModal .txt-birthday').val(birthday.substring(0,2) + '/' + birthday.substring(2,4) + '/' + birthday.substring(4,8));
        	$('#idCardModal .txt-gender').val(cccd[4] == 'Nam' ? 1 : 2);
        	
            DSScanner.Destroy();
        	
        	break;
        }
    }
        
};

function onError(err) {
	//console.log(err.message);
}

function CreateScanner() {
    var scannerSettings = {
		scanner: {
			key: '053-96093989-93891463',
            frameTimeout:	100,
            barcodeTimeout:	1000,
            beep: true,
		},
        viewport: {
            id: 'datasymbol-barcode-viewport'
        },
        camera: {
			facingMode: 'environment'
        },
        barcode: {
            barcodeTypes: ['EAN13', 'UPCA', 'Code128', 'DataMatrix', 'QRCode', 'QRCodeUnrecognized'],
            bQRCodeFindMicro: false
        }
    };

    DSScanner.addEventListener('onError', onError);
    DSScanner.addEventListener('onBarcode', onBarcodeReady);

    DSScanner.addEventListener('onScannerReady', function () {
        DSScanner.StartScanner();
    });

    DSScanner.Create(scannerSettings);
}
</script>