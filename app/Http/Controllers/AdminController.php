<?php

namespace App\Http\Controllers;

use App\Http\Resources\NewsResource;
use App\Imports\NewsImport;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;
use Maatwebsite\Excel\Facades\Excel;


class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('admin')->except('importNews');
    }


    public function deleteNews($id)
    {
        News::destroy($id);

        return response()->json(['message' => 'News deleted successfully']);
    }


}
