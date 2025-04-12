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
     *          description="Data mata pelajaran berhasil didapatkan",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Data mata pelajaran berhasil didapatkan")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Invalid credentials",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Invalid credentials")
     *          )
     *      )
     * )
     */
    public function index()
    {
        return response()->json(Subject::all());
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
