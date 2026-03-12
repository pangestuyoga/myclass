<?php

namespace Database\Seeders;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $tenants = '[]';
        $users = '[]';
        $userTenantPivot = '[]';
        $rolesWithPermissions = <<<'JSON'
        [
            {
                "name": "Developer",
                "guard_name": "web",
                "permissions" : [
                    "ViewAny:Student",
                    "View:Student",
                    "Create:Student",
                    "Update:Student",
                    "Delete:Student",
                    "Restore:Student",
                    "ForceDelete:Student",
                    "ForceDeleteAny:Student",
                    "RestoreAny:Student",
                    "Replicate:Student",
                    "Reorder:Student",
                    
                    "ViewAny:Lecturer",
                    "View:Lecturer",
                    "Create:Lecturer",
                    "Update:Lecturer",
                    "Delete:Lecturer",
                    "Restore:Lecturer",
                    "ForceDelete:Lecturer",
                    "ForceDeleteAny:Lecturer",
                    "RestoreAny:Lecturer",
                    "Replicate:Lecturer",
                    "Reorder:Lecturer",

                    "ViewAny:Course",
                    "View:Course",
                    "Create:Course",
                    "Update:Course",
                    "Delete:Course",
                    "Restore:Course",
                    "ForceDelete:Course",
                    "ForceDeleteAny:Course",
                    "RestoreAny:Course",
                    "Replicate:Course",
                    "Reorder:Course",

                    "ViewAny:CourseSchedule",
                    "View:CourseSchedule",
                    "Create:CourseSchedule",
                    "Update:CourseSchedule",
                    "Delete:CourseSchedule",
                    "Restore:CourseSchedule",
                    "ForceDelete:CourseSchedule",
                    "ForceDeleteAny:CourseSchedule",
                    "RestoreAny:CourseSchedule",
                    "Replicate:CourseSchedule",
                    "Reorder:CourseSchedule",

                    "ViewAny:Assignment",
                    "View:Assignment",
                    "Create:Assignment",
                    "Update:Assignment",
                    "Delete:Assignment",
                    "Restore:Assignment",
                    "ForceDelete:Assignment",
                    "ForceDeleteAny:Assignment",
                    "RestoreAny:Assignment",
                    "Replicate:Assignment",
                    "Reorder:Assignment",

                    "ViewAny:Attendance",
                    "View:Attendance",
                    "Create:Attendance",
                    "Update:Attendance",
                    "Delete:Attendance",
                    "Restore:Attendance",
                    "ForceDelete:Attendance",
                    "ForceDeleteAny:Attendance",
                    "RestoreAny:Attendance",
                    "Replicate:Attendance",
                    "Reorder:Attendance",

                    "ViewAny:Material",
                    "View:Material",
                    "Create:Material",
                    "Update:Material",
                    "Delete:Material",
                    "Restore:Material",
                    "ForceDelete:Material",
                    "ForceDeleteAny:Material",
                    "RestoreAny:Material",
                    "Replicate:Material",
                    "Reorder:Material",

                    "ViewAny:StudyGroup",
                    "View:StudyGroup",
                    "Create:StudyGroup",
                    "Update:StudyGroup",
                    "Delete:StudyGroup",
                    "Restore:StudyGroup",
                    "ForceDelete:StudyGroup",
                    "ForceDeleteAny:StudyGroup",
                    "RestoreAny:StudyGroup",
                    "Replicate:StudyGroup",
                    "Reorder:StudyGroup",

                    "ViewAny:Role",
                    "View:Role",
                    "Create:Role",
                    "Update:Role",
                    "Delete:Role",
                    "Restore:Role",
                    "ForceDelete:Role",
                    "ForceDeleteAny:Role",
                    "RestoreAny:Role",
                    "Replicate:Role",
                    "Reorder:Role",

                    "View:LogTable",
                    "View:ManageSettings"
                ]
            },
            {
                "name": "Mahasiswa",
                "guard_name": "web",
                "permissions" : [
                    "ViewAny:Student",
                    "View:Student",

                    "ViewAny:Lecturer",
                    "View:Lecturer",

                    "ViewAny:Course",
                    "View:Course",

                    "ViewAny:CourseSchedule",
                    "View:CourseSchedule",

                    "ViewAny:Assignment",
                    "View:Assignment",

                    "ViewAny:Attendance",
                    "View:Attendance",

                    "ViewAny:Material",
                    "View:Material",

                    "ViewAny:StudyGroup",
                    "View:StudyGroup"
                ]
            },
            {
                "name": "Kosma",
                "guard_name": "web",
                "permissions" : [
                    "Create:Student",
                    "Update:Student",
                    "Delete:Student",
                
                    "Create:Lecturer",
                    "Update:Lecturer",
                    "Delete:Lecturer",

                    "Create:Course",
                    "Update:Course",
                    "Delete:Course",

                    "Create:CourseSchedule",
                    "Update:CourseSchedule",
                    "Delete:CourseSchedule",

                    "Create:Assignment",
                    "Update:Assignment",
                    "Delete:Assignment",

                    "Create:Material",
                    "Update:Material",
                    "Delete:Material",

                    "Create:StudyGroup",
                    "Update:StudyGroup",
                    "Delete:StudyGroup",

                    "View:ManageSettings"
                ]
            }

        ]
        JSON;
        $directPermissions = '[]';

        // 1. Seed tenants first (if present)
        if (! blank($tenants) && $tenants !== '[]') {
            static::seedTenants($tenants);
        }

        // 2. Seed roles with permissions
        static::makeRolesWithPermissions($rolesWithPermissions);

        // 3. Seed direct permissions
        static::makeDirectPermissions($directPermissions);

        // 4. Seed users with their roles/permissions (if present)
        if (! blank($users) && $users !== '[]') {
            static::seedUsers($users);
        }

        // 5. Seed user-tenant pivot (if present)
        if (! blank($userTenantPivot) && $userTenantPivot !== '[]') {
            static::seedUserTenantPivot($userTenantPivot);
        }

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function seedTenants(string $tenants): void
    {
        if (blank($tenantData = json_decode($tenants, true))) {
            return;
        }

        $tenantModel = '';
        if (blank($tenantModel)) {
            return;
        }

        foreach ($tenantData as $tenant) {
            $tenantModel::firstOrCreate(
                ['id' => $tenant['id']],
                $tenant
            );
        }
    }

    protected static function seedUsers(string $users): void
    {
        if (blank($userData = json_decode($users, true))) {
            return;
        }

        $userModel = 'App\Models\User';
        $tenancyEnabled = false;

        foreach ($userData as $data) {
            // Extract role/permission data before creating user
            $roles = $data['roles'] ?? [];
            $permissions = $data['permissions'] ?? [];
            $tenantRoles = $data['tenant_roles'] ?? [];
            $tenantPermissions = $data['tenant_permissions'] ?? [];
            unset($data['roles'], $data['permissions'], $data['tenant_roles'], $data['tenant_permissions']);

            $user = $userModel::firstOrCreate(
                ['email' => $data['email']],
                $data
            );

            // Handle tenancy mode - sync roles/permissions per tenant
            if ($tenancyEnabled && (! empty($tenantRoles) || ! empty($tenantPermissions))) {
                foreach ($tenantRoles as $tenantId => $roleNames) {
                    $contextId = $tenantId === '_global' ? null : $tenantId;
                    setPermissionsTeamId($contextId);
                    $user->syncRoles($roleNames);
                }

                foreach ($tenantPermissions as $tenantId => $permissionNames) {
                    $contextId = $tenantId === '_global' ? null : $tenantId;
                    setPermissionsTeamId($contextId);
                    $user->syncPermissions($permissionNames);
                }
            } else {
                // Non-tenancy mode
                if (! empty($roles)) {
                    $user->syncRoles($roles);
                }

                if (! empty($permissions)) {
                    $user->syncPermissions($permissions);
                }
            }
        }
    }

    protected static function seedUserTenantPivot(string $pivot): void
    {
        if (blank($pivotData = json_decode($pivot, true))) {
            return;
        }

        $pivotTable = '';
        if (blank($pivotTable)) {
            return;
        }

        foreach ($pivotData as $row) {
            $uniqueKeys = [];

            if (isset($row['user_id'])) {
                $uniqueKeys['user_id'] = $row['user_id'];
            }

            $tenantForeignKey = 'team_id';
            if (! blank($tenantForeignKey) && isset($row[$tenantForeignKey])) {
                $uniqueKeys[$tenantForeignKey] = $row[$tenantForeignKey];
            }

            if (! empty($uniqueKeys)) {
                DB::table($pivotTable)->updateOrInsert($uniqueKeys, $row);
            }
        }
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            return;
        }

        /** @var \Illuminate\Database\Eloquent\Model $roleModel */
        $roleModel = Utils::getRoleModel();
        /** @var \Illuminate\Database\Eloquent\Model $permissionModel */
        $permissionModel = Utils::getPermissionModel();

        $tenancyEnabled = false;
        $teamForeignKey = 'team_id';

        foreach ($rolePlusPermissions as $rolePlusPermission) {
            $tenantId = $rolePlusPermission[$teamForeignKey] ?? null;

            // Set tenant context for role creation and permission sync
            if ($tenancyEnabled) {
                setPermissionsTeamId($tenantId);
            }

            $roleData = [
                'name' => $rolePlusPermission['name'],
                'guard_name' => $rolePlusPermission['guard_name'],
            ];

            // Include tenant ID in role data (can be null for global roles)
            if ($tenancyEnabled && ! blank($teamForeignKey)) {
                $roleData[$teamForeignKey] = $tenantId;
            }

            $role = $roleModel::firstOrCreate($roleData);

            if (! blank($rolePlusPermission['permissions'])) {
                $permissionModels = collect($rolePlusPermission['permissions'])
                    ->map(fn ($permission) => $permissionModel::firstOrCreate([
                        'name' => $permission,
                        'guard_name' => $rolePlusPermission['guard_name'],
                    ]))
                    ->all();

                $role->syncPermissions($permissionModels);
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (blank($permissions = json_decode($directPermissions, true))) {
            return;
        }

        /** @var \Illuminate\Database\Eloquent\Model $permissionModel */
        $permissionModel = Utils::getPermissionModel();

        foreach ($permissions as $permission) {
            if ($permissionModel::whereName($permission['name'])->doesntExist()) {
                $permissionModel::create([
                    'name' => $permission['name'],
                    'guard_name' => $permission['guard_name'],
                ]);
            }
        }
    }
}
