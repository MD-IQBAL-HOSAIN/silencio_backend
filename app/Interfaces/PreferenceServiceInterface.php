<?php

namespace App\Interfaces;

use App\Models\Preference;
use Illuminate\Support\Collection;

interface PreferenceServiceInterface
{
    /**
     * Retrieve all preferences ordered by latest first.
     *
     * @return Collection<int, Preference>
     */
    public function getAllLatest(): Collection;

    /**
     * Retrieve a preference by ID.
     *
     * @param int $id Preference identifier.
     * @return Preference
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int $id): Preference;

    /**
     * Create and persist a new preference.
     *
     * @param array{name:string,type:string} $data Validated payload.
     * @return Preference
     */
    public function create(array $data): Preference;

    /**
     * Update an existing preference.
     *
     * @param int $id Preference identifier.
     * @param array{name:string,type:string} $data Validated payload.
     * @return Preference
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(int $id, array $data): Preference;

    /**
     * Toggle preference status between active and inactive.
     *
     * @param int $id Preference identifier.
     * @return Preference
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function toggleStatus(int $id): Preference;

    /**
     * Delete a preference by ID.
     *
     * @param int $id Preference identifier.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function delete(int $id): void;
}
