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

    /**
     * Frontend scripts, styles and data
     *
     * @param string $hook slug value for current page
     * @return void
     */
    public function admin_scripts($hook)
    {
        if ($hook == $this->_getArg('page_hook_suffix')) {
            \wp_register_script('vuejs', "https://unpkg.com/vue");
            \wp_register_script('tweenmax', "https://cdnjs.cloudflare.com/ajax/libs/gsap/1.20.3/TweenMax.min.js");
            \wp_register_script('vue-resource', "https://cdn.jsdelivr.net/npm/vue-resource");

            $resource_path = 'assets/js/lorem-settings-components.js';
            $resource_version = filemtime(\plugin_dir_path($this->_getArg('file')) . $resource_path);
            \wp_register_script('lorem-settings-components', plugin_dir_url($this->_getArg('file')). $resource_path, array('vuejs', 'vue-resource'), $resource_version, true);

            $main_js_path = 'assets/js/lorem-settings.js';
            $main_js_version = filemtime(\plugin_dir_path($this->_getArg('file')) . $main_js_path);
            \wp_enqueue_script('lorem-settings', plugin_dir_url($this->_getArg('file')). $main_js_path, array('vuejs','tweenmax','vue-resource','lorem-settings-components'), $main_js_version, true);
            \wp_enqueue_style('lorem-settings-style', plugin_dir_url($this->_getArg('file')). 'assets/css/eb_lorem_style.css');

            $options_key = $this->_getArg('options_key');

            $protocol = isset($_SERVER['https'])? 'https':'http';
            $ajax_url = \admin_url('admin-ajax.php', $protocol);

            $settings = array(
              'data' => array(
                'post_count' => $this-> count_generated_posts(),
                'page_title' => $this->_getArg('page_title'),
                'ajax_url'=>$ajax_url,
                'ajax_action_generate' =>'eb_lorem_generate_posts',
                'ajax_action_delete' =>'eb_lorem_delete_posts',
                'nonce' => wp_create_nonce('eb_lorem_generate_posts'),
                'dev_data'=> ['name'=>'Erdem Bircan', 'email'=>'erdem.erdembircan@gmail.com',
                'version'=> (\get_plugin_data($this->_getArg('file')))['Version'],
                'github'=>'https://github.com/erdembircan'
              ],
                'inputs' => array(
                    [   'title' => 'use custom phrases' ,'elements'=>[['element'=>'input', 'domProps'=> ['name' => $options_key . '[use_custom]' ,'type'=>'checkbox', 'checked'=> $this->_get_options('use_custom') == 'on' ? 'checked':'']]]],
                    ['title' => 'default number of paragraphs', 'elements'=>[['element'=>'input', 'domProps'=> [ 'name' => $options_key . '[shortcode_default_paragraph_length]' ,'type'=>'number', 'value'=> $this->_get_options('shortcode_default_paragraph_length'),'min'=>1, 'max'=>100]]]],
                    [   'title' => 'default paragraph length (in words)' ,'elements'=>[
                      ['element'=>'span', 'domProps'=>['innerHTML'=>'Min:']],
                      ['element'=>'input', 'domProps'=> ['name' => $options_key . '[shortcode_default_min_paragraph_length]' ,'type'=>'number', 'value'=> $this->_get_options('shortcode_default_min_paragraph_length'),'min'=>1, 'max'=>100]],
                      ['element'=>'span', 'domProps'=>['innerHTML'=>'Max:']],
                      ['element'=>'input', 'domProps'=> ['name' => $options_key . '[shortcode_default_max_paragraph_length]' ,'type'=>'number', 'value'=> $this->_get_options('shortcode_default_max_paragraph_length'),'min'=>1, 'max'=>100]]
                      ]],
                    [   'title' => 'default sentence length (in words)' ,'elements'=>[
                      ['element'=>'span', 'domProps'=>['innerHTML'=>'Min:']],
                      ['element'=>'input', 'domProps'=> ['name' => $options_key . '[shortcode_default_min_sentence]' ,'type'=>'number', 'value'=> $this->_get_options('shortcode_default_min_sentence'),'min'=>1, 'max'=>100]],
                      ['element'=>'span', 'domProps'=>['innerHTML'=>'Max:']],
                      ['element'=>'input', 'domProps'=> ['name' => $options_key . '[shortcode_default_max_sentence]' ,'type'=>'number', 'value'=> $this->_get_options('shortcode_default_max_sentence'),'min'=>1, 'max'=>100]]
                      ]],
                    [   'title' => 'custom phrases' ,'elements'=>[
                      ['element'=>'textarea', 'domProps'=> ['name' => $options_key . '[lorem_raw]' ,'innerHTML'=> $this->_get_options('lorem_raw'), 'rows'=>5, 'cols'=>'100']],
                      ]],
                    )
                )
            );
            wp_localize_script('lorem-settings', 'loremSettings', $settings);
        }
    }

    /**
     * ajax endpoint for generating posts based on the requested count
     *
     * @return string response data
     */
    public function eb_lorem_generate_posts()
    {
        if (isset($_POST['post_count'])&&current_user_can('manage_options') && \check_ajax_referer('eb_lorem_generate_posts', 'nonce', false)&& $this->_insert_new_post(absint($_POST['post_count']))) {
            $resp = ['data'=> ['totalCount'=> $this->count_generated_posts()]];
            echo \json_encode($resp);
        } else {
            $resp = ['error'=>'an error occured, please try again later'];
            echo \json_encode($resp);
        }
        die();
    }

    /**
     * ajax endpoint for deleting all generated posts
     *
     * will be identifying the generated ones with post meta data
     *
     * @return string response data
     */
    public function eb_lorem_delete_posts()
    {
        if (!current_user_can('manage_options') || !\check_ajax_referer('eb_lorem_generate_posts', 'nonce', false)) {
            $resp = ['error' => 'unauthorized'];
            echo json_encode($resp);
            die();
        }
        $args = array(
          'meta_key'=>$this->_getArg('meta_key'),
          'meta_value'=> 'true',
          'posts_per_page'=>-1
        );

        $generated = new \WP_Query($args);

        if ($generated->have_posts()): while ($generated->have_posts()):$generated->the_post();
        $id = \get_the_ID();
        \wp_delete_post($id);
        endwhile;
        endif;
        \wp_reset_postdata();
        $resp = ['data'=> ['totalCount'=> $this->count_generated_posts()]];
        echo \json_encode($resp);
        die();
    }
}
