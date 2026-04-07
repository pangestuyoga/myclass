<?php

namespace Database\Seeders;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Eloquent\Model;
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
                    "View:Changelog",
                    "Create:Changelog",
                    "Update:Changelog",
                    "Delete:Changelog",

                    "Create:Assignment",
                    "Delete:Assignment",
                    "ForceDelete:Assignment",
                    "ForceDeleteAny:Assignment",
                    "Replicate:Assignment",
                    "Reorder:Assignment",
                    "Restore:Assignment",
                    "RestoreAny:Assignment",
                    "Share:Assignment",
                    "Update:Assignment",
                    "View:Assignment",
                    "ViewAny:Assignment",

                    "Create:Attendance",
                    "Delete:Attendance",
                    "ForceDelete:Attendance",
                    "ForceDeleteAny:Attendance",
                    "Replicate:Attendance",
                    "Reorder:Attendance",
                    "Restore:Attendance",
                    "RestoreAny:Attendance",
                    "Update:Attendance",
                    "View:Attendance",
                    "ViewAny:Attendance",

                    "Create:AttendanceMonitoring",
                    "Delete:AttendanceMonitoring",
                    "ForceDelete:AttendanceMonitoring",
                    "ForceDeleteAny:AttendanceMonitoring",
                    "Replicate:AttendanceMonitoring",
                    "Reorder:AttendanceMonitoring",
                    "Restore:AttendanceMonitoring",
                    "RestoreAny:AttendanceMonitoring",
                    "Update:AttendanceMonitoring",
                    "View:AttendanceMonitoring",
                    "ViewAny:AttendanceMonitoring",

                    "Create:ClassSession",
                    "Delete:ClassSession",
                    "ForceDelete:ClassSession",
                    "ForceDeleteAny:ClassSession",
                    "Replicate:ClassSession",
                    "Reorder:ClassSession",
                    "Restore:ClassSession",
                    "RestoreAny:ClassSession",
                    "Update:ClassSession",
                    "View:ClassSession",
                    "ViewAny:ClassSession",

                    "Create:Course",
                    "Delete:Course",
                    "ForceDelete:Course",
                    "ForceDeleteAny:Course",
                    "Replicate:Course",
                    "Reorder:Course",
                    "Restore:Course",
                    "RestoreAny:Course",
                    "Update:Course",
                    "View:Course",
                    "ViewAny:Course",

                    "Create:CourseSchedule",
                    "Delete:CourseSchedule",
                    "ForceDelete:CourseSchedule",
                    "ForceDeleteAny:CourseSchedule",
                    "Replicate:CourseSchedule",
                    "Reorder:CourseSchedule",
                    "Restore:CourseSchedule",
                    "RestoreAny:CourseSchedule",
                    "Update:CourseSchedule",
                    "View:CourseSchedule",
                    "ViewAny:CourseSchedule",

                    "View:LogTable",

                    "View:ManageSettings",

                    "Create:Material",
                    "Delete:Material",
                    "ForceDelete:Material",
                    "ForceDeleteAny:Material",
                    "Replicate:Material",
                    "Reorder:Material",
                    "Restore:Material",
                    "RestoreAny:Material",
                    "Update:Material",
                    "View:Material",
                    "ViewAny:Material",

                    "Create:Role",
                    "Delete:Role",
                    "ForceDelete:Role",
                    "ForceDeleteAny:Role",
                    "Replicate:Role",
                    "Reorder:Role",
                    "Restore:Role",
                    "RestoreAny:Role",
                    "Update:Role",
                    "View:Role",
                    "ViewAny:Role",

                    "Create:Student",
                    "Delete:Student",
                    "ForceDelete:Student",
                    "ForceDeleteAny:Student",
                    "Replicate:Student",
                    "Reorder:Student",
                    "Restore:Student",
                    "RestoreAny:Student",
                    "Update:Student",
                    "View:Student",
                    "ViewAny:Student",

                    "Create:StudyGroup",
                    "Delete:StudyGroup",
                    "ForceDelete:StudyGroup",
                    "ForceDeleteAny:StudyGroup",
                    "Replicate:StudyGroup",
                    "Reorder:StudyGroup",
                    "Restore:StudyGroup",
                    "RestoreAny:StudyGroup",
                    "Update:StudyGroup",
                    "View:StudyGroup",
                    "ViewAny:StudyGroup"
                ]
            },
            {
                "name": "Mahasiswa",
                "guard_name": "web",
                "permissions" : [
                    "View:Changelog",

                    "View:Assignment",
                    "ViewAny:Assignment",

                    "View:Attendance",
                    "ViewAny:Attendance",

                    "View:Course",
                    "ViewAny:Course",

                    "View:CourseSchedule",
                    "ViewAny:CourseSchedule",

                    "View:Material",
                    "ViewAny:Material",

                    "View:StudyGroup",
                    "ViewAny:StudyGroup"
                ]
            },
            {
                "name": "Kosma",
                "guard_name": "web",
                "permissions" : [
                    "View:Changelog",

                    "Create:Assignment",
                    "Delete:Assignment",
                    "ForceDelete:Assignment",
                    "ForceDeleteAny:Assignment",
                    "Restore:Assignment",
                    "RestoreAny:Assignment",
                    "Share:Assignment",
                    "Update:Assignment",
                    "View:Assignment",
                    "ViewAny:Assignment",

                    "Create:Attendance",
                    "Delete:Attendance",
                    "ForceDelete:Attendance",
                    "ForceDeleteAny:Attendance",
                    "Restore:Attendance",
                    "RestoreAny:Attendance",
                    "Update:Attendance",
                    "View:Attendance",
                    "ViewAny:Attendance",

                    "Create:AttendanceMonitoring",
                    "Delete:AttendanceMonitoring",
                    "ForceDelete:AttendanceMonitoring",
                    "ForceDeleteAny:AttendanceMonitoring",
                    "Restore:AttendanceMonitoring",
                    "RestoreAny:AttendanceMonitoring",
                    "Update:AttendanceMonitoring",
                    "View:AttendanceMonitoring",
                    "ViewAny:AttendanceMonitoring",

                    "Create:ClassSession",
                    "Delete:ClassSession",
                    "ForceDelete:ClassSession",
                    "ForceDeleteAny:ClassSession",
                    "Restore:ClassSession",
                    "RestoreAny:ClassSession",
                    "Update:ClassSession",
                    "View:ClassSession",
                    "ViewAny:ClassSession",

                    "Create:Course",
                    "Delete:Course",
                    "ForceDelete:Course",
                    "ForceDeleteAny:Course",
                    "Restore:Course",
                    "RestoreAny:Course",
                    "Update:Course",
                    "View:Course",
                    "ViewAny:Course",

                    "Create:CourseSchedule",
                    "Delete:CourseSchedule",
                    "ForceDelete:CourseSchedule",
                    "ForceDeleteAny:CourseSchedule",
                    "Restore:CourseSchedule",
                    "RestoreAny:CourseSchedule",
                    "Update:CourseSchedule",
                    "View:CourseSchedule",
                    "ViewAny:CourseSchedule",

                    "View:ManageSettings",

                    "Create:Material",
                    "Delete:Material",
                    "ForceDelete:Material",
                    "ForceDeleteAny:Material",
                    "Restore:Material",
                    "RestoreAny:Material",
                    "Update:Material",
                    "View:Material",
                    "ViewAny:Material",

                    "Create:Student",
                    "Delete:Student",
                    "ForceDelete:Student",
                    "ForceDeleteAny:Student",
                    "Restore:Student",
                    "RestoreAny:Student",
                    "Update:Student",
                    "View:Student",
                    "ViewAny:Student",

                    "Create:StudyGroup",
                    "Delete:StudyGroup",
                    "ForceDelete:StudyGroup",
                    "ForceDeleteAny:StudyGroup",
                    "Restore:StudyGroup",
                    "RestoreAny:StudyGroup",
                    "Update:StudyGroup",
                    "View:StudyGroup",
                    "ViewAny:StudyGroup"
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

        $userModel = 'App\\Models\\User';
        $tenancyEnabled = false;

        foreach ($userData as $data) {
            $roles = $data['roles'] ?? [];
            $permissions = $data['permissions'] ?? [];
            $tenantRoles = $data['tenant_roles'] ?? [];
            $tenantPermissions = $data['tenant_permissions'] ?? [];
            unset($data['roles'], $data['permissions'], $data['tenant_roles'], $data['tenant_permissions']);

            $user = $userModel::firstOrCreate(
                ['email' => $data['email']],
                $data
            );

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

            if (! empty($makeRolesWithPermissions)) {
                DB::table($pivotTable)->updateOrInsert($uniqueKeys, $row);
            }
        }
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            return;
        }

        /** @var Model $roleModel */
        $roleModel = Utils::getRoleModel();
        /** @var Model $permissionModel */
        $permissionModel = Utils::getPermissionModel();

        $tenancyEnabled = false;
        $teamForeignKey = 'team_id';

        foreach ($rolePlusPermissions as $rolePlusPermission) {
            $tenantId = $rolePlusPermission[$teamForeignKey] ?? null;

            if ($tenancyEnabled) {
                setPermissionsTeamId($tenantId);
            }

            $roleData = [
                'name' => $rolePlusPermission['name'],
                'guard_name' => $rolePlusPermission['guard_name'],
            ];

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

        /** @var Model $permissionModel */
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
