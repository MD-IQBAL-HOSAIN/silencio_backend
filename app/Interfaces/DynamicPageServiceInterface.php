<?php

namespace App\Interfaces;

use App\Models\Page;
use Illuminate\Support\Collection;

/**
 * Defines the domain operations for dynamic pages.
 */
interface DynamicPageServiceInterface
{
    /**
     * Retrieve all dynamic pages ordered by latest first.
     *
     * @return Collection<int, Page>
     */
    public function getAllLatest(): Collection;

    /**
     * Retrieve a dynamic page by ID.
     *
     * @param int $id Dynamic page identifier.
     * @return Page
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int $id): Page;

    /**
     * Create and persist a new dynamic page.
     *
     * @param array{page_title:string,page_content:string} $data Validated payload.
     * @return Page
     */
    public function create(array $data): Page;

    /**
     * Update an existing dynamic page.
     *
     * @param int $id Dynamic page identifier.
     * @param array{page_title:string,page_content:string} $data Validated payload.
     * @return Page
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(int $id, array $data): Page;

    /**
     * Toggle dynamic page status between "active" and "inactive".
     *
     * @param int $id Dynamic page identifier.
     * @return Page
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function toggleStatus(int $id): Page;

    /**
     * Delete a dynamic page by ID.
     *
     * @param int $id Dynamic page identifier.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function delete(int $id): void;
}
