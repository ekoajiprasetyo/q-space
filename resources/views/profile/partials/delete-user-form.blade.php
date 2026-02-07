<section class="space-y-6">
    <header>
        <h2 class="sr-only">Delete Account</h2>
        <div class="flex items-start gap-4 p-4 rounded-2xl bg-red-100/50 border border-red-200">
             <div class="p-2 bg-red-100 text-red-600 rounded-lg shrink-0">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
             </div>
             <div>
                 <h4 class="font-bold text-red-800">Zona Bahaya</h4>
                 <p class="text-sm text-red-600/80 mt-1">Akun yang dihapus tidak dapat dikembalikan. Pastikan Anda telah mengunduh semua data penting sebelum melanjutkan.</p>
             </div>
        </div>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="rounded-full px-6 py-3 font-bold shadow-lg shadow-red-500/20"
    >Hapus Akun Saya</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable maxWidth="md">
        <form method="post" action="{{ route('profile.destroy') }}" class="p-8 text-center">
            @csrf
            @method('delete')

            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6 text-red-600 animate-bounce">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </div>

            <h2 class="text-2xl font-bold text-slate-800 mb-2">
                Hapus Akun Permanen?
            </h2>

            <p class="text-sm text-slate-500 mb-8 max-w-xs mx-auto">
                Tindakan ini tidak dapat dibatalkan. Masukkan password Anda untuk konfirmasi penghapusan.
            </p>

            <div class="mt-6 text-left">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="block w-full rounded-xl border-slate-300 focus:border-red-500 focus:ring-red-500"
                    placeholder="Masukkan Password Anda"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-8 flex justify-center gap-3">
                <x-secondary-button x-on:click="$dispatch('close')" class="rounded-full px-6 py-3">
                    Batal
                </x-secondary-button>

                <x-danger-button class="rounded-full px-6 py-3 shadow-xl shadow-red-500/20">
                    Ya, Hapus Akun
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
