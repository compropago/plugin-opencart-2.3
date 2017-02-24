<?php echo $header; ?>
<?php echo $column_left ?>

<div class="container">
    <div class="row">
        <div class ="col-ms-12" style="padding:0px 15px;">
            <h1>Su pedido está confirmado.</h1>
            <p>Se ha enviado un correo electrónico a su dirección de correo electrónico <?php echo $email; ?> .</p>
            <hr>
        </div>
        <div class="col-sm-12">
            <div class="compropagoDivFrame" id="compropagodContainer" style="width: 100%;">
                <iframe style="width: 100%;"
                    id="compropagodFrame"
                    src="https://www.compropago.com/comprobante/?confirmation_id=<?php echo $orderId;?>"
                    frameborder="0"
                    scrolling="yes">
                </iframe>
            </div>
        </div>
        <div class ="col-ms-12" style="padding:20px 15px;">
            <br>
            <hr>
            <p>Si quieres hacer otra compra da click en el botón.</p>
            <a href="index.php" class="btn btn-success">Regresar</a>
        </div>
    </div>
</div>

<?php echo $footer; ?>


<script type="text/javascript">
    function resizeIframe() {
        var container=document.getElementById("compropagodContainer");
        var iframe=document.getElementById("compropagodFrame");
        if(iframe && container){
            var ratio=585/811;
            var width=container.offsetWidth;
            var height=(width/ratio);
            if(height>937){ height=937;}
            iframe.style.width=width + 'px';
            iframe.style.height=height + 'px';
        }
    }
    window.onload = function(event) {
        resizeIframe();
    };
    window.onresize = function(event) {
        resizeIframe();
    };
</script>