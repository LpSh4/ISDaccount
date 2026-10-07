<?php
namespace Modules\Account\Services;
use App\Models\User;

class ProfileService
{
    public function updateProfile(User $user, array $data): void
    {
        $user->update($data);
    }
}
