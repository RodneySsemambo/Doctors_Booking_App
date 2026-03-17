<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Medical Record</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
    </style>
</head>
<body>
    <h2>Medical Record</h2>

    <p><strong>Patient:</strong> {{ $record->patient->first_name }}</p>
    <p><strong>Record Type:</strong> {{ $record->record_type }}</p>
    <p><strong>Title:</strong> {{ $record->title }}</p>
    <p><strong>Recorded Date:</strong> {{ $record->recorded_date }}</p>

    <hr>

    <p>Generated on {{ now()->format('d M Y') }}</p>
</body>
</html>
