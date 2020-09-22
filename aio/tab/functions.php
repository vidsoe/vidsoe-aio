<?php

if(!function_exists('v_tab')){
    function v_tab($settings_page = '', $tab = ''){
        return new _V_Tab($settings_page, $tab);
    }
}
