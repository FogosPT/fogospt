<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha384-1H217gwSVyLSIfaLxHbE7dRb3v4mYCKbpQvzx0cegeju1MVsGrX5xXxAvs/HgeFs" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

@stack('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js" integrity="sha384-VDls8ImYGI8SwVxpmjX2Bn27U2TcNodzTNROTusVEWO55+lmL+H9NczoQJk6mwZR" crossorigin="anonymous"></script>
<script>
    // Toast bodies carry upstream/FCM text — never render them as HTML.
    toastr.options.escapeHtml = true;
</script>

@php($gaId = config('services.google_analytics'))
@if($gaId)
<!-- Google Analytics — the gtag script is loaded on demand by
     cookie-consent.js only if the user grants the "analytics" category.
     Do NOT add a direct <script src="…/gtag/js"> here. -->
<script>window.__FOGOS_GA_ID__ = @json($gaId);</script>
@endif

<script defer src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
<script defer src="https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js"></script>
<script>
    // Web Push VAPID key — Firebase Console → Project Settings → Cloud Messaging → Web configuration.
    window.__FIREBASE_VAPID_KEY__ = @json(config('services.firebase.vapid_key'));
    // Firebase SDK scripts above use `defer` so they don't block parsing; the
    // SDK is only consumed inside user-triggered handlers, never at parse
    // time. Defer guarantees they run before DOMContentLoaded, so this init
    // is safe in that event.
    document.addEventListener('DOMContentLoaded', function () {
        firebase.initializeApp({
            apiKey: "AIzaSyCxxu_jTrBrGE8Em1kaqn3wTbCBa8_Ra7M",
            authDomain: "admob-app-id-6663345165.firebaseapp.com",
            databaseURL: "https://admob-app-id-6663345165.firebaseio.com",
            projectId: "admob-app-id-6663345165",
            storageBucket: "admob-app-id-6663345165.appspot.com",
            messagingSenderId: "726949968874",
            appId: @json(config('services.firebase.app_id'))
        });
    });
</script>

{{-- dayjs (+utc) replaces moment.js (maintenance mode, ~10x larger). The
     plugin is registered by dayjs-setup.js (deferred scripts run in order). --}}
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.11.23/dayjs.min.js" integrity="sha512-XEVgwGep2YaTBuaL+NyliCBBiKVkMy4Rpa0Camg4MBsd5qfpAf6ZQYwBadQTDGtpEex+hFN9q2yOw0saY7mj/A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.11.23/plugin/utc.min.js" integrity="sha512-L6+JBir4Tuvf3skEd3u8/q0ZBW4O1Xu0W/QBLBz+WZ8DiFjpNBMVirATJsFfHcv0TH0GTinCUG/7jVWG0PMnLA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script defer src="/js/dayjs-setup.js?v={{ filemtime(public_path('js/dayjs-setup.js')) }}"></script>

{{-- First-party cookie consent (replaces Cookiebot). Reads window.trans.consent
     for labels, so this must come after the window.trans blob is set in
     app.blade.php's inline script. Loading it after all other scripts here
     keeps the banner from blocking anything else. --}}
<script src="/js/cookie-consent.js?v={{ filemtime(public_path('js/cookie-consent.js')) }}" defer></script>