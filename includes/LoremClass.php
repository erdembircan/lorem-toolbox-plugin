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
    private $_defaults = array( 'prefix'=> 'eb_lorem_',
    'lorem_raw'=>'Lorem ipsum dolor sit amet consectetur adipisicing elit. Perferendis molestias ipsa modi nihil? Ad mollitia vero rem fugit culpa dolorem, sint ipsa impedit natus provident dolores molestiae itaque dignissimos totam.');
    
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
        $options = \get_option($this->_getArg('options_key'), array());
        $options['lorem_raw'] = $this->_getArg('lorem_raw');
        \update_option($this->_getArg('options_key'), $options);
    }
}
