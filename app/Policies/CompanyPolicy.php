<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    public function view(User $user, Company $company): bool
    {
        return $company->owner_id === $user->id;
    }

    public function update(User $user, Company $company): bool
    {
        return $company->owner_id === $user->id;
    }
}
