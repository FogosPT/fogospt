<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

@stack('scripts')

<script src="https://unpkg.com/leaflet@1.3.1/dist/leaflet.js"
        integrity="sha512-/Nsx9X4HebavoBvEBuyp3I7od5tA0UzAxs+j83KgC8PU0kgB4XiK4Lfe4y4cgBtaRJQEIFCW+oC506aPT2L1zw=="
        crossorigin=""></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>

<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-29689840-9"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }

    gtag('js', new Date());

    gtag('config', 'UA-29689840-9');
</script>

<script src="https://www.gstatic.com/firebasejs/4.13.0/firebase.js"></script>
<script>
    // Initialize Firebase
    var config = {
        apiKey: "AIzaSyCxxu_jTrBrGE8Em1kaqn3wTbCBa8_Ra7M",
        authDomain: "admob-app-id-6663345165.firebaseapp.com",
        databaseURL: "https://admob-app-id-6663345165.firebaseio.com",
        projectId: "admob-app-id-6663345165",
        storageBucket: "admob-app-id-6663345165.appspot.com",
        messagingSenderId: "726949968874"
    };
    firebase.initializeApp(config);
</script>
