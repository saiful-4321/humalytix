<?php
 
namespace App\Modules\Main\Models\Scopes;
 
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
 
class DocumentScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where(function($query) {
            if (auth()->hasUser() && auth()->user()->hasRole('User')) {
                $query->where('user_id', auth()->user()->id);
            }
        });
    }
}