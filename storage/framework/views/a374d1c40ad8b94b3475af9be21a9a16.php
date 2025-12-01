<?php $__env->startSection('title', __('Create Role')); ?>

<?php $__env->startSection('header'); ?>
    <h2 class="text-xl font-semibold text-gray-800"><?php echo e(__('Roles')); ?></h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-3xl">
                <h3 class="mb-4"><?php echo e(__('Create Role')); ?></h3>
                <form method="POST" action="<?php echo e(route('roles.store')); ?>" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label class="block mb-1">Name</label>
                        <input type="text" name="name" value="<?php echo e(old('name')); ?>" class="w-full px-3 py-2 border rounded" required />
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label class="block mb-2">Permissions</label>
                        <div class="grid grid-cols-2 gap-2">
                            <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="permissions[]" value="<?php echo e($perm->name); ?>" />
                                    <span><?php echo e($perm->name); ?></span>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700" type="submit">Simpan</button>
                        <a class="inline-block px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300" href="<?php echo e(route('roles.index')); ?>">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/mymac/ardhi/project/rapi-app/resources/views/admin/roles/create.blade.php ENDPATH**/ ?>