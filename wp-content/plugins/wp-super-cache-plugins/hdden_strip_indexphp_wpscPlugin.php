<?php
/**
 * Plugin Name: WP Super Cache - index.php redirect fix
 * Version:     1.2.1
 * Plugin URI:  https://github.com/HDDen/WP_indexphp_redir
 * Description: Убираем index.php из url. Оформлено в виде плагина для WP Super Cache, т.к. без поздней инициализации MU-вариант не работает. MU нужен из-за срабатывания htaccess-правила на rest-запросы
 * Author:      HDDen
 * Author URI:  https://github.com/HDDen
 */

if (!function_exists('hdden_strip_indexphp')){
    function hdden_strip_indexphp(){
        $url = $_SERVER['REQUEST_URI'];

        $is_rest = function_exists('wp_is_rest_endpoint') ? wp_is_rest_endpoint() : false;
        $is_ajax = function_exists('wp_doing_ajax') ? wp_doing_ajax() : false;
        $has_indexphp = (mb_strpos($_SERVER['REQUEST_URI'], "index.php") !== false) ? true : false;
        $is_wp_path = (mb_strpos($_SERVER['REQUEST_URI'], "wp-") === false) ? false : true;
        $is_get = ($_SERVER['REQUEST_METHOD'] === 'GET') ? true : false;

        $remove_trailing_slash = true;

        if ( $is_get && !$is_rest && !$is_ajax && $has_indexphp && !$is_wp_path ){
            $target_url = $url;

            if ($remove_trailing_slash){
                $target_url = str_replace('index.php', '', $_SERVER['REQUEST_URI']); // remove index.php
                $target_url = trim($target_url, '/'); // trim slashes
                $target_url = str_replace('/?', '?', $target_url); // glue /? and ? versions
            } else {
                $target_url = str_replace('index.php', '', $_SERVER['REQUEST_URI']); // remove index.php
                $target_url = ltrim($target_url, '/'); // trim left slash
            }

            header('X-HDDEN-WPSCPlugin-INDEXPHPREDIR: active');
            if (function_exists('wp_redirect')){
                wp_redirect(home_url('/').$target_url, 301);
            } else {
                header("Location: /".$target_url,TRUE,301);
            }

            exit;
        }
    }
}
hdden_strip_indexphp();