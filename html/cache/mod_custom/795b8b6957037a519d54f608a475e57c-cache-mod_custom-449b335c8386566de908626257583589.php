<?php die("Access Denied"); ?>#x#a:2:{s:6:"result";s:948:"

<div class="custom"  >
	<!-- Messenger Плагин чата Code -->
    <div id="fb-root"></div>

    <!-- Your Плагин чата code -->
    <div id="fb-customer-chat" class="fb-customerchat">
    </div>

    <script>
      var chatbox = document.getElementById('fb-customer-chat');
      chatbox.setAttribute("page_id", "464904030713439");
      chatbox.setAttribute("attribution", "biz_inbox");

      window.fbAsyncInit = function() {
        FB.init({
          xfbml            : true,
          version          : 'v11.0'
        });
      };

      (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = 'https://connect.facebook.net/ro_RO/sdk/xfbml.customerchat.js';
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));
    </script></div>
";s:6:"output";a:3:{s:4:"body";s:0:"";s:4:"head";a:0:{}s:13:"mime_encoding";s:9:"text/html";}}