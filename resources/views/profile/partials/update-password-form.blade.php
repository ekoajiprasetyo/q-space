<section>
    <header>
        <h2 class="text-lg font-bold text-slate-800">
            Perbarui Password
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            Pastikan akun Anda menggunakan password yang panjang dan acak agar tetap aman.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Password Saat Ini')" class="font-bold text-slate-700" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full rounded-xl border-slate-300 focus:ring-teal-500 focus:border-teal-500" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('Password Baru')" class="font-bold text-slate-700" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full rounded-xl border-slate-300 focus:ring-teal-500 focus:border-teal-500" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Konfirmasi Password')" class="font-bold text-slate-700" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full rounded-xl border-slate-300 focus:ring-teal-500 focus:border-teal-500" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="bg-teal-600 hover:bg-teal-700 rounded-full px-8 py-3 shadow-lg shadow-teal-500/30 font-bold transition-all hover:-translate-y-0.5">Simpan Password</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-teal-600 font-bold flex items-center gap-2"
                ><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Tersimpan.</p>
            @endif
        </div>
    </form>
</section>
