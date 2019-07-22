<div class='wrap'>
  <h2><?php echo $this->_getArg('page_title'); ?>
    <hr>
  </h2>
  <form action='options.php' method='post'>
    <?php settings_fields($this->_getArg('options_key')); ?>
    <table class='form-table'>
      <h2>Shortcode Settings</h2>
      <hr>
      <tr>
        <th scope='row'>Use custom phrases</th>
        <td>
          <input type='checkbox'
            name='<?php echo $options_key . '[use_custom]'; ?>'
            <?php checked($options['use_custom'], 'on'); ?>
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
        <th scope='row'>Default paragraph lengths (in words)</th>
        <td>
          <div>
            <label>Min: </label>
            <input type='number' min='1' max='100'
              name='<?php echo $options_key . '[shortcode_default_min_paragraph_length]'; ?>'
              value='<?php echo $this->_get_options('shortcode_default_min_paragraph_length');?>'>
          </div>
          <div>
            <label>Max: </label>
            <input type='number' min='1' max='100'
              name='<?php echo $options_key . '[shortcode_default_max_paragraph_length]'; ?>'
              value='<?php echo $this->_get_options('shortcode_default_max_paragraph_length');?>'>
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
      <tr>
        <td colspan='2'>
          <hr>
        </td>
      </tr>
      <tr>
        <th scope='row'>Custom phrases</th>
        <td>
          <textarea rows='5' cols='100%'
            name='<?php echo $options_key . '[lorem_raw]'; ?>'><?php echo $this->_get_options('lorem_raw');?>
            </textarea>
        </td>
      </tr>
    </table> <?php submit_button('Save Changes'); ?>
  </form>

  <div id='lorem_app' class='wrap'>
    <h2>{{page_title | capAF}}</h2>
    <form action='options.php' method='post'>
      <?php settings_fields($this->_getArg('options_key')); ?>
      <h2>Shortcode Settings</h2>
      <settings-table :settings='inputs'></settings-table>
    </form>
  </div>
</div>

<!-- settings-table template -->
<script type='x-template' id='loremSettingsTable'>
  <table class='form-table'>
    <tr v-for='s in settings'>
      <th scope='row'>{{s.title | capF}}</th>
      <td>
        <setting-row :element='s.element' :domProps='s.domProps'></setting-row>
      </td>
    </tr>
  </table>
</script>