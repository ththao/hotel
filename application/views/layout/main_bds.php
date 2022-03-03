<!DOCTYPE html>
<html>
<head>
    <?php $this->load->view('layout/partials/head_bds');?>
</head>
<body>
    <!--Content-->
    <?php if(isset($content)) $this->load->view($content['link'], $content['data']);?>
    <!--End content-->
    
    <!--Footer-->
    <div id="footer">
    </div>
    <!--End footer-->
</body>
</html>