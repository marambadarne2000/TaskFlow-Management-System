<?php
// מאפשר למנהל למחוק משימה קיימת.
if ($action === "task-delete") {
    admin();
    $id = (int) ($data["id"] ?? 0);
    if ($id <= 0) {
        out(["error" => "מזהה משימה לא תקין"], 422);
    }

    $q = db()->prepare("SELECT title FROM tasks WHERE id=?");
    $q->execute([$id]);
    $title = $q->fetchColumn();
    if ($title === false) {
        out(["error" => "המשימה לא נמצאה"], 404);
    }

    $q = db()->prepare("DELETE FROM tasks WHERE id=?");
    $q->execute([$id]);
    log_activity("מחיקת משימה", "נמחקה המשימה: " . $title);
    out(["ok" => true]);
}
