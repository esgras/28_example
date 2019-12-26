<?php

namespace AppBundle\Service\Landing;

use AppBundle\Entity\Landing\Block;
use AppBundle\Entity\Landing\Template;

class LandingView
{
    private $landingTemplatesDir;

    public function __construct(string $landingTemplatesDir)
    {
        $this->landingTemplatesDir = $landingTemplatesDir;
    }

    public function renderBlock(Block $block)
    {
        $template = $block->getTemplate();
        $blockData = $block->getData();

        
        $templateFile = rtrim($this->landingTemplatesDir, '/') . DIRECTORY_SEPARATOR . $template->getName() . '.html';
        $content = file_get_contents($templateFile);
        foreach ($template->getData() as $key => $templateData) {
//            $replacement = isset($blockData[$key]) ? $blockData[$key] : '';
//            $content = $this->renderElement($content, )
//            if ($template->getName() == 'change_block') {
//                dump($template); die;
//            }

            $content = $this->renderElement($content, $key, $templateData, $blockData);
//
//            switch($type) {
//                case Template::DATA_TYPE_TEXT: $content = $this->renderText($content, $key, $blockData); break;
//            }
        }

        return $content;
    }

    protected function renderElement($content, $key, $templateData, $data)
    {
        if (!is_array($templateData)) {
            return $this->renderNode($content, $templateData, $data, $key);
        }
        

        $pattern = '#\['. strtoupper($key) . '\](.*)\[' . strtoupper($key) . '\]#s';
//        dump($content);
        preg_match($pattern, $content, $arr);
        $template = isset($arr[1]) ? $arr[1] : '';

//        dump($template); die;

        if (!isset($data[$key])) {
            return $content;
        }
        
//        dump($data[$key]);
//        dump($templateData);
//        die;

        $res = '';
        foreach ($data[$key] as $item) {
//            $str = $this->renderElement($template, $key, $templateData, $item);
            $str = $template;
            foreach ($templateData as $templateKey => $templateItem) {
//                dump($template); die;
//                dump($templateItem); die;
//                dump($item); die;
                $str = $this->renderElement($str, $templateKey, $templateItem, $item);
//                dump($str); die;
            }
            $res .= $str;
        }
        
//        dump($str); die;

//        foreach ($data as $el) {
//            $str = $this->renderElement($template, $newKey, $el, $data[$key][$newKey]);
//        }
//
//        foreach ($templateData as $newKey => $el) {
//            if (!isset($data[$key][$newKey])) continue;
//
//            dump($data[$key][$newKey]); die;
//
//            $str = $this->renderElement($template, $newKey, $el, $data[$key][$newKey]);
//        }

        return preg_replace($pattern, $res, $content);

    }

    protected function renderNode($content, $type, $data, $key)
    {
        switch($type) {
            case Template::DATA_TYPE_TEXT: $content = $this->renderText($content, $key, $data); break;
        }

        return $content;
    }

    protected function renderText($content, $key, $data)
    {
        $replacement = isset($data[$key]) ? $data[$key] : '';

        return str_replace('['.strtoupper($key).']', $replacement, $content);
    }
}