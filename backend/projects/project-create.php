<?php
// יוצר פרויקט חדש, מקשר אותו למנהל המחובר ורושם את הפעולה בהיסטוריה
if ($action === "project-create") {
    $u = admin();
    $q = db()->prepare(
        "INSERT INTO projects(name,description,due_date,created_by) VALUES(?,?,?,?)",
    );
    $q->execute([
        $data["name"],
        $data["description"] ?? null,
        $data["due_date"],
        $u["id"],
    ]);
    $projectId = (int) db()->lastInsertId();
    log_activity("יצירת פרויקט", "נוצר הפרויקט: " . $data["name"]);
    out(["id" => $projectId]);
}
