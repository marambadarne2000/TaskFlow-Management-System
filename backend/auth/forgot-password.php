<?php
// יוצר אסימון זמני וקישור מאובטח לאיפוס סיסמה
if ($action === "forgot-password") {
    $email = trim((string) ($data["email"] ?? ""));
    $q = db()->prepare(
        'SELECT id,full_name FROM users WHERE email=? AND status="active"',
    );
    $q->execute([$email]);
    $u = $q->fetch();
    if (!$u) {
        out(["message" => "אם האימייל קיים במערכת, נוצר עבורו קישור איפוס"]);
    }
    $token = bin2hex(random_bytes(24));
    $q = db()->prepare(
        "INSERT INTO password_resets(user_id,token,expires_at) VALUES(?,?,DATE_ADD(NOW(),INTERVAL 30 MINUTE))",
    );
    $q->execute([$u["id"], $token]);
    $link = "http://127.0.0.1:4300/?reset_token=" . $token;
 // בפרויקט המקומי אין שרת שליחת דואר, לכן מציגים את הקישור מיד במסך
    out([
        "message" => "קישור איפוס נוצר. תוקפו 30 דקות.",
        "reset_link" => $link,
    ]);
}
