<?php

namespace App\Services;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class SubjectService
{
    /**
     * Mendapatkan semua mata pelajaran
     *
     * @return Collection
     */
    public function getAllSubjects(): Collection
    {
        return Subject::all();
    }

    /**
     * Mendapatkan mata pelajaran dengan paginasi
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedSubjects(int $perPage = 10): LengthAwarePaginator
    {
        return Subject::paginate($perPage);
    }

    /**
     * Mendapatkan mata pelajaran berdasarkan ID
     *
     * @param int $id
     * @return Subject|null
     */
    public function getSubjectById(int $id): ?Subject
    {
        return Subject::findOrFail($id);
    }

    /**
     * Membuat mata pelajaran baru
     *
     * @param array $data
     * @return Subject
     */
    public function createSubject(array $data): Subject
    {
        return Subject::create($data);
    }

    /**
     * Mengupdate mata pelajaran
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateSubject(int $id, array $data): bool
    {
        $subject = $this->getSubjectById($id);
        
        if (!$subject) {
            return false;
        }

        return $subject->update($data);
    }

    /**
     * Menghapus mata pelajaran
     *
     * @param int $id
     * @return bool
     */
    public function deleteSubject(int $id): bool
    {
        $subject = $this->getSubjectById($id);
        
        if (!$subject) {
            return false;
        }

        return $subject->delete();
    }

    /**
     * Mencari mata pelajaran berdasarkan nama
     *
     * @param string $search
     * @return Collection
     */
    public function searchSubjects(string $search): Collection
    {
        return Subject::where('name', 'like', "%{$search}%")
            ->orWhere('code', 'like', "%{$search}%")
            ->get();
    }
}
