<?php
// יוצר משתמש חדש עם סיסמה מוצפנת, תפקיד, שכר ומגבלת משימות
if ($action === "user-create") {
    // בדיקת הרשאת מנהל ותקינות השדות שהתקבלו מהטופס
    admin();
    $name = trim((string) ($data["full_name"] ?? ""));
    $email = trim((string) ($data["email"] ?? ""));
    $password = (string) ($data["password"] ?? "");
    if (
        $name === "" ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($password) < 6
    ) {
        out(
            ["error" => "יש להזין שם, אימייל תקין וסיסמה של 6 תווים לפחות"],
            422,
        );
    }
    // מטפל בפעולת בסיס נתונים ובשגיאות אפשריות.
    try {
 // הפונקציה להצפנת הסיסמה שומר סיסמה מוצפנת ולא טקסט גלוי בבסיס הנתונים
        $q = db()->prepare(
            "INSERT INTO users(full_name,email,password_hash,role,phone,hourly_rate,max_active_tasks) VALUES(?,?,?,?,?,?,?)",
        );
        $q->execute([
            $name,
            $email,
            password_hash($password, PASSWORD_DEFAULT),
            $data["role"] ?? "employee",
            $data["phone"] ?? "",
            $data["hourly_rate"] ?? 0,
            $data["max_active_tasks"] ?? 5,
        ]);
        $userId = (int) db()->lastInsertId();
        log_activity("הוספת עובד", "נוסף המשתמש: " . $name);
        out(["id" => $userId]);
    } catch (PDOException $e) {
        out(["error" => "כתובת האימייל כבר קיימת במערכת"], 409);
    }
}
