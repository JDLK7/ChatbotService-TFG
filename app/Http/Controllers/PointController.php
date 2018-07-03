<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PointService;
use Illuminate\Support\Facades\Validator;

class PointController extends Controller
{
    public function import(Request $request) {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimetypes:text/xml',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $file = $request->file('file');
        $path = $file->store('imports');

        $pointService = new PointService();
        $pointService->importFromXml(basename($path));

        return response()->json([
            'success' => true,
        ]);
    }
}
