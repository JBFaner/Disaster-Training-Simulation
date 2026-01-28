<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>LGU Disaster Preparedness Training & Simulation</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    </head>
    <body class="bg-slate-100 text-slate-900">
        <div
            id="app"
            data-role="{{ auth()->check() ? (auth()->user()->role ?? 'PARTICIPANT') : 'PARTICIPANT' }}"
            data-section="{{ $section ?? 'dashboard' }}"
            @isset($modules)
                data-modules='@json($modules)'
            @endisset
            @isset($module)
                data-module='@json($module)'
            @endisset
            @isset($scenarios)
                data-scenarios='@json($scenarios)'
            @endisset
            @isset($scenario)
                data-scenario='@json($scenario)'
            @endisset
            @isset($events)
                data-events='@json($events)'
            @endisset
            @isset($event)
                data-event='@json($event)'
            @endisset
            @isset($participants)
                data-participants='@json($participants)'
            @endisset
            @isset($participant)
                data-participant='@json($participant)'
            @endisset
            @isset($registrations)
                data-registrations='@json($registrations)'
            @endisset
            @isset($resources)
                data-resources='@json($resources)'
            @endisset
            @isset($resource)
                data-resource='@json($resource)'
            @endisset
            @isset($evaluation)
                data-evaluation='@json($evaluation)'
            @endisset
            @isset($criteria)
                data-criteria='@json($criteria)'
            @endisset
            @isset($attendances)
                data-attendances='@json($attendances)'
            @endisset
            @isset($participant_evaluations)
                data-participant-evaluations='@json($participant_evaluations)'
            @endisset
            @if(auth()->check())
                data-user='@json(auth()->user())'
            @elseif(isset($user))
                data-user='@json($user)'
            @endisset
            @isset($attendance)
                data-attendance='@json($attendance)'
            @endisset
            @isset($participant_evaluation)
                data-participant-evaluation='@json($participant_evaluation)'
            @endisset
            @isset($barangay_profile)
                data-barangay-profile='@json($barangay_profile)'
            @endisset
            @isset($scores)
                data-scores='@json($scores)'
            @endisset
            @isset($criterion_averages)
                data-criterion-averages='@json($criterion_averages)'
            @endisset
            @isset($total_participants)
                data-total-participants="{{ $total_participants }}"
            @endisset
            @isset($passed_count)
                data-passed-count="{{ $passed_count }}"
            @endisset
            @isset($failed_count)
                data-failed-count="{{ $failed_count }}"
            @endisset
            @isset($overall_average)
                data-overall-average="{{ $overall_average }}"
            @endisset
            @if (session('status'))
                data-status="{{ session('status') }}"
            @endif
        ></div>
    </body>
</html>
