<div id='lorem_app' class='wrap'>
  <h2>{{page_title | capAF}}</h2>
  <settings-table :settings='inputs'></settings-table>
  <br />
  <hr>
  <h2>Generate Posts <lorem-tip>default settings will be used to generate posts</lorem-tip>
  </h2>
  <p>There are <b>{{animatedCount}}</b> generated posts</p>
  <generate-posts :ajaxurl='ajax_url' :ajaxactiongenerate='ajax_action_generate' :ajaxactiondelete='ajax_action_delete'
    :nonce='nonce' @fetched='updatePostCount'></generate-posts>
  <br />
  <hr>
  <dev-info :devdata='dev_data'></dev-info>
</div>

<!-- lorem-status template -->
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
    <h2>Default Settings <lorem-tip>you can override default settings with shortcode attributes</lorem-tip></h2>
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

<!-- dev-info template -->
<script type='x-template' id='devInfo'>
  <div class='eb-lorem-info-container'>
    <div>
    <i>
      {{devdata.name}} @ 2019 - v{{devdata.version}}
    </i>
    </div>
    <div class='items'>
      <a :href="devdata.github" target='_blank'>🌐</a>
      <a :href="`mailto:${devdata.email}`">✉️</a>
    </div>
  </div>
</script>

<!-- lorem-tip template -->
<script type='x-template' id='loremTip'>
  <span class='eb-lorem-tip' :title='getSlot'>💡</span>
</script>