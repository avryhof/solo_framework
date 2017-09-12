<?php
/*
 * The Solo Class fills in various pieces needed to tie things together.
 */

class Solo {
    var $base_dir = '';
    var $global_context = [];
    var $app_dirs = [];

    var $templates = [];
    var $template_dirs = [];

    function __construct() {
        $this->base_dir = constant('BASE_DIR');

        $this->templates = constant('TEMPLATES');
        $this->template_dirs = $this->templates['DIRS'];

        $load_vars = ['APP_NAME', 'SITE_NAME', 'SITE_URL', 'LANGUAGE_CODE'];

        foreach ($load_vars as $load_var) {
            if (defined($load_var)) {
                $var_key = strtolower($load_var);
                $this->global_context[$var_key] = constant($load_var);
            }
        }
    }

    function find_app_dirs() {
        if (defined('INSTALLED_APPS')) {
            foreach (constant('INSTALLED_APPS') as $installed_app) {
                $app_dir = $this->base_dir . DIRECTORY_SEPARATOR . $installed_app . DIRECTORY_SEPARATOR;
                $this->app_dirs[] = $app_dir;
            }
        }
    }

    function find_template_dirs() {
        foreach (constant('INSTALLED_APPS') as $installed_app) {
            $app_dir = $this->base_dir . DIRECTORY_SEPARATOR . $installed_app . DIRECTORY_SEPARATOR;
            if ($this->templates['APP_DIRS'] && is_dir($app_dir . 'templates')) {
                array_push($this->template_dirs, $app_dir . 'templates');
            }
        }
    }

    function get_global_context() {
        return $this->global_context;
    }
}