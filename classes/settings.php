<?php

class Settings extends Dbh
{
    private const DEFAULTS = [
        'app_name' => 'EARN-NOW',
        'support_email' => '',
    ];

    public function getAppSettings()
    {
        $settings = self::DEFAULTS;

        try {
            $stmt = $this->settingsConnection()->query(
                "SELECT setting_key, setting_value
                 FROM app_settings
                 WHERE setting_key IN ('app_name', 'support_email')"
            );

            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                if (array_key_exists($row['setting_key'], $settings)) {
                    $settings[$row['setting_key']] = (string)$row['setting_value'];
                }
            }

            return ['values' => $settings, 'available' => true];
        } catch (PDOException $exception) {
            return ['values' => $settings, 'available' => false];
        }
    }

    public function getRegistrationStatus()
    {
        try {
            $pdo = $this->settingsConnection();
            $stmt = $pdo->query(
                "SELECT setting_id, registration_status
                 FROM settings
                 ORDER BY setting_id ASC
                 LIMIT 1"
            );
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                'status' => $row ? (string)$row['registration_status'] : 'active',
                'available' => true,
            ];
        } catch (PDOException $exception) {
            return ['status' => 'active', 'available' => false];
        }
    }

    protected function persistAppSettings($appName, $supportEmail, $adminId)
    {
        try {
            $pdo = $this->settingsConnection();
            $stmt = $pdo->prepare(
                "INSERT INTO app_settings (setting_key, setting_value, updated_by)
                 VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE
                    setting_value = VALUES(setting_value),
                    updated_by = VALUES(updated_by)"
            );

            $pdo->beginTransaction();
            $stmt->execute(['app_name', $appName, $adminId]);
            $stmt->execute(['support_email', $supportEmail, $adminId]);
            $pdo->commit();

            return true;
        } catch (PDOException $exception) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return false;
        }
    }

    protected function persistRegistrationStatus($registrationStatus)
    {
        try {
            $pdo = $this->connect();
            $pdo->beginTransaction();
            $settingsRow = $pdo->query(
                "SELECT setting_id FROM settings ORDER BY setting_id ASC LIMIT 1 FOR UPDATE"
            )->fetch(PDO::FETCH_ASSOC);
            if ($settingsRow) {
                $registrationStmt = $pdo->prepare(
                    "UPDATE settings SET registration_status = ? WHERE setting_id = ?"
                );
                $registrationStmt->execute([$registrationStatus, (int)$settingsRow['setting_id']]);
            } else {
                $registrationStmt = $pdo->prepare(
                    "INSERT INTO settings (registration_status) VALUES (?)"
                );
                $registrationStmt->execute([$registrationStatus]);
            }

            $pdo->commit();

            return true;
        } catch (PDOException $exception) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return false;
        }
    }

    private function settingsConnection()
    {
        $pdo = $this->connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }
}
