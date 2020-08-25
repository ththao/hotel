<?php if ($room->human): ?>
    <div class="paid-icon remove-human" rent-id="<?php echo $room->rent_id; ?>">
        <span class="small-detail human"></span>
        <div class="ellipse orange remove-human" rent-id="<?php echo $room->rent_id; ?>">
        	<p class="center text-weight white"><?php echo $room->human; ?></p>
        </div>
        <br/><br/><p style="font-size: 18px;">Phụ Thu</p>
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
    <div class="paid-icon received-item received_id_card" type="id_card" title="Nhận <?php echo $id_card_cnt; ?> CMND mang tên <?php echo $id_cards; ?>">
        <span class="small-detail id-card"></span>
        <div class="ellipse orange received-item" type="id_card">
        	<p class="center text-weight white"><?php echo $id_card_cnt; ?></p>
        </div>
        <br/><br/><p style="font-size: 18px;">CMND</p>
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
    <div class="paid-icon received-item received_driving_license" type="driving_license" title="Nhận <?php echo $driving_license_cnt; ?> bằng lái xe mang tên <?php echo $driving_licenses; ?>">
        <span class="small-detail drive"></span>
        <div class="ellipse orange"><p class="center text-weight white"><?php echo $driving_license_cnt; ?></p></div>
        <br/><br/><p style="font-size: 18px;">Bằng Lái</p>
    </div>
<?php endif; ?>

<?php
    $cavet_cnt = 0;
    $cavets = '';
    foreach ($room->items_received as $item) {
        if ($item->type == 'cavet' && !$item->early_return) {
            $cavet_cnt ++;
            $cavets .= ($cavets ? ', ' : '') . $item->name;
        }
    }
?>
<?php if ($cavet_cnt): ?>
    <div class="paid-icon received-item received_cavet" type="cavet" title="Nhận <?php echo $cavet_cnt; ?> cà vẹt xe mang tên <?php echo $cavets; ?>">
        <span class="small-detail drive"></span>
        <div class="ellipse orange"><p class="center text-weight white"><?php echo $cavet_cnt; ?></p></div>
        <br/><br/><p style="font-size: 18px;">Cà Vẹt</p>
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
    <div class="paid-icon received-item received_passport" type="passport" title="Nhận <?php echo $passport_cnt; ?> hộ chiếu mang tên <?php echo $passports; ?>">
        <span class="small-detail passport"></span>
        <div class="ellipse orange"><p class="center text-weight white"><?php echo $passport_cnt; ?></p></div>
        <br/><br/><p style="font-size: 18px;">Hộ Chiếu</p>
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
    <div class="paid-icon received-item received_bike" type="bike" title="Giữ <?php echo $bike_cnt; ?> xe máy biển số <?php echo $bikes; ?>">
        <span class="small-detail motobike"></span>
        <div class="ellipse orange"><p class="center text-weight white"><?php echo $bike_cnt; ?></p></div>
        <br/><br/><p style="font-size: 18px;">Xe</p>
    </div>
<?php endif; ?>