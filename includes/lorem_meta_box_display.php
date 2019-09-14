<div>
  <table class='form-table'>
    <tr scope='col'>
      <?php if ($have_lorem_shortcut): ?>
      <td>
        <input type="checkbox" name="eb_remove_lorem" id='eb_remove_lorem'>
      </td>
      <td>
        <label for='eb_remove_lorem' style='color:tomato'>Remove Lorem from post</label>
      </td>
      <?php else: ?>
      <td>
        <input type="checkbox" name="eb_add_lorem" id='eb_add_lorem'>
      </td>
      <td>
        <label for='eb_add_lorem' style='color:green'>Add Lorem to post</label>
      </td>
      <?php endif; ?>
    </tr>
  </table>
  <div>
    <table>
      <tr scope='col'>
        <td>p</td>
        <td>number of paragraphs</td>
      </tr>
      <tr scope='col'>
        <td>pmin</td>
        <td>minimum paragraph length(words)</td>
      </tr>
      <tr scope='col'>
        <td>pmax</td>
        <td>maximum paragraph length(words)</td>
      </tr>
      <tr scope='col'>
        <td>smin</td>
        <td>minimum sentence length(words)</td>
      </tr>
      <tr scope='col'>
        <td>smax</td>
        <td>maximum sentence length(words)</td>
      </tr>
    </table>
  </div>
</div>