@extends('layouts.app')

@section('title', __('Dashboard'))

@section('header')
    <h2 class="text-xl font-semibold text-gray-800">{{ __('Dashboard') }}</h2>
@endsection

@section('content')
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <div class="flex items-center gap-6">
                        <div class="space-y-1">
                            <h3 class="text-2xl font-semibold text-gray-800">
                                {{ $user->name }}
                            </h3>

                            <p class="text-gray-600">
                                {{ $user->email }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="p-6 bg-white shadow rounded-lg">
                    <span class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center">
                         <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-16">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                    </svg>


                    </span>
                    <h4 class="text-lg font-semibold text-gray-700">
                        Produk
                    </h4>
                    <p class="text-3xl font-bold text-indigo-600 mt-2">
                        12
                    </p>
                </div>

                <div class="p-6 bg-white shadow rounded-lg">
                    <span class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-16">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>

                    </span>
                    <h4 class="text-lg font-semibold text-gray-700">
                        Pelanggan
                    </h4>
                    <p class="text-3xl font-bold text-indigo-600 mt-2">
                        12
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
