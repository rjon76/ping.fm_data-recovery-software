<?php

namespace Lozad;

use DOMDocument;
use DOMXPath;
use Lozad\Classes\Settings\SettingsConstants;

class ContentProcessing
{
    const LOZAD_BASE_CLASS = 'lazyload';
    const EMPTY_IMAGE = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAABLAAAAKkAQMAAAAqeFQ7AAAAA3NCSVQICAjb4U/gAAAABlBMVEX///////9VfPVsAAAAAnRSTlMA/1uRIrUAAAAJcEhZcwAACxIAAAsSAdLdfvwAAAAcdEVYdFNvZnR3YXJlAEFkb2JlIEZpcmV3b3JrcyBDUzbovLKMAAAAFnRFWHRDcmVhdGlvbiBUaW1lADAxLzI5LzE536V52wAAAHpJREFUeJztwQENAAAAwqD3T20ON6AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAIAHA47LAAHp9LjEAAAAAElFTkSuQmCC';

    private $pluginOptions;
    private $content;

    private $search = [];
    private $replace = [];

    public function __construct($content)
    {
        $this->content = $content;
        $this->pluginOptions = get_option(SettingsConstants::PAGE_SETTING_OPTION_NAME);
        libxml_use_internal_errors(true);
    }

    public function handle()
    {
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 'xmlhttprequest' == strtolower( $_SERVER['HTTP_X_REQUESTED_WITH']);

        if (!get_the_ID() || $isAjax || !$this->checkOption(SettingsConstants::SETTING_ENABLE_LAZY_LOAD)) {
            return $this->content;
        }

        $this->addLozadToImg();

        $this->addBGImgOriginal();

        $this->addLazyLoadVideoIFrameContent();

        $this->addLazyLoadVideoContent();

        $search = array_unique($this->search);
        $replace = array_unique($this->replace);

        return str_replace($search, $replace, $this->content);
    }

    private function addLozadToImg()
    {
        if (!$this->checkOption(SettingsConstants::SETTING_ENABLE_LAZY_LOAD_IMAGE)) {
            return false;
        }

        $contentImages = [];

        preg_match_all('/<img[^>]*(?:(?:src=\"[^\"\']*\")|(?:src=\'[^\'\"]*\'))[^>]*>/im', $this->content, $contentImages);

        $skipImagesRegex = $this->getSkipImagesRegexLozad();

        $imageTagRequiredBaseAttribute = $this->checkOption(SettingsConstants::SETTING_ENABLE_IMAGE_TAG_REQUIRED_ATTR);

        foreach ($contentImages[0] as $imgHTML) {
            if (!preg_match($skipImagesRegex, $imgHTML)) {

                $doc = new DOMDocument();
                $doc->loadHTML($imgHTML);
                $xpath = new DOMXPath($doc);
                $imgSrc = $xpath->evaluate("string(//img/@src)");
                $imgSrcSet = $xpath->evaluate("string(//img/@srcset)");
                $sizes = $xpath->evaluate("string(//img/@sizes)");
                $dataHeight = $xpath->evaluate("string(//img/@data-height)");

                if (!empty($imgSrcSet)) {
                    $imgSrcSet = preg_replace('/\s{2,}/',' ', $imgSrcSet);
                    $imgSrcSet = trim($imgSrcSet);
                    $replaceHTML = preg_replace( '/<img(.*?)srcset\s*=\s*[\'"]([^\'"]*)[\'"](.*?)/is', '<img$1srcset="' . self::EMPTY_IMAGE . '"$3', $imgHTML);
                } else {
                    $imgSrcSet = $imgSrc;
                    $replaceHTML = preg_replace( '/<img/is', '<img srcset="' . self::EMPTY_IMAGE . '"', $imgHTML);
                }

                if ($this->checkOption(SettingsConstants::SETTING_ENABLE_LAZY_LOAD_IMAGE_SIZES) && !empty($sizes)) {
                    $replaceHTML = preg_replace('/ sizes=/is', ' data-sizes=', $replaceHTML);
                }

                unset($doc);
                unset($xpath);

                $replaceHTML = preg_replace('/<img(.*?)src=/is', '<img $1src="' . $imgSrc . '" data-srcset="' . $imgSrcSet . '" data-lazy-type="image" data-original=', $replaceHTML);

                $classList = self::LOZAD_BASE_CLASS;

                foreach (['height' => (empty($dataHeight) ? 10 : $dataHeight), 'width' => 10, 'alt' => 'img'] as $imageBaseAttr => $imageBaseAttrValue) {
                    if ($imageBaseAttr != 'alt' && !$imageTagRequiredBaseAttribute) {
                        continue;
                    }
                    if (strripos($replaceHTML, " $imageBaseAttr=") === false) {
                        $replacement = sprintf('<img %s="%s"', $imageBaseAttr, $imageBaseAttrValue);
                        $replaceHTML = preg_replace('/<img/is', $replacement, $replaceHTML);
                        $classList .= sprintf(' lozad-%s', $imageBaseAttr);
                    }
                }

                // add the lazy class to the img element
                if (preg_match('/class=["\']/i', $replaceHTML)) {
                    $replaceHTML = preg_replace('/class=(["\'])(.*?)["\']/is', 'class=$1' . $classList . ' $2$1', $replaceHTML);
                } else {
                    $replaceHTML = preg_replace('/<img/is', '<img class="' . $classList . '"', $replaceHTML);
                }

            } else {
                $replaceHTML = $imgHTML;
            }

            $this->pushArrays($imgHTML, str_replace('<img ', '<img decoding="async" ', $replaceHTML));
        }

        return true;
    }

