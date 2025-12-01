<?php $__env->startSection('title', __('Products')); ?>

<?php $__env->startSection('header'); ?>
    <h2 class="text-xl font-semibold text-gray-800"><?php echo e(__('Products')); ?></h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-auto">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="mb-4"><?php echo e(__('Products')); ?></h3>
                        <a href="<?php echo e(route('products.create')); ?>" class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            + Tambah Products
                        </a>
                    </div>
                    <table class="min-w-full border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 border">No</th>
                                <th class="px-4 py-2 border">Barcode</th>
                                <th class="px-4 py-2 border">Nama Produk</th>
                                <th class="px-4 py-2 border">Kategori</th>
                                <th class="px-4 py-2 border">Harga</th>
                                <th class="px-4 py-2 border">Satuan</th>
                                <th class="px-4 py-2 border">Foto Produk</th>
                                <th class="px-4 py-2 border">Min Pemberitahuan Stok</th>
                                <th class="px-4 py-2 border">Status</th>
                                <th class="px-4 py-2 border">Aksi</th>
                            </tr>
                        </thead>
                        <?php if($products->isEmpty()): ?>
                            <tbody>
                                <tr>
                                    <td colspan="10" class="py-6">
                                        <div class="text-center text-gray-600 text-lg font-medium">
                                            Belum Ada Product.
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        <?php endif; ?>

                        <tbody>
                            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="text-center hover:bg-gray-50">
                                    <td class="px-4 py-2 border"><?php echo e($loop->iteration); ?></td>
                                    <td class="px-4 py-2 border">
                                        <div class="flex flex-col items-center justify-center">
                                            
                                            <div class="flex justify-center">
                                                <?php echo DNS1D::getBarcodeHTML($p->barcode, 'C128', 2, 60); ?>

                                            </div>

                                            
                                            <div class="text-sm font-bold mt-2">
                                                <?php echo e($p->barcode); ?>

                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-2 border"><?php echo e($p->nama_produk); ?></td>
                                    <td class="px-4 py-2 border"><?php echo e($p->kategori); ?></td>
                                    <td class="px-4 py-2 border"><?php echo e($p->harga); ?></td>
                                    <td class="px-4 py-2 border"><?php echo e($p->satuan); ?></td>
                                    <td class="px-4 py-2 border">
                                        <?php if($p->foto_produk): ?>
                                            <img src="<?php echo e(asset('storage/' . $p->foto_produk)); ?>" alt="<?php echo e($p->nama_produk); ?>" class="w-16 h-16 object-cover mx-auto">
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-2 border"><?php echo e($p->min_stok_alert); ?></td>
                                    <td class="px-4 py-2 border"><?php echo e($p->status); ?></td>
                                    <td class="px-4 py-2 border">
                                        <a href="<?php echo e(route('products.edit', $p->id)); ?>" class="text-blue-600 hover:underline">Edit</a>
                                        <form action="<?php echo e(route('products.destroy', $p->id)); ?>" method="POST" style="display:inline;">
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
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/mymac/ardhi/project/rapi-app/resources/views/products/index.blade.php ENDPATH**/ ?>