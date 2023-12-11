<?php

namespace App\Http\Controllers;

use App\Http\Resources\NewsResource;
use App\Imports\NewsImport;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class NewsController extends Controller
{


    public function getHomePageNews(Request $request)
    {
        $userId = $request->user()->id;

        $news = News::all()->map(function ($item) use ($userId) {
            $item['is_bookmarked'] = Bookmark::where('user_id', $userId)
                ->where('news_id', $item->id)
                ->exists();
            return $item;
        });

        return response()->json(['news' => $news]);
    }


    public function importNews(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:json,csv,excel,xlsx|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(),422);
        }

        if ($request->hasFile('file')) {


            $file = $request->file('file');
            $fileContents = file_get_contents($file);
            $filenameWithExt = $file->getClientOriginalName();

            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

            $extension = $file->getClientOriginalExtension();

            $fileNameWithExtension = $filename . '_' . time() . '.' . $extension;

            $import = new NewsImport(true, [], null);

            $path = Storage::disk('local')->put($fileNameWithExtension, $fileContents);


            Excel::import($import, $fileNameWithExtension, 'local');
            $fileUrlImport = str_replace('%5C', '/', Storage::disk('local')->url($fileNameWithExtension));


            if ($import->getErrorCount()) {

                $invalidRows = $import->getInvalidItems();

                if (empty($invalidRows)) {
                    return $this->sendError('Error: Excel heading or some other issue with the selected file.', 422);
                }


                $exportRows = collect($invalidRows);

                $filename = time().'_'.bin2hex(random_bytes(8)).'.xlsx';

                $fileUrl = str_replace('%5C', '/', Storage::disk('local')->url($filename));


                return $this->sendError( 'Some Error with the Selected file',$fileUrl);

            }
            return response()->json(['message' => 'News imported successfully',200]);
        }

    }


    public function newsStore(Request $request){


        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'content' => 'required',
            'date' => 'required',
            'source' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(),422);
        }


        $news = new News();
        $news->title = $request->title;
        $news->content = $request->content;
        $news->date = $request->date;
        $news->source = $request->source;
        $news->save();

        return response()->json(['data' => NewsResource::make($news), 'message' => 'Successfully news inserted'], 201);


    }

    protected function sendError($message, $data = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }


}
