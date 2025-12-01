<?php $__env->startSection('title', __('Edit Product')); ?>

<?php $__env->startSection('header'); ?>
    <h2 class="text-xl font-semibold text-gray-800"><?php echo e(__('Edit Product')); ?></h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="mb-4"><?php echo e(__('Edit Product')); ?></h3>

                    <form method="POST" action="<?php echo e(route('products.update', $product->id)); ?>" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        
                        <div class="mb-4">
                            <label for="nama_produk" class="block text-sm font-medium text-gray-700">
                                <?php echo e(__('Nama Produk')); ?>

                            </label>
                            <input type="text" name="nama_produk" id="nama_produk"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                          focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                   required
                                   value="<?php echo e(old('nama_produk', $product->nama_produk)); ?>">
                        </div>

                        
                        <div class="mb-4">
                            <label for="barcode" class="block text-sm font-medium text-gray-700">
                                <?php echo e(__('Barcode')); ?>

                            </label>

                            <div class="flex gap-2">
                                <input type="text" name="barcode" id="barcode"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                              focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                       required
                                       value="<?php echo e(old('barcode', $product->barcode)); ?>">

                                
                                <button type="button"
                                        onclick="generateBarcode()"
                                        class="mt-1 px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                    Generate
                                </button>
                            </div>
                        </div>

                        
                        <div class="mb-4">
                            <label for="kategori" class="block text-sm font-medium text-gray-700"><?php echo e(__('Kategori')); ?></label>
                            <input type="text" name="kategori" id="kategori"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                          focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                   required
                                   value="<?php echo e(old('kategori', $product->kategori)); ?>">
                        </div>

                        
                        <div class="mb-4">
                            <label for="harga" class="block text-sm font-medium text-gray-700"><?php echo e(__('Harga')); ?></label>
                            <input type="number" name="harga" id="harga"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                          focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                   required
                                   value="<?php echo e(old('harga', $product->harga)); ?>">
                        </div>

                        
                        <div class="mb-4">
                            <label for="satuan" class="block text-sm font-medium text-gray-700"><?php echo e(__('Satuan')); ?></label>
                            <input type="text" name="satuan" id="satuan"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                          focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                   required
                                   value="<?php echo e(old('satuan', $product->satuan)); ?>">
                        </div>

                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700"><?php echo e(__('Foto Produk')); ?></label>

                            <?php if($product->foto_produk): ?>
                                <div class="mb-2">
                                    <img src="<?php echo e(asset('storage/' . $product->foto_produk)); ?>"
                                         alt="Foto Produk"
                                         class="w-32 h-32 object-cover rounded-md border mx-auto">
                                </div>
                            <?php endif; ?>

                            <input type="file" name="foto_produk" accept="image/*"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                          focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            <small class="text-gray-600">Biarkan kosong jika tidak ingin mengganti foto.</small>
                        </div>

                        
                        <div class="mb-4">
                            <label for="min_stok_alert" class="block text-sm font-medium text-gray-700">
                                <?php echo e(__('Min Pemberitahuan Stok')); ?>

                            </label>
                            <input type="number" name="min_stok_alert" id="min_stok_alert"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                          focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                   required
                                   value="<?php echo e(old('min_stok_alert', $product->min_stok_alert)); ?>">
                        </div>

                        
                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700"><?php echo e(__('Status')); ?></label>
                            <select name="status" id="status"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                           focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                    required>
                                <option disabled>Pilih Status</option>
                                <option value="available" <?php echo e(old('status', $product->status) == 'available' ? 'selected' : ''); ?>>Available</option>
                                <option value="unavailable" <?php echo e(old('status', $product->status) == 'unavailable' ? 'selected' : ''); ?>>Unavailable</option>
                            </select>
                        </div>

                        
                        <div class="flex items-center gap-4">
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <?php echo e(__('Simpan')); ?>

                            </button>

                            <a href="<?php echo e(route('products.index')); ?>"
                               class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                <?php echo e(__('Batal')); ?>

                            </a>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    function generateBarcode() {
        let randomNumber = Math.floor(Math.random() * 100000000)
            .toString()
            .padStart(8, '0');

        document.getElementById('barcode').value = randomNumber;
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/mymac/ardhi/project/rapi-app/resources/views/products/edit.blade.php ENDPATH**/ ?>