<?php
// נתב השרת מקבל את שם הפעולה מהממשק וטוען את קובץ הטיפול המתאים
declare(strict_types=1);
// טוען קובץ תלות הנדרש להמשך הטיפול בבקשה.
require __DIR__ . "/config.php";

// שם הפעולה מגיע מהממשק דרך המשתנה שבכתובת
$action = $_GET["action"] ?? "";
$data = input();

// כל פעולה מופנית לקובץ קטן ונפרד כדי לשמור על צד שרת ברור
$routes = [
    "attendance-list" => "attendance/attendance-list.php",
    "activity-list" => "activity/activity-list.php",
    "chat-list" => "chat/chat-list.php",
    "chat-send" => "chat/chat-send.php",
    "chat-upload" => "chat/chat-upload.php",
    "chat-file" => "chat/chat-file.php",
    "clock-in" => "attendance/clock-in.php",
    "clock-out" => "attendance/clock-out.php",
    "weekly-target-list" => "attendance/weekly-target-list.php",
    "weekly-target-save" => "attendance/weekly-target-save.php",
    "dashboard" => "dashboard/dashboard.php",
    "forgot-password" => "auth/forgot-password.php",
    "health" => "health.php",
    "login" => "auth/login.php",
    "logout" => "auth/logout.php",
    "me" => "auth/me.php",
    "notification-read" => "notifications/notification-read.php",
    "notifications" => "notifications/notifications.php",
    "notification-send" => "notifications/notification-send.php",
    "payroll-generate" => "payroll/payroll-generate.php",
    "payroll-list" => "payroll/payroll-list.php",
    "project-create" => "projects/project-create.php",
    "project-update" => "projects/project-update.php",
    "project-delete" => "projects/project-delete.php",
    "projects" => "projects/projects.php",
    "request-list" => "requests/request-list.php",
    "request-create" => "requests/request-create.php",
    "request-answer" => "requests/request-answer.php",
    "reset-password" => "auth/reset-password.php",
    "task-create" => "tasks/task-create.php",
    "task-update" => "tasks/task-update.php",
    "task-delete" => "tasks/task-delete.php",
    "tasks" => "tasks/tasks.php",
    "task-status" => "tasks/task-status.php",
    "user-create" => "users/user-create.php",
    "users" => "users/users.php",
    "user-role" => "users/user-role.php",
    "user-status" => "users/user-status.php",
    "user-upload-cv" => "users/user-upload-cv.php",
];

// אם הפעולה אינה קיימת מוחזרת שגיאת 404 מסודרת
if (!isset($routes[$action])) {
    out(["error" => "פעולה לא מוכרת"], 404);
}

// טעינת הקובץ שמטפל בפעולה המבוקשת
require __DIR__ . "/" . $routes[$action];
