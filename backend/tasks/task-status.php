<?php
// מעדכן מצב משימה ומסמן אוטומטית את הפרויקט כהסתיים כשכולן הושלמו
if ($action === "task-status") {
    $u = user();
    // שליפת המשימה מאפשרת לבדוק שהעובד מעדכן רק משימה שהוקצתה אליו
    $q = db()->prepare("SELECT project_id,assignee_id FROM tasks WHERE id=?");
    $q->execute([$data["id"]]);
    $t = $q->fetch();
    if (
        !$t ||
        ($u["role"] !== "admin" && (int) $t["assignee_id"] !== (int) $u["id"])
    ) {
        out(["error" => "אין הרשאה"], 403);
    }
 // שמירת הסטטוס ותאריך השלמה רק כאשר המצב הוא הושלם
    $q = db()->prepare(
        "UPDATE tasks SET status=?,completed_at=IF(?='done',NOW(),NULL) WHERE id=?",
    );
    $q->execute([$data["status"], $data["status"], $data["id"]]);
    // ספירת כל המשימות והמשימות שהושלמו בפרויקט
    $q = db()->prepare(
        "SELECT COUNT(*) total,SUM(status='done') done FROM tasks WHERE project_id=?",
    );
    $q->execute([$t["project_id"]]);
    $c = $q->fetch();
    // הפרויקט מסתיים אוטומטית רק כאשר קיימת משימה וכל המשימות הושלמו
    $complete = (int) $c["total"] > 0 && (int) $c["total"] === (int) $c["done"];
    $q = db()->prepare(
        "UPDATE projects SET status=?,completed_at=? WHERE id=?",
    );
    $q->execute([
        $complete ? "completed" : "in_progress",
        $complete ? date("Y-m-d H:i:s") : null,
        $t["project_id"],
    ]);
    log_activity(
        "עדכון משימה",
        "מצב משימה מספר " . $data["id"] . " שונה ל-" . $data["status"],
    );
    out(["ok" => true, "project_completed" => $complete]);
}
