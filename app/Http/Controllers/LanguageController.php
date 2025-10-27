<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class LanguageController extends Controller
{
    public function switchLang(Request $request)
    {
        // Support both 'lang' and 'language' parameters for compatibility
        $langCode = $request->input('language') ?? $request->input('lang');
        $language = Language::where('code', $langCode)->first();
        if ($language) {
            session()->put('locale', $language->code);

            // Ensure a language JSON file exists for this language. If not, create a starter file.
            $path = resource_path("lang/{$language->code}.json");
            if (! \Illuminate\Support\Facades\File::exists($path)) {
                $this->createTranslationFile($language->code);
            }
        }

        return Redirect::back();
    }

    public function switch(Request $request)
    {
        return $this->switchLang($request);
    }
}
