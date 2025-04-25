@if ($landingpage->isNotEmpty())
    <style>
        .fi-sidebar-item:hover,
        .fi-sidebar-item>a:hover,
        .fi-sidebar-item>a>svg:hover,
        .fi-sidebar-item>a>span:hover,
        .fi-topbar-item:hover,
        .fi-topbar-item>a:hover,
        .fi-topbar-item>a>svg:hover,
        .fi-topbar-item>a>span:hover,
        .active-link,
        .active-link svg,
        .active-link span {
            color: white !important;
            background-color: #1e7e34 !important;
            border-radius: 5px;
            font-weight: bold;
        }
    </style>
    <div class="space-y-6" style="z-index: 0 !important; position: relative">
        @foreach ($landingpage as $page)
            <section id="{{ $page->kode_navigasi }}">
                {!! $page->content !!}
            </section>
        @endforeach
    </div>
    <script>
        function updateActiveNavItems() {
            const currentHash = window.location.hash || '#home';

            // Untuk mobile sidebar
            document.querySelectorAll('.fi-sidebar-item').forEach(item => {
                const link = item.querySelector('a[href^="#"]');
                if (!link) return;

                if (link.getAttribute('href') === currentHash) {
                    item.classList.add('active-link');
                } else {
                    item.classList.remove('active-link');
                }
            });

            // Untuk desktop topbar
            document.querySelectorAll('.fi-topbar-item').forEach(item => {
                const link = item.querySelector('a[href^="#"]');
                if (!link) return;

                if (link.getAttribute('href') === currentHash) {
                    item.classList.add('active-link');
                } else {
                    item.classList.remove('active-link');
                }
            });
        }

        window.addEventListener('DOMContentLoaded', updateActiveNavItems);
        window.addEventListener('hashchange', updateActiveNavItems);
    </script>
@else
    <div class="text-gray-500">
        Tidak ada konten untuk ditampilkan.
    </div>
@endif
