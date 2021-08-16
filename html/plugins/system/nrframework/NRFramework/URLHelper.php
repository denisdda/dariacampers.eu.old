<?php 

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework;

use NRFramework\URL;

defined('_JEXEC') or die('Restricted access');

class URLHelper
{
    /**
     * Searches the given HTML for all external links and appends the affiliate paramter aff=id to every link based on an affiliate list.
     *
     * @param   string  $text           The html to search for external links
     * @param   array   $affiliates     A key value array: domain name => affiliate parameter
     *
     * @return  string
     */
    public static function replaceAffiliateLinks($text, $affiliates, $factory = null)
    {
        if (!class_exists('DOMDocument') || empty($text))
		{
            return $text;
        }

        $factory = $factory ? $factory : new \NRFramework\Factory();

		libxml_use_internal_errors(true);
        $dom = new \DOMDocument;
        $dom->encoding = 'UTF-8';
        $dom->loadHTML($text);

        $links = $dom->getElementsByTagName('a');

        foreach ($links as $link)
        {
            $linkHref = $link->getAttribute('href');

            if (empty($linkHref))
            {
                continue;
            }

            $url = new URL($linkHref, $factory);

            if ($url->isInternal())
            {
                continue;
            }

            $domain = $url->getDomainName();

            if (!array_key_exists($domain, $affiliates))
            {
                continue;
            }

            $urlInstance = $url->getInstance();
            $urlQuery = $urlInstance->getQuery();
            $affQuery = $affiliates[$domain];

            // If both queries are the same, skip the link tag
            if ($urlQuery === $affQuery)
            {
                continue;
            }

            if (empty($urlQuery))
            {
                $urlInstance->setQuery($affQuery);
            } else 
            {
                parse_str($urlQuery, $params);
                parse_str($affQuery, $params_);
                $params_new = array_merge($params, $params_);
                $urlInstance->setQuery(http_build_query($params_new));
            }

            $newURL = $urlInstance->toString();

            if ($newURL === $linkHref)
            {
                continue;
            }

            $link->setAttribute('href', $newURL);
        }

        return $dom->saveHtml();
    }

    /**
     * Convert all <img> and <a> tags with relative paths to absolute URLs
     *
     * @param  string   $text     The text/HTML to search for relative paths
     * @param  object   $factory  The framework's factory
     *
     * @return string   The converted HTML string
     */
    public static function relativePathsToAbsoluteURLs($text, $factory = null)
    {
        // Make sure DOMDocument is installed
        if (!class_exists('DOMDocument'))
		{
            return $text;
        }

        // Quick check the given text has some links or images
        $hasImages = strpos($text, '<img') !== false;
        $hasLinks = strpos($text, '<a') !== false;

        if (empty($text) || (!$hasImages && !$hasLinks))
        {
            return $text;
        }

        $factory = $factory ? $factory : new \NRFramework\Factory();

        try
        {
            libxml_use_internal_errors(true);
            $dom = new \DOMDocument;
            $dom->encoding = 'UTF-8';
    
            // Load HTML without adding a doctype and <body> tags.
            // https://stackoverflow.com/questions/4879946/how-to-savehtml-of-domdocument-without-html-wrapper
            $dom->loadHTML($text, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    
            // Replace links
            $links = $dom->getElementsByTagName('a');
    
            foreach ($links as $link)
            {
                $resource = $link->getAttribute('href');
    
                if (empty($resource) || mb_substr($resource, 0, 1) == '#')
                {
                    continue;
                }
    
                $url = new URL($resource, $factory);
    
                if (!$url->isInternal())
                {
                    continue;
                }
    
                $newURL = $url->toAbsolute();
    
                $link->setAttribute('href', $newURL);
            }
    
            // Replace images
            $images = $dom->getElementsByTagName('img');
    
            foreach ($images as $image)
            {
                $resource = $image->getAttribute('src');
    
                if (empty($resource))
                {
                    continue;
                }
    
                $url = new URL($resource, $factory);
    
                if (!$url->isInternal())
                {
                    continue;
                }
    
                $newURL = $url->toAbsolute();
    
                $image->setAttribute('src', $newURL);
            }
    
            return $dom->saveHTML();

        } catch (\Throwable $th)
        {
            return $text;
        }
    }
}