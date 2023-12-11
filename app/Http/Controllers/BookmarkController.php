<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\News;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{

    public function bookmarkNews(Request $request, $id)
    {
        $userId = $request->user()->id;

        $news = News::find($id);

        if (!$news) {
            return response()->json(['error' => 'News article not found'], 404);
        }

        if (Bookmark::where('user_id', $userId)->where('news_id', $id)->exists()) {
            return response()->json(['error' => 'News article already bookmarked'], 400);
        }

        Bookmark::create([
            'user_id' => $userId,
            'news_id' => $id,
        ]);

        return response()->json(['message' => 'News article bookmarked successfully']);
    }

    public function unbookmarkNews(Request $request, $id)
    {
        $userId = $request->user()->id;

        $news = News::find($id);

        if (!$news) {
            return response()->json(['error' => 'News article not found'], 404);
        }

        $bookmark = Bookmark::where('user_id', $userId)->where('news_id', $id)->first();

        if (!$bookmark) {
            return response()->json(['error' => 'News article not bookmarked'], 400);
        }

        $bookmark->delete();

        return response()->json(['message' => 'News article unbookmarked successfully']);
    }
}
