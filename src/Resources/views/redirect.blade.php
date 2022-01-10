<body data-gr-c-s-loaded="true" cz-shortcut-listen="true">
    You will be redirected to the WeConnect website in a few seconds.

    <form action="https://accept.paymobsolutions.com/api/acceptance/iframes/{{ $iFrameId }}?payment_token={{ $paymentKey['token'] }}" id="we_connect_checkout" method="POST">
        <input value="Click here if you are not redirected within 10 seconds..." type="submit">

    </form>

    <script type="text/javascript">
        document.getElementById("we_connect_checkout").submit();
    </script>
</body>