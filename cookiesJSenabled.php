
<div id="cookiesNotEnabled"></div>
<script>
    if(!navigator.cookieEnabled) {
        document.getElementById("cookiesNotEnabled").innerHTML= "<br><h3 style='color:red'><strong>Warning! Website with limited functionalities</strong></h3><br>"
            + "<p style='color:red'>Unfortunately your browser <strong>does not support Cookies!</strong><br>"
            + "In order to use the website we recommend you to enable Cookies in your browser.</p>";
    } else {
        window.location.href = "index.php";
    }
</script>

<noscript>
    <br><h3 style="color:red"><strong>Warning! Website with limited functionalities</strong></h3><br>
    <p style="color:red">Unfortunately your browser <strong>does not support Javascript!</strong><br>
	In order to use the website we recommend you to enable JavaScript in your browser. For more informations check <a href="http://www.enable-javascript.com/" >http://www.enable-javascript.com/</a>.
    </p>
</noscript>