<?php
// מאפשר למנהל לערוך משימה קיימת תוך שמירה על כללי העובד החסום ומגבלת המשימות.
if ($action === "task-update") {
    admin();
    $id = (int) ($data["id"] ?? 0);
    $projectId = (int) ($data["project_id"] ?? 0);
    $assigneeId = (int) ($data["assignee_id"] ?? 0);
    $title = trim((string) ($data["title"] ?? ""));
    $priority = (string) ($data["priority"] ?? "medium");
    $dueDate = (string) ($data["due_date"] ?? "");

    if ($id <= 0 || $projectId <= 0 || $assigneeId <= 0 || $title === "" || $dueDate === "") {
        out(["error" => "יש למלא את כל פרטי המשימה"], 422);
    }
    if (!in_array($priority, ["low", "medium", "high"], true)) {
        out(["error" => "עדיפות לא תקינה"], 422);
    }

    $pdo = db();
    $q = $pdo->prepare("SELECT id FROM projects WHERE id=?");
    $q->execute([$projectId]);
    if (!$q->fetchColumn()) {
        out(["error" => "הפרויקט לא נמצא"], 404);
    }

    $q = $pdo->prepare("SELECT role,status,max_active_tasks FROM users WHERE id=?");
    $q->execute([$assigneeId]);
    $employee = $q->fetch();
    if (!$employee || $employee["role"] !== "employee" || $employee["status"] !== "active") {
        out(["error" => "ניתן להקצות משימה רק לעובד פעיל"], 422);
    }

    $q = $pdo->prepare("SELECT status FROM tasks WHERE id=?");
    $q->execute([$id]);
    $taskStatus = $q->fetchColumn();
    if ($taskStatus === false) {
        out(["error" => "המשימה לא נמצאה"], 404);
    }
    if ($taskStatus !== "done") {
        $q = $pdo->prepare(
            "SELECT COUNT(*) FROM tasks WHERE assignee_id=? AND status!='done' AND id!=?",
        );
        $q->execute([$assigneeId, $id]);
        if ((int) $q->fetchColumn() >= (int) $employee["max_active_tasks"]) {
            out(["error" => "העובד הגיע למגבלת המשימות"], 409);
        }
    }

    $q = $pdo->prepare(
        "UPDATE tasks SET project_id=?,assignee_id=?,title=?,priority=?,due_date=? WHERE id=?",
    );
    $q->execute([$projectId, $assigneeId, $title, $priority, $dueDate, $id]);
    log_activity("עדכון משימה", "עודכנה המשימה: " . $title);
    out(["ok" => true]);
}
