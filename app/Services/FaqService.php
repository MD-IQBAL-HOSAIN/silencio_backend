<?php

namespace App\Services;

use App\Interfaces\FaqServiceInterface;
use App\Models\Faq;
use Illuminate\Support\Collection;

/**
 * Default FAQ service implementation.
 *
 * This class encapsulates FAQ persistence operations and keeps controllers
 * focused on HTTP concerns.
 */
class FaqService implements FaqServiceInterface
{
    /**
     * Retrieve all FAQ records in descending creation order.
     *
     * @return Collection<int, Faq>
     */
    public function getAllLatest(): Collection
    {
        return Faq::latest()->get();
    }

    /**
     * Retrieve a FAQ by ID or fail if it does not exist.
     *
     * @param int $id FAQ identifier.
     * @return Faq
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int $id): Faq
    {
        return Faq::findOrFail($id);
    }

    /**
     * Create and persist a new FAQ record.
     *
     * @param array{question:string,answer:string} $data Validated payload.
     * @return Faq
     */
    public function create(array $data): Faq
    {
        return Faq::create([
            'question' => $data['question'],
            'answer' => $data['answer'],
        ]);
    }

    /**
     * Update a FAQ record with provided fields.
     *
     * @param int $id FAQ identifier.
     * @param array{question?:string,answer?:string} $data Validated payload.
     * @return Faq
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(int $id, array $data): Faq
    {
        $faq = $this->findOrFail($id);
        $faq->question = $data['question'] ?? $faq->question;
        $faq->answer = $data['answer'] ?? $faq->answer;
        $faq->save();

        return $faq;
    }

    /**
     * Toggle the FAQ status between "active" and "inactive".
     *
     * @param int $id FAQ identifier.
     * @return Faq
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function toggleStatus(int $id): Faq
    {
        $faq = $this->findOrFail($id);
        $faq->status = $faq->status === 'active' ? 'inactive' : 'active';
        $faq->save();

        return $faq;
    }

    /**
     * Delete a FAQ record by ID.
     *
     * @param int $id FAQ identifier.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function delete(int $id): void
    {
        $this->findOrFail($id)->delete();
    }
}
