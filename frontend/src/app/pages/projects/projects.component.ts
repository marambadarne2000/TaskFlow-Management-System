// קובץ זה אחראי על ניהול פרויקטים.
import { Component } from "@angular/core";
import { CommonModule } from "@angular/common";
import { FormsModule } from "@angular/forms";
import { TaskflowStoreService } from "../../core/services/taskflow-store.service";

/** מסך פרויקטים: מציג פרויקטים, מנהל, צוות, התקדמות וסינון לפי תאריך */
@Component({
  selector: "app-projects",
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: "./projects.component.html",
  styleUrl: "./projects.component.scss",
})
export class ProjectsComponent {
  // מזריק לרכיב את השירות המרכזי שמספק נתונים ופעולות.
  constructor(public vm: TaskflowStoreService) {}
}
