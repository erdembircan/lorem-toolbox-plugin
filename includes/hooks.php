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
}
