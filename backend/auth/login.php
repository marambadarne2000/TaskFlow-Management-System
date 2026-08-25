<?php
// בודק אימייל וסיסמה, שומר את המשתמש ב-נתוני ההתחברות השמורים בשרת ורושם כניסה בהיסטוריה
if ($action === "login") {
    // חיפוש המשתמש לפי האימייל שהתקבל מטופס הכניסה
    $q = db()->prepare(
        "SELECT id,full_name,email,password_hash,role,status,hourly_rate,max_active_tasks FROM users WHERE email=?",
    );
    $q->execute([$data["email"] ?? ""]);
    $u = $q->fetch();
 // הפונקציה לבדיקת הסיסמה משווה את הסיסמה הרגילה מול הסיסמה המוצפנת שבטבלה
    if (
        !$u ||
        !password_verify($data["password"] ?? "", $u["password_hash"]) ||
        $u["status"] !== "active"
    ) {
        out(["error" => "פרטי הכניסה אינם נכונים"], 401);
    }
    // לא מחזירים את הסיסמה המוצפנת לצד הלקוח
    unset($u["password_hash"]);
    $_SESSION["user"] = $u;
    log_activity("כניסה למערכת", "המשתמש התחבר למערכת");
    out(["user" => $u]);
}
