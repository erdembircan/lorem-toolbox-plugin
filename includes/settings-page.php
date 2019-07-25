<div id='lorem_app' class='wrap'>
  <h2>{{page_title | capAF}}</h2>
  <settings-table :settings='inputs'></settings-table>
  <hr>
  <h2>Generate Posts</h2>
  <generate-posts :ajaxurl='ajax_url' :ajaxaction='ajax_action'></generate-posts>
</div>

<!-- settings-table template -->
<script type='x-template' id='loremSettingsTable'>
  <form action='options.php' method='post'>
    <?php settings_fields($this->_getArg('options_key')); ?>
    <h2>Shortcode Settings</h2>
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
  </form>
</script>

<script type="x-template" id="generatePosts">
  <form :action='ajaxurl' method='post' @submit.prevent='handleForm'>
    <table class='form-table'>
    <input type='hidden' name='action' :value='ajaxaction'>
      <tr>
        <th scope='row'>Number of posts</th>
        <td>
          <input type='number' min='1' max='100' :value='postCount' name='post_count'>
        </td>
      </tr>
    </table>
    <input type='submit' value='Generate Posts' class='button button-secondary'>
  </form>
</script>