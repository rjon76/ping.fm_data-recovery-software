<?php

namespace Lozad;

use Lozad\Classes\Settings\SettingsConstants;

class FrontPageProcessing
{
    public static function start()
    {
        (new self())->handle();
    }

    private function handle()
    {

        add_filter('wp_img_tag_add_decoding_attr', function () {
            return null;
        });

        add_filter('wp_get_attachment_image_attributes', function ($attr) {
            unset($attr['decoding']);
            return $attr;
        });

        add_filter('wp_lazy_loading_enabled', function () {
            return false;
        });

        add_filter('get_avatar', function ($avatar) {
            foreach (['async', 'sync', 'auto'] as $decoding) {
                $avatar = str_replace([' decoding="' . $decoding . '"', " decoding='$decoding'"], '', $avatar);
            }
            return $avatar;
        });

        add_action('wp_enqueue_scripts', function () {
            $pluginDirUrl = plugin_dir_url(__DIR__);
            $includeInFooter = (bool) $this->getPluginOptionsValue(SettingsConstants::SETTING_WHERE_INCLUDE_SCRIPT);

            wp_register_script('lozad_script', "{$pluginDirUrl}js/lozad.js", [], false, $includeInFooter);
            wp_enqueue_script('lozad_script');
            wp_register_script('lozad_init_script', "{$pluginDirUrl}js/initLozad.js", ['lozad_script'], false, $includeInFooter);
            wp_enqueue_script('lozad_init_script');
        });

        add_action('plugins_loaded', function () {
            if (is_admin()) {
                return true;
            }

            $hookLevel = (int) $this->getPluginOptionsValue(SettingsConstants::SETTING_SCRIPT_HOOK_LEVEL);
            if ($hookLevel < 10) {
                $hookLevel = 10;
            }

            global $cache_enabled;
            if (has_filter('final_output')) {
                add_filter('final_output', [$this, 'lozadContentProcessing'], $hookLevel);
            } elseif (
                $cache_enabled
                && has_filter('wp_cache_ob_callback_filter')
                && function_exists('wp_cache_user_agent_is_rejected') && !wp_cache_user_agent_is_rejected()
                && function_exists('wpsc_is_backend') && !wpsc_is_backend()
            ) {
                add_filter('wp_cache_ob_callback_filter', [$this, 'lozadContentProcessing'], $hookLevel);
            } else {
                add_action('template_redirect', function () {
                    ob_start(function ($buffer) {
                        return $this->lozadContentProcessing($buffer);
                    });
                }, $hookLevel);
            }
            return true;
        }) ;
    }

    public function lozadContentProcessing($content)
    {
        return (new ContentProcessing($content))->handle();
    }

    private function getPluginOptionsValue($option)
    {
        $val = get_option(SettingsConstants::PAGE_SETTING_OPTION_NAME);
        return isset($val[$option]) ? $val[$option] : null;
    }
}