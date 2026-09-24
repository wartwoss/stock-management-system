# StockFlow Launcher & Backup Setup

## Quick Start (Daily Use)

Double-click **`StockFlow.vbs`** on the Desktop to launch the system.

> The shortcut will:
> - Start MySQL (via XAMPP) if not already running
> - Start the Laravel backend on port 8000
> - Open Chrome to http://localhost:8000 automatically

---

## Initial Setup — Desktop Shortcut

1. Right-click `StockFlow.vbs` → **Send to → Desktop (create shortcut)**
2. Right-click the desktop shortcut → **Properties**
3. Click **Change Icon** → browse to any icon you like
4. Rename the shortcut to **"StockFlow"**

---

## Initial Setup — Google Drive Backup (One Time)

Run `first-time-setup.bat` **once** on the store PC.

### What it does:
1. Downloads **rclone** (free, ~20MB) into the `launcher/rclone/` folder
2. Opens a configuration wizard in CMD
3. Opens your browser to authorize Google Drive access

### Steps during setup:
| Prompt | What to enter |
|--------|--------------|
| New remote name | `gdrive` |
| Storage type | Choose **Google Drive** |
| Client ID / Secret | Press **Enter** (leave blank) |
| Scope | Choose **1** (full access) |
| Open browser? | Press **Y** |
| Log in | Use the store Google account |
| Finished? | Press **Q** to quit |

---

## Using Google Drive Backup

1. Open the app in Chrome
2. Go to **Settings**
3. Click **"Backup to Google Drive"**
4. Done! The `.sql` file is saved to `storage/app/backups/` locally  
   and uploaded to your Google Drive folder `stockflow-backups/`

---

## Backup Location

| Where | Path |
|-------|------|
| Local | `stock-management-system-backend/storage/app/backups/` |
| Google Drive | `My Drive → stockflow-backups/` |

The last 30 local backups are kept automatically (older ones are deleted).

---

## Troubleshooting

| Problem | Solution |
|---------|----------|
| Chrome opens but shows blank page | Wait 10 seconds and refresh — Laravel needs a moment to start |
| Backup says "rclone not found" | Run `first-time-setup.bat` first |
| Backup says "upload failed" | Open CMD, run `launcher\rclone\rclone.exe listremotes` to check auth |
| MySQL wont start | Open XAMPP Control Panel and start MySQL manually |
