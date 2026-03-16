@component('mail::message')

# {{ config('forge-monitor.strings.email.disk_details') }}

{!! nl2br(str_replace(' ', '&nbsp;', $disk_details)) !!}


# {{ config('forge-monitor.strings.email.disk_usage') }}
@foreach($command_output as $line)
{{ $line }}
@endforeach


# {{ config('forge-monitor.strings.email.db_usage') }}
<x-mail::table>
| {{ config('forge-monitor.strings.email.db') }}    | {{ config('forge-monitor.strings.email.size') }}      |
|-------------|----------:|
@foreach($database_sizes as $database_details)
@if($database_details[0] === config('forge-monitor.strings.total'))
| **{{ $database_details[0] }}** | **{{ number_format($database_details[1] / 1024 / 1024, 2) }} MB** |
@else
| {{ $database_details[0] }} | {{ number_format($database_details[1] / 1024 / 1024, 2) }} MB |
@endif
@endforeach
</x-mail::table>
@endcomponent