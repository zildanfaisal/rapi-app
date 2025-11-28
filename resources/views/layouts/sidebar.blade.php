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

                <!-- Users -->
                <li>
                    <a href="#" title="User" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10" :class="sidebarCollapsed ? 'justify-center' : ''">
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

                <!-- Master Products -->
                <li x-data="{ open: false }" class="relative">
                    <button @click.prevent="open = !open" :aria-expanded="open.toString()" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10" :class="sidebarCollapsed ? 'justify-center' : ''">
                        <span class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v3a3 3 0 0 0-3 3v6.75a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3v-6.75a3 3 0 0 0-3-3v-3c0-2.9-2.35-5.25-5.25-5.25Zm3.75 8.25v-3a3.75 3.75 0 1 0-7.5 0v3h7.5Z" clip-rule="evenodd" />
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
                        <li>
                            <a href="#" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Products</a>
                        </li>
                        <li>
                            <a href="#" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Product Batches</a>
                        </li>
                        <li>
                            <a href="#" class="block px-3 py-2 rounded-lg hover:bg-white/10 text-sm">Laporan Product Batches</a>
                        </li>
                    </ul>
                </li>

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