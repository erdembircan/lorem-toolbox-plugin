<?php
namespace erdembircan\lorem_plugin\construct;

require 'hooks.php';

/**
 * OOP Class for plugin
 */
class LoremClass
{
    use \erdembircan\lorem_plugin\traits\ActionHooks;
  
    /**
     * predefined defaults array
     *
     * @var array
     */
    private $_defaults = array(
      'prefix'=> 'eb_lorem_',
      'page_title'=> 'lorem plugin settings',
      /**
       * default options
       */
      'internal'=> array(
        'use_custom'=>'off',
        'lorem_raw'=>'Lorem ipsum dolor sit amet consectetur adipisicing elit. Perferendis molestias ipsa modi nihil? Ad mollitia vero rem fugit culpa dolorem, sint ipsa impedit natus provident dolores molestiae itaque dignissimos totam.',
        'shortcode_default_paragraph_length'=>3,
        'shortcode_default_min_paragraph_length'=> 50,
        'shortcode_default_max_paragraph_length'=> 100,
        'shortcode_default_min_sentence'=> 5,
        'shortcode_default_max_sentence'=> 10,
        )
      );

    private $_sanitization_options = array(
        'use_custom'=>'\sanitize_text_field',
        'lorem_raw'=>'\sanitize_textarea_field',
        'shortcode_default_paragraph_length'=>'\absint',
        'shortcode_default_min_paragraph_length'=> '\absint',
        'shortcode_default_max_paragraph_length'=> '\absint',
        'shortcode_default_min_sentence'=> '\absint',
        'shortcode_default_max_sentence'=>'\absint'
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

        \add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));

        \add_action('wp_ajax_eb_lorem_generate_posts', array($this, 'eb_lorem_generate_posts'));
        \add_action('wp_ajax_eb_lorem_delete_posts', array($this, 'eb_lorem_delete_posts'));

        \add_action('save_post', array($this, 'save_post'), 10, 2);
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
        'p'=> $this->_get_options('shortcode_default_paragraph_length'),
        'pmin'=> $this->_get_options('shortcode_default_min_paragraph_length'),
        'pmax'=> $this->_get_options('shortcode_default_max_paragraph_length'),
        'smin'=> $this->_get_options('shortcode_default_min_sentence'),
        'smax'=> $this->_get_options('shortcode_default_max_sentence'),
      );

        $parsed_atts = \shortcode_atts($default_atts, $atts);
        \extract($parsed_atts);
        
        $content = $this->_generate_lorem($p, $pmin, $pmax, $smin, $smax);

        array_walk($content, function (&$p) {
            $p = "<p>$p</p>";
        });

        $generated_content = \implode('', $content);

        return $generated_content;
    }

    /**
     * function responsible for generating random sentences based on plugin settings and supplied arguments
     *
     * @param integer $p number of paragraphs
     * @param integer $pMin min paragraph length in words
     * @param integer $pMax max paragraph length in words
     * @param integer $sMin min sentence length in words
     * @param integer $sMax max sentence length in words
     * @return string generated paragraphs
     */
    private function _generate_lorem($p=1, $pMin=50, $pMax=100, $sMin=5, $sMax=9)
    {
        $use_custom = $this->_get_options('use_custom');
        $lorem = $use_custom=='off'?($this->_get_options('internal'))['lorem_raw']:$this->_get_options('lorem_raw');
        $words = array_map('\strtolower', \preg_split('/\W/', $lorem));

        $generated = array();

        for ($i=0; $i < $p; $i++) {
            $wordCount = rand($pMin, $pMax);
            $paragraph= '';

            for ($x=0; $x <$wordCount ; $x++) {
                $remainingWords = $wordCount -$x;
                $rW = rand($sMin, $sMax);
                $minSentenceLength = $rW >= $remainingWords? $remainingWords: $rW;

                $sentence= '';
                for ($y=0; $y < $minSentenceLength; $y++) {
                    $sentence .= $words[rand(0, sizeof($words)-1)]. ' ';
                }
                $sentence = \ucfirst(\trim($sentence) . '. ');
                $paragraph .= $sentence;

                $x += $minSentenceLength-1;
            }
            $generated[]= $paragraph;
        }

        return $generated;
    }

    /**
     * sanitization function for settings page
     *
     * will also be responsible to make sure internal options will persist
     *
     * @param array $input current option sent to options.php
     * @return array sanitized options
     */
    public function sanitize_form($input=array())
    {
        $options = $this->_get_options();
        $input = $this->_sanitize_array($input);

        // persist internal options
        $options = wp_parse_args($input, array('internal'=>$options['internal']));

        return $options;
    }

    /**
     * input array sanitization
     *
     * will sanitize array fields based on the method provided on options
     * also will make sure no keys not defined in sanitization options merged into options database
     *
     * @param array $arr array to be sanitized
     * @return array sanitized array
     */
    private function _sanitize_array($arr)
    {
        $opt = $this->_sanitization_options;
        $tempArr = array();
        foreach ($opt as $key=>$method) {
            if (isset($arr[$key])) {
                $tempArr[$key]=\call_user_func($method, $arr[$key]);
            }
        }

        return $tempArr;
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
     * Setup various properties for class
     *
     * @return void
     */
    private function _setUp()
    {
        ($this->args)['options_key'] = $this->_getArg('prefix') . '_options';
        ($this->args)['meta_key'] ='_' . $this->_getArg('prefix') . '_meta';
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
     * insert new posts
     *
     * @param number $count number of posts to be generated
     * @return boolean operation success info
     */
    private function _insert_new_post($count)
    {
        for ($c=0; $c<$count; $c++) {
            $post_args = array(
            'post_title' => implode('', $this->_generate_lorem(1, 5, 9)),
            'post_status' => 'publish',
            'post_content' => '[lorem]',
            'meta_input' => [$this->_getArg('meta_key') => 'true']
            );
            if (!wp_insert_post($post_args)) {
                return false;
            }
        }
        return true;
    }

    /**
     * query the database for posts generated by plugin
     *
     * @return number count of generated posts
     */
    public function count_generated_posts()
    {
        $args = array(
          'meta_key'=>$this->_getArg('meta_key'),
          'meta_value'=>'true',
        );

        $generated_posts = new \WP_Query($args);
        \wp_reset_postdata();

        return $generated_posts->found_posts;
    }
}
