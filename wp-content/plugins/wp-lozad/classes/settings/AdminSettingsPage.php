<?php


namespace Lozad\Settings;


use Lozad\Classes\Settings\SettingsConstants;

class AdminSettingsPage
{
    public static function start()
    {
        if (!is_admin()) {
            return false;
        }

        include_once(ABSPATH . 'wp-includes/pluggable.php');

        (new self())->handle();

        return true;
    }

    private function handle()
    {
        add_action('admin_menu', [$this, 'addAdminMenu']);

        add_action('admin_init', [$this, 'pluginSettings']);

        add_filter('plugin_action_links', [$this, 'lozadPluginActionLinks'], 10, 2);
    }

    public function addAdminMenu()
    {
        add_options_page('WP Lozad', 'WP Lozad', 'manage_options', SettingsConstants::PAGE_SLUG, [$this, 'pluginOptionsPageOutput']);
    }

    public function lozadPluginActionLinks($links, $file)
    {
        if (strpos($file, LOZAD_INDEX_FILE) === false) {
            return $links;
        }

        $pluginSettingsUrl = sprintf('options-general.php?page=%s&section=settings', SettingsConstants::PAGE_SLUG);
        $pluginSettingsLink = sprintf('<a href="%s">%s</a>', admin_url($pluginSettingsUrl), __( 'Settings', 'Settings'));

        array_unshift($links, $pluginSettingsLink);

        return $links;
    }

    public function pluginOptionsPageOutput()
    {
        ?>
        <div class="wrap">
            <h2><?php echo get_admin_page_title() ?></h2>

            <form method="POST" action="options.php">
                <?php
                settings_fields('option_group');
                do_settings_sections(SettingsConstants::PAGE_SETTING_SECTION_SLUG);
                submit_button(__('Save Changes'));
                ?>
            </form>
        </div>
        <?php
    }

    public function pluginSettings()
    {
        register_setting('option_group', SettingsConstants::PAGE_SETTING_OPTION_NAME, [$this, 'submitAction']);

        add_settings_section(SettingsConstants::PAGE_SETTING_ID, __('Base settings'), '', SettingsConstants::PAGE_SETTING_SECTION_SLUG);

        $options = SettingsConstants::getOptionsMapping();

        foreach ($options as $name => $optionConf) {
            add_settings_field(
                $name,
                $optionConf['label'],
                [$this, 'fillSettingField'],
                SettingsConstants::PAGE_SETTING_SECTION_SLUG,
                SettingsConstants::PAGE_SETTING_ID,
                compact('name', 'optionConf')
            );
        }
    }

    public function fillSettingField($args)
    {
        $optionName = $args['name'];
        $value = get_option(SettingsConstants::PAGE_SETTING_OPTION_NAME);
        $value = isset($value[$optionName]) ? $value[$optionName] : null;
        $optionConfig = $args['optionConf'];
        $formOptionName = SettingsConstants::PAGE_SETTING_OPTION_NAME . '[' . $optionName . ']';

        switch ($optionConfig['type']) {
            case SettingsConstants::OPTION_DISPLAY_TYPE_CHECKBOX: $this->renderCheckboxElement($formOptionName, $value); break;
            case SettingsConstants::OPTION_DISPLAY_TYPE_SELECT: $this->renderSelectElement($formOptionName, $value, $optionConfig['options']); break;
            case SettingsConstants::OPTION_DISPLAY_TYPE_INPUT: $this->renderInputElement($formOptionName, $value, $optionConfig['placeholder']); break;
        }
    }

    public function submitAction($options)
    {
        foreach ($options as $name => &$val) {
            if (in_array($name, [SettingsConstants::SETTING_SKIPPED_CLASSES_STRING, SettingsConstants::SETTING_SCRIPT_HOOK_LEVEL])) {
                $val = strip_tags($val);
                if ($name == SettingsConstants::SETTING_SCRIPT_HOOK_LEVEL && (int) $val < 10) {
                    $val = '';
                }
            } else {
                $val = intval($val);
            }
        }

        return $options;
    }

    private function renderCheckboxElement($optionName, $optionValue)
    {
        ?>
        <label>
            <input type="checkbox" name="<?php echo $optionName; ?>" value="1" <?php checked( 1, $optionValue ) ?> />
            <?php echo __('checked'); ?>
        </label>
        <?php
    }

    private function renderSelectElement($optionName, $optionValue, $options)
    {
        ?>
        <label>
            <select name="<?php echo $optionName;?>">
            <?php foreach ($options as $index => $option) : ?>
                <option value="<?php echo $index; ?>" <?php if ($index == $optionValue) {echo 'selected';}?>>
                    <?php echo $option?>
                </option>
            <?php endforeach; ?>
            </select>
        </label>
        <?php
    }


    private function renderInputElement($optionName, $optionValue, $placeholder)
    {
        ?>
        <label>
            <input type="text" name="<?php echo $optionName;?>" value="<?php echo esc_attr($optionValue) ?>" placeholder="<?php echo $placeholder?>"/>
        </label>
        <?php
    }
}