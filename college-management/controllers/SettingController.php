<?php
declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Repositories\SettingRepository;
use Repositories\ActivityLogRepository;
use Services\UploadService;

final class SettingController extends Controller
{
    private SettingRepository $settings;
    private ActivityLogRepository $logs;

    public function __construct()
    {
        parent::__construct();
        $this->settings = new SettingRepository();
        $this->logs = new ActivityLogRepository();
    }

    public function index(): void
    {
        $this->authorize('admin');
        $allSettings = $this->settings->allAssoc();

        $this->view('settings.index', [
            'settings' => $allSettings,
            'title' => 'System Settings',
        ]);
    }

    public function update(): void
    {
        $this->authorize('admin');
        $this->requireCsrf();

        $inputs = [
            'institute_name' => $this->request->input('institute_name'),
            'institute_email' => $this->request->input('institute_email'),
            'institute_phone' => $this->request->input('institute_phone'),
            'institute_address' => $this->request->input('institute_address'),
            'academic_year' => $this->request->input('academic_year'),
            'currency_symbol' => $this->request->input('currency_symbol', '$'),
            'currency_code' => $this->request->input('currency_code', 'USD'),
            'allow_registration' => $this->request->input('allow_registration') ? '1' : '0',
            'email_notifications' => $this->request->input('email_notifications') ? '1' : '0',
            'theme_mode' => $this->request->input('theme_mode', 'light'),
        ];

        // Handle Logo Upload
        if (!empty($_FILES['institute_logo']['name'])) {
            try {
                $logoPath = UploadService::saveImage($_FILES['institute_logo'], 'logos');
                if ($logoPath) {
                    $inputs['institute_logo'] = $logoPath;
                }
            } catch (\Throwable $e) {
                flash('error', $e->getMessage());
                redirect('/settings');
            }
        }

        $this->settings->setMany($inputs);
        $this->logs->record(auth()['id'], 'settings_updated', 'settings', null, null, $inputs);

        flash('success', 'System settings updated successfully.');
        redirect('/settings');
    }
}
