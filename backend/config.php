<?php
// הגדרות משותפות: חיבור ל-מסד הנתונים,, נתוני ההתחברות השמורים בשרת ופונקציות הרשאה ותשובה
declare(strict_types=1);

// פרטי החיבור ל-מסד הנתונים של 
const DB_HOST = "127.0.0.1";
const DB_NAME = "taskflow_student";
const DB_USER = "root";
const DB_PASS = "1234";

// יוצר חיבור יחיד ומשתמש בו בכל בקשות השרת
function db(): PDO
{
    static $pdo;
    if (isset($pdo)) {
        return $pdo;
    }
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ],
    );
    return $pdo;
}

// כל תשובת השרת נשלחת לממשק במבנה מסודר ובקידוד עברי תקין
header("Content-Type: application/json; charset=utf-8");
$origin = $_SERVER["HTTP_ORIGIN"] ?? "";
if (
    in_array(
        $origin,
        [
            "http://127.0.0.1:4200",
            "http://127.0.0.1:4201",
            "http://127.0.0.1:4202",
            "http://localhost:4200",
            "http://localhost:4201",
            "http://localhost:4202",
            "http://127.0.0.1:4300",
            "http://localhost:4300",
        ],
        true,
    )
) {
    header("Access-Control-Allow-Origin: " . $origin);
}
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET,POST,PUT,OPTIONS");
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    exit();
}
// נתוני ההתחברות השמורים בשרת שומר את המשתמש המחובר בין בקשות שונות
session_start();

// קורא את נתוני הבקשה שנשלחו מהממשק
function input(): array
{
    return json_decode(file_get_contents("php://input"), true) ?: [];
}
function out(array $data, int $code = 200): never
{
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}
// מחזיר את המשתמש המחובר או שגיאת 401
function user(): array
{
    if (empty($_SESSION["user"])) {
        out(["error" => "נדרשת התחברות"], 401);
    }
    return $_SESSION["user"];
}
// מגן על פעולות שמותרות למנהל בלבד
function admin(): array
{
    $u = user();
    if ($u["role"] !== "admin") {
        out(["error" => "פעולה למנהל בלבד"], 403);
    }
    return $u;
}

// שומר פעולה חשובה בהיסטוריה לצורך מעקב בדשבורד המנהל
function log_activity(string $actionName, string $details): void
{
    $currentUser = user();
    $query = db()->prepare(
        "INSERT INTO activity_logs(user_id,action_name,details) VALUES(?,?,?)",
    );
    $query->execute([$currentUser["id"], $actionName, $details]);
}
