<?php
// Set username cookie (1 hour)
setcookie("username", "Gulnara Serik", time() + 3600);

// Retrieve username cookie
echo $_COOKIE["username"] ?? "Cookie 'username' not found.";

// Delete username cookie
setcookie("username", "", time() - 3600);

// Set session userid
session_start();
$_SESSION["userid"] = 10020;

// Retrieve session userid
echo $_SESSION["userid"] ?? "userid not set.";

// Destroy session
session_start();
session_unset();
session_destroy();

// Set secure cookie (HTTPS only)
setcookie("secureData", "secret", time()+3600, "/", "", true, true);

// Check visited cookie

if (isset($_COOKIE["visited"])) {
    echo "Welcome back!";
} else {
    setcookie("visited", "yes", time()+3600);
    echo "First time visiting!";
}


// Store array in session

session_start();
$_SESSION["preferences"] = [
    "theme" => "dark",
    "font" => "large",
    "language" => "English"
];


// Retrieve session preferences

session_start();
print_r($_SESSION["preferences"] ?? "No preferences stored.");


// Session timeout (30 minutes)

session_start();
if (!isset($_SESSION["last_activity"])) {
    $_SESSION["last_activity"] = time();
}
if (time() - $_SESSION["last_activity"] > 1800) {
    session_unset(); session_destroy();
    echo "Session timed out.";
} else {
    $_SESSION["last_activity"] = time();
    echo "Session active.";
}


// Count active sessions

$path = session_save_path();
$files = glob($path . "/sess_*");
echo "Active sessions: " . count($files);


// Limit to 3 concurrent sessions

session_start();
$user = "john_doe";
$file = "sessions_$user.json";
$sessions = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

$current = session_id();
if (!in_array($current, $sessions)) {
    $sessions[] = $current;
}
if (count($sessions) > 3) {
    echo "Maximum sessions exceeded.";
    exit;
}
file_put_contents($file, json_encode($sessions));
echo "Session allowed.";


// Regenerate session ID

session_start();
session_regenerate_id(true);
echo "Session ID regenerated.";


// Display last access time

session_start();
if (!isset($_SESSION["last_access"])) {
    $_SESSION["last_access"] = time();
    echo "First access.";
} else {
    echo "Last access: " . date("H:i:s", $_SESSION["last_access"]);
    $_SESSION["last_access"] = time();
}


// Cookie and session same name

session_start();
setcookie("data", "cookie_value", time()+3600);
$_SESSION["data"] = "session_value";

echo "Cookie: ".($_COOKIE["data"] ?? "not set")."\n";
echo "Session: ".$_SESSION["data"];

?>
