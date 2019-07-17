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
      'page_title'=> 'Lorem Plugin Settings',
      /**
       * default options
       */
      'internal'=> array(
        'lorem_raw'=>'Lorem ipsum dolor sit amet consectetur adipisicing elit. Perferendis molestias ipsa modi nihil? Ad mollitia vero rem fugit culpa dolorem, sint ipsa impedit natus provident dolores molestiae itaque dignissimos totam.',
        'shortcode_default_paragraph_length'=>3,
        'shortcode_default_min_word_length'=> 50,
        'shortcode_default_max_word_length'=> 100,
        'shortcode_default_min_sentence'=> 5,
        'shortcode_default_max_sentence'=> 10,
        )
      );
    
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
        $this->args = \array_replace_recursive($this->_defaults, $supplied_args);

        $this->_setUp();

        \register_activation_hook($this->_getArg('file'), array($this, 'activation_hook'));
        \register_deactivation_hook($this->_getArg('file'), array($this, 'deactivation_hook'));

        \add_action('admin_init', array($this, 'admin_init'));
        \add_action('admin_menu', array($this, 'admin_menu'));

        \add_action('init', array($this, 'register_shortcode'));
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

    /**
     * shortcode working logic
     *
     * @param mixed $atts user requested shortcode attributes
     * @return mixed generated content
     */
    public function shortcode_logic($atts)
    {
        $default_atts = array(
        'p'=> $this->_get_options('shortcode_default_paragraph_length')
      );
        $parsed_atts = \shortcode_atts($default_atts, $atts);
        
        $content = $this->_generate_lorem(absint($parsed_atts["p"]), 50, 100, 5, 10);

        return $content;
    }

    private function _generate_lorem($p=1, $minW=50, $maxW=100, $minSL=5, $maxSL=9)
    {
        $lorem = $this->_get_options('lorem_raw');
        $words = array_map('\strtolower', \preg_split('/\W/', $lorem));

        $generated = '';
        for ($i=0; $i < $p; $i++) {
            $wordCount = rand($minW, $maxW);
            $paragraph= '';

            for ($x=0; $x <$wordCount ; $x++) {
                $remainingWords = $wordCount -$x;
                $rW = rand($minSL, $maxSL);
                $minSentenceLength = $rW >= $remainingWords? $remainingWords: $rW;

                $sentence= '';
                for ($y=0; $y < $minSentenceLength; $y++) {
                    $sentence .= $words[rand(0, sizeof($words)-1)]. ' ';
                }
                $sentence = \ucfirst(\trim($sentence) . '. ');
                $paragraph .= $sentence;

                $x += $minSentenceLength-1;
            }
            $generated .= "<p>$paragraph</p>";
        }

        return $generated;
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

        $options['internal'] = $this->_getArg('internal');
        \update_option($options_key, $options);
    }
}
