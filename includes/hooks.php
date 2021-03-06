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
        add_meta_box('eb_lorem_meta_box', '<span style="color:skyblue">Lorem Plugin</span>', array($this, 'lorem_meta_box_display'), 'post');
    }

    /**
     * checks the content of the post for any lorem shortcode
     *
     * @param string $content post content
     * @return boolean have lorem shortcode or not
     */
    public function have_lorem($content)
    {
        return filter_var(\preg_match('/.*(\[lorem(\s.+)?])/', $content), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * WordPress post meta box display
     *
     * @param object $post post object
     * @return void
     */
    public function lorem_meta_box_display($post)
    {
        $current_content = $post->post_content;
        $have_lorem_shortcut = $this->have_lorem($current_content);

        require(plugin_dir_path($this->_getArg('file')) . 'includes/lorem_meta_box_display.php');
    }

    /**
     * WordPress save post hook callback
     *
     * will be using this hook mainly for adding lorem functionality from meta-box
     *
     * @param number $post_id associated post id
     * @return void
     */
    public function save_post($post_id, $post)
    {
        if (! wp_is_post_revision($post_id)) {
            if (isset($_POST['eb_add_lorem']) && ($this->have_lorem($post->post_content)) == false) {
                remove_action('save_post', array($this, 'save_post'));
                $content_array = array('ID'=>$post_id, 'post_content' => $post->post_content . '[lorem]');
                wp_update_post($content_array);
                add_action('save_post', array($this, 'save_post'), 10, 2);
            } elseif (isset($_POST['eb_remove_lorem'])&& ($this->have_lorem($post->post_content))==true) {
                remove_action('save_post', array($this, 'save_post'));
                $replaced_content = \preg_replace('/(\[lorem(\s.+)?\])/', '', $post->post_content);
                $content_array = array('ID'=>$post_id, 'post_content' => $replaced_content);
                wp_update_post($content_array);
                add_action('save_post', array($this, 'save_post'), 10, 2);
            }
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
            $dir_url = plugin_dir_url($this->_getArg('file'));

            // vuejs registration
            \wp_register_script('vuejs', $dir_url . 'assets/libs/js/vue.js', array(), '2.6.10');

            // lorem-tween registration
            \wp_register_script('lorem-tween', $dir_url . 'assets/js/lorem-tween.js', array(), '1.0.0');

            // vue-resource registration
            \wp_register_script('vue-resource', $dir_url . 'assets/libs/js/vue-resource.js', array(), '1.5.1');

            $this->enqueue_file('assets/js/lorem-settings-components.js', array('handle' => 'lorem-settings-components', 'deps'=>['vuejs', 'vue-resource'], 'footer'=>true));

            $this->enqueue_file('assets/js/lorem-settings.js', array('handle'=>'lorem-settings', 'footer'=>true, 'deps' => ['vuejs','lorem-tween', 'vue-resource', 'lorem-settings-components']));

            $this->enqueue_file('assets/css/eb_lorem_style.css', array('handle'=>'lorem-settings-style'));

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
        } elseif ($hook=='post.php' || $hook =='post-new.php') {
            $this->enqueue_file('assets/js/lorem-meta-box.js', array('handle'=>'eb_lorem_metabox', 'footer'=>true, 'deps' => ['jquery']));

            $this->enqueue_file('assets/css/eb_meta_box_style.css', array('handle' =>'eb_lorem_metabox_style'));
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
