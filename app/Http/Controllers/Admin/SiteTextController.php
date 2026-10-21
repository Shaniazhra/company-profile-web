<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SiteTextController extends Controller
{
    /** Home page texts. Value = max characters. */
    private const HOME = [
        'tagline' => 200,
        'heart_statement' => 2000,
    ];

    /** About page texts. Value = max characters. */
    private const ABOUT = [
        'about_text' => 5000,
        'story' => 5000,
        'mission' => 2000,
        'vision' => 1000,
        'impact_statement' => 2000,
    ];

    public function home(Request $request): RedirectResponse
    {
        return $this->saveAll($request, self::HOME, 'texts-home', 'grp-home', 'All Home page texts saved.');
    }

    public function about(Request $request): RedirectResponse
    {
        return $this->saveAll($request, self::ABOUT, 'texts-about', 'grp-about', 'All About page texts saved.');
    }

    /** Save every text of one group in one request. The site is English only, so the text goes into the "en" language. */
    private function saveAll(Request $request, array $fields, string $section, string $anchor, string $message): RedirectResponse
    {
        $rules = [];
        foreach ($fields as $field => $max) {
            $rules[$field] = ['nullable', 'string', 'max:'.$max];
        }

        $data = $request->validate($rules);
        $site = SiteSetting::current();

        foreach (array_keys($fields) as $field) {
            $value = trim(str_replace("\r\n", "\n", (string) ($data[$field] ?? '')));

            if (method_exists($site, 'isTranslatableAttribute') && $site->isTranslatableAttribute($field)) {
                $site->setTranslation($field, 'en', $value);
            } else {
                $site->{$field} = $value;
            }
        }

        $site->save();

        return redirect()
            ->to(route('admin.site.edit').'#'.$anchor)
            ->with('status', $message)
            ->with('section', $section);
    }
}