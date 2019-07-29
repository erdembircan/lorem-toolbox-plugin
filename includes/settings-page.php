<div id='lorem_app' class='wrap'>
  <h2>{{page_title | capAF}}</h2>
  <settings-table :settings='inputs'></settings-table>
  <br />
  <hr>
  <br />
  <h2>Generate Posts</h2>
  <p>There are <b>{{animatedCount}}</b> generated posts</p>
  <generate-posts :ajaxurl='ajax_url' :ajaxactiongenerate='ajax_action_generate' :ajaxactiondelete='ajax_action_delete'
    :nonce='nonce' @fetched='updatePostCount'></generate-posts>
</div>

<script type='x-template' id='loremStatus'>
  <div>
    <span v-show='fetching' class='eb-lorem-fetch-indicator'>🔷</span>
    <span v-show='fetching ===false' :style="{color: statusColor}">{{data.message}}</span>
  </div>
</script>

<!-- settings-table template -->
<script type='x-template' id='loremSettingsTable'>
  <form action='options.php' method='post'>
    <?php settings_fields($this->_getArg('options_key')); ?>
    <h2>Default Settings</h2>
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

<!-- generate-posts template -->
<script type="x-template" id="generatePosts">
  <form :action='ajaxurl' method='post' @submit.prevent='handleForm'>
    <table class='form-table'>
    <input type='hidden' name='action' :value='ajaxactiongenerate'>
    <input type='hidden' name='nonce' :value='nonce'>
      <tr>
        <th scope='row'>Number of posts</th>
        <td>
          <input type='number' min='1' max='1000' v-model:value='postCount' name='post_count'>
        </td>
      </tr>
    </table>
    <div class='eb-lorem-button-container'>
      <input ref='submitButton' type='submit' value='Generate Posts' class='button button-secondary'>
      <input ref='deleteButton' type='button' @click.prevent='deleteGenerated' class='button eb-lorem-button-red' value='Delete Generated Posts'>
      <status :fetching='fetching' :data='messageData'></status>
    </div>
  </form>
</script>