<?php
require '../connection.php';

$events = [];

// Load general calendar events
$cursor = $database->calendar_events->find();

foreach ($cursor as $event) {
    $start = isset($event['start_date']) ? $event['start_date']->toDateTime()->format('Y-m-d') : null;
    $end = isset($event['end_date']) ? $event['end_date']->toDateTime()->format('Y-m-d') : null;

    $events[] = [
        'title' => $event['title'],
        'start' => $start,
        'end' => $end,
        'color' => $event['color'] ?? '#007bff',
        'extendedProps' => [
            'description' => $event['description'] ?? 'No description provided'
        ]
    ];
}
// Load birthdays from employees
$employees = $database->employees->find([
    'personal_info.birth_date' => ['$exists' => true]
]);

foreach ($employees as $emp) {
    $birthDateStr = $emp['personal_info']['birth_date'] ?? null;

if (!$birthDateStr) continue;

// Format birthday to this year only
$originalDate = new DateTime($birthDateStr);
$currentYear = date('Y');

// Show birthday this year (e.g., 1999-08-05 → 2025-08-05)
$birthdayThisYear = $currentYear . '-' . $originalDate->format('m-d');

$name = $emp['personal_info']['first_name'] . ' ' . $emp['personal_info']['last_name'];
$photoId = $emp['personal_info']['photo_id'] ?? null;
$photoUrl = $photoId
    ? "../handlers/get_image.php?id=" . $photoId
    : "../image/placeholder.png";

$events[] = [
    'title' => "🎂 " . $name,
    'start' => $birthdayThisYear,
    'allDay' => true,
    'color' => '#ff0000ff',
    'extendedProps' => [
        'photo' => $photoUrl,
        'name' => $name,
        'type' => 'birthday'
    ]
];
}

header('Content-Type: application/json');
echo json_encode($events);
