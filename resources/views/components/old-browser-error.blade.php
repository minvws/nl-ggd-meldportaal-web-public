<noscript>
    <section class="error">
        <div>
            <p>
                <span>@lang("Error"):</span> @lang('Javascript not detected')
            </p>
        </div>
    </div>
</noscript>
<section class="error hidden no-print sticky" id="browserErrorMessage">
    <div>
        <span>@lang("Error"):</span>
        <p>@lang('Browser too old')</p>
        <ul>
            <li><a href="https://www.google.com/chrome/">Google Chrome</a></li>
            <li><a href="https://www.mozilla.org/nl/firefox/new/">Mozilla Firefox</a></li>
            <li><a href="https://developer.microsoft.com/en-us/microsoft-edge/">Microsoft Edge</a></li>
        </ul>
    </div>
</section>
<script nonce="{{ csp_nonce() }}">
    function getChromeVersion () {
        var raw = navigator.userAgent.match(/Chrom(e|ium)\/([0-9]+)\./);
        return raw ? parseInt(raw[2], 10) : false;
    }

    function getFirefoxVersion () {
        var raw = navigator.userAgent.match(/Firefox\/([0-9]+)\./);
        return raw ? parseInt(raw[1], 10) : false;
    }

    function setBrowserError() {
        const browserErrorMessage = document.getElementById("browserErrorMessage");
        // Move Rijksoverheid logo down
        document.getElementsByTagName("header")[0].style.marginTop = "400px";

        // Make error message visible
        browserErrorMessage.classList.remove("hidden");
    }

    window.onload = function () {
        // Supported browser versions
        // https://en.wikipedia.org/wiki/Google_Chrome_version_history
        // https://en.wikipedia.org/wiki/Firefox_version_history
        const supportedChromeVersion = 87;
        const supportedFireFoxVersion = 77;

        // Detect which versin of the browser is running
        const isFirefox = typeof InstallTrigger !== 'undefined';
        const isChrome = !!window.chrome && (!!window.chrome.webstore || !!window.chrome.runtime);
        const isIE = false || !!document.documentMode;

        if (isIE) { setBrowserError(); return; };
        if (isChrome && getChromeVersion() < supportedChromeVersion) { setBrowserError(); return; }
        if (isFirefox && getFirefoxVersion() < supportedFireFoxVersion) { setBrowserError(); return; }
    };
</script>
