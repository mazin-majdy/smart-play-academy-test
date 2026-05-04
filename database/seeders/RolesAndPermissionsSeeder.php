<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles/permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── PERMISSIONS ──
        $permissions = [
            // Children management
            'create children',
            'view children',
            'edit children',
            'delete children',

            // Content
            'manage subjects',
            'manage topics',
            'manage games',
            'manage questions',

            // Reports
            'view own reports',
            'view all reports',
            'export reports',

            // AI
            'use ai tutor',
            'manage ai content',

            // Admin
            'manage users',
            'manage subscriptions',
            'view analytics',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // ── ROLES ──

        // الطفل — لا يملك account حقيقي، session فقط
        // (نحتاجه فقط للـ middleware check)

        // الأهل
        $parent = Role::firstOrCreate(['name' => 'parent']);
        $parent->givePermissionTo([
            'create children',
            'view children',
            'edit children',
            'view own reports',
            'use ai tutor',
        ]);

        // المعلم
        $teacher = Role::firstOrCreate(['name' => 'teacher']);
        $teacher->givePermissionTo([
            'view children',
            'view own reports',
            'export reports',
            'use ai tutor',
        ]);

        // مدير المحتوى
        $contentManager = Role::firstOrCreate(['name' => 'content_manager']);
        $contentManager->givePermissionTo([
            'manage subjects',
            'manage topics',
            'manage games',
            'manage questions',
            'manage ai content',
        ]);

        // السوبر أدمن
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());
    }
}
