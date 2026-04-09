<?php

namespace App\Services;

use App\Interfaces\DynamicPageServiceInterface;
use App\Models\Page;
use Illuminate\Support\Collection;

/**
 * Default implementation for dynamic page operations.
 */
class DynamicPageService implements DynamicPageServiceInterface
{
    /**
     * Retrieve all dynamic pages ordered by latest first.
     *
     * @return Collection<int, Page>
     */
    public function getAllLatest(): Collection
    {
        return Page::latest()->get();
    }

    /**
     * Retrieve one dynamic page by ID.
     *
     * @param int $id Dynamic page identifier.
     * @return Page
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int $id): Page
    {
        return Page::findOrFail($id);
    }

    /**
     * Create and persist a new dynamic page.
     *
     * @param array{page_title:string,page_content:string} $data Validated payload.
     * @return Page
     */
    public function create(array $data): Page
    {
        return Page::create([
            'page_title' => $data['page_title'],
            'page_content' => $data['page_content'],
        ]);
    }

    /**
     * Update an existing dynamic page.
     *
     * @param int $id Dynamic page identifier.
     * @param array{page_title:string,page_content:string} $data Validated payload.
     * @return Page
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(int $id, array $data): Page
    {
        $page = $this->findOrFail($id);
        $page->page_title = $data['page_title'];
        $page->page_content = $data['page_content'];
        $page->save();

        return $page;
    }

    /**
     * Toggle status between "active" and "inactive".
     *
     * @param int $id Dynamic page identifier.
     * @return Page
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function toggleStatus(int $id): Page
    {
        $page = $this->findOrFail($id);
        $page->status = $page->status === 'active' ? 'inactive' : 'active';
        $page->save();

        return $page;
    }

    /**
     * Delete a dynamic page by ID.
     *
     * @param int $id Dynamic page identifier.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function delete(int $id): void
    {
        $this->findOrFail($id)->delete();
    }
}
