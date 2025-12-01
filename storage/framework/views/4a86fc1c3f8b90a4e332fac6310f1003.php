<?php $__env->startSection('title', __('Roles')); ?>

<?php $__env->startSection('header'); ?>
    <h2 class="text-xl font-semibold text-gray-800"><?php echo e(__('Roles')); ?></h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="mb-4"><?php echo e(__('Roles')); ?></h3>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('roles.create')): ?>
                    <a href="<?php echo e(route('roles.create')); ?>" class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        + Create Role
                    </a>
                    <?php endif; ?>
                </div>

                <?php if(session('status')): ?>
                    <div class="mb-4 px-4 py-2 bg-green-100 text-green-800 rounded"><?php echo e(session('status')); ?></div>
                <?php endif; ?>

                <table class="min-w-full border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 border">No</th>
                            <th class="px-4 py-2 border text-left">Name</th>
                            <th class="px-4 py-2 border text-left">Permissions</th>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['roles.update','roles.delete'])): ?>
                            <th class="px-4 py-2 border">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <?php if(collect($roles)->isEmpty()): ?>
                        <tbody>
                            <tr>
                                <td colspan="4" class="text-center py-6">Belum Ada Role.</td>
                            </tr>
                        </tbody>
                    <?php endif; ?>
                    <tbody>
                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="text-center hover:bg-gray-50">
                                <td class="px-4 py-2 border"><?php echo e($loop->iteration); ?></td>
                                <td class="px-4 py-2 border text-left"><?php echo e($role->name); ?></td>
                                <td class="px-4 py-2 border text-left"><?php echo e($role->permissions->pluck('name')->join(', ')); ?></td>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['roles.update','roles.delete'])): ?>
                                <td class="px-4 py-2 border">
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('roles.update')): ?>
                                    <a href="<?php echo e(route('roles.edit', $role)); ?>" class="text-blue-600 hover:underline">Edit</a>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('roles.delete')): ?>
                                    <form action="<?php echo e(route('roles.destroy', $role)); ?>" method="POST" style="display:inline;">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-red-600 hover:underline ms-4" onclick="return confirm('Delete role?')">Hapus</button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/mymac/ardhi/project/rapi-app/resources/views/admin/roles/index.blade.php ENDPATH**/ ?>