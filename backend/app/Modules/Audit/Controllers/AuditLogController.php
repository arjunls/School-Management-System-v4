<?php

namespace App\Modules\Audit\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Audit\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function __construct(protected AuditService $auditService) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['module', 'event', 'user_id', 'date_from', 'date_to', 'per_page']);
            $logs = $this->auditService->getAll($filters);

            return response()->json([
                'success' => true,
                'data' => $logs->items(),
                'pagination' => [
                    'total' => $logs->total(),
                    'per_page' => $logs->perPage(),
                    'current_page' => $logs->currentPage(),
                    'last_page' => $logs->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error fetching audit logs', ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Internal server error'], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $log = \App\Modules\Audit\Models\AuditLog::with(['user', 'auditable'])->find($id);

            if (! $log) {
                return response()->json(['success' => false, 'message' => 'Audit log not found'], 404);
            }

            return response()->json(['success' => true, 'data' => $log]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error fetching audit log', ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Internal server error'], 500);
        }
    }
}
