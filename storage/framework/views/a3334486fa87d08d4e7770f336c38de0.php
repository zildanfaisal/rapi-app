<?php $__env->startSection('title', __('Customers')); ?>

<?php $__env->startSection('header'); ?>
    <h2 class="text-xl font-semibold text-gray-800"><?php echo e(__('Customers')); ?></h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-auto">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="mb-4"><?php echo e(__('Customers')); ?></h3>
                        <a href="<?php echo e(route('customers.create')); ?>" class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            + Tambah Customers
                        </a>
                    </div>
                    <table class="min-w-full border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 border">No</th>
                                <th class="px-4 py-2 border">Nama Customer</th>
                                <th class="px-4 py-2 border">No. HP</th>
                                <th class="px-4 py-2 border">E-mail</th>
                                <th class="px-4 py-2 border">Alamat</th>
                                <th class="px-4 py-2 border">Point</th>
                                <th class="px-4 py-2 border">Aksi</th>
                            </tr>
                        </thead>
                        <?php if($customers->isEmpty()): ?>
                            <tbody>
                                <tr>
                                    <td colspan="7" class="text-center py-6">Belum Ada Customer.</td>
                                </tr>
                            </tbody>
                        <?php endif; ?>
                        <tbody>
                            <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="text-center hover:bg-gray-50">
                                    <td class="px-4 py-2 border"><?php echo e($loop->iteration); ?></td>
                                    <td class="px-4 py-2 border"><?php echo e($c->nama_customer); ?></td>
                                    <td class="px-4 py-2 border"><?php echo e($c->no_hp); ?></td>
                                    <td class="px-4 py-2 border"><?php echo e($c->email); ?></td>
                                    <td class="px-4 py-2 border"><?php echo e($c->alamat); ?></td>
                                    <td class="px-4 py-2 border"><?php echo e($c->point); ?></td>
                                    <td class="px-4 py-2 border">
                                        <a href="<?php echo e(route('customers.edit', $c->id)); ?>" class="text-blue-600 hover:underline">Edit</a>
                                        <form action="<?php echo e(route('customers.destroy', $c->id)); ?>" method="POST" style="display:inline;">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="text-red-600 hover:underline ms-4">Hapus</button>
                                        </form>    
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/mymac/ardhi/project/rapi-app/resources/views/customers/index.blade.php ENDPATH**/ ?>