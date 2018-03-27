<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/cppayment.css" />


<form class="form-horizontal">

        <fieldset id="payment">

        <section class="cpcontainer cpprovider-select">
            <div class="row">
                <div class="col-sm-12">
                    <h4 style="color:#000">¿Dónde quieres pagar?<sup>*</sup></h4>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 cpprovider-select" id="cppayment_store">
                    <select name="compropagoProvider" title="Proveedores" class="providers_list">
                        <?php foreach ($providers as $provider){ ?>
                        <option value="<?php echo $provider->internal_name; ?>"> <?php echo $provider->name; ?> </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="cppayment_text">
                    <br>
                    <p><sup>*</sup>Comisionistas <a href="https://compropago.com/legal/corresponsales_cnbv.pdf" target="_blank">autorizados por la CNBV</a> como corresponsales bancarios.</p>
                    </div>
                </div>
            </div>

        </section>

        <script>
            var providers = document.querySelectorAll(
                    ".cpcontainer.cpprovider-select ul li label img"
            );
            for (x = 0; x < providers.length; x++){
                providers[x].addEventListener('click', function(){
                    cleanCpRadio();
                    id = this.getAttribute("alt");
                    document.querySelector("#"+id).checked = true;
                });
            }
            function cleanCpRadio(){
                for(y = 0; y < providers.length; y++){
                    id = providers[y].parentNode.getAttribute('for');
                    document.querySelector("#"+id).checked = false;
                }
            }
        </script>
    </fieldset>
</form>

<div class="buttons">
    <div class="pull-right">
        <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary" />
    </div>
</div>


<script type="text/javascript">
$(document).ready(function(){
    $('#button-confirm').on('click', function() {
        var internal = $("select[name=compropagoProvider]").val();
        console.log(internal);
        $.ajax({
            url: 'index.php?route=extension/payment/compropago/saveOrder',
            type: 'post',
            data: {
                compropagoProvider: internal, 
            },
            dataType: 'json',
            beforeSend: function() {
                $('#button-confirm').button('loading');
            },
            complete: function() {
                $('#button-confirm').button('reset');
            },
            success: function(json) {
                if (json['error']) {
                    alert(json['error']);
                }
                if (json['success']) {
                    location = json['success'];
                }
            }
        });
    });    
});
    
    
</script>

