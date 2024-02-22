<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>

<script>
window.onload = function () {
    
    
    var charts = [];
    <?php for ($i = 0; $i <= 9; $i ++) { ?>
        /*charts[<?php echo $i; ?>] = new CanvasJS.Chart("chartContainer<?php echo $i; ?>", {
        	axisY: {title: "<?php echo $i; ?>"},
        	data: [{type: "line", dataPoints: <?php echo json_encode($data[$i], JSON_NUMERIC_CHECK); ?>}]
        });
        charts[<?php echo $i; ?>].render();*/
        charts["l<?php echo $i; ?>"] = new CanvasJS.Chart("lastChartContainer<?php echo $i; ?>", {
        	axisY: {title: "<?php echo $i; ?>"},
        	data: [{type: "line", dataPoints: <?php echo json_encode($data2[$i], JSON_NUMERIC_CHECK); ?>}]
        });
        charts["l<?php echo $i; ?>"].render();
    <?php } ?>
    
    <?php foreach ($last2 as $date => $chartData) { ?>
        charts["<?php echo $date; ?>"] = new CanvasJS.Chart("chartContainer<?php echo $date; ?>", {
    	animationEnabled: true,
        	title: {text: "<?php echo $date; ?>"},
        	axisX: {interval: 1},
        	axisY: {interval: 1},
        	data: [{
        		type: "bar",
        		dataPoints: <?php echo json_encode($chartData['chart'], JSON_NUMERIC_CHECK); ?>
        	}]
        });
        charts["<?php echo $date; ?>"].render();
    <?php } ?>
}
</script>

<div id="content">
    <?php for ($i = 0; $i <= 9; $i ++) { ?>
    <!-- <div id="chartContainer<?php echo $i; ?>" style="height: 400px; width: 100%; margin-bottom: 50px;"></div> -->
    <div id="lastChartContainer<?php echo $i; ?>" style="height: 400px; width: 100%; margin-bottom: 50px;"></div>
    <?php } ?>
    
    <?php foreach ($last2 as $date => $chartData) { ?>
    <?php print_r($chartData['numbers']) ?>
    <div id="chartContainer<?php echo $date; ?>" style="height: 400px; width: 100%;  margin-bottom: 50px;"></div>
    <?php } ?>
</div>