<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Sistem Peminjaman Alat</title>

    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        serif: ['Cormorant Garamond', 'Georgia', 'serif'],
                        sans:  ['Montserrat', 'sans-serif'],
                    },
                    colors: {
                        espresso: '#1c1917',
                        ink:      '#1a1714',
                        dim:      '#4a4540',
                        label:    '#6e665e',
                        rule:     '#c8bfb0',
                        ghost:    '#a89f94',
                        paper:    '#fffdf9',
                        cream:    '#f5f0e8',
                        sand:     '#e8e0d0',
                    },
                    animation: {
                        'fade-up': 'fadeUp 0.65s ease both',
                    },
                    keyframes: {
                        fadeUp: {
                            '0%':   { opacity: '0', transform: 'translateY(16px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                    },
                }
            }
        }
    </script>
</head>
<body class="bg-cream font-sans min-h-screen">

    {{-- Header --}}
    <x-header />

    {{-- Mobile overlay backdrop --}}
    <div id="sidebarBackdrop" class="hidden fixed inset-0 bg-black/50 z-20 md:hidden" onclick="toggleSidebar()"></div>

    <div class="flex pt-[70px]">
        {{-- Sidebar --}}
        <x-sidebar />

        {{-- Main Content --}}
        <main class="flex-1 w-full overflow-auto">
            <div class="p-4 md:p-8">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }

        // Close sidebar when clicking a link
        document.querySelectorAll('#sidebar a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 768) {
                    toggleSidebar();
                }
            });
        });
    </script>

</body>
</html>