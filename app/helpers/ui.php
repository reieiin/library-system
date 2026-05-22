<?php

if (!function_exists('roleBadgeClass')) {
    function roleBadgeClass(string $roleName): string
    {
        $normalized = strtolower($roleName);

        if (str_contains($normalized, 'admin')) {
            return 'bg-danger';
        }

        if (str_contains($normalized, 'librarian')) {
            return 'bg-primary';
        }

        if (str_contains($normalized, 'staff')) {
            return 'bg-warning text-dark';
        }

        return 'bg-success';
    }
}

if (!function_exists('reservationBadgeClass')) {
    function reservationBadgeClass(string $status): string
    {
        if ($status === 'fulfilled') {
            return 'bg-success';
        }

        if ($status === 'cancelled') {
            return 'bg-danger';
        }

        return 'bg-primary';
    }
}

if (!function_exists('selectedAuthorBadge')) {
    function selectedAuthorBadge(string $authors): string
    {
        return $authors !== '' ? $authors : 'Unassigned';
    }
}

if (!function_exists('fineBadgeClass')) {
    function fineBadgeClass(string $status): string
    {
        return $status === 'paid' ? 'bg-success' : 'bg-danger';
    }
}

if (!function_exists('borrowStatusBadge')) {
    function borrowStatusBadge(string $status): string
    {
        if ($status === 'returned') {
            return 'bg-success';
        }

        if ($status === 'overdue') {
            return 'bg-danger';
        }

        return 'bg-warning text-dark';
    }
}

if (!function_exists('formatDisplayDate')) {
    function formatDisplayDate(?string $value, bool $withTime = false, string $fallback = '-', ?string $format = null): string
    {
        if ($value === null || trim($value) === '') {
            return $fallback;
        }

        $timestamp = strtotime($value);

        if ($timestamp === false) {
            return $fallback;
        }

        if ($format !== null) {
            return date($format, $timestamp);
        }

        return $withTime ? date('M d, Y h:i A', $timestamp) : date('M d, Y', $timestamp);
    }
}
