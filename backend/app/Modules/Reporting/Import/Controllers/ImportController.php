<?php

namespace App\Modules\Reporting\Import\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Modules\Reporting\Import\Requests\ImportUsersRequest;
use Illuminate\Support\Facades\Validator;

/**
 * @group Imports
 *
 * APIs for importing data
 */
class ImportController extends Controller
{
    /**
     * Import students from CSV
     */
    public function importStudents(Request $request)
    {
        return $this->importUsers($request, 'student');
    }

    /**
     * Import teachers from CSV
     */
    public function importTeachers(Request $request)
    {
        return $this->importUsers($request, 'teacher');
    }

    protected function importUsers(Request $request, string $role)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:csv,txt|max:2048',
            ]);

            $file = $request->file('file');
            $handle = fopen($file->getPathname(), 'r');
            $headers = fgetcsv($handle);

            $required = ['name', 'email'];
            $missing = array_diff($required, array_map('strtolower', $headers));
            if (! empty($missing)) {
                fclose($handle);
                return $this->error('CSV must contain columns: ' . implode(', ', $required), 422);
            }

            $headerMap = [];
            foreach ($headers as $i => $h) {
                $headerMap[strtolower(trim($h))] = $i;
            }

            $created = 0;
            $errors = [];
            $rowNum = 1;

            while (($row = fgetcsv($handle)) !== false) {
                $rowNum++;
                $data = [];
                foreach ($headerMap as $field => $index) {
                    $data[$field] = $row[$index] ?? '';
                }

                $data['password'] = $data['password'] ?? 'password';
                $data['role'] = $role;

                $validator = Validator::make($data, [
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users,email',
                    'password' => 'required|string|min:8',
                    'phone' => 'nullable|string|max:20',
                    'address' => 'nullable|string',
                    'gender' => 'nullable|in:male,female,other',
                    'nisn' => 'nullable|string|max:20|unique:users,nisn',
                    'status' => 'nullable|in:active,inactive,suspended',
                ]);

                if ($validator->fails()) {
                    $errors[] = "Row {$rowNum}: " . implode('; ', $validator->errors()->all());
                    continue;
                }

                $validated = $validator->validated();
                $validated['password'] = Hash::make($validated['password']);
                $validated['role'] = $role;
                $validated['status'] = $validated['status'] ?? 'active';

                User::create($validated);
                $created++;
            }

            fclose($handle);

            $data = [
                'created' => $created,
                'errors' => $errors,
                'total' => $created + count($errors),
            ];
            $message = "Imported {$created} {$role}(s)" . (! empty($errors) ? " with " . count($errors) . " error(s)" : '');
            return $this->success($data, $message);
        } catch (\Exception $e) {
            Log::error('Error importing ' . $role . 's', ['exception' => $e]);
            return $this->error('Internal server error', 500);
        }
    }
}
