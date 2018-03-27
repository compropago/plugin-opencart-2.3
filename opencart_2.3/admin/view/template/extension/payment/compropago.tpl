<?php echo $header; ?>
<?php echo $column_left; ?>

<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <a id="save_config" data-toggle="tooltip" title="Save" class="btn btn-primary" style="cursor:pointer;"><i class="fa fa-save"></i></a>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="Cancel" class="btn btn-default"><i class="fa fa-reply"></i></a>
      </div>
     
      <h1><?php echo $heading_title; ?></h1>

      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>

  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>

    <div class="panel panel-default">

      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>

      <div class="panel-body">
        <form name=compropago_form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-compropago" class="form-horizontal">
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status">Status</label>
            <div class="col-sm-10">
              <select name="compropago_status" id="input-status" class="form-control">
                <option value="1" <?php echo $status ? "selected" : ""; ?> >Enabled</option>
                <option value="0" <?php echo !$status ? "selected" : ""; ?> >Disabled</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label">Public Key</label>
            <div class="col-sm-10">
              <input type="text" name="compropago_public_key" value="<?php echo $public_key; ?>" placeholder="" id="input-total" class="form-control" />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label">Private Key</label>
            <div class="col-sm-10">
              <input type="text" name="compropago_private_key" value="<?php echo $private_key; ?>" placeholder="" id="input-total" class="form-control" />
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label">Mode</label>
            <div class="col-sm-10">
              <select name="compropago_mode" class="form-control">
                <option value="1" <?php if ($mode) { echo 'selected'; } ?>>Active</option>
                <option value="0" <?php if (!$mode) { echo 'selected'; } ?>>Test</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label">Providers</label>
            <div class="col-sm-5">
              <select id="deactive_providers" multiple="" class="form-control"style="height:100px;">
                <?php foreach ($deactive_providers as $provider) { ?>
                <option value="<?php echo $provider->internal_name; ?>"><?php echo $provider->name; ?></option>
                <?php } ?>
              </select> <br>
              <a id="deactive_provider" href="#" class="btn btn-primary" style="width:100%">Active</a>
            </div>

            <div class="col-sm-5">
              <select id="active_providers" multiple="" class="form-control" style="height:100px;">
                <?php foreach ($active_providers as $provider) { ?>
                <option type="button" value="<?php echo $provider->internal_name; ?>"><?php echo $provider->name; ?></option>
                <?php } ?>
              </select> <br>
              <a id="active_provider" href="#" class="btn btn-primary" style="width:100%">Deactive</a>
            </div>

            <input id="providers" name="compropago_providers" type="hidden" value=""/>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?> 

<script>
  $('#active_provider').click(function(e){
    e.preventDefault();
    $("#active_providers option").each(function(){
      if($(this).is(':selected')){
        $('#deactive_providers').append('<option value="'+$(this).val()+'">'+$(this).html()+'</option>');
        $(this).remove();
      }
    });
  });
  $('#deactive_provider').click(function(e){
    e.preventDefault();
    
    $("#deactive_providers option").each(function(){
      if($(this).is(':selected')){
        $('#active_providers').append('<option value="'+$(this).val()+'">'+$(this).html()+'</option>');
        $(this).remove();
      }
    });
  });
  $('#save_config').click(function(e) {
    e.preventDefault();
    var active = '';
    $('#active_providers option').each(function(){
      if (active == '') {
        active += $(this).val();
      } else {
        active += ','+$(this).val();
      }
    });
    console.log(active);
    $('#providers').val(active);
    document.compropago_form.submit();
  });
</script>