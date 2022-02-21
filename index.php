<?php 
require_once 'blti.php';

$secret = "LUVDX64L6USTS2VN";
// $secret = "LUVDX64L6USTS2VA";

$context = new BLTI( $secret, false, false );

if ( ! empty( $_REQUEST['relaunch_url'] ) && ! empty( $_REQUEST['platform_state'] ) ) {
    basicLTIRelaunch( $_REQUEST['relaunch_url'], $_REQUEST['platform_state'] );
}
// $context = new OAuthServer( $secret, false, false );

if ( ! $context->valid ) {
    print_r(validateLTIData( $context->message, $key ));
}

echo "<pre>";
print_r($context);
echo "</pre>";

function validateLTIData( $msg, $key ) {
    $err_id = '';
    $now    = time();
    if ( ! $_REQUEST['resource_link_id'] ) {
        $err_id = 'resource_link_id is required.';
    } elseif ( $_REQUEST['lti_version'] != 'LTI-1p0' ) {
        $err_id = 'Incorrect LTI version. LTI version should be LTI-1p0.';
    } elseif ( $_REQUEST['lti_message_type'] != 'basic-lti-launch-request' ) {
        $err_id = 'Incorrect lti_message_type. LTI version should be basic-lti-launch-request.';
    } elseif ( ! $_REQUEST['oauth_timestamp'] ) {
        $err_id = 'Missing oauth_timestamp parameter.';
    } elseif ( abs( $now - $_REQUEST['oauth_timestamp'] ) > 300 ) {
        $err_id = 'Expired timestamp, yours ' . $_REQUEST['oauth_timestamp'] . ', ours ' . $now . '.';
    } elseif ( strpos( $msg, 'signature' ) ) {
        $our_sign = explode( ' ', $msg );
        $now      = $our_sign[3];
        $err_id   = 'Invalid signature, yours ' . $_REQUEST['oauth_signature'] . ', ours ' . $now . '.';
    } elseif ( $err_id == '' && $msg == '' ) {
        $err_id = 'Invalid encrypted data. LTI Secret might be wrong.';
    } 

    return $err_id;
}



function basicLTIRelaunch( $relaunch_url, $platform_state ) {
	$page = <<< EOD
    <html>
    <head>
    <script>
        function doOnLoad() {
            document.forms[0].submit();
        }
        window.onload=doOnLoad;
    </script>
    </head>
    <body>
        <form action="{$relaunch_url}" method="post">
            <input type="hidden" name="platform_state" value="{$platform_state}" />
            <input type="hidden" name="tool_state" value="{$platform_state}" />
        </form>
    </body>
    </html>
EOD;
	print_r( $page );
}
