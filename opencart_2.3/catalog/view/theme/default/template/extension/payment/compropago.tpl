
<link rel="stylesheet" href="vendor/assets/styles.css">


<form class="form-horizontal">

        <fieldset id="payment">

        <section class="cpcontainer cpprovider-select">
            <div class="cprow">
                <div class="cpcolumn">
                    <h1>Tiendas disponibles</h1>
                </div>
            </div>

            <div class="cprow">
                <div class="cpcolumn">
                   <p>Antes de finalizar seleccione la tienda de su preferencia</p><hr>
                </div>
            </div>


            <?php if($showLogo == 'yes') { ?>
                <ul>
                    <?php foreach($providers as $provider){ ?>
                        <li>
                            <input type="radio" id="compropago_<?php echo $provider->internal_name; ?>" name="compropagoProvider" value="<?php echo $provider->internal_name; ?>">
                            <label for="compropago_<?php echo $provider->internal_name; ?>">
                                <img src="<?php echo $provider->image_medium; ?>" alt="compropago_<?php echo $provider->internal_name; ?>">
                            </label>
                        </li>
                    <?php } ?>
                </ul>


            <?php } else { ?>
                <select name="compropagoProvider" title="Proveedores">
                    <?php foreach ($providers as $provider){ ?>
                        <option value="<?php echo $provider->internal_name; ?>"> <?php echo $provider->name; ?> </option>
                    <?php } ?>
                </select>
            <?php } ?>
        </section>

        <div>
        </div>


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
        var internal = $("input[name=compropagoProvider]:checked").val();
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
