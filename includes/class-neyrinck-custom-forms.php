<?php


class Neyrinck_Custom_Forms{

    protected $loader;

    protected $plugin_name;

    protected $version;

	
    public function __construct() {

        $this->plugin_name = 'neyrinck-custom-forms';
        $this->version = '1.0.0';

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    
    }


    private function define_admin_hooks() {
        $plugin_admin = new Neyrinck_Custom_Forms_Admin();
       $this->loader->add_action('admin_menu', $plugin_admin, 'load_actions');
    }

    private function define_public_hooks(){
        
    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

   
    public function get_loader() {
        return $this->loader;
    }

    public function get_version() {
        return $this->version;
    }

    public function run() {
        $this->loader->run();
    }


    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once NCF_PLUGIN_DIR . '/includes/class-neyrinck-custom-forms-loader.php';


        require_once NCF_PLUGIN_DIR . '/admin/class-neyrinck-custom-forms-admin.php';
        require_once NCF_PLUGIN_DIR . '/shortcode/shortcode.php';

        // javascripts
        add_action( 'wp_enqueue_scripts', array($this,'load_js') );
        // style-sheet
        add_action( 'wp_enqueue_scripts', array($this,'load_css') );

        $this->loader = new Neyrinck_Custom_Forms_Loader();


    }

    public function load_js(){
        wp_register_script('IC_form_download', plugins_url('../shortcode/scripts/download_form.js',__FILE__ ));
        wp_enqueue_script('IC_form_download');

        wp_register_script('IC_form_contactus', plugins_url('../shortcode/scripts/contactus_form.js',__FILE__ ));
        wp_enqueue_script('IC_form_contactus');

        wp_register_script('IC_form_support', plugins_url('../shortcode/scripts/support_form.js',__FILE__ ));
        wp_enqueue_script('IC_form_support');

        wp_register_script('IC_form_support', plugins_url('../shortcode/scripts/support_form.js',__FILE__ ));
        wp_enqueue_script('IC_form_support');

    }


    public function load_css(){
        // wp_register_style('IC_form_css', plugins_url('/css/style.css',__FILE__ ));
        // wp_enqueue_style('IC_form_css');
    }

}
?>