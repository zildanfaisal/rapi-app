<aside :class="sidebarCollapsed ? 'w-20' : 'w-64'" class="hidden md:block bg-gradient-to-b from-purple-700 via-purple-600 to-blue-400 text-white shadow-lg transition-all duration-200">
    <div class="h-full flex flex-col">
        <div class="flex items-center justify-between p-4 border-b border-white/10">
            <div class="flex items-center gap-3 flex-1">
                <div x-show="!sidebarCollapsed" class="leading-tight flex-1">
                    <div class="text-sm font-semibold">RAPI</div>
                    <div class="text-sm font-semibold">Plafon</div>
                </div>
            </div>
        </div>
        <nav class="p-4 flex-1 overflow-auto">
            <ul class="space-y-2">

                <!-- Dashboard -->
                <li>
                    <a href="{{ route('dashboard') }}" title="Dashboard" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10" :class="sidebarCollapsed ? 'justify-center' : ''">
                        <span class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                <path d="M11.47 3.841a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 0 1.06-1.061l-8.689-8.69a2.25 2.25 0 0 0-3.182 0l-8.69 8.69a.75.75 0 1 0 1.061 1.06l8.69-8.689Z" />
                                <path d="m12 5.432 8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 0 1-.75-.75v-4.5a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75V21a.75.75 0 0 1-.75.75H5.625a1.875 1.875 0 0 1-1.875-1.875v-6.198a2.29 2.29 0 0 0 .091-.086L12 5.432Z" />
                            </svg>
                        </span>
                        <span x-show="!sidebarCollapsed" class="ms-2">Dashboard</span>
                    </a>
                </li>

                <!-- Users (permission-based) -->
                @can('users.view')
                <li>
                    <a href="{{ route('users.index') }}" title="User" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10" :class="sidebarCollapsed ? 'justify-center' : ''">
                        <span class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                <path d="M11.7 2.805a.75.75 0 0 1 .6 0A60.65 60.65 0 0 1 22.83 8.72a.75.75 0 0 1-.231 1.337 49.948 49.948 0 0 0-9.902 3.912l-.003.002c-.114.06-.227.119-.34.18a.75.75 0 0 1-.707 0A50.88 50.88 0 0 0 7.5 12.173v-.224c0-.131.067-.248.172-.311a54.615 54.615 0 0 1 4.653-2.52.75.75 0 0 0-.65-1.352 56.123 56.123 0 0 0-4.78 2.589 1.858 1.858 0 0 0-.859 1.228 49.803 49.803 0 0 0-4.634-1.527.75.75 0 0 1-.231-1.337A60.653 60.653 0 0 1 11.7 2.805Z" />
                                <path d="M13.06 15.473a48.45 48.45 0 0 1 7.666-3.282c.134 1.414.22 2.843.255 4.284a.75.75 0 0 1-.46.711 47.87 47.87 0 0 0-8.105 4.342.75.75 0 0 1-.832 0 47.87 47.87 0 0 0-8.104-4.342.75.75 0 0 1-.461-.71c.035-1.442.121-2.87.255-4.286.921.304 1.83.634 2.726.99v1.27a1.5 1.5 0 0 0-.14 2.508c-.09.38-.222.753-.397 1.11.452.213.901.434 1.346.66a6.727 6.727 0 0 0 .551-1.607 1.5 1.5 0 0 0 .14-2.67v-.645a48.549 48.549 0 0 1 3.44 1.667 2.25 2.25 0 0 0 2.12 0Z" />
                                <path d="M4.462 19.462c.42-.419.753-.89 1-1.395.453.214.902.435 1.347.662a6.742 6.742 0 0 1-1.286 1.794.75.75 0 0 1-1.06-1.06Z" />
                            </svg>
                        </span>
                        <span x-show="!sidebarCollapsed" class="ms-2">Users</span>
                    </a>
                </li>
                @endcan

                <!-- Roles & Permissions (permission-based) -->
                @can('roles.view')
                <li x-data="{ open: false }" class="relative">
                    <button @click.prevent="open = !open" :aria-expanded="open.toString()" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10" :class="sidebarCollapsed ? 'justify-center' : ''">
                        <span class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                <path fill-rule="evenodd" d="M5.25 3A2.25 2.25 0 0 0 3 5.25v13.5A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V5.25A2.25 2.25 0 0 0 18.75 3H5.25Zm1.5 3a.75.75 0 0 0-.75.75v9a.75.75 0 0 0 .75.75h10.5a.75.75 0 0 0 .75-.75v-9a.75.75 0 0 0-.75-.75H6.75Z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span x-show="!sidebarCollapsed" class="ms-2 flex-1 text-start">Roles & Permissions</span>
                        <svg x-show="!sidebarCollapsed" :class="open ? 'rotate-90' : ''" class="h-4 w-4 transform transition-transform text-white/80" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <ul x-cloak x-show="open" x-transition class="mt-2 space-y-1 ps-10" style="display:none;">
                        @can('roles.view')
                        <li>
                            <a href="{{ route('roles.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Roles</a>
                        </li>
                        @endcan
                        @can('permissions.view')
                        <li>
                            <a href="{{ route('permissions.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Permissions</a>
                        </li>
                        @endcan
                        
                    </ul>
                </li>
                @endcan

                <!-- Master Products -->
                @can('products.view')
               <li x-data="{ open: {{ request()->routeIs('products.*') || request()->routeIs('product-batches.*')  ? 'true' : 'false' }} }" class="relative">
                <button @click.prevent="open = !open"
                        :aria-expanded="open.toString()"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('products.*') || request()->routeIs('product-batches.*')  ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-50' }}"
                        :class="sidebarCollapsed ? 'justify-center' : ''">
                    <span class="w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200 {{ request()->routeIs('products.*') || request()->routeIs('product-batches.*')  ? 'bg-blue-100' : 'bg-slate-100 group-hover:bg-slate-200' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                    </span>
                    <span x-show="!sidebarCollapsed" class="flex-1 text-start font-medium">Master Produk</span>
                    <svg x-show="!sidebarCollapsed"
                        :class="open ? 'rotate-90' : ''"
                        class="h-4 w-4 transform transition-transform text-slate-600"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                  <ul x-cloak x-show="open" x-transition class="mt-1.5 space-y-1 pl-12" style="display:none;">
                        @can('products.view')
                        <li>
                            <a href="{{ route('products.index') }}"
                            class="block px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('products.index') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
                                Produk
                            </a>
                        </li>
                        @endcan
                        @can('product-batches.view')
                        <li>
                            <a href="{{ route('product-batches.index') }}" class="block px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('product-batches.index') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
                                Batch Produk</a>
                        </li>
                        @endcan
                        <li>
                            <button 
                                @click.prevent="$dispatch('open-batch-report')"
                                class="block px-3 py-2 rounded-lg text-sm transition-all duration-200 text-slate-600 hover:bg-slate-50">
                                Laporan Batch Produk
                            </button>
                        </li>

                    </ul>
                </li>
                @endcan

               <!-- Master Penjualan -->
                <li x-data="{ open: false }" class="relative">
                    <button @click.prevent="open = !open"
                            :aria-expanded="open.toString()"
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group text-slate-700 hover:bg-slate-50"
                            :class="sidebarCollapsed ? 'justify-center' : ''">
                        <span class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center group-hover:bg-slate-200 transition-all duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                            </svg>
                        </span>
                        <span x-show="!sidebarCollapsed" class="ms-2 flex-1 text-start">Master Products</span>
                        <!-- chevron shown only when sidebar not collapsed -->
                        <svg x-show="!sidebarCollapsed" :class="open ? 'rotate-90' : ''" class="h-4 w-4 transform transition-transform text-white/80" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <!-- submenu (desktop) -->
                    <ul x-cloak x-show="open" x-transition class="mt-2 space-y-1 ps-10" style="display:none;">
                     @can('products.view')    
                        <li>
                            <a href="{{ route('products.index') }}" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Products</a>
                        </li>
                        @endcan
                         @can('products.view') 
                        <li>
                            <a href="#" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Product Batches</a>
                        </li>
                        @endcan
                         @can('products.view') 
                        <li>
                            <a href="#" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Laporan Product Batches</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan

                <!-- Master Penjualan -->
                <li x-data="{ open: false }" class="relative">
                    <button @click.prevent="open = !open" :aria-expanded="open.toString()" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10" :class="sidebarCollapsed ? 'justify-center' : ''">
                        <span class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v3a3 3 0 0 0-3 3v6.75a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3v-6.75a3 3 0 0 0-3-3v-3c0-2.9-2.35-5.25-5.25-5.25Zm3.75 8.25v-3a3.75 3.75 0 1 0-7.5 0v3h7.5Z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span x-show="!sidebarCollapsed" class="ms-2 flex-1 text-start">Master Penjualan</span>
                        <!-- chevron shown only when sidebar not collapsed -->
                        <svg x-show="!sidebarCollapsed" :class="open ? 'rotate-90' : ''" class="h-4 w-4 transform transition-transform text-white/80" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <!-- submenu (desktop) -->
                    <ul x-cloak x-show="open" x-transition class="mt-2 space-y-1 ps-10" style="display:none;">
                        <li>
                            <a href="#" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Penjualan</a>
                        </li>
                        <li>
                            <a href="#" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Surat Jalan</a>
                        </li>
                        <li>
                            <a href="#" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">History Transaksi</a>
                        </li>
                    </ul>
                </li>

                <!-- Master Finance -->
                <li x-data="{ open: false }" class="relative">
                    <button @click.prevent="open = !open" :aria-expanded="open.toString()" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10" :class="sidebarCollapsed ? 'justify-center' : ''">
                        <span class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v3a3 3 0 0 0-3 3v6.75a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3v-6.75a3 3 0 0 0-3-3v-3c0-2.9-2.35-5.25-5.25-5.25Zm3.75 8.25v-3a3.75 3.75 0 1 0-7.5 0v3h7.5Z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span x-show="!sidebarCollapsed" class="ms-2 flex-1 text-start">Master Finance</span>
                        <!-- chevron shown only when sidebar not collapsed -->
                        <svg x-show="!sidebarCollapsed" :class="open ? 'rotate-90' : ''" class="h-4 w-4 transform transition-transform text-white/80" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <!-- submenu (desktop) -->
                    <ul x-cloak x-show="open" x-transition class="mt-2 space-y-1 ps-10" style="display:none;">
                        <li>
                            <a href="#" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Budget Target</a>
                        </li>
                        <li>
                            <a href="#" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Input Finance</a>
                        </li>
                        <li>
                            <a href="#" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">History Finance</a>
                        </li>
                    </ul>
                </li>

                <!-- Customers -->
                <li>
                    <a href="{{ route('customers.index') }}" title="Customers" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10" :class="sidebarCollapsed ? 'justify-center' : ''">
                        <span class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span x-show="!sidebarCollapsed" class="ms-2">Customers</span>
                    </a>
                </li>

                <!-- Logout -->
                <li>
                    <form action="{{ route('logout') }}" method="POST" data-logout-confirm>
                        @csrf
                        <button type="submit" title="Logout" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10" :class="sidebarCollapsed ? 'justify-center' : ''">
                            <span class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                  <path fill-rule="evenodd" d="M16.5 3.75a1.5 1.5 0 0 1 1.5 1.5v13.5a1.5 1.5 0 0 1-1.5 1.5h-6a1.5 1.5 0 0 1-1.5-1.5V15a.75.75 0 0 0-1.5 0v3.75a3 3 0 0 0 3 3h6a3 3 0 0 0 3-3V5.25a3 3 0 0 0-3-3h-6a3 3 0 0 0-3 3V9A.75.75 0 1 0 9 9V5.25a1.5 1.5 0 0 1 1.5-1.5h6ZM5.78 8.47a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 0 0 0 1.06l3 3a.75.75 0 0 0 1.06-1.06l-1.72-1.72H15a.75.75 0 0 0 0-1.5H4.06l1.72-1.72a.75.75 0 0 0 0-1.06Z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            <span x-show="!sidebarCollapsed" class="ms-2">Logout</span>
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>
<div 
    x-data="{ open: false }"
    x-on:open-batch-report.window="open = true"
    x-show="open"
    x-cloak
    class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">

    <div class="bg-white rounded-lg w-full max-w-md p-6 shadow-lg">
        <h2 class="text-lg font-semibold mb-4">Filter Laporan Batch Produk</h2>

        <form action="{{ route('product-batches.report') }}" method="GET" target="_blank">
            <div class="mb-4">
                <label class="block text-sm font-medium">Jenis Filter</label>
                <select name="filter" class="mt-1 w-full border rounded p-2" required>
                    <option value="all">Semua</option>
                    <option value="tanggal_masuk">Tanggal Masuk</option>
                    <option value="tanggal_expired">Tanggal Expired</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Dari Tanggal</label>
                <input type="month" name="start_date" class="mt-1 w-full border rounded p-2">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Sampai Tanggal</label>
                <input type="month" name="end_date" class="mt-1 w-full border rounded p-2">
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" @click="open = false" class="px-4 py-2 bg-red-500 text-white rounded">
                    Batal
                </button>

                <!-- Tombol Reset -->
                <button 
                    type="reset" 
                    class="px-4 py-2 bg-yellow-500 text-white rounded">
                    Reset
                </button>

                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
                    Generate PDF
                </button>
            </div>
        </form>
    </div>
</div>
