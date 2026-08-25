<?php
// מאפשר למנהל לעדכן את פרטי הפרויקט בלי לשנות את המנהל או תאריך היצירה.
if ($action === "project-update") {
    admin();
    $id = (int) ($data["id"] ?? 0);
    $name = trim((string) ($data["name"] ?? ""));
    $description = trim((string) ($data["description"] ?? ""));
    $dueDate = (string) ($data["due_date"] ?? "");

    if ($id <= 0 || $name === "" || $dueDate === "") {
        out(["error" => "יש למלא שם ומועד סיום"], 422);
    }

    $q = db()->prepare(
        "UPDATE projects SET name=?, description=?, due_date=? WHERE id=?",
    );
    $q->execute([$name, $description ?: null, $dueDate, $id]);
    if ($q->rowCount() === 0) {
        $exists = db()->prepare("SELECT 1 FROM projects WHERE id=?");
        $exists->execute([$id]);
        if (!$exists->fetchColumn()) {
            out(["error" => "הפרויקט לא נמצא"], 404);
        }
    }

    log_activity("עדכון פרויקט", "עודכן הפרויקט: " . $name);
    out(["ok" => true]);
}
