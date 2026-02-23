@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')

    {{-- ══ PAGE HEADER ══ --}}
    <div class="flex items-end justify-between mb-8">
        <div>
            <p class="font-sans text-[0.58rem] font-semibold tracking-[0.35em] uppercase text-label mb-1">
                Administrasi
            </p>
            <h2 class="font-serif text-ink text-3xl font-normal leading-none">
                Manajemen Pengguna
            </h2>
            <div class="mt-3 h-px w-10 bg-rule"></div>
        </div>

        <button
            onclick="openModal()"
            class="relative overflow-hidden flex items-center gap-2 bg-espresso px-5 py-3
                   font-sans text-[0.62rem] font-semibold tracking-[0.2em] uppercase text-paper
                   transition-colors duration-200 hover:bg-ink active:scale-[0.99]
                   after:content-[''] after:absolute after:inset-0 after:bg-white/[0.06]
                   after:-translate-x-full after:transition-transform after:duration-300
                   hover:after:translate-x-0"
        >
            <i class="fas fa-plus text-xs"></i>
            <span>Tambah Pengguna</span>
        </button>
    </div>

    {{-- ══ SUCCESS ALERT ══ --}}
    @if(session('success'))
        <div class="flex items-center justify-between border-l-2 border-espresso bg-cream px-4 py-3 mb-6">
            <span class="font-sans text-[0.75rem] tracking-wide text-ink">{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-label hover:text-ink transition-colors ml-4">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    @endif

    {{-- ══ TABLE ══ --}}
    <div class="bg-paper border border-rule overflow-hidden">
        <table class="min-w-full">
            <thead>
                <tr class="border-b border-rule bg-cream">
                    <th class="px-5 py-3.5 text-left font-sans text-[0.55rem] font-semibold tracking-[0.25em] uppercase text-label">
                        Username
                    </th>
                    <th class="px-5 py-3.5 text-left font-sans text-[0.55rem] font-semibold tracking-[0.25em] uppercase text-label">
                        Level
                    </th>
                    <th class="px-5 py-3.5 text-left font-sans text-[0.55rem] font-semibold tracking-[0.25em] uppercase text-label">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rule">
                @forelse($users as $user)
                    <tr class="hover:bg-cream/40 transition-colors duration-100">

                        {{-- Username --}}
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-espresso flex items-center justify-center flex-shrink-0">
                                    @php
                                        $icon = match(strtolower($user->level)) {
                                            'admin'    => 'user-shield',
                                            'petugas'  => 'user-cog',
                                            default    => 'user',
                                        };
                                    @endphp
                                    <i class="fas fa-{{ $icon }} text-paper text-[0.6rem]"></i>
                                </div>
                                <span class="font-sans text-[0.82rem] font-medium text-ink">
                                    {{ $user->username }}
                                </span>
                            </div>
                        </td>

                        {{-- Level Badge --}}
                        <td class="px-5 py-4 whitespace-nowrap">
                            @if($user->level == 'admin')
                                <span class="px-2.5 py-1 border border-espresso/30 bg-espresso/5 font-sans text-[0.52rem] font-semibold tracking-[0.15em] uppercase text-espresso">
                                    Admin
                                </span>
                            @elseif($user->level == 'petugas')
                                <span class="px-2.5 py-1 border border-dim/30 bg-dim/5 font-sans text-[0.52rem] font-semibold tracking-[0.15em] uppercase text-dim">
                                    Petugas
                                </span>
                            @else
                                <span class="px-2.5 py-1 border border-rule bg-cream font-sans text-[0.52rem] font-semibold tracking-[0.15em] uppercase text-label">
                                    Peminjam
                                </span>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <button
                                    onclick="editUser({{ $user->user_id }}, '{{ $user->username }}', '{{ $user->level }}')"
                                    class="flex items-center gap-1.5 border border-rule px-3 py-1.5
                                           font-sans text-[0.58rem] font-semibold tracking-[0.15em] uppercase text-label
                                           hover:border-espresso hover:text-espresso transition-all duration-150"
                                >
                                    <i class="fas fa-edit text-[0.6rem]"></i>
                                    <span>Edit</span>
                                </button>

                                <form action="{{ route('users.destroy', $user->user_id) }}" method="POST"
                                    class="inline" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-7 h-7 flex items-center justify-center border border-rule text-ghost
                                               hover:border-espresso hover:text-espresso transition-all duration-150">
                                        <i class="fas fa-trash text-[0.6rem]"></i>
                                    </button>
                                </form>
                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-5 py-16 text-center">
                            <div class="w-12 h-12 bg-cream border border-rule flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-users text-ghost text-base"></i>
                            </div>
                            <p class="font-serif text-ink text-lg font-normal mb-1">Belum ada data pengguna</p>
                            <p class="font-sans text-[0.7rem] text-label tracking-wide">
                                Klik tombol "Tambah Pengguna" untuk menambahkan pengguna baru.
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ══ MODAL TAMBAH / EDIT USER ══ --}}
    <div id="userModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-8"
        style="background:rgba(26,23,20,0.55)">
        <div class="relative w-full max-w-sm bg-paper border border-rule shadow-2xl flex flex-col animate-fade-up">

            {{-- Modal Header --}}
            <div class="flex items-end justify-between px-8 pt-7 pb-5 border-b border-rule">
                <div>
                    <p class="font-sans text-[0.5rem] font-semibold tracking-[0.35em] uppercase text-label mb-1">
                        Formulir
                    </p>
                    <h3 id="modalTitle" class="font-serif text-ink text-2xl font-normal leading-none">
                        Tambah Pengguna
                    </h3>
                </div>
                <button onclick="closeModal()"
                    class="w-7 h-7 flex items-center justify-center border border-rule text-ghost
                           hover:border-espresso hover:text-ink transition-all duration-150 mb-0.5">
                    <i class="fas fa-times text-[0.6rem]"></i>
                </button>
            </div>

            {{-- Modal Body --}}
            <form id="userForm" method="POST" action="{{ route('users.store') }}" class="px-8 py-6 space-y-6">
                @csrf
                <input type="hidden" id="methodField" name="_method" value="POST">
                <input type="hidden" id="userId" name="user_id">

                {{-- Username --}}
                <div class="relative">
                    <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                        Username <span class="text-espresso">*</span>
                    </label>
                    <input
                        type="text" id="username" name="username" required
                        placeholder="Masukkan username"
                        class="peer w-full bg-transparent border-b border-rule pb-2.5 pt-1 font-sans text-[0.85rem] text-ink outline-none placeholder-ghost/60 transition-colors duration-200 focus:border-ink"
                    >
                    <span class="absolute bottom-0 left-0 h-px w-0 bg-ink transition-all duration-300 peer-focus:w-full"></span>
                </div>

                {{-- Level --}}
                <div>
                    <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                        Level <span class="text-espresso">*</span>
                    </label>
                    <div class="relative">
                        <select id="level" name="level" required
                            class="w-full appearance-none bg-cream border border-rule px-3 py-2.5 font-sans text-[0.82rem] text-ink outline-none focus:border-ink transition-colors duration-200 cursor-pointer">
                            <option value="">Pilih Level</option>
                            <option value="admin">Admin</option>
                            <option value="petugas">Petugas</option>
                            <option value="peminjam">Peminjam</option>
                        </select>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-ghost text-[0.55rem] pointer-events-none"></i>
                    </div>
                </div>

                {{-- Password --}}
                <div class="relative">
                    <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                        Password
                        <span id="passwordOptional" class="normal-case tracking-normal font-normal text-ghost ml-1"></span>
                    </label>
                    <input
                        type="password" id="password" name="password" minlength="6"
                        placeholder="Minimal 6 karakter"
                        class="peer w-full bg-transparent border-b border-rule pb-2.5 pt-1 font-sans text-[0.85rem] text-ink outline-none placeholder-ghost/60 transition-colors duration-200 focus:border-ink"
                    >
                    <span class="absolute bottom-0 left-0 h-px w-0 bg-ink transition-all duration-300 peer-focus:w-full"></span>
                </div>

                {{-- Footer Buttons --}}
                <div class="flex gap-3 pt-2 border-t border-rule">
                    <button type="submit"
                        class="flex-1 bg-espresso text-paper font-sans text-[0.6rem] font-semibold tracking-[0.25em] uppercase py-3.5 hover:bg-ink transition-colors duration-200">
                        Simpan
                    </button>
                    <button type="button" onclick="closeModal()"
                        class="flex-1 border border-rule text-label font-sans text-[0.6rem] font-semibold tracking-[0.25em] uppercase py-3.5 hover:border-espresso hover:text-espresso transition-all duration-200">
                        Batal
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('userModal').classList.remove('hidden');
            document.getElementById('modalTitle').textContent = 'Tambah Pengguna';
            document.getElementById('userForm').action = '{{ route("users.store") }}';
            document.getElementById('methodField').value = 'POST';
            document.getElementById('userId').value = '';
            document.getElementById('username').value = '';
            document.getElementById('level').value = '';
            document.getElementById('password').value = '';
            document.getElementById('password').required = true;
            document.getElementById('passwordOptional').textContent = '';
        }

        function closeModal() {
            document.getElementById('userModal').classList.add('hidden');
        }

        function editUser(id, username, level) {
            document.getElementById('userModal').classList.remove('hidden');
            document.getElementById('modalTitle').textContent = 'Edit Pengguna';
            document.getElementById('userForm').action = '/users/' + id;
            document.getElementById('methodField').value = 'PUT';
            document.getElementById('userId').value = id;
            document.getElementById('username').value = username;
            document.getElementById('level').value = level;
            document.getElementById('password').value = '';
            document.getElementById('password').required = false;
            document.getElementById('passwordOptional').textContent = '(kosongkan jika tidak diubah)';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('userModal');
            if (event.target == modal) closeModal();
        }
    </script>

@endsection