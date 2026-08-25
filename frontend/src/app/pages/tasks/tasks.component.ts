// קובץ זה אחראי על ניהול והקצאת משימות.
import { Component } from "@angular/core";
import { CommonModule } from "@angular/common";
import { FormsModule } from "@angular/forms";
import { TaskflowStoreService } from "../../core/services/taskflow-store.service";

/** מסך משימות: מציג משימות, סטטוס, עובד אחראי, מועדים וסימון איחורים */
@Component({
  selector: "app-tasks",
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: "./tasks.component.html",
  styleUrl: "./tasks.component.scss",
})
export class TasksComponent {
  // מזריק לרכיב את השירות המרכזי שמספק נתונים ופעולות.
  constructor(public vm: TaskflowStoreService) {}
}