    private function addBGImgOriginal()
    {
        if (!$this->checkOption(SettingsConstants::SETTING_ENABLE_LAZY_LOAD_BG)) {
            return false;
        }

        $stylesBGAttributes = ['background-image', 'background'];

        foreach ($stylesBGAttributes as $attribute) {
            $tagsWithBgUrl = [];
            preg_match_all('/<[^>]*?style=[^>]*?' . $attribute . '\s*?:\s*?url\s*\([^>]+\)[^>]*?>/', $this->content, $tagsWithBgUrl);

            $skipTagClassesRegex = $this->getSkipImagesRegexLozad();

            foreach ($tagsWithBgUrl[0] as $htmlTag) {
                if (preg_match($skipTagClassesRegex, $htmlTag) !== 0) {
                    continue;
                }

                $bgImgMatches = [];
                preg_match('/' . $attribute . ':\s*url\s*\(\s*[\'"]?([^\'"]*)[\'"]?\)/im', $htmlTag, $bgImgMatches);

                $bgImg = isset($bgImgMatches[1]) ? $bgImgMatches[1] : null;
                if (empty($bgImg)) {
                    continue;
                }

                $bgImgSlashes = preg_replace(['/\//', '/\./'], ['\/', '\.'], $bgImg);

                // del bg image from style tag
                $replaceHtmlTag = preg_replace('/(.*?style=.*?)' . $attribute . ':\s*url\s*\(\s*[\'"]' . $bgImgSlashes . '[\'"]\s*\);*\s*(.*?)/is', '$1$2', $htmlTag);
                $replaceHtmlTag = preg_replace('/(.*?style=.*?)' . $attribute . ':\s*url\s*\(\s*' . $bgImgSlashes . '\s*\);*\s*(.*?)/is', '$1$2', $replaceHtmlTag);

                // add lazyload class
                $replaceHtmlTag = preg_replace('/(.*?)class=([\'"])(.*?)/is', '$1class=$2' . self::LOZAD_BASE_CLASS . ' $3', $replaceHtmlTag);

                $doc = new DOMDocument();
                $doc->loadHTML($replaceHtmlTag);
                $xpath = new DOMXPath($doc);
                $nodes = $xpath->evaluate('//*[@style]');
                foreach ($nodes as $node) {
                    if (strlen($node->getAttribute('style')) < 5) { // if empty style tag now delete style tag
                        $replaceHtmlTag = preg_replace('/(.*?)style=[\'"].*[\'"](.*?)/is', '$1$2', $replaceHtmlTag);
                    } else {
                        // add ; symbol to end style attr
                        $replaceHtmlTag = preg_replace('/(.*?style=[\'"])(.*)([\'"].*?)/is', '$1$2;$3', $replaceHtmlTag);
                        // replace ;; symbol to ; in the end style attr
                        $replaceHtmlTag = preg_replace('/;\s*;/is', ';', $replaceHtmlTag);
                    }
                }
                unset($doc);
                unset($xpath);

                // https://apoorv.pro/lozad.js/
                if ($this->checkOption(SettingsConstants::SETTING_ENABLE_LAZY_LOAD_BG_2X)) {
                    // for use with responsive background images (image-set)
                    // <div class="lozad" data-background-image-set="url('photo.jpg') 1x, url('photo@2x.jpg') 2x"></div>
                    $pathInfo = pathinfo($bgImg);
                    $ext = isset($pathInfo['extension']) ? $pathInfo['extension'] : null;
                    if (!$ext) {
                        continue;
                    }
                    $bg2xImg = preg_replace("/\.$ext/", "@2x.$ext", $bgImg);
                    if (file_exists(str_replace(trim(site_url(), '/') . '/', ABSPATH, $bg2xImg))) {
                        $replaceHtmlTag = preg_replace('/<(.*)>/is', '<$1 data-background-image-set="url(\'' . $bgImg . '\') 1x, url(\'' . $bg2xImg . '\') 2x">', $replaceHtmlTag);
                    } else {
                        $replaceHtmlTag = preg_replace('/<(.*)>/is', '<$1 data-background-image="' . $bgImg . '">', $replaceHtmlTag);
                    }
                } else {
                    //for use with background images
                    // <div class="lozad" data-background-image="image.png"></div>
                    $replaceHtmlTag = preg_replace('/<(.*)>/is', '<$1 data-background-image="' . $bgImg . '">', $replaceHtmlTag);
                }

                $this->pushArrays($htmlTag, $replaceHtmlTag);
            }
        }

        return true;
    }

