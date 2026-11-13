Ваша заявка не прошла модерацию.<br><br>

@php
    $host = (string) parse_url((string) config('app.url'), PHP_URL_HOST);
    $host = preg_replace('/^www\./i', '', $host) ?? $host;
    $domainParts = array_values(array_filter(explode('.', $host)));
    $brandSlug = count($domainParts) >= 2
        ? $domainParts[count($domainParts) - 2]
        : ($domainParts[0] ?? '');
    $brandName = ucfirst(strtolower($brandSlug));
@endphp

@if(trim($rejectDesc) != '')
    Причина: {!! nl2br($rejectDesc) !!}<br><br>
@endif

Обращаем ваше внимание на то, что для участия в розыгрыше принимаются заявки
только с опубликованными отзывами на товар бренда {{ $brandName }},
приобретенный с лотерейным билетом.<br><br>

Ваш код: {{ $ticketCode }}