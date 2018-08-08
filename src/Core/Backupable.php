<?php

namespace slelorrain\Aushowmatic\Core;

abstract class Backupable implements BackupableInterface
{

    const BACKUP_EXTENSION = '.bak';

    public static function createBackup()
    {
        copy(static::getBackupableFile(), self::getBackupFile());
    }

    public static function deleteBackup()
    {
        unlink(self::getBackupFile());
    }

    public static function restoreBackup()
    {
        copy(self::getBackupFile(), static::getBackupableFile());
        self::deleteBackup();
    }

    private static function getBackupFile()
    {
        return static::getBackupableFile() . self::BACKUP_EXTENSION;
    }
}
