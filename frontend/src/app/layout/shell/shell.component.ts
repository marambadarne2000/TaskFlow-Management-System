// קובץ זה אחראי על מעטפת המערכת ותפריט הניווט.
import { Component } from "@angular/core";
import { CommonModule } from "@angular/common";
import { TaskflowStoreService } from "../../core/services/taskflow-store.service";
import { DashboardComponent } from "../../pages/dashboard/dashboard.component";
import { ProjectsComponent } from "../../pages/projects/projects.component";
import { TasksComponent } from "../../pages/tasks/tasks.component";
import { EmployeesComponent } from "../../pages/employees/employees.component";
import { NotificationsComponent } from "../../pages/notifications/notifications.component";
import { AttendanceComponent } from "../../pages/attendance/attendance.component";
import { PayrollComponent } from "../../pages/payroll/payroll.component";
import { ChatComponent } from "../../pages/chat/chat.component";
import { RequestsComponent } from "../../pages/requests/requests.component";

/** מעטפת המערכת: מציגה תפריט ניווט, כותרת, משתמש מחובר ופעמון התראות */
@Component({
  selector: "app-shell",
  standalone: true,
  imports: [
    CommonModule,
    DashboardComponent,
    ProjectsComponent,
    TasksComponent,
    EmployeesComponent,
    NotificationsComponent,
    AttendanceComponent,
    PayrollComponent,
    ChatComponent,
    RequestsComponent,
  ],
  templateUrl: "./shell.component.html",
  styleUrl: "./shell.component.scss",
})
export class ShellComponent {
  // מזריק לרכיב את השירות המרכזי שמספק נתונים ופעולות.
  constructor(public vm: TaskflowStoreService) {}
}
