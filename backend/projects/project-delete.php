<?php
// מחיקת פרויקט מותרת למנהל בלבד. המשימות והודעות הצ'אט נמחקות באמצעות מנגנון המחיקה המקושרת.
if ($action === "project-delete") {
    admin();
    $id = (int) ($data["id"] ?? 0);
    if ($id <= 0) {
        out(["error" => "מזהה פרויקט לא תקין"], 422);
    }

    $q = db()->prepare("SELECT name FROM projects WHERE id=?");
    $q->execute([$id]);
    $name = $q->fetchColumn();
    if ($name === false) {
        out(["error" => "הפרויקט לא נמצא"], 404);
    }

    $q = db()->prepare("DELETE FROM projects WHERE id=?");
    $q->execute([$id]);
    log_activity("מחיקת פרויקט", "נמחק הפרויקט: " . $name);
    out(["ok" => true]);
}
