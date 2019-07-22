<?php
namespace erdembircan\lorem_plugin\traits;

/**
 * Hooks for varios WordPress actions
 */
trait ActionHooks
{
    /**
     * WordPress activation hook callback
     *
     * @return void
     */
    public function activation_hook()
    {
        $min_PHP_version = '5.4.0';
        if (!\version_compare($min_PHP_version, \phpversion(), '<')) {
            exit("Required PHP version is $min_PHP_version, please update your version to use this plugin");
        }
        $options_key = $this->_getArg('options_key');
        $options = $this->_get_options();

        $options['internal'] = $this->_getArg('internal');
        \update_option($options_key, $options);
    }

    /**
     * WordPress deactivation hook callback
     *
     * @return void
     */
    public function deactivation_hook()
    {
        // TODO uncomment for production
        // $options = \get_option($this->_getArg('options_key'));
        // unset($options['lorem_raw']);
        // \update_option($this->_getArg('options_key'), $options);

        // normally the solution above is the preferred one for production
        // but for development purposes, in order to easily delete options, will use the below approach

        // TODO comment for production
        \delete_option($this->_getArg('options_key'));
    }

    /**
     * WordPress admin init hook callback
     *
     * @return void
     */
    public function admin_init()
    {
        $options_key = $this->_getArg('options_key');
        \register_setting($options_key, $options_key, array($this, 'sanitize_form'));
    }

    /**
     * WordPress admin menu hook callback
     *
     * @return void
     */
    public function admin_menu()
    {
        ($this->args)['page_hook_suffix'] =  \add_options_page($this->_getArg('page_title'), $this->_getArg('page_title'), 'manage_options', $this->_getArg('prefix') . 'slug', array($this, 'options_page'));
    }

    /**
     * WordPress shortcode hook callback
     *
     * @return void
     */
    public function register_shortcode()
    {
        \add_shortcode('lorem', array($this, 'shortcode_logic'));
    }

    public function admin_scripts($hook)
    {
        if ($hook == $this->_getArg('page_hook_suffix')) {
            \wp_register_script('vuejs', "https://cdn.jsdelivr.net/npm/vue");

            \wp_enqueue_script('lorem-settings', plugin_dir_url($this->_getArg('file')). 'assets/js/lorem-settings.js', array('vuejs'), false, true);

            $options_key = $this->_getArg('options_key');
            $settings = array(
              data => array(
              page_title => $this->_getArg('page_title'),
              inputs => array(
                  ['name' => $options_key . '[use_custom]' ,  'element'=>'input', 'title' => 'use custom phrases' , 'domProps'=> ['type'=>'checkbox', 'checked'=> $options['use_custom']]],
                  ['name' => $options_key . '[shortcode_default_paragraph_length]' ,  'element'=>'input', 'title' => 'default number of paragraphs' , 'domProps'=> ['type'=>'number', 'min'=>'1', 'max'=>'100', 'value'=> $this->_get_options('shortcode_default_paragraph_length')]],
                  ['name' => $options_key . '[lorem_raw]' ,  'element'=>'textarea', 'title' => 'custom phrases' , 'domProps'=> ['rows'=>'5', 'cols'=>'100', 'innerHTML'=> $this->_get_options('lorem_raw')]],
                )
              )
            );
            wp_localize_script('lorem-settings', 'loremSettings', $settings);
        }
    }
}
