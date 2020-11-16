<?php

namespace App\Http\Controllers;

use App\SpmsHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HomeController extends Controller
{
    public function index(Request $request)
    {

    }

    public function formSelections()
    {
        try
        {
            $data = SpmsHelper::getFormSelections();
            return response()->json($data);
        } catch (\Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
