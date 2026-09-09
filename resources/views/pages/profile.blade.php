@extends('layouts.app')

@section('content')

@php
$user = auth()->user() ?? \App\Models\User::first();

$avatarUrl = $user->avatar
? asset('storage/' . $user->avatar)
: 'https://ui-avatars.com/api/?name=' . urlencode($user->name ?? 'User') . '&background=0D8ABC&color=fff&size=128';
@endphp

<div x-data="{
    isProfileInfoModal: false,
    isProfileAddressModal: false,
    previewAvatar(event) {
        const file = event.target.files[0];

        if (file) {
            this.$refs.avatarPreview.src = URL.createObjectURL(file);
        }
    }
}">
    <x-common.page-breadcrumb pageTitle="User Profile" />

    <div class="rounded-2xl border border-gray-200 bg-white p-5 lg:p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        <h3 class="mb-5 text-lg font-semibold text-gray-800 lg:mb-7 dark:text-white/90">
            Profil Saya
        </h3>

        <!-- Info -->
        <div class="mb-6 rounded-2xl border border-gray-200 p-5 lg:p-6 dark:border-gray-800">
            <div class="flex flex-col gap-5 sm:flex-row xl:gap-10">
                <div class="flex-1">
                    <div class="mb-6 flex flex-col gap-5 sm:flex-row xl:items-center xl:justify-between">
                        <div class="flex w-full flex-col items-start gap-6 sm:flex-row sm:items-center">
                            <div class="relative h-24 w-24 overflow-hidden rounded-full border-2 border-gray-200 dark:border-gray-700">
                                <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="h-full w-full object-cover" />
                            </div>
                            <div class="text-start">
                                <h4 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white/90">
                                    {{ $user->name ?? 'N/A' }}
                                </h4>
                            </div>
                        </div>
                    </div>
                    <div
                        class="relative grid max-w-4xl grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4 xl:gap-x-11 xl:gap-y-7">
                        <div>
                            <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">
                                Nama Lengkap
                            </p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                {{ $user->name ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">
                                Role Akses
                            </p>
                            <div>
                                @if(($user->role ?? '') === 'superadmin')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400 uppercase">
                                    SUPERADMIN
                                </span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 uppercase">
                                    ADMIN
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="hidden xl:block"></div>
                        <div class="hidden xl:block"></div>
                        <div>
                            <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">
                                Email Address
                            </p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                {{ $user->email ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">
                                No. Telepon
                            </p>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                {{ $user->phone ?? '-' }}
                            </p>
                        </div>


                    </div>
                </div>
                <div>
                    <button @click="isProfileInfoModal = true"
                        class="shadow-theme-xs flex h-10 w-full items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-800 lg:inline-flex lg:w-auto dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                        <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z"
                                fill="" />
                        </svg>
                        Edit
                    </button>
                </div>
            </div>
        </div>


    </div>

    <!-- BEGIN MODAL: Profile Info -->
    <div x-show="isProfileInfoModal" x-cloak
        class="fixed inset-0 z-99999 flex items-center justify-center overflow-y-auto p-5">
        <div class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"></div>
        <div @click.outside="isProfileInfoModal = false"
            class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 lg:p-11 dark:bg-gray-900">
            <!-- close btn -->
            <button @click="isProfileInfoModal = false"
                class="transition-color absolute top-5 ltr:right-5 rtl:left-5 z-999 flex h-11 w-11 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:bg-gray-700 dark:bg-white/[0.05] dark:text-gray-400 dark:hover:bg-white/[0.07] dark:hover:text-gray-300">
                <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M6.04289 16.5418C5.65237 16.9323 5.65237 17.5655 6.04289 17.956C6.43342 18.3465 7.06658 18.3465 7.45711 17.956L11.9987 13.4144L16.5408 17.9565C16.9313 18.347 17.5645 18.347 17.955 17.9565C18.3455 17.566 18.3455 16.9328 17.955 16.5423L13.4129 12.0002L17.955 7.45808C18.3455 7.06756 18.3455 6.43439 17.955 6.04387C17.5645 5.65335 16.9313 5.65335 16.5408 6.04387L11.9987 10.586L7.45711 6.04439C7.06658 5.65386 6.43342 5.65386 6.04289 6.04439C5.65237 6.43491 5.65237 7.06808 6.04289 7.4586L10.5845 12.0002L6.04289 16.5418Z"
                        fill="" />
                </svg>
            </button>
            <div class="px-2 ltr:pr-14 rtl:pl-14">
                <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
                    Edit Profil Saya
                </h4>
                <p class="mb-6 text-sm text-gray-500 lg:mb-7 dark:text-gray-400">
                    Update detail profil anda.
                </p>
            </div>
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="flex flex-col">
                @csrf
                @method('PUT')

                <div class="custom-scrollbar h-[450px] overflow-y-auto px-2">
                    <!-- Change Profile Picture -->
                    <div>
                        <h4 class="mb-6 text-lg font-medium text-gray-800 dark:text-white/90">
                            Change Profile Picture
                        </h4>
                        <div class="mb-6 flex max-w-sm items-center gap-6 lg:pr-5">
                            <div class="relative size-20 shrink-0 rounded-full sm:size-25">
                                <img x-ref="avatarPreview" src="{{ $avatarUrl }}" alt="Profile Picture"
                                    class="size-20 rounded-full object-cover sm:size-25" />
                                <label for="avatar"
                                    class="absolute right-0 bottom-0 flex size-8 cursor-pointer items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400">
                                    <input type="file" name="avatar" id="avatar" class="hidden" accept="image/*"
                                        @change="previewAvatar($event)" />
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M12.6731 3.41904C12.4371 3.10308 12.0659 2.91699 11.6715 2.91699H8.32809C7.93374 2.91699 7.56252 3.10308 7.32656 3.41904L6.83173 4.08164C6.59576 4.3976 6.22454 4.58369 5.83019 4.58369H3.5415C2.85115 4.58369 2.2915 5.14333 2.2915 5.83369V14.3754C2.2915 15.0657 2.85115 15.6254 3.5415 15.6254H16.4582C17.1485 15.6254 17.7082 15.0657 17.7082 14.3754V5.83369C17.7082 5.14333 17.1485 4.58369 16.4582 4.58369H14.1694C13.7751 4.58369 13.4039 4.3976 13.1679 4.08164L12.6731 3.41904Z"
                                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path
                                            d="M13.3332 9.79362C13.3332 11.6346 11.8408 13.127 9.99984 13.127C8.15889 13.127 6.6665 11.6346 6.6665 9.79362C6.6665 7.95267 8.15889 6.46029 9.99984 6.46029C11.8408 6.46029 13.3332 7.95267 13.3332 9.79362Z"
                                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                </label>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Unggah foto dalam format JPEG, PNG, atau JPG (Maks. 2MB).
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Personal Information Inputs -->
                    <div class="mb-6">
                        <h4 class="mb-5 text-lg font-medium text-gray-800 lg:mb-6 dark:text-white/90">
                            Personal Information
                        </h4>
                        <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
                            <!-- Nama Lengkap -->
                            <div>
                                <label class="mb-2 block text-xs font-medium text-gray-500 dark:text-gray-400">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required
                                    class="h-[42px] w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-blue-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                            </div>

                            <!-- Email Address -->
                            <div>
                                <label class="mb-2 block text-xs font-medium text-gray-500 dark:text-gray-400">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required
                                    class="h-[42px] w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-blue-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                            </div>

                            <!-- No. Telepon -->
                            <div class="lg:col-span-2">
                                <label class="mb-2 block text-xs font-medium text-gray-500 dark:text-gray-400">
                                    No. Telepon
                                </label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}" placeholder="Contoh: +62 812 3456 7890"
                                    class="h-[42px] w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-blue-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex items-center gap-3 px-2 lg:justify-end">
                    <button @click="isProfileInfoModal = false" type="button"
                        class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                        Close
                    </button>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 flex w-full justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-white sm:w-auto">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- END MODAL: Profile Info -->

</div>
@endsection