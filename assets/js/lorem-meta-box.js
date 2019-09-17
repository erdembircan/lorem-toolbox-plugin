(function noConflict($) {
  console.log('document loaded and ready to use.');
  $('#eb_toggle_button').click(function() {
    $('#legend_table').toggle('fast');
  });
})(jQuery);
