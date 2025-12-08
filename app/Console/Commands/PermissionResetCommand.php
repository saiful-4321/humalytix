<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use App\Modules\Main\Models\Permission;
use Exception;

class PermissionResetCommand extends Command
{
    protected $signature = 'permission:reset';

    protected $description = 'Reset spatie permissions';

    /**
     * @return void
     */
    public function handle(): void
    {
        if (app()->isProduction()) {
            $this->error('Unable to run in production environment');
        }

        $this->info('Starting the reset process...');
        DB::beginTransaction();
        try {
            $this->callSilent('cache:clear');
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            Permission::query()->delete();
            DB::table('model_has_permissions')->delete();
            DB::table('role_has_permissions')->delete();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->callSilent('db:seed', ['--class' => 'PermissionsSeeder']);
            $this->callSilent('cache:clear');
            DB::commit();

            $this->info('Permissions reset successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            $this->error('An error occurred: ' . $e->getMessage());
        }
    }
}
