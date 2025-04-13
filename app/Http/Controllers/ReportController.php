<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reports;
use App\Models\User;
use Illuminate\Support\Facades\Validator;


class ReportController extends Controller
{
    /**
 * @OA\Post(
 *     path="/api/report",
 *     tags={"Reports"},
 *     summary="Upload a report file",
 *     description="Endpoint untuk mengunggah Report Bug / Error berupa file.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"user_id", "report_type", "generate_date", "report_file"},
 *                 @OA\Property(property="user_id", type="integer", example=1),
 *                 @OA\Property(property="report_type", type="string", example="Monthly Report"),
 *                 @OA\Property(property="generate_date", type="string", format="date", example="2025-04-10"),
 *                 @OA\Property(property="report_file", type="string", format="binary")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Report uploaded successfully!",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Report uploaded successfully!"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=10),
 *                 @OA\Property(property="user_id", type="integer", example=1),
 *                 @OA\Property(property="report_type", type="string", example="Monthly Report"),
 *                 @OA\Property(property="generate_date", type="string", example="2025-04-10"),
 *                 @OA\Property(property="report_file", type="string", example="reports/abcd1234.pdf")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="errors", type="object")
 *         )
 *     )
 * )
 */


    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'user_id' => 'required|exists:users,id',
        'report_type' => 'required|string|max:50',
        'generate_date' => 'required|date',
        'report_file' => 'required|file|max:10240', 
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    
    $file = $request->file('report_file');
    
    $filePath = $file->store('reports', 'public');

    
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
