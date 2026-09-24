' StockFlow Silent Launcher
' This VBScript runs StockFlow.bat silently (no CMD window popup).
' Pin this file as the Desktop Shortcut.

Dim WshShell
Set WshShell = CreateObject("WScript.Shell")

Dim LauncherPath
LauncherPath = Left(WScript.ScriptFullName, InStrRev(WScript.ScriptFullName, "\")) & "StockFlow.bat"

WshShell.Run Chr(34) & LauncherPath & Chr(34), 0, False

Set WshShell = Nothing
