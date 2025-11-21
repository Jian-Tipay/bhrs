<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Property;

class PropertyPolicy
{
    /**
     * Determine if the given property can be viewed by the user.
     */
    public function view(User $user, Property $property)
    {
        return $this->ownsProperty($user, $property);
    }

    /**
     * Determine if the given property can be updated by the user.
     */
    public function update(User $user, Property $property)
    {
        return $this->ownsProperty($user, $property);
    }

    /**
     * Determine if the given property can be deleted by the user.
     */
    public function delete(User $user, Property $property)
    {
        return $this->ownsProperty($user, $property);
    }

    /**
     * Shared ownership logic.
     */
    private function ownsProperty(User $user, Property $property)
    {
        return $property->landlord && $property->landlord->user_id === $user->id;
    }
}
