<?php

namespace App\Policies;

use App\Models\RecurringRule;
use App\Models\User;

class RecurringRulePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, RecurringRule $recurringRule): bool
    {
        return $user->id === $recurringRule->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, RecurringRule $recurringRule): bool
    {
        return $user->id === $recurringRule->user_id;
    }

    public function delete(User $user, RecurringRule $recurringRule): bool
    {
        return $user->id === $recurringRule->user_id;
    }
}
