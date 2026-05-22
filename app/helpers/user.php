<?php

include_once(dirname(__DIR__) . '/helpers/admin.php');

if (!function_exists('userRedirectWithFlash')) {
    function userRedirectWithFlash(string $icon, string $message): void
    {
        $_SESSION['code'] = $icon;
        $_SESSION['message'] = $message;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

if (!function_exists('userFetchAllRows')) {
    function userFetchAllRows(mysqli $conn, string $query): array
    {
        return fetchAllRows($conn, $query);
    }
}

if (!function_exists('userFetchFirstRow')) {
    function userFetchFirstRow(mysqli $conn, string $query): ?array
    {
        return fetchFirstRow($conn, $query);
    }
}

if (!function_exists('userFetchCount')) {
    function userFetchCount(mysqli $conn, string $query): int
    {
        return fetchCount($conn, $query);
    }
}

if (!function_exists('userFormatDate')) {
    function userFormatDate(?string $value, string $fallback = '-'): string
    {
        // Preserve original user-facing date style (no leading zero on day)
        return formatDisplayDate($value, false, $fallback, 'M j, Y');
    }
}

if (!function_exists('userFormatMoney')) {
    function userFormatMoney($value): string
    {
        return '₱' . number_format((float) $value, 2);
    }
}

if (!function_exists('userLabelFromStatus')) {
    function userLabelFromStatus(string $status): string
    {
        return ucwords(str_replace(['_', '-'], ' ', trim($status)));
    }
}

if (!function_exists('userStatusClass')) {
    function userStatusClass(string $status): string
    {
        switch (strtolower(trim($status))) {
            case 'available':
            case 'borrowed':
            case 'fulfilled':
                return 'user-status-success';
            case 'active':
            case 'pending':
            case 'reserved':
                return 'user-status-warning';
            case 'overdue':
            case 'cancelled':
            case 'unpaid':
            case 'out_of_stock':
            case 'out-of-stock':
                return 'user-status-danger';
            default:
                return 'user-status-neutral';
        }
    }
}

if (!function_exists('userBookInitials')) {
    function userBookInitials(string $title): string
    {
        $cleanTitle = trim(preg_replace('/[^A-Za-z0-9]+/', ' ', $title) ?? '');

        if ($cleanTitle === '') {
            return 'BK';
        }

        $parts = preg_split('/\s+/', $cleanTitle) ?: [];
        $initials = '';

        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= strtoupper(substr($part, 0, 1));
        }

        if ($initials === '') {
            $initials = strtoupper(substr($cleanTitle, 0, 2));
        }

        return $initials !== '' ? $initials : 'BK';
    }
}

if (!function_exists('userBookCoverStyle')) {
    function userBookCoverStyle(string $seed): string
    {
        $hash = abs(crc32($seed));
        $firstHue = $hash % 360;
        $secondHue = ($firstHue + 24) % 360;

        return sprintf(
            'background: linear-gradient(160deg, hsl(%d, 42%%, 36%%), hsl(%d, 46%%, 28%%));',
            $firstHue,
            $secondHue
        );
    }
}