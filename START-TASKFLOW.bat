@echo off
title TaskFlow Starter
cd /d "%~dp0"
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp0run-project.ps1"
if errorlevel 1 (
  echo.
  echo TaskFlow could not start. Send the files from the runtime folder for checking.
  pause
)
