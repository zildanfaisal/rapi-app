<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('title', config('app.name', 'App')); ?></title>
    <!-- Vite: CSS -->
    <?php if(file_exists(public_path('mix-manifest.json'))): ?>
        <!-- fallback if mix is used -->
        <link href="<?php echo e(mix('css/app.css')); ?>" rel="stylesheet">
    <?php else: ?>
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>
    <?php endif; ?>

    <?php echo $__env->yieldPushContent('head'); ?>
    <!-- Tom Select (searchable selects) -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.tailwindcss.min.css">
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div x-data="{ sidebarCollapsed: (localStorage.getItem('sidebarCollapsed') === 'true'), mobileOpen: false }"
        x-init="$watch('sidebarCollapsed', value => localStorage.setItem('sidebarCollapsed', value))"
        class="min-h-screen flex"
        :class="sidebarCollapsed ? 'sidebar-collapsed' : ''">
        
        <?php echo $__env->make('layouts.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        
        <div class="flex-1 flex flex-col min-h-screen">
            

            
            <?php if (! empty(trim($__env->yieldContent('header')))): ?>
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <!-- Mobile hamburger next to header title -->
                            <button @click="mobileOpen = true" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 md:hidden hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>

                            <div class="flex-1 flex items-center gap-3">
                                <button @click.prevent="sidebarCollapsed = !sidebarCollapsed" class="text-black p-1 rounded hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-gray-200">
                                    <svg x-show="!sidebarCollapsed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 12L6 6V18Z" /></svg>
                                    <svg x-show="sidebarCollapsed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 6L6 12L18 18V6Z" /></svg>
                                </button>
                                <div class="flex-1">
                                    <?php echo $__env->yieldContent('header'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="ms-4 flex items-center">
                            <!-- Profile dropdown placed in header -->
                            <?php if (isset($component)) { $__componentOriginaldf8083d4a852c446488d8d384bbc7cbe = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown','data' => ['align' => 'right','width' => '48']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['align' => 'right','width' => '48']); ?>
                                 <?php $__env->slot('trigger', null, []); ?> 
                                    <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700">
                                        <div class="mr-3 text-right">
                                            <div class="text-xs text-gray-500"><?php echo e(Auth::user()->name ?? 'User'); ?></div>
                                            <div class="text-xs text-gray-400"><?php echo e(Auth::user()->email ?? ''); ?></div>
                                        </div>

                                        <div>
                                            <?php
                                                $name = Auth::user()->name ?? 'U';
                                                $parts = preg_split('/\s+/', trim($name));
                                                $initials = '';
                                                foreach ($parts as $p) {
                                                    if ($p === '') continue;
                                                    $initials .= mb_strtoupper(mb_substr($p, 0, 1));
                                                    if (mb_strlen($initials) >= 2) break; // limit to 2 chars
                                                }
                                                if ($initials === '') { $initials = 'U'; }
                                            ?>

                                            <?php if(!empty(Auth::user()->profile_photo_url)): ?>
                                                <img class="h-9 w-9 rounded-full object-cover" src="<?php echo e(Auth::user()->profile_photo_url); ?>" alt="<?php echo e($initials); ?>" />
                                            <?php else: ?>
                                                <div class="h-9 w-9 rounded-full bg-gray-200 text-sm font-semibold flex items-center justify-center text-gray-700" aria-hidden="false" role="img" aria-label="<?php echo e($initials); ?>">
                                                    <?php echo e($initials); ?>

                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="ms-2">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                 <?php $__env->endSlot(); ?>

                                 <?php $__env->slot('content', null, []); ?> 
                                    <?php if (isset($component)) { $__componentOriginal68cb1971a2b92c9735f83359058f7108 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal68cb1971a2b92c9735f83359058f7108 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => route('profile.edit')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('profile.edit'))]); ?>
                                        <?php echo e(__('Profile')); ?>

                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $attributes = $__attributesOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__attributesOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $component = $__componentOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__componentOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>

                                    <!-- Authentication -->
                                    <form method="POST" action="<?php echo e(route('logout')); ?>" data-logout-confirm>
                                        <?php echo csrf_field(); ?>
                                       <?php if (isset($component)) { $__componentOriginal68cb1971a2b92c9735f83359058f7108 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal68cb1971a2b92c9735f83359058f7108 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => route('logout'),'onclick' => 'event.preventDefault(); this.closest(\'form\').requestSubmit();']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('logout')),'onclick' => 'event.preventDefault(); this.closest(\'form\').requestSubmit();']); ?>
                                            <?php echo e(__('Log Out')); ?>

                                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $attributes = $__attributesOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__attributesOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $component = $__componentOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__componentOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
                                    </form>
                                 <?php $__env->endSlot(); ?>
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe)): ?>
<?php $attributes = $__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe; ?>
<?php unset($__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldf8083d4a852c446488d8d384bbc7cbe)): ?>
<?php $component = $__componentOriginaldf8083d4a852c446488d8d384bbc7cbe; ?>
<?php unset($__componentOriginaldf8083d4a852c446488d8d384bbc7cbe); ?>
<?php endif; ?>
                        </div>
                    </div>
                </div>
            </header>
            <?php endif; ?>

            
            <main class="flex-1">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <?php echo $__env->yieldContent('content'); ?>
                </div>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.tailwindcss.min.js"></script>

    
    <?php if(file_exists(public_path('mix-manifest.json'))): ?>
        <script src="<?php echo e(mix('js/app.js')); ?>"></script>
    <?php else: ?>
        <?php echo app('Illuminate\Foundation\Vite')(['resources/js/app.js']); ?>
    <?php endif; ?>

    
    <script>
        window.FLASH_MESSAGES = {
            <?php if(session('status')): ?> status: <?php echo json_encode(session('status'), 15, 512) ?>, <?php endif; ?>
            <?php if(session('success')): ?> success: <?php echo json_encode(session('success'), 15, 512) ?>, <?php endif; ?>
            <?php if(session('error')): ?> error: <?php echo json_encode(session('error'), 15, 512) ?>, <?php endif; ?>
            <?php if(session('warning')): ?> warning: <?php echo json_encode(session('warning'), 15, 512) ?>, <?php endif; ?>
            <?php if(session('info')): ?> info: <?php echo json_encode(session('info'), 15, 512) ?>, <?php endif; ?>
        };
    </script>

    
    <script>
        (function(){
            if (typeof Alpine === 'undefined') {
                var s = document.createElement('script');
                s.src = 'https://unpkg.com/alpinejs@3.12.0/dist/cdn.min.js';
                s.defer = true;
                document.head.appendChild(s);
            }
        })();
    </script>

    
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // initialize all selects with class .tom-select
            document.querySelectorAll('select.tom-select').forEach(function (el) {
                // avoid double initialization
                if (el.tomselect) return;
                new TomSelect(el, {
                    create: false,
                    sortField: {field: "text", direction: "asc"},
                    maxOptions: 100,
                });
            });
        });
    </script>
    
    <script>
        // Prevent installing handler multiple times (e.g., when layout is included twice)
        if (!window._dataHrefHandlerInstalled) {
            window._dataHrefHandlerInstalled = true;
            document.addEventListener('click', function(e) {
                const row = e.target.closest && e.target.closest('tr[data-href]');
                if (!row) return;
                // don't navigate when clicking interactive elements inside the row
                if (e.target.closest && (e.target.closest('a') || e.target.closest('button') || e.target.closest('form'))) return;
                const href = row.getAttribute('data-href');
                if (href) window.location = href;
            });
        }
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /Users/mymac/ardhi/project/rapi-app/resources/views/layouts/app.blade.php ENDPATH**/ ?>