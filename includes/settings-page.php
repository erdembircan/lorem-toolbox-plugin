<div id='lorem_app' class='wrap'>
  <h2>{{page_title | capAF}}</h2>
  <form action='options.php' method='post'>
    <?php settings_fields($this->_getArg('options_key')); ?>
    <h2>Shortcode Settings</h2>
    <settings-table :settings='inputs'></settings-table>
  </form>
  <hr>
  <h2>Generate Posts</h2>
</div>

<!-- settings-table template -->
<script type='x-template' id='loremSettingsTable'>
  <table class='form-table'>
    <tr v-for='s in settings'>
      <th scope='row'>{{s.title | capF}}</th>
      <td>
      <div>
        <setting-row v-for='el in s.elements' :element='el.element' :domProps='el.domProps'></setting-row>
      </div>
      </td>
    </tr>
    <input type="submit" value="Save Changes" class="button button-primary">
  </table>
</script>