<?php

namespace App\Http\Controllers;

use App\Models\Hadith;
use App\Models\HadithErrorReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HadithErrorReportController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        if (filled($request->input('website'))) {
            return response()->json([
                'ok' => true,
                'message' => 'Thank you. Your report has been submitted.',
            ]);
        }

        $data = $request->validate([
            'hadith_id' => ['required', 'integer', 'exists:hadiths,id'],
            'error_type' => ['required', 'string', Rule::in(array_keys(HadithErrorReport::errorTypes()))],
            'other_detail' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf(fn () => $request->input('error_type') === HadithErrorReport::TYPE_OTHER),
            ],
            'details' => ['nullable', 'string', 'max:5000'],
            'notify_email' => ['sometimes', 'boolean'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::requiredIf(fn () => $request->boolean('notify_email')),
            ],
            'page_url' => ['nullable', 'string', 'max:2048'],
        ]);

        $hadith = Hadith::query()->findOrFail($data['hadith_id']);

        HadithErrorReport::query()->create([
            'hadith_id' => $hadith->id,
            'error_type' => $data['error_type'],
            'other_detail' => $data['other_detail'] ?? null,
            'details' => $data['details'] ?? null,
            'notify_email' => (bool) ($data['notify_email'] ?? false),
            'email' => $data['email'] ?? null,
            'status' => HadithErrorReport::STATUS_PENDING,
            'page_url' => $data['page_url'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 1000),
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Thank you. Your report has been submitted.',
        ]);
    }
}
