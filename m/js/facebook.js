///////////////           
/////////////// FACEBOOK JAVASCRIPT SDK
window.fbAsyncInit = function() {
    FB.init({
        appId      : '146921452113038', // App ID
        channelUrl : 'http://hueclues.com/', // Channel File
        status     : true, // check login status
        cookie     : true, // enable cookies to allow the server to access the session
        xfbml      : true  // parse XFBML
    });
    FB.Event.subscribe('auth.login', function(response) {
        window.location = '/history.php';
    });
};
// Load the SDK Asynchronously
(function(d){
    var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
    if (d.getElementById(id)) {
        return;
    }
    js = d.createElement('script');
    js.id = id;
    js.async = true;
    js.src = "//connect.facebook.net/en_US/all.js";
    ref.parentNode.insertBefore(js, ref);
}(document));