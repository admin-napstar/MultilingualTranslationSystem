        </section>

        <footer class="footer">

            <p>
                2026 Multilingual Translation System | Nare Tlabela| Eric Dolo
            </p>

        </footer>

    </main>

</div>
<script>

document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('appSidebar');
    const toggle = document.getElementById('sidebarToggle');
    const backdrop = document.getElementById('sidebarBackdrop');

    if (!sidebar || !toggle || !backdrop) return;

    const icon = toggle.querySelector('i');

    function setSidebarOpen(open) {
        sidebar.classList.toggle('is-open', open);
        backdrop.classList.toggle('is-visible', open);
        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute(
            'aria-label',
            open ? 'Close navigation menu' : 'Open navigation menu'
        );

        if (icon) {
            icon.classList.toggle('fa-bars', !open);
            icon.classList.toggle('fa-times', open);
        }

        document.body.classList.toggle('sidebar-menu-open', open);
    }

    toggle.addEventListener('click', function () {
        const isOpen = sidebar.classList.contains('is-open');
        setSidebarOpen(!isOpen);
    });

    backdrop.addEventListener('click', function () {
        setSidebarOpen(false);
    });

    sidebar.querySelectorAll('nav a, .sidebar-bottom a')
        .forEach(function (link) {
            link.addEventListener('click', function () {
                setSidebarOpen(false);
            });
        });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            setSidebarOpen(false);
        }
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth > 768) {
            setSidebarOpen(false);
        }
    });
});
</script>
</body>

</html>