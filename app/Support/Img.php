<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;

/** Image helpers for the public pages. */
class Img
{
    /**
     * width="..." height="..." for an image that lives in /public (including /public/storage), so the browser can
     * reserve the space before the photo arrives and the page does not jump. Returns nothing when the size is unknown.
     * The size is read once per file and remembered until the file changes.
     */
    public static function attrs(?string $url): HtmlString
    {
        if (! $url) {
            return new HtmlString('');
        }

        $path = parse_url($url, PHP_URL_PATH);

        if (! is_string($path) || str_contains($path, '..')) {
            return new HtmlString('');
        }

        $file = public_path(ltrim(rawurldecode($path), '/'));

        if (! is_file($file)) {
            return new HtmlString('');
        }

        $size = Cache::rememberForever(
            'img-size:'.md5($file).':'.filemtime($file),
            fn () => ($info = @getimagesize($file)) ? [(int) $info[0], (int) $info[1]] : false
        );

        return $size
            ? new HtmlString('width="'.$size[0].'" height="'.$size[1].'"')
            : new HtmlString('');
    }
}