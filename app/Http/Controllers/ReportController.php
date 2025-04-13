<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reports;
use App\Models\User;
use Illuminate\Support\Facades\Validator;


class ReportController extends Controller
{
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'user_id' => 'required|exists:users,id',
        'report_type' => 'required|string|max:50',
        'generate_date' => 'required|date',
        'report_file' => 'required|file|max:10240', // Maks 10MB
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Simpan file ke storage
    $file = $request->file('report_file');
    //$filePath = $file->store('reports'); // simpan di storage/app/reports
    $filePath = $file->store('reports', 'public');

    // Simpan ke database
    $report = Reports::create([
        'user_id' => $request->user_id,
        'report_type' => $request->report_type,
        'generate_date' => $request->generate_date,
        'report_file' => $filePath,
    ]);

    return response()->json([
        'message' => 'Report uploaded successfully!',
        'data' => $report
    ], 201);
}

}
