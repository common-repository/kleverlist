<?php
class Kleverlist
{
    
    protected $loader;

    protected $plugin_name;
    protected $version;

    public function __construct()
    {
        if (defined('KLEVERLIST_VERSION')) {
            $this->version = KLEVERLIST_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'kleverlist';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();

        // Used with WooCommerce
        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            $this->define_wc_hooks(); // Sendy
            $this->define_mailchimp_wc_hooks(); // Mailchimp
            $this->define_kleverlist_aweber(); // AWeber
        }

        $this->define_kleverlist_admin_ajax_hooks();
        $this->define_public_hooks();
    }

    private function load_dependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-kleverlist-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-kleverlist-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-kleverlist-admin.php';

        /**
         * The class responsible for defining wc product option.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-kleverlist-wc.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-kleverlist-mailchimp-wc.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-kleverlist-aweber.php';

        /**
         * The class responsible for defining admin ajax.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-kleverlist-ajax.php';

        /**
         * The class responsible for defining AWeber admin ajax.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-kleverlist-aweber-ajax.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-kleverlist-public.php';

        $this->loader = new Kleverlist_Loader();
    }

    private function set_locale()
    {

        $plugin_i18n = new Kleverlist_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the WC Sendy integration admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_wc_hooks()
    {

        $plugin_admin = new Kleverlist_WC($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'wc_enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'wc_enqueue_scripts');
    }
    
    /**
     * Register all of the hooks related to the WC Mailchimp integration admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_mailchimp_wc_hooks()
    {

        $plugin_admin = new Kleverlist_Mailchimp_WC($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'mailchimp_wc_enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'mailchimp_wc_enqueue_scripts');
    }

    /**
     * Register all of the hooks related to the WC AWeber integration admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_kleverlist_aweber()
    {
        $plugin_admin = new Kleverlist_AWeber($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'aweber_enqueue_scripts');
        
        $plugin_admin = new Kleverlist_AWeber_Ajax($this->get_plugin_name(), $this->get_version());
    }

    /**
     * Register all of the hooks related to the admin ajax area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_kleverlist_admin_ajax_hooks()
    {
        $plugin_admin = new Kleverlist_Ajax($this->get_plugin_name(), $this->get_version());
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {

        $plugin_admin = new Kleverlist_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {

        $plugin_public = new Kleverlist_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Kleverlist_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }
}
