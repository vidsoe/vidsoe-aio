<?php

if(!class_exists('_V_Tab')){
    class _V_Tab {

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        static protected $meta_boxes = [], $settings_pages = [], $tabs = [];

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        static protected function get_settings_page_id($settings_page = ''){
            $settings_page_id = '';
            if(is_string($settings_page)){
                $settings_page_id = 'vidsoe';
                if($settings_page != 'General'){
                    $settings_page_id .= '-' . sanitize_title(wp_strip_all_tags($settings_page));
                }
            } elseif(is_array($settings_page)){
                if(!empty($settings_page['id'])){
                    $settings_page_id = $settings_page['id'];
                }
            }
            return $settings_page_id;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        static protected function get_tab_id($tab = ''){
            return sanitize_title(self::get_tab_title($tab));
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        static protected function get_tab_title($tab = ''){
            $title = '';
            if(is_array($tab)){
                if(!empty($tab['label'])){
                    $tab = $tab['label'];
                } else {
                    $tab = '';
                }
            }
            if(is_string($tab)){
                $title = wp_strip_all_tags($tab);
            }
            return $title;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        static protected function maybe_add_settings_page($settings_page = ''){
            $settings_page_id = self::get_settings_page_id($settings_page);
            if($settings_page_id){
                if(!array_key_exists($settings_page_id, self::$settings_pages)){
                    if(is_string($settings_page)){
                        if($settings_page_id == 'vidsoe'){
                            self::$settings_pages[$settings_page_id] = [
                                'columns' => 1,
                                'icon_url' => 'data:image/svg+xml;base64,PHN2ZyBpZD0iTGF5ZXJfMSIgZGF0YS1uYW1lPSJMYXllciAxIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAzNTkuNiAzMjAiPjxkZWZzPjxzdHlsZT4uY2xzLTF7ZmlsbDojZmZmO308L3N0eWxlPjwvZGVmcz48cGF0aCBjbGFzcz0iY2xzLTEiIGQ9Ik0zODIuNDYsNTExLjMzYTMyLDMyLDAsMCwxLTQuMjcsMTZMMjMwLjQzLDc4My4yNWwwLC4wOGEzMiwzMiwwLDAsMS01NS40NCwwLC41Ni41NiwwLDAsMSwwLS4wOEwyNy4xNSw1MjcuMzNhMzIsMzIsMCwxLDEsNTUuNDEtMzJoMGwuNDQuNzVhLjgzLjgzLDAsMCwwLC4wNy4xM0wyMDIuNjYsNzAzLjM0LDMyMi4zMyw0OTYuMDhjLjEzLS4yNi4yOC0uNTEuNDMtLjc1YTMyLDMyLDAsMCwxLDU5LjcsMTZaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtMjIuODcgLTQ3OS4zMykiLz48L3N2Zz4=',
                                'id' => $settings_page_id,
                                'menu_title' => 'Vidsoe',
                                'option_name' => str_replace('-', '_', $settings_page_id),
                                'page_title' => 'General Settings',
                                'revision' => true,
                                'style' => 'no-boxes',
                                'submenu_title' => 'General',
                                'submit_button' => 'Save General Settings',
                                'tabs' => [],
                                'tab_style' => 'left',
                            ];
                        } else {
                            self::$settings_pages[$settings_page_id] = [
                                'columns' => 1,
                                'id' => $settings_page_id,
                                'menu_title' => $settings_page,
                                'option_name' => str_replace('-', '_', $settings_page_id),
                                'page_title' => $settings_page . ' Settings',
                                'parent' => 'vidsoe',
                                'revision' => true,
                                'style' => 'no-boxes',
                                'submit_button' => 'Save ' . $settings_page . ' Settings',
                                'tabs' => [],
                                'tab_style' => 'left',
                            ];
                        }
                    } elseif(is_array($settings_page)){
                        self::$settings_pages[$settings_page_id] = $settings_page;
                    }
                    ksort(self::$settings_pages);
                }
            }
            return $settings_page_id;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        static protected function maybe_add_tab($settings_page_id = '', $tab = ''){
            if(!array_key_exists($settings_page_id, self::$tabs)){
                self::$tabs[$settings_page_id] = [];
            }
            if(!array_key_exists($settings_page_id, self::$meta_boxes)){
                self::$meta_boxes[$settings_page_id] = [];
            }
            $tab_id = self::get_tab_id($tab);
            if($tab_id){
                if(!array_key_exists($tab_id, self::$tabs[$settings_page_id])){
                    self::$tabs[$settings_page_id][$tab_id] = $tab;
                    ksort(self::$tabs[$settings_page_id]);
                }
                if(!array_key_exists($tab_id, self::$meta_boxes[$settings_page_id])){
                    self::$meta_boxes[$settings_page_id][$tab_id] = [
                        'fields' => [],
                        'id' => $settings_page_id . '-' . $tab_id,
                        'settings_pages' => $settings_page_id,
                        'tab' => $tab_id,
                        'title' => self::get_tab_title($tab),
                    ];
                }
            }
            return $tab_id;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        static protected function maybe_add_field($settings_page_id = '', $tab_id = '', $args = []){
            if(empty($args['columns'])){
                $args['columns'] = 12;
            }
            if(array_key_exists($settings_page_id, self::$meta_boxes)){
               if(array_key_exists($tab_id, self::$meta_boxes[$settings_page_id])){
                    self::$meta_boxes[$settings_page_id][$tab_id]['fields'][] = $args;
                }
            }
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        static public function load(){
            add_action('mb_settings_pages', [__CLASS__, 'mb_settings_pages']);
            add_action('rwmb_meta_boxes', [__CLASS__, 'rwmb_meta_boxes']);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        static public function mb_settings_pages($settings_pages){
            if(self::$settings_pages){
                $general_id = 'vidsoe';
                if(array_key_exists($general_id, self::$settings_pages)){
                    $general = self::$settings_pages[$general_id];
                    unset(self::$settings_pages[$general_id]);
                    self::$settings_pages = array_merge([
                        $general_id => $general,
                    ], self::$settings_pages);
                }
                foreach(self::$settings_pages as $settings_page_id => $settings_page){
                    $empty = true;
                    if(array_key_exists($settings_page_id, self::$meta_boxes)){
                        foreach(self::$meta_boxes[$settings_page_id] as $meta_box){
                            if(!empty($meta_box['fields'])){
                                $empty = false;
                                break;
                            }
                        }
                    }
                    if(!$empty){
                        $tabs = self::$tabs[$settings_page_id];
                        $general_id = sanitize_title('General');
                        if(!empty($tabs[$general_id])){
                            $general = $tabs[$general_id];
                            unset($tabs[$general_id]);
                            $tabs = array_merge([
                                $general_id => $general,
                            ], $tabs);
                        }
                        foreach($tabs as $tab_id => $tab){
                            if(empty(self::$meta_boxes[$settings_page_id][$tab_id]['fields'])){
                                unset($tabs[$tab_id]);
                            }
                        }
                        $settings_page['tabs'] = $tabs;
                        $settings_pages[] = $settings_page;
                    }
                }
            }
            return $settings_pages;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        static public function rwmb_meta_boxes($meta_boxes){
            if(is_admin()){
                if(self::$meta_boxes){
                    foreach(self::$meta_boxes as $tmp){
                        if($tmp){
                            foreach($tmp as $meta_box){
                                if(!empty($meta_box['fields'])){
                                    $meta_boxes[] = $meta_box;
                                }
                            }
                        }
                    }
                }
            }
            return $meta_boxes;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        protected $debug_backtrace = [], $settings_page_id = '', $tab_id = '';

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function __construct($settings_page = 'General', $tab = 'General'){
            if(!$settings_page){
                $settings_page = 'General';
            }
            if(!$tab){
                $tab = 'General';
            }
            $this->debug_backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
            $this->settings_page_id = self::maybe_add_settings_page($settings_page);
            $this->tab_id = self::maybe_add_tab($this->settings_page_id, $tab);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function __get($name = ''){
            return $this->get_option($name);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function add_field($id = '', $args = []){
            if(!$id){
                $id = uniqid();
            }
            if(empty($args['name'])){
                $args['name'] = '';
            }
            $args['id'] = $this->tab_id . '_' . $id;
            return self::maybe_add_field($this->settings_page_id, $this->tab_id, $args);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function admin_url(){
            return admin_url('admin.php?page=' . $this->settings_page_id . '#tab-' . $this->tab_id);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function current_dir_path(){
            if(!empty($this->debug_backtrace[0]['file'])){
                return plugin_dir_path($this->debug_backtrace[0]['file']);
            }
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function get_option($option = '', $default = false){
            $option = $this->tab_id . '_' . $option;
            $options = get_option(str_replace('-', '_', $this->settings_page_id));
            if(isset($options[$option])){
                return $options[$option];
            }
            return $default;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public function is_current_screen(){
            if(is_admin()){
                $current_screen = get_current_screen();
                if($current_screen){
                    if($this->settings_page_id == 'vidsoe'){
                        if($current_screen->id == 'toplevel_page_' . 'vidsoe'){
                            return true;
                        }
                    } else {
                        if($current_screen->id == 'vidsoe_page_' . $this->settings_page_id){
                            return true;
                        }
                    }
                }
            }
            return false;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    }
    _V_Tab::load();
}
