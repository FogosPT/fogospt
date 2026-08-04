<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>

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

<script defer src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/moment.min.js" integrity="sha512-rmZcZsyhe0/MAjquhTgiUcb4d9knaFc7b5xAfju483gbEXTkeJRUMIPk6s3ySZMYUHEcjKbjLjyddGWMrNEvZg==" crossorigin="anonymous"></script>

{{-- First-party cookie consent (replaces Cookiebot). Reads window.trans.consent
     for labels, so this must come after the window.trans blob is set in
     app.blade.php's inline script. Loading it after all other scripts here
     keeps the banner from blocking anything else. --}}
<script src="/js/cookie-consent.js?v={{ filemtime(public_path('js/cookie-consent.js')) }}" defer></script>