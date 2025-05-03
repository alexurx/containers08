<?php

require_once __DIR__ . '/testframework.php';
require_once '/var/www/html/config.php';
require_once '/var/www/html/modules/database.php';
require_once '/var/www/html/modules/page.php';

$tests = new TestFramework();

// Test: Database connection
$tests->add('Database connection', function () use ($config) {
    $db = new Database($config['db']['path']);
    return assertExpression($db instanceof Database, 'Connection successful', 'Connection failed');
});

// Test: Count
$tests->add('Count rows', function () use ($config) {
    $db = new Database($config['db']['path']);
    $count = $db->Count('page');
    return assertExpression($count >= 3, "Count = $count", 'Count failed');
});

// Test: Create and Read
$tests->add('Create and Read record', function () use ($config) {
    $db = new Database($config['db']['path']);
    $id = $db->Create('page', ['title' => 'Test', 'content' => 'Content']);
    $record = $db->Read('page', $id);
    return assertExpression($record['title'] === 'Test', 'Read success', 'Read failed');
});

// Test: Update
$tests->add('Update record', function () use ($config) {
    $db = new Database($config['db']['path']);
    $id = $db->Create('page', ['title' => 'Old', 'content' => 'Old']);
    $db->Update('page', $id, ['title' => 'New']);
    $record = $db->Read('page', $id);
    return assertExpression($record['title'] === 'New', 'Update success', 'Update failed');
});

// Test: Delete
$tests->add('Delete record', function () use ($config) {
    $db = new Database($config['db']['path']);
    $id = $db->Create('page', ['title' => 'ToDelete', 'content' => 'Delete me']);
    $db->Delete('page', $id);
    $record = $db->Read('page', $id);
    return assertExpression($record === null, 'Delete success', 'Delete failed');
});

// Test: Page rendering
$tests->add('Page render', function () {
    $page = new Page(__DIR__ . '/../site/templates/index.tpl');
    $html = $page->Render(['title' => 'T', 'content' => 'C']);
    return assertExpression(strpos($html, 'T') !== false && strpos($html, 'C') !== false, 'Render success', 'Render failed');
});

// Run all
$tests->run();
echo $tests->getResult();
