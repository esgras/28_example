<?php

namespace AppBundle\Helper;

class YoutubeHelper
{
    const IMAGE_URL = 'https://i1.ytimg.com/vi/[ID]/sddefault.jpg';
    const INLINE_URL = 'https://www.youtube.com/embed/[ID]';
    const SIMPLE_URL = 'https://www.youtube.com/watch?v=[ID]';

    public function getImagePathForLink($link)
    {
        return str_replace('[ID]', $this->getIdFromLink($link), self::IMAGE_URL);

    }

    public function getEmbedLink($link)
    {
        return !($this->checkLink($link) && $this->videoExists($link)) ? false
            :  str_replace('[ID]', $this->getIdFromLink($link), self::INLINE_URL);
    }

    protected function makeSimpleLink($url)
    {
        return str_replace('[ID]', $this->getIdFromLink($url), self::SIMPLE_URL);
    }

    public function videoExists($url)
    {
        if (!$this->isSimple($url)) {
            $url = $this->makeSimpleLink($url);
        }

        return $this->checkUrl($url);
    }

    protected function checkUrl($url)
    {
        $headers = get_headers($url);
        return strpos($headers[0], '200') !== false;
    }

    protected function isSimple($url)
    {

        $simpleUrl = substr(self::SIMPLE_URL, 0, -4);
        return preg_match('#' . $simpleUrl . '#', $url);

        
//        return strpos('embed', $url) !== false;
    }

    public function checkLink($link)
    {
        return preg_match('#^https://(www.)?youtu#', $link);
    }

    protected function getIdFromLink($link)
    {
        if (preg_match('#embed#', $link)) {
            $pos = strpos($link, '?');
            $link = substr($link, 0, $pos ? $pos :  strlen($link));

            return substr($link, strrpos($link, '/') + 1);
        } elseif (preg_match('#youtu\.be#', $link)) {
            return substr($link, strrpos($link, '/') + 1);
        } elseif (preg_match('#youtube\.com#', $link)) {
            $link = substr($link, strpos($link, 'v='));
            $parts = explode('&', $link);
            return substr($parts[0], strpos($parts[0], '=') + 1);
        }

        return '';
    }
}