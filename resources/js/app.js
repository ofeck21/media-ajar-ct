import Sortable from 'sortablejs';
window.Sortable = Sortable;

window.toggleFullscreen = function() {
    var isFull = !!(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement);
    if (!isFull) {
        var elem = document.documentElement;
        var req = elem.requestFullscreen || elem.webkitRequestFullscreen || elem.mozRequestFullScreen || elem.msRequestFullscreen;
        if (req) {
            req.call(elem).catch(function(err) {
                console.warn('Fullscreen request failed:', err);
            });
        }
    } else {
        var exit = document.exitFullscreen || document.webkitExitFullscreen || document.mozCancelFullScreen || document.msExitFullscreen;
        if (exit) {
            exit.call(document).catch(function(err) {
                console.warn('Exit fullscreen failed:', err);
            });
        }
    }
};

window.initAutoFullscreen = function() {
    if (window._autoFullscreenSetup) return;
    window._autoFullscreenSetup = true;

    function requestAuto() {
        var isFull = !!(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement);
        if (!isFull) {
            var elem = document.documentElement;
            var req = elem.requestFullscreen || elem.webkitRequestFullscreen || elem.mozRequestFullScreen || elem.msRequestFullscreen;
            if (req) {
                req.call(elem).catch(function() {});
            }
        }
    }

    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        requestAuto();
    } else {
        window.addEventListener('DOMContentLoaded', requestAuto, { once: true });
    }

    function onInteraction() {
        requestAuto();
        window.removeEventListener('click', onInteraction, true);
        window.removeEventListener('touchstart', onInteraction, true);
        window.removeEventListener('keydown', onInteraction, true);
        window.removeEventListener('pointerdown', onInteraction, true);
    }

    window.addEventListener('click', onInteraction, { capture: true, once: true });
    window.addEventListener('touchstart', onInteraction, { capture: true, once: true });
    window.addEventListener('keydown', onInteraction, { capture: true, once: true });
    window.addEventListener('pointerdown', onInteraction, { capture: true, once: true });
};

window.initAutoFullscreen();

