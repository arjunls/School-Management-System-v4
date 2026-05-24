<?php

namespace App\Kernel\Audit\Services;

use App\Kernel\Audit\Models\AuditLog;
use Illuminate\Pagination\LengthAwarePaginator;

class AuditService
{
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = AuditLog::with('user');

        if (! empty($filters['module'])) {
            $query->where('module', $filters['module']);
        }

        if (! empty($filters['event'])) {
            $query->where('event', $filters['event']);
        }

        if (! empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $perPage = $filters['per_page'] ?? 15;

        return $query->latest()->paginate($perPage);
    }

    public function findByAuditable(string $type, int $id): LengthAwarePaginator
    {
        return AuditLog::where('auditable_type', $type)
            ->where('auditable_id', $id)
            ->with('user')
            ->latest()
            ->paginate(15);
    }

    public function log(
        string $event,
        string $module,
        string $description,
        ?int $userId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
    ): AuditLog {
        return AuditLog::create([
            'event' => $event,
            'module' => $module,
            'description' => $description,
            'user_id' => $userId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
