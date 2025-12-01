<?php $__env->startSection('title', __('Users')); ?>

<?php $__env->startSection('header'); ?>
    <h2 class="text-xl font-semibold text-gray-800"><?php echo e(__('Users')); ?></h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="mb-4"><?php echo e(__('Users')); ?></h3>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('users.create')): ?>
                    <a href="<?php echo e(route('users.create')); ?>" class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        + Create User
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
                            <th class="px-4 py-2 border text-left">Email</th>
                            <th class="px-4 py-2 border text-left">Status</th>
                            <th class="px-4 py-2 border text-left">Roles</th>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['users.update','users.delete'])): ?>
                            <th class="px-4 py-2 border">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <?php if($users->isEmpty()): ?>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center py-6">Belum Ada User.</td>
                            </tr>
                        </tbody>
                    <?php endif; ?>
                    <tbody>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="text-center hover:bg-gray-50">
                                <td class="px-4 py-2 border"><?php echo e(($users->currentPage()-1) * $users->perPage() + $loop->iteration); ?></td>
                                <td class="px-4 py-2 border text-left"><?php echo e($user->name); ?></td>
                                <td class="px-4 py-2 border text-left"><?php echo e($user->email); ?></td>
                                <td class="px-4 py-2 border text-left">
                                    <span class="inline-block px-2 py-1 rounded text-xs <?php echo e($user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700'); ?>">
                                        <?php echo e(ucfirst($user->status ?? 'inactive')); ?>

                                    </span>
                                </td>
                                <td class="px-4 py-2 border text-left"><?php echo e($user->roles->pluck('name')->join(', ') ?: '-'); ?></td>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['users.update','users.delete'])): ?>
                                <td class="px-4 py-2 border">
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('users.update')): ?>
                                    <a href="<?php echo e(route('users.edit', $user)); ?>" class="text-blue-600 hover:underline">Edit</a>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('users.delete')): ?>
                                    <form action="<?php echo e(route('users.destroy', $user)); ?>" method="POST" style="display:inline;">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-red-600 hover:underline ms-4" onclick="return confirm('Delete user?')">Hapus</button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>

                <div class="mt-4">
                    <?php echo e($users->links()); ?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/mymac/ardhi/project/rapi-app/resources/views/admin/users/index.blade.php ENDPATH**/ ?>