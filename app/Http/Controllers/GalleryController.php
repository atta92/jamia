<?php

namespace App\Http\Controllers;

use App\Models\GalleryItem;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function photos(): View
    {
        return $this->index('photo');
    }

    public function videos(): View
    {
        return $this->index('video');
    }

    protected function index(string $type): View
    {
        $items = GalleryItem::query()
            ->published()
            ->where('type', $type)
            ->orderByDesc('year')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        $highlightItems = $items
            ->filter(fn (GalleryItem $item) => $item->category === 'highlights')
            ->values();

        $yearItems = $items
            ->reject(fn (GalleryItem $item) => $item->category === 'highlights')
            ->values();

        $years = $yearItems->pluck('year')->filter()->unique()->sortDesc()->values();

        $grouped = $yearItems
            ->groupBy(fn (GalleryItem $item) => $item->year ?: 'دیگر')
            ->sortKeysDesc();

        if ($highlightItems->isNotEmpty()) {
            $grouped->put('highlights', $highlightItems);
        }

        return view('gallery.index', [
            'type' => $type,
            'items' => $items,
            'years' => $years,
            'grouped' => $grouped,
            'hasHighlights' => $highlightItems->isNotEmpty(),
            'solidHeader' => true,
        ]);
    }
}
