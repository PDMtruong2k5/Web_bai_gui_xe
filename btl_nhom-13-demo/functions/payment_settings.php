<?php
// Simple file-based storage for admin payment settings
// Stored at data/payment_settings.json (create if missing)

function get_payment_settings_path() {
    $dir = __DIR__ . '/../data';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    return $dir . '/payment_settings.json';
}

function load_payment_settings() {
    $path = get_payment_settings_path();
    if (!file_exists($path)) return ['bank_account' => '', 'bank_name' => ''];
    $json = file_get_contents($path);
    $data = json_decode($json, true);
    if (!is_array($data)) return ['bank_account' => '', 'bank_name' => ''];
    return array_merge(['bank_account' => '', 'bank_name' => '', 'qr_image' => ''], $data);
}

function save_payment_settings($bank_account, $bank_name, $qr_image = '') {
    $path = get_payment_settings_path();
    $data = [
        'bank_account' => trim($bank_account),
        'bank_name' => trim($bank_name),
        'qr_image' => trim($qr_image)
    ];
    return (bool) file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}
