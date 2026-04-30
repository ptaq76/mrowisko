<?php

namespace App\Http\Controllers\Plac;

use App\Http\Controllers\Controller;
use App\Models\LoadingItem;
use App\Models\LoadingItemPhoto;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DeliveryPhotoController extends Controller
{
    private const MAX_PHOTOS_PER_ITEM = 5;

    public function index(Order $order, LoadingItem $item)
    {
        if ($item->order_id !== $order->id) {
            return response()->json(['success' => false], 403);
        }

        $photos = $item->photos()->orderBy('id')->get()->map(fn ($p) => [
            'id' => $p->id,
            'url' => $p->url,
            'thumb_url' => $p->thumb_url,
        ]);

        return response()->json([
            'success' => true,
            'photos' => $photos,
            'limit' => self::MAX_PHOTOS_PER_ITEM,
        ]);
    }

    public function store(Request $request, Order $order, LoadingItem $item)
    {
        if ($item->order_id !== $order->id) {
            return response()->json(['success' => false], 403);
        }

        $request->validate([
            'photo' => ['required', 'file', 'image', 'max:8192'],
            'thumb' => ['required', 'file', 'image', 'max:1024'],
        ]);

        $current = $item->photos()->count();
        if ($current >= self::MAX_PHOTOS_PER_ITEM) {
            return response()->json([
                'success' => false,
                'message' => 'Limit '.self::MAX_PHOTOS_PER_ITEM.' zdjęć na towar.',
            ], 422);
        }

        $disk = Storage::disk('public');
        $dir = "delivery_photos/{$order->id}/{$item->id}";

        $uuid = (string) Str::uuid();
        $path = $request->file('photo')->storeAs($dir, $uuid.'.jpg', 'public');
        $thumbPath = $request->file('thumb')->storeAs($dir, 'thumb_'.$uuid.'.jpg', 'public');

        $photo = LoadingItemPhoto::create([
            'loading_item_id' => $item->id,
            'order_id' => $order->id,
            'path' => $path,
            'thumb_path' => $thumbPath,
            'uploaded_by' => auth()->user()->id ?? null,
        ]);

        return response()->json([
            'success' => true,
            'photo' => [
                'id' => $photo->id,
                'url' => $photo->url,
                'thumb_url' => $photo->thumb_url,
            ],
            'count' => $item->photos()->count(),
            'limit' => self::MAX_PHOTOS_PER_ITEM,
        ]);
    }

    public function destroy(Order $order, LoadingItem $item, LoadingItemPhoto $photo)
    {
        if ($item->order_id !== $order->id || $photo->loading_item_id !== $item->id) {
            return response()->json(['success' => false], 403);
        }

        $photo->delete();

        return response()->json([
            'success' => true,
            'count' => $item->photos()->count(),
        ]);
    }
}
