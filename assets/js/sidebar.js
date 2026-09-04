/* SIDEBAR TOGGLE - shared behavior
   Collapses/expands the admin sidebar */

(function () {
    'use strict';

    function getLayout() {
        return (
            document.querySelector('.admin-layout') ||
            document.querySelector('.container')
        );
    }

    function getToggle() {
        return (
            document.getElementById('sidebarToggle') ||
            document.querySelector('.sidebar-toggle')
        );
    }

    function updateState(layout, toggle) {
        if (!toggle) {
            return;
        }

        toggle.setAttribute(
            'aria-expanded',
            layout.classList.contains('sidebar-collapsed') ? 'false' : 'true'
        );
    }

    function init() {
        var layout = getLayout();
        var toggle = getToggle();

        if (!layout || !toggle) {
            return;
        }

        toggle.addEventListener('click', function () {
            layout.classList.toggle('sidebar-collapsed');
            updateState(layout, toggle);
        });

        updateState(layout, toggle);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();