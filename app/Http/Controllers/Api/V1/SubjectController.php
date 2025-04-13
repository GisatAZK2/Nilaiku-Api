<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/subjects",
     *      tags={"Subjects"},
     *      summary="Mata pelajaran",
     *      description="List dari semua mata pelajaran yang ingin diprediksi .",
     *      @OA\Response(
     *          response=200,
     *          description="Successfully retrieved data",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Successfully retrieved data")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Data not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Data not found")
     *          )
     *      )
     * )
     */
    public function index()
    {
        $subjects = Subject::all();

        if ($subjects->isEmpty()) {
            return response()->json([
                'error' => 'Data not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Successfully retrieved data',
            'subjects' => $subjects
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $subject = Subject::create($validated);
        return response()->json($subject, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $subject->update($validated);
        return response()->json($subject);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject)
    {
        $subject->delete();
        return response()->json(null, 204);
    }
}
