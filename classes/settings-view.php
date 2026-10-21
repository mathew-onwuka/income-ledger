<?php

class SettingsView extends Settings
{
    public function showSettings()
    {
        $appSettings = $this->getAppSettings();
        $registration = $this->getRegistrationStatus();

        return [
            'values' => $appSettings['values'],
            'available' => $appSettings['available'],
            'registration_status' => $registration['status'],
            'registration_available' => $registration['available'],
        ];
    }
}
