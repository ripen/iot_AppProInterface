@echo off
@echo Welcome use anychat web plugin auto build script for win32
@echo.

@echo.
set PATH=%PATH%;%~dp0\InnoSetup5;

@echo 启动安装程序生成脚本....
Compil32.exe /cc %~dp0\AnyChatWeb.iss

