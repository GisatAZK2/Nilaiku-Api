<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedbacks;
use App\Models\PredictionResult;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/feedbacks",
     *     summary="Submit feedback dari user",
     *     tags={"Feedback"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "prediction_id", "comment", "rating"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="prediction_id", type="integer", example=4),
     *             @OA\Property(property="comment", type="string", example="iiih takutnee"),
     *             @OA\Property(property="rating", type="number", format="float", example=9.99)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Feedback berhasil disimpan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Feedback berhasil disimpan"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="prediction_id", type="integer", example=4),
     *                 @OA\Property(property="comment", type="string", example="iiih takutnee"),
     *                 @OA\Property(property="rating", type="number", example=9.99),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-04-13 04:51:28")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validasi gagal",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validasi gagal"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'prediction_id' => 'required|exists:prediction_results,id',
            'comment' => 'required|string|max:255',
            'rating' => 'required|numeric|between:0,10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $feedback = Feedbacks::create([
            'user_id' => $request->user_id,
            'prediction_id' => $request->prediction_id,
            'comment' => $request->comment,
            'rating' => $request->rating,
            'date' => now(),
        ]);

        return response()->json([
            'message' => 'Feedback berhasil disimpan',
            'data' => $feedback
        ], 201);
    }
}
