<?php
// בודק ושומר קובץ קורות חיים ומקשר אותו לעובד
if ($action === "user-upload-cv") {
    admin();
    $id = (int) ($_POST["user_id"] ?? 0);
    if (empty($_FILES["cv"])) {
        out(["error" => "לא נבחר קובץ"], 422);
    }
    $file = $_FILES["cv"];
    if ($file["error"] !== UPLOAD_ERR_OK || $file["size"] > 5 * 1024 * 1024) {
        out(["error" => "הקובץ אינו תקין או גדול מ־5MB"], 422);
    }
    $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    if (!in_array($ext, ["pdf", "doc", "docx"], true)) {
        out(["error" => "ניתן להעלות PDF, DOC או DOCX בלבד"], 422);
    }
    $dir = __DIR__ . "/uploads/cv";
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    $name = "employee_" . $id . "_" . time() . "." . $ext;
    if (!move_uploaded_file($file["tmp_name"], $dir . "/" . $name)) {
        out(["error" => "שמירת הקובץ נכשלה"], 500);
    }
    $q = db()->prepare("UPDATE users SET cv_file=? WHERE id=?");
    $q->execute([$name, $id]);
    out(["ok" => true, "file" => $name]);
}
