@component('mail::message')

@if(!empty($disk_result))

# Disk details:

{!!  nl2br(str_replace(' ', '&nbsp;', $disk_result)) !!}
@endif
@if(!empty($project_results))

# Forge Site Disk Usage Results:

@foreach($project_results as $line)
{{ $line }}
@endforeach
@endif

@if(!empty($self_project_result))

# Forge Self Site Disk Usage Results:

{{ $self_project_result }}
@endif

@if(!empty($db_results))

# Database Usage:

<x-mail::table>
| Database    | Size      |
|-------------|----------:|
@foreach($database_sizes as $database_details)
@if($database_details[0] === 'Total')
| **{{ $database_details[0] }}** | **{{ number_format($database_details[1] / 1024 / 1024, 2) }} MB** |
@else
| {{ $database_details[0] }} | {{ number_format($database_details[1] / 1024 / 1024, 2) }} MB |
@endif
@endforeach
</x-mail::table>
@endif


Thanks,<br>
{{ config('app.name') }}
@endcomponent
