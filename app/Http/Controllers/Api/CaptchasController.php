<?php

namespace App\Http\Controllers\Api;

use Gregwar\Captcha\PhraseBuilder;
use  Illuminate\Support\Str;
use Illuminate\Http\Request;
use Gregwar\Captcha\CaptchaBuilder;
use App\Http\Requests\Api\CaptchaRequest;

class CaptchasController extends Controller
{
    public function store(CaptchaRequest $request)
    {
        $phraseBuilder = new PhraseBuilder(5, '0123456789');
        $captchaBuilder = new CaptchaBuilder(null, $phraseBuilder);
        $key = 'captcha-'.Str::random(15);
        $phone = $request->phone;

        $captcha = $captchaBuilder->build();
        $expiredAt = now()->addMinutes(2);
        \Cache::put($key, ['phone' => $phone, 'code' => $captcha->getPhrase()], $expiredAt);

        $result = [
            'captcha_key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
            'captcha_image_content' => $captcha->inline()
        ];

        return response()->json($result)->setStatusCode(201);
    }
}
