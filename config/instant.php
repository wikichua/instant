<?php

return [
    'broadcast' => [
        'driver' => '', // pusher or ably or ''
    ],
    'route' => [
        'root' => '/admin', // if empty please add /
    ],
    'stubs' => [
        'path' => 'vendor/wikichua/instant/stubs',
    ],
    'audit' => [
        'masks' => [ // masking the key in data field within the activity log model
            'password',
            'password_confirmation',
            'token',
        ],
    ],
    'reauth' => [
        'timeout' => 600, // default 10 mins
        'reset' => true,
    ],
    'Controllers' => [
        'Auth' => [
            'AuthenticatedSession' => \Wikichua\Instant\Http\Controllers\Auth\AuthenticatedSessionController::class,
            'ConfirmablePassword' => \Wikichua\Instant\Http\Controllers\Auth\ConfirmablePasswordController::class,
            'EmailVerificationNotification' => \Wikichua\Instant\Http\Controllers\Auth\EmailVerificationNotificationController::class,
            'EmailVerificationPrompt' => \Wikichua\Instant\Http\Controllers\Auth\EmailVerificationPromptController::class,
            'NewPassword' => \Wikichua\Instant\Http\Controllers\Auth\NewPasswordController::class,
            'PasswordResetLink' => \Wikichua\Instant\Http\Controllers\Auth\PasswordResetLinkController::class,
            'RegisteredUser' => \Wikichua\Instant\Http\Controllers\Auth\RegisteredUserController::class,
            'VerifyEmail' => \Wikichua\Instant\Http\Controllers\Auth\VerifyEmailController::class,
            'Reauth' => \Wikichua\Instant\Http\Controllers\Auth\ReauthController::class,
        ],
        'Profile' => \Wikichua\Instant\Http\Controllers\Admin\ProfileController::class,
        'Dashboard' => \Wikichua\Instant\Http\Controllers\Admin\DashboardController::class,
        'User' => \Wikichua\Instant\Http\Controllers\Admin\UserController::class,
        'PAT' => \Wikichua\Instant\Http\Controllers\Admin\UserPersonalAccessTokenController::class,
        'Permission' => \Wikichua\Instant\Http\Controllers\Admin\PermissionController::class,
        'Role' => \Wikichua\Instant\Http\Controllers\Admin\RoleController::class,
        'Setting' => \Wikichua\Instant\Http\Controllers\Admin\SettingController::class,
        'Audit' => \Wikichua\Instant\Http\Controllers\Admin\AuditController::class,
        'Report' => \Wikichua\Instant\Http\Controllers\Admin\ReportController::class,
        'GlobalSearch' => \Wikichua\Instant\Http\Controllers\Admin\GlobalSearchController::class,
        'Cronjob' => \Wikichua\Instant\Http\Controllers\Admin\CronjobController::class,
        'LogViewer' => \Wikichua\Instant\Http\Controllers\Admin\LogViewerController::class,
        'Mailer' => \Wikichua\Instant\Http\Controllers\Admin\MailerController::class,
        'Versionizer' => \Wikichua\Instant\Http\Controllers\Admin\VersionizerController::class,
        'FailedJob' => \Wikichua\Instant\Http\Controllers\Admin\FailedJobController::class,
        'Brand' => \Wikichua\Instant\Http\Controllers\Admin\BrandController::class,
        'Page' => \Wikichua\Instant\Http\Controllers\Admin\PageController::class,
        'Component' => \Wikichua\Instant\Http\Controllers\Admin\ComponentController::class,
        'File' => \Wikichua\Instant\Http\Controllers\Admin\FileController::class,
        'Nav' => \Wikichua\Instant\Http\Controllers\Admin\NavController::class,
        'Pusher' => \Wikichua\Instant\Http\Controllers\Admin\PusherController::class,
        'Carousel' => \Wikichua\Instant\Http\Controllers\Admin\CarouselController::class,
    ],
    'Models' => [
        'User' => \App\Models\User::class,
        'Versionizer' => \Wikichua\Instant\Models\Versionizer::class,
        'Searchable' => \Wikichua\Instant\Models\Searchable::class,
        'Role' => \Wikichua\Instant\Models\Role::class,
        'Permission' => \Wikichua\Instant\Models\Permission::class,
        'Setting' => \Wikichua\Instant\Models\Setting::class,
        'Report' => \Wikichua\Instant\Models\Report::class,
        'Audit' => \Wikichua\Instant\Models\Audit::class,
        'FailedJob' => \Wikichua\Instant\Models\FailedJob::class,
        'Brand' => \Wikichua\Instant\Models\Brand::class,
        'Page' => \Wikichua\Instant\Models\Page::class,
        'Nav' => \Wikichua\Instant\Models\Nav::class,
        'Component' => \Wikichua\Instant\Models\Component::class,
        'Carousel' => \Wikichua\Instant\Models\Carousel::class,
        'Cronjob' => \Wikichua\Instant\Models\Cronjob::class,
        'Mailer' => \Wikichua\Instant\Models\Mailer::class,
        'Pusher' => \Wikichua\Instant\Models\Pusher::class,
        'Alert' => \Wikichua\Instant\Models\Alert::class,
    ],
];
