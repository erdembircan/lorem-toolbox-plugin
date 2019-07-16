<div class='wrap'>
  <h2><?php echo $this->_getArg('page_title'); ?>
  </h2>
  <form action='options.php' method='post'>
    <?php
  settings_fields($this->_getArg('options_key'));
  ?>
    <table class='form-table'>
      <h3>Shortcode Settings</h3>
      <tr>
        <th scope='row'>Use default lorem</th>
        <td>
          <input type='checkbox'
            name='<?php echo $options_key . '[use_default]'; ?>'
            <?php checked($options['use_default'], 'on'); ?>
          >
        </td>
      </tr>
      <tr>
        <th scope='row'>Default number of sentences</th>
        <td>
          <input type='number' min='1' max='100'
            name='<?php echo $options_key . '[shortcode_default_length]'; ?>'
            value='<?php echo $this->_get_options('shortcode_default_length');?>'>
        </td>
      </tr>
    </table> <?php submit_button('Save Changes'); ?>
  </form>
  <pre style='background:white; white-space: pre-wrap; color:grey; padding: 10px'>
  <?php print_r($options); ?>
</div>