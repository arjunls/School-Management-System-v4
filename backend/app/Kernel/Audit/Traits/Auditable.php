<?php

namespace App\Kernel\Audit\Traits;

use App\Kernel\Audit\Models\AuditLog;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            $model->auditEvent('created', [], $model->toArray());
        });

        static::updated(function ($model) {
            $changed = $model->getDirty();
            $old = [];
            $new = [];

            foreach ($changed as $key => $value) {
                $old[$key] = $model->getOriginal($key);
                $new[$key] = $value;
            }

            if (! empty($changed)) {
                $model->auditEvent('updated', $old, $new);
            }
        });

        static::deleted(function ($model) {
            $model->auditEvent('deleted', $model->toArray(), []);
        });

        static::restored(function ($model) {
            $model->auditEvent('restored', [], $model->toArray());
        });
    }

    public function auditEvent(string $event, array $oldValues = [], array $newValues = []): AuditLog
    {
        $module = class_basename($this);

        return AuditLog::create([
            'auditable_type' => static::class,
            'auditable_id' => $this->getKey(),
            'user_id' => Auth::id() ?? (defined('UNIT_TESTING') ? 1 : null),
            'event' => $event,
            'module' => $module,
            'description' => $this->getAuditDescription($event),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    protected function getAuditDescription(string $event): string
    {
        $name = method_exists($this, 'auditName') ? $this->auditName() : ($this->name ?? $this->title ?? "#{$this->getKey()}");

        return match ($event) {
            'created' => "{$this->getAuditModule()} {$name} dibuat",
            'updated' => "{$this->getAuditModule()} {$name} diperbarui",
            'deleted' => "{$this->getAuditModule()} {$name} dihapus",
            'restored' => "{$this->getAuditModule()} {$name} dipulihkan",
            default => "{$this->getAuditModule()} {$name} {$event}",
        };
    }

    protected function getAuditModule(): string
    {
        $reflection = new \ReflectionClass($this);
        $parts = explode('\\', $reflection->getNamespaceName());
        $moduleIndex = array_search('Modules', $parts);

        return $moduleIndex !== false && isset($parts[$moduleIndex + 1])
            ? $parts[$moduleIndex + 1]
            : class_basename($this);
    }

    public function audits(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }
}
