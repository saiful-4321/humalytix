<?php
 
namespace App\Modules\Main\Models\Scopes;
 
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Route;

class UserScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where(function($query) {
            // Check if the current route is 'dashboard.user.remove-pretend' and if there's an impersonate session
            if (Route::currentRouteName() == 'dashboard.user.remove-pretend' && session()->has('impersonate_id')) { 
                return;
            } 

            if (!auth()->hasUser()) {
                return;
            }

            $user = auth()->user();

            // Handle query conditions based on user roles
            if ($user->hasRole('Agent')) {
                $query->where('users.id', $user->id)
                    ->orWhere('users.agent_id', $user->id); 
            } elseif ($user->hasRole('User')) {
                $query->where('users.id', $user->id);
            } elseif ($user->hasRole('Super Admin')) {
                $query->where('users.id', '!=', $user->id);
            }
        });
    }
}