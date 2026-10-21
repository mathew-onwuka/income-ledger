<?php

class SettingsContr extends SettingsView
{
    public function updateSettings($appName, $supportEmail, $adminId)
    {
        if (!is_string($appName) || !is_string($supportEmail)) {
            return false;
        }

        $appName = trim((string)$appName);
        $supportEmail = trim((string)$supportEmail);

        if ($appName === '' || strlen($appName) > 100) {
            return false;
        }

        if (
            $supportEmail !== '' &&
            (strlen($supportEmail) > 190 || !filter_var($supportEmail, FILTER_VALIDATE_EMAIL))
        ) {
            return false;
        }

        return $this->persistAppSettings(
            $appName,
            $supportEmail,
            (int)$adminId
        );
    }

    public function updateRegistrationStatus($registrationStatus)
    {
        if (!is_string($registrationStatus) || !in_array($registrationStatus, ['active', 'inactive'], true)) {
            return false;
        }

        return $this->persistRegistrationStatus($registrationStatus);
    }
}
