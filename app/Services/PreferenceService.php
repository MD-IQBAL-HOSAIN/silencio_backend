<?php

namespace App\Services;

use App\Interfaces\PreferenceServiceInterface;
use App\Models\Preference;
use Illuminate\Support\Collection;

class PreferenceService implements PreferenceServiceInterface
{
    /**
     * Retrieve all preferences ordered by latest first.
     *
     * @return Collection<int, Preference>
     */
    public function getAllLatest(): Collection
    {
        return Preference::latest()->get();
    }

    /**
     * Retrieve one preference by ID.
     *
     * @param int $id Preference identifier.
     * @return Preference
     */
    public function findOrFail(int $id): Preference
    {
        return Preference::findOrFail($id);
    }

    /**
     * Create and persist a new preference.
     *
     * @param array{name:string,type:string} $data Validated payload.
     * @return Preference
     */
    public function create(array $data): Preference
    {
        return Preference::create([
            'name' => $data['name'],
            'type' => $data['type'],
        ]);
    }

    /**
     * Update an existing preference.
     *
     * @param int $id Preference identifier.
     * @param array{name:string,type:string} $data Validated payload.
     * @return Preference
     */
    public function update(int $id, array $data): Preference
    {
        $preference = $this->findOrFail($id);
        $preference->name = $data['name'];
        $preference->type = $data['type'];
        $preference->save();

        return $preference;
    }

    /**
     * Toggle status between active and inactive.
     *
     * @param int $id Preference identifier.
     * @return Preference
     */
    public function toggleStatus(int $id): Preference
    {
        $preference = $this->findOrFail($id);
        $preference->status = $preference->status === 'active' ? 'inactive' : 'active';
        $preference->save();

        return $preference;
    }

    /**
     * Delete a preference by ID.
     *
     * @param int $id Preference identifier.
     */
    public function delete(int $id): void
    {
        $this->findOrFail($id)->delete();
    }
}