    private function addLazyLoadVideoIFrameContent()
    {
        if (!$this->checkOption(SettingsConstants::SETTING_ENABLE_LAZY_LOAD_IFRAME)) {
            return false;
        }

        $matches = [];
        preg_match_all('/<iframe[^>]*?>(.*?)<\/iframe>/sim', $this->content, $matches);

        $skipFramesRegex = $this->getSkipImagesRegexLozad('if-video');

        foreach ($matches[0] as $iframeHTML) {
            if (!preg_match($skipFramesRegex, $iframeHTML)) {

                $replaceIframeHtml = '<span class="' . self::LOZAD_BASE_CLASS . '" data-original_content="' . base64_encode($iframeHTML) . '"></span>';

                $this->pushArrays($iframeHTML, $replaceIframeHtml);
            }
        }

        return true;
    }

    private function addLazyLoadVideoContent()
    {
        if (!$this->checkOption(SettingsConstants::SETTING_ENABLE_LAZY_LOAD_VIDEO)) {
            return false;
        }

        $videoWithPosterTags = [];
        preg_match_all('/<video[^>]*?>(.*?)<\/video>/sim', $this->content, $videoWithPosterTags);

        $skipTagClassesRegex = $this->getSkipImagesRegexLozad();

        foreach ($videoWithPosterTags[0] as $videoWithPosterTag) {
            if (preg_match($skipTagClassesRegex, $videoWithPosterTag) !== 0) {
                continue;
            }

            $replaceVideoWithPosterTag = preg_replace('/<(.*)poster=/is', '<$1 data-poster=', $videoWithPosterTag);

            $baseLazyloadClass = self::LOZAD_BASE_CLASS;
            if (preg_match('/class=["\']/i', $replaceVideoWithPosterTag)) {
                $replaceVideoWithPosterTag = preg_replace('/class=(["\'])(.*?)["\']/is', "class=$1{$baseLazyloadClass} $2$1", $replaceVideoWithPosterTag);
            } else {
                $replaceVideoWithPosterTag = preg_replace('/<video/is', '<video class="' . $baseLazyloadClass . '"', $replaceVideoWithPosterTag);
            }

            $this->pushArrays($videoWithPosterTag, $replaceVideoWithPosterTag);

            $sourceTagMatches = [];
            preg_match_all('/<source[^>]*?\/>/sim', $videoWithPosterTag, $sourceTagMatches);

            foreach ($sourceTagMatches[0] as $sourceTagMatch) {
                $sourceTagReplace = preg_replace('/<(.*)src=/is', '<$1 src="' . self::EMPTY_IMAGE . '" data-src=', $sourceTagMatch);

                $this->pushArrays($sourceTagMatch, $sourceTagReplace);
            }
        }

        return true;
    }

    private function getSkipImagesRegexLozad($additionalClass = null)
    {
        $skippedClasses = [];
        if ($this->checkOption(SettingsConstants::SETTING_SKIPPED_CLASSES_STRING)) {
            $classes = explode(',', $this->pluginOptions[SettingsConstants::SETTING_SKIPPED_CLASSES_STRING]);
            foreach ($classes as $class) {
                $skippedClasses[] = trim($class);
            }
        }
        if (!$skippedClasses) {
            $skippedClasses = ['no-' . self::LOZAD_BASE_CLASS, self::LOZAD_BASE_CLASS];
        }

        if ($additionalClass) {
            $skippedClasses[] = $additionalClass;
        }

        $skipImagesPregQuoted = array_map(function ($what) {
            return str_replace(['\*', '\.'], '', preg_quote($what, '/'));
        }, $skippedClasses);

        return sprintf('/class=".*(%s).*"/s', implode('|', $skipImagesPregQuoted));
    }

    private function checkOption($option)
    {
        return isset($this->pluginOptions[$option]) && $this->pluginOptions[$option];
    }

    private function pushArrays($search, $replace)
    {
        $this->search[] = $search;
        $this->replace[] = $replace;
    }
}