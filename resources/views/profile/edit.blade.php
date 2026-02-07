<x-app-layout>
    <div class="min-h-screen bg-gray-50/50 pb-28 md:pb-12">
        <!-- Modern Hero Section -->
        <div class="relative bg-slate-900 pb-32 pt-32 overflow-hidden">
            <!-- decorative background -->
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-slate-800 via-slate-900 to-slate-900 opacity-80"></div>
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-teal-500/20 blur-3xl mix-blend-screen animate-pulse"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-purple-500/20 blur-3xl mix-blend-screen"></div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <!-- Back Button -->
                <div class="mb-8">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-full backdrop-blur-sm transition-all text-sm font-bold border border-white/5 group">
                        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Kembali ke Dashboard
                    </a>
                </div>

                <div class="flex items-center gap-6">
                    <!-- Profile Avatar -->
                    <div class="relative group">
                         <div class="w-24 h-24 rounded-[2rem] bg-gradient-to-br from-teal-400 to-cyan-500 flex items-center justify-center text-white text-4xl font-bold shadow-2xl shadow-teal-500/30 transform group-hover:scale-105 transition-all duration-300 ring-4 ring-white/10 backdrop-blur-sm">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="absolute -bottom-2 -right-2 bg-green-500 w-6 h-6 rounded-full border-4 border-slate-900"></div>
                    </div>
                    
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold text-white tracking-tight mb-1">
                            {{ Auth::user()->name }}
                        </h1>
                        <div class="flex items-center gap-3 text-slate-400">
                             <div class="flex items-center gap-1.5 px-3 py-1 bg-white/5 rounded-full backdrop-blur border border-white/5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                <span class="text-sm font-medium">{{ Auth::user()->email }}</span>
                             </div>
                             <div class="hidden md:flex items-center gap-1.5 px-3 py-1 bg-teal-500/10 text-teal-400 rounded-full border border-teal-500/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-teal-400"></span>
                                <span class="text-sm font-bold uppercase tracking-wider text-xs">{{ Auth::user()->role === 'guru' ? 'Guru' : 'Siswa' }}</span>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overlapping Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 relative z-20">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Left Sidebar (Navigation) -->
                <div class="lg:col-span-4 lg:sticky lg:top-32 self-start space-y-6">
                     <!-- Q-Link Integration Card -->
                    <div class="bg-white rounded-[2rem] p-6 shadow-xl shadow-slate-200/50 ring-1 ring-slate-100 overflow-hidden relative">
                         <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-teal-50 to-transparent rounded-bl-[4rem] -mr-8 -mt-8 opacity-50"></div>
                         
                         <h3 class="font-bold text-lg text-slate-800 mb-1 relative z-10">Keamanan & Privasi</h3>
                         <p class="text-sm text-slate-500 mb-6 relative z-10">Pusat pengaturan akun Q-Store Anda.</p>
                         
                         <div class="space-y-1 relative z-10" id="sidebar-nav">
                              <a href="#profile-info" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition-all text-slate-600 hover:bg-slate-50" data-target="profile-info">
                                  <div class="icon-container w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shadow-sm transition-colors">
                                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                  </div>
                                  Informasi Profil
                              </a>
                              <a href="#update-password" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition-all text-slate-600 hover:bg-slate-50" data-target="update-password">
                                  <div class="icon-container w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shadow-sm transition-colors">
                                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                  </div>
                                  Ganti Password
                              </a>
                              <a href="#delete-account" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition-all text-slate-600 hover:bg-red-50 hover:text-red-600" data-target="delete-account">
                                  <div class="icon-container w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shadow-sm transition-colors group-hover:bg-red-100 group-hover:text-red-500">
                                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                  </div>
                                  Hapus Akun
                              </a>
                         </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="lg:col-span-8 space-y-8">
                     <!-- Update Profile Card -->
                    <div id="profile-info" class="scroll-section bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 ring-1 ring-slate-100 scroll-mt-32">
                        <div class="max-w-xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <!-- Update Password Card -->
                    <div id="update-password" class="scroll-section bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 ring-1 ring-slate-100 scroll-mt-32">
                         <div class="max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <!-- Delete Account Card -->
                    <div id="delete-account" class="scroll-section bg-red-50/30 rounded-[2.5rem] p-8 shadow-none ring-1 ring-red-100 border border-red-100/50 scroll-mt-32">
                         <div class="max-w-xl">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scrollspy Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sections = document.querySelectorAll('.scroll-section');
            const navLinks = document.querySelectorAll('.nav-link');

            function setActiveLink(id) {
                navLinks.forEach(link => {
                    const target = link.getAttribute('data-target');
                    const iconContainer = link.querySelector('.icon-container');
                    
                    if (target === id) {
                        // Active State
                        if (target === 'delete-account') {
                             link.classList.remove('text-slate-600', 'hover:bg-slate-50');
                             link.classList.add('bg-red-50', 'text-red-600');
                             iconContainer.classList.remove('bg-slate-100', 'text-slate-500');
                             iconContainer.classList.add('bg-red-100', 'text-red-500');
                        } else {
                            link.classList.remove('text-slate-600', 'hover:bg-slate-50');
                            link.classList.add('bg-teal-50', 'text-teal-700');
                            iconContainer.classList.remove('bg-slate-100', 'text-slate-500');
                            iconContainer.classList.add('bg-white', 'text-teal-600');
                        }
                    } else {
                        // Inactive State
                        link.classList.add('text-slate-600', 'hover:bg-slate-50');
                        link.classList.remove('bg-teal-50', 'text-teal-700', 'bg-red-50', 'text-red-600');
                        iconContainer.classList.add('bg-slate-100', 'text-slate-500');
                        iconContainer.classList.remove('bg-white', 'text-teal-600', 'bg-red-100', 'text-red-500');
                    }
                });
            }

            // Intersection Observer
            const observerOptions = {
                root: null,
                rootMargin: '-20% 0px -60% 0px', // Trigger when section is near top
                threshold: 0
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        setActiveLink(entry.target.id);
                    }
                });
            }, observerOptions);

            sections.forEach(section => observer.observe(section));
        });
    </script>
            </div>
        </div>
    </div>
</x-app-layout>
