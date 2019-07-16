<?php
namespace erdembircan\lorem_plugin\construct;

/**
 * OOP Class for plugin
 */
class LoremClass
{
    /**
     * predefined defaults array
     *
     * @var array
     */
    private $_defaults = array(
      'prefix'=> 'eb_lorem_',
      'lorem_raw'=>'Lorem ipsum dolor sit amet consectetur adipisicing elit. Perferendis molestias ipsa modi nihil? Ad mollitia vero rem fugit culpa dolorem, sint ipsa impedit natus provident dolores molestiae itaque dignissimos totam.',
      'page_title'=> 'Lorem Plugin Settings',
      'shortcode_default_length'=>5);
    
    /**
     * array containing various class specifics options
     *
     * @var array
     */
    public $args = array();

    /**
     * Class Constructor
     *
     * @param array $supplied_args Array of arguments which will be merged with defaults
     */
    public function __construct($supplied_args = array())
    {
        $this->args = \wp_parse_args($supplied_args, $this->_defaults);

        $this->_setUp();

        \register_activation_hook($this->_getArg('file'), array($this, 'activation_hook'));
        \register_deactivation_hook($this->_getArg('file'), array($this, 'deactivation_hook'));

        \add_action('admin_init', array($this, 'admin_init'));
        \add_action('admin_menu', array($this, 'admin_menu'));
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
     * sanitization function for settings page
     *
     * will be using this callback to make sure internal options will persist
     *
     * @param array $input current option sent to options.php
     * @return array sanitized options
     */
    public function sanitize_form($input=array())
    {
        $options = $this->_get_options();
        $options = wp_parse_args($input, array('internal'=>$options['internal']));

        return $options;
    }

    /**
     * wrapper function for getting easy WordPress options for plugin
     *
     * if a key is supplied, key is searched wthin options array and then internals and found value is returned
     *
     * @param string $key key for options array
     * @return mixed options
     */
    private function _get_options($key=null)
    {
        $options_key = $this->_getArg('options_key');
        $options = \get_option($options_key, array());
        if (!isset($key)) {
            return $options;
        } else {
            return (isset($options[$key]))?$options[$key]:($options['internal'])[$key];
        }
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
     * settings page visual display callback
     *
     * @return void
     */
    public function options_page()
    {
        $settings_display_page = \plugin_dir_path($this->_getArg('file')) . 'includes/settings-page.php';

        $options_key = $this->_getArg('options_key');
        $options = \get_option($options_key);

        // output buffer start
        ob_start();
        require_once $settings_display_page;
        ob_end_flush();
        // output buffer end
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
     * Setup various properties for class
     *
     * @return void
     */
    private function _setUp()
    {
        ($this->args)['options_key'] = $this->_getArg('prefix') . '_options';
    }

    /**
     * Wrapper function for getting argument values
     *
     * @param string $key args key
     * @return mixed args value
     */
    private function _getArg($key)
    {
        return ($this->args)[$key];
    }

    /**
     * WordPress activation hook callback
     *
     * @return void
     */
    public function activation_hook()
    {
        $options_key = $this->_getArg('options_key');
        $options = $this->_get_options();

        $options['internal'] = array(
          'lorem_raw' => $this->_getArg('lorem_raw'),
          'shortcode_default_length'=>$this->_getArg('shortcode_default_length'));
        \update_option($options_key, $options);
    }
}
