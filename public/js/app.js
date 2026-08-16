document.addEventListener('DOMContentLoaded', function () {
    // AOS init (frontend pages)
    if (window.AOS) {
        AOS.init({ duration: 700, easing: 'ease-out-cubic', once: true, offset: 60 });
    }

    // Sidebar toggle (dashboard layouts)
    var toggleBtn = document.querySelector('.sidebar-toggle-btn');
    var sidebar = document.querySelector('.dash-sidebar');
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('show');
        });
        document.addEventListener('click', function (e) {
            if (window.innerWidth <= 991 && sidebar.classList.contains('show')
                && !sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        });
    }

    // Back to top
    var backToTop = document.querySelector('.back-to-top');
    var mainNav = document.querySelector('.main-nav');
    window.addEventListener('scroll', function () {
        var y = window.scrollY;
        if (backToTop) backToTop.classList.toggle('show', y > 400);
        if (mainNav) mainNav.classList.toggle('shadow-sm', y > 20);
    });
    if (backToTop) {
        backToTop.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // Auto-dismiss alerts after 5s
    document.querySelectorAll('.alert-auto-dismiss').forEach(function (alertEl) {
        setTimeout(function () {
            if (window.bootstrap) {
                var alert = bootstrap.Alert.getOrCreateInstance(alertEl);
                alert.close();
            } else {
                alertEl.remove();
            }
        }, 5000);
    });

    // Confirm-before-delete forms
    document.querySelectorAll('[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!confirm(form.dataset.confirm || 'Are you sure?')) {
                e.preventDefault();
            }
        });
    });

    // Bootstrap tooltip init
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
        if (window.bootstrap) new bootstrap.Tooltip(el);
    });

    // Copy-to-clipboard buttons (data-copy-target="#someInputId")
    document.querySelectorAll('[data-copy-target]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var target = document.querySelector(this.dataset.copyTarget);
            if (!target) return;
            navigator.clipboard.writeText(target.value).then(function () {
                var original = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check2"></i> Copied';
                setTimeout(function () { btn.innerHTML = original; }, 1800);
            });
        });
    });

    // Lazy-loaded modal iframes (lesson video/PDF previews): only fetch the
    // src once the modal is actually opened, and clear it again on close so
    // hidden content never auto-loads (or keeps playing) in the background.	
	
	document.querySelectorAll('.modal').forEach(function (modalEl) {

        modalEl.addEventListener('shown.bs.modal', function () {

            const iframe = modalEl.querySelector('.lesson-pdf-frame');

            if (iframe && !iframe.src) {
                iframe.src = iframe.dataset.src;
            }

        });

        modalEl.addEventListener('hidden.bs.modal', function () {

            const iframe = modalEl.querySelector('.lesson-pdf-frame');

            if (iframe) {
                iframe.src = '';
            }

        });

    });
	
});
