<div class="wrap">
  <table class='form-table'>
    <tr scope='col'>
      <?php if ($have_lorem_shortcut): ?>
      <td>
        <label for='eb_remove_lorem' style='color:tomato'>Remove Lorem from post</label>
      </td>
      <td>
        <input type="checkbox" name="eb_remove_lorem">
      </td>
      <?php else: ?>
      <td>
        <label for='eb_add_lorem' style='color:green'>Add Lorem to post</label>
      </td>
      <td>
        <input type="checkbox" name="eb_add_lorem">
      </td>
      <?php endif; ?>
    </tr>
  </table>
</div>