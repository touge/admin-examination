<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2019-11-19
 * Time: 16:56
 */

namespace Touge\AdminExamination\Supports;

use Illuminate\Support\Str;

class TagHelper
{
    public static function array2String(array $tags)
    {
        $filterTags = [];
        foreach ($tags as &$tag) {
            $tag = trim($tag);
            if ($tag == '' || Str::contains($tag, ':')) {
                continue;
            }
            $filterTags[] = ':' . $tag . ':';
        }
        return join('', array_unique($filterTags));
    }

    public static function string2Array($tags)
    {
        $tags = trim($tags, ':');
        $tags = explode('::', $tags);
        $filterTags = [];
        foreach ($tags as &$tag) {
            $tag = trim($tag);
            if (empty($tag)) {
                continue;
            }
            $filterTags[] = $tag;
        }
        return array_unique($filterTags);
    }

    public static function mapInfo($tags, array $tagMap = [])
    {
        foreach ($tags as &$tag) {
            if (array_key_exists($tag, $tagMap)) {
                $tag = $tagMap[$tag];
            }
        }
        return $tags;
    }

    public static function map($tags, array $tagMap = [])
    {
        if (is_string($tags)) {
            $tags = self::string2Array($tags);
        }
        $mapped = [];
        foreach ($tags as $tag) {
            if (array_key_exists($tag, $tagMap)) {
                $mapped[$tag] = $tagMap[$tag];
            } else {
                $mapped[$tag] = null;
            }
        }
        return $mapped;
    }

    public static function urlJoin($url, array $tags, $except = null, $tagType = 'number', $glue = '_')
    {
        if (null !== $except) {
            foreach ($tags as $index => $tag) {
                if ($tag == $except) {
                    unset($tags[$index]);
                }
            }
        }
        if ('number' == $tagType) {
            sort($tags, SORT_NUMERIC);
        } else {
            sort($tags, SORT_STRING);
        }
        if ($url) {
            $tags = array_merge([$url], $tags);
        }
        return join($glue, $tags);
    }
}