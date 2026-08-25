<?php
// מחזיר למנהל את עשרים הפעולות האחרונות שנשמרו בהיסטוריית המערכת
if ($action === "activity-list") {
    admin();
    $sql = "SELECT a.id,a.user_id,a.action_name,a.details,a.created_at,u.full_name
            FROM activity_logs a
            JOIN users u ON u.id=a.user_id
            ORDER BY a.created_at DESC
            LIMIT 20";
    out(["items" => db()->query($sql)->fetchAll()]);
}
