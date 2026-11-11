<?php

namespace App\Http\Controllers;

use App\Models\FaizaENazar;
use App\Models\SiteSetting;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        return view('pages.about', [
            'site' => SiteSetting::current()->toSiteArray(),
            'teachers' => Teacher::query()->forAbout()->get(),
            'faizaENazars' => FaizaENazar::query()->published()->orderBy('sort_order')->orderByDesc('id')->get(),
            'solidHeader' => true,
        ]);
    }

    public function contact(): View
    {
        return view('pages.contact', [
            'site' => SiteSetting::current()->toSiteArray(),
            'solidHeader' => true,
        ]);
    }

    public function storeContact(Request $request): RedirectResponse
    {
        if (filled($request->input('website'))) {
            return back()->with('contact_success', 'آپ کا پیغام موصول ہو گیا۔');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160'],
            'phone' => ['nullable', 'string', 'max:40'],
            'subject' => ['nullable', 'string', 'max:160'],
            'message' => ['required', 'string', 'max:5000'],
        ], [
            'name.required' => 'نام ضروری ہے۔',
            'email.required' => 'ای میل ضروری ہے۔',
            'email.email' => 'درست ای میل لکھیں۔',
            'message.required' => 'پیغام ضروری ہے۔',
        ]);

        $settings = SiteSetting::current();
        $to = $settings->email ?: config('mail.from.address');
        $subject = $data['subject'] ?: ('رابطہ فارم - '.$data['name']);

        $body = implode("\n", array_filter([
            'نام: '.$data['name'],
            'ای میل: '.$data['email'],
            filled($data['phone'] ?? null) ? 'فون: '.$data['phone'] : null,
            '',
            $data['message'],
        ]));

        try {
            if ($to) {
                Mail::raw($body, function ($message) use ($to, $subject, $data) {
                    $message->to($to)
                        ->subject($subject)
                        ->replyTo($data['email'], $data['name']);
                });
            } else {
                Log::info('Contact form submission', $data);
            }
        } catch (\Throwable $e) {
            Log::warning('Contact form mail failed', [
                'error' => $e->getMessage(),
                'payload' => $data,
            ]);
        }

        return back()->with('contact_success', 'آپ کا پیغام موصول ہو گیا۔ جلد رابطہ کیا جائے گا۔');
    }
}
