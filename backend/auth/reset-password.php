<?php
// בודק את אסימון האיפוס ומעדכן סיסמה מוצפנת חדשה
if ($action === "reset-password") {
    $token = (string) ($data["token"] ?? "");
    $password = (string) ($data["password"] ?? "");
    if (strlen($password) < 6) {
        out(["error" => "הסיסמה חייבת להכיל לפחות 6 תווים"], 422);
    }
    $q = db()->prepare(
        "SELECT id,user_id FROM password_resets WHERE token=? AND used_at IS NULL AND expires_at>NOW()",
    );
    $q->execute([$token]);
    $r = $q->fetch();
    if (!$r) {
        out(["error" => "הקישור אינו תקין או שפג תוקפו"], 422);
    }
    $pdo = db();
    $pdo->beginTransaction();
    $q = $pdo->prepare("UPDATE users SET password_hash=? WHERE id=?");
    $q->execute([password_hash($password, PASSWORD_DEFAULT), $r["user_id"]]);
    $q = $pdo->prepare("UPDATE password_resets SET used_at=NOW() WHERE id=?");
    $q->execute([$r["id"]]);
    $pdo->commit();
    out(["ok" => true, "message" => "הסיסמה עודכנה בהצלחה"]);
}
