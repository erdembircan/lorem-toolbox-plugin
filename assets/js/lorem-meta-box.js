(function noConflict($) {
  $('#legend_table').hide();
  $('#eb_toggle_button').click(function t() {
    $('#legend_table').toggle('fast');
    $(this).attr('aria-expanded', function toggle(i, val) {
      return val !== 'true';
    });
  });
})(jQuery);
