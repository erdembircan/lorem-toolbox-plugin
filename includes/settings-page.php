<div class='wrap'>
  <h2><?php echo $this->_getArg('page_title'); ?>
  </h2>
  <form action='options.php' method='post'>
    <?php
  settings_fields($this->_getArg('options_key'));
  ?>
    <table class='form-table'>
      <h2>Shortcode Settings</h2>
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
        <th scope='row'>Default number of paragraphs</th>
        <td>
          <input type='number' min='1' max='100'
            name='<?php echo $options_key . '[shortcode_default_paragraph_length]'; ?>'
            value='<?php echo $this->_get_options('shortcode_default_paragraph_length');?>'>
        </td>
      </tr>
      <tr>
        <th scope='row'>Default word lengths (in character)</th>
        <td>
          <div>
            <label>Min: </label>
            <input type='number' min='1' max='100'
              name='<?php echo $options_key . '[shortcode_default_min_word_length]'; ?>'
              value='<?php echo $this->_get_options('shortcode_default_min_word_length');?>'>
          </div>
          <div>
            <label>Max: </label>
            <input type='number' min='1' max='100'
              name='<?php echo $options_key . '[shortcode_default_max_word_length]'; ?>'
              value='<?php echo $this->_get_options('shortcode_default_max_word_length');?>'>
          </div>
        </td>
      </tr>
      <tr>
        <th scope='row'>Default sentence lengths (in words)</th>
        <td>
          <div>
            <label>Min: </label>
            <input type='number' min='1' max='100'
              name='<?php echo $options_key . '[shortcode_default_min_sentence]'; ?>'
              value='<?php echo $this->_get_options('shortcode_default_min_sentence');?>'>
          </div>
          <div>
            <label>Max: </label>
            <input type='number' min='1' max='100'
              name='<?php echo $options_key . '[shortcode_default_max_sentence]'; ?>'
              value='<?php echo $this->_get_options('shortcode_default_max_sentence');?>'>
          </div>
        </td>
      </tr>
    </table> <?php submit_button('Save Changes'); ?>
  </form>
  <pre style='background:white; white-space: pre-wrap; color:grey; padding: 10px'>
  <?php print_r($options); ?>
</div>