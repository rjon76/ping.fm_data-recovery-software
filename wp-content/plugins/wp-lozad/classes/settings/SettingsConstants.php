<?php

namespace Lozad\Classes\Settings;

class SettingsConstants
{
    const PAGE_SLUG = 'lozad-setting-page';
    const PAGE_SETTING_SECTION_SLUG = 'lazy_load_setting_page';
    const PAGE_SETTING_ID = 'lazy_load_settings';
    const PAGE_SETTING_OPTION_NAME = 'lazy_load_settings_options';

    const SETTING_ENABLE_LAZY_LOAD = 'enable_lazy_load';
    const SETTING_ENABLE_LAZY_LOAD_IMAGE = 'enable_lazy_load_image';
    const SETTING_ENABLE_IMAGE_TAG_REQUIRED_ATTR = 'enable_image_tag_required_attribute';
    const SETTING_ENABLE_LAZY_LOAD_BG = 'enable_lazy_load_background';
    const SETTING_ENABLE_LAZY_LOAD_BG_2X = 'enable_lazy_load_background_2x';
    const SETTING_ENABLE_LAZY_LOAD_IFRAME = 'enable_lazy_load_iframe';
    const SETTING_ENABLE_LAZY_LOAD_VIDEO = 'enable_lazy_load_video';
    const SETTING_WHERE_INCLUDE_SCRIPT = 'include_script_in_footer'; // 0 - header, 1 - footer
    const SETTING_SKIPPED_CLASSES_STRING = 'skipped_classes_string';
    const SETTING_ENABLE_LAZY_LOAD_IMAGE_SIZES = 'enable_lazy_load_image_sizes';
    const SETTING_SCRIPT_HOOK_LEVEL = 'lazy_load_hook_level'; // default -10

    const OPTION_DISPLAY_TYPE_CHECKBOX = 'checkbox';
    const OPTION_DISPLAY_TYPE_SELECT = 'select';
    const OPTION_DISPLAY_TYPE_INPUT = 'input';

    public static function getOptionsMapping()
    {
        $whereIncludeOption = self::getOptionConfigArr(__('Include script in'), self::OPTION_DISPLAY_TYPE_SELECT);
        $whereIncludeOption['options'] = ['Header', 'Footer'];

        $skippedClassesOption = self::getOptionConfigArr(__('Skipped classes separate with comma'), self::OPTION_DISPLAY_TYPE_INPUT);
        $skippedClassesOption['placeholder'] = 'Ex. class1, class2, class3';

        $hookLevelOption = self::getOptionConfigArr(__('Hook level'), self::OPTION_DISPLAY_TYPE_INPUT);
        $hookLevelOption['placeholder'] = 'Default and min 10';

        return [
            self::SETTING_ENABLE_LAZY_LOAD => self::getOptionConfigArr(__('Enable Lazy Load')),
            self::SETTING_ENABLE_LAZY_LOAD_IMAGE => self::getOptionConfigArr(__('Enable Lazy Load for Image')),
            self::SETTING_ENABLE_IMAGE_TAG_REQUIRED_ATTR => self::getOptionConfigArr(__('Add image required attribute (width, height)')),
            self::SETTING_ENABLE_LAZY_LOAD_IMAGE_SIZES => self::getOptionConfigArr(__('Enable Lazy Load for Image size attr')),
            self::SETTING_ENABLE_LAZY_LOAD_BG => self::getOptionConfigArr(__('Enable Lazy Load for Background')),
            self::SETTING_ENABLE_LAZY_LOAD_BG_2X => self::getOptionConfigArr(__('Enable Lazy Load for Background 2x')),
            self::SETTING_ENABLE_LAZY_LOAD_IFRAME => self::getOptionConfigArr(__('Enable Lazy Load for Iframe')),
            self::SETTING_ENABLE_LAZY_LOAD_VIDEO => self::getOptionConfigArr(__('Enable Lazy Load for Video')),
            self::SETTING_WHERE_INCLUDE_SCRIPT => $whereIncludeOption,
            self::SETTING_SKIPPED_CLASSES_STRING => $skippedClassesOption,
            self::SETTING_SCRIPT_HOOK_LEVEL => $hookLevelOption
        ];
    }

    private static function getOptionConfigArr($label, $optionDisplayType = self::OPTION_DISPLAY_TYPE_CHECKBOX)
    {
        return ['label' => $label, 'type' => $optionDisplayType];
    }
}