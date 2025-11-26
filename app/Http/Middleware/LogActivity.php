<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\ActivityLog;

class LogActivity
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if (!Auth::check()) return $response;

        $user = Auth::user();
        $route = $request->route();
        $routeName = $route?->getName() ?? 'unknown';
        $method = $request->method();

        /*
        |--------------------------------------------------------------------------
        | ACTION TYPE
        |--------------------------------------------------------------------------
        */
        $action = match ($method) {
            'POST'   => 'Created',
            'PUT', 'PATCH' => 'Updated',
            'DELETE' => 'Deleted',
            default => (Str::contains($routeName, ['index']) ? 'Viewed List' : 'Viewed')
        };

        /*
        |--------------------------------------------------------------------------
        | FIND SUBJECT (MODEL + ID + NAME)
        |--------------------------------------------------------------------------
        */
        $subjectType = null;
        $subjectId = null;
        $subjectName = null;

        // 1. Detect route model binding
        foreach ($route->parameters() ?? [] as $param) {
            if (is_object($param) && property_exists($param, 'id')) {
                $subjectType = class_basename($param);
                $subjectId = $param->id;

                $subjectName = $param->name
                    ?? $param->title
                    ?? $param->property_name
                    ?? $param->amenity_name
                    ?? $param->full_name
                    ?? "ID {$subjectId}";

                break;
            }
        }

        // 2. Detect numeric ID from URL segments
        if (!$subjectId) {
            foreach ($request->segments() as $segment) {
                if (is_numeric($segment)) {
                    $subjectId = $segment;
                    break;
                }
            }
        }

        // 3. Auto-detect subject type from route name
        if (!$subjectType) {
            $map = [
                'properties' => 'Property',
                'users'      => 'User',
                'amenities'  => 'Amenity',
                'bookings'   => 'Booking',
                'reviews'    => 'Review',
            ];

            foreach ($map as $key => $model) {
                if (Str::contains($routeName, $key)) {
                    $subjectType = $model;
                }
            }
        }

        // 4. Load model only if type + id exist
        if ($subjectType && $subjectId && !$subjectName) {
            $modelClass = "App\\Models\\{$subjectType}";
            if (class_exists($modelClass)) {
                $model = $modelClass::find($subjectId);
                if ($model) {
                    $subjectName =
                        $model->name
                        ?? $model->title
                        ?? $model->property_name
                        ?? $model->amenity_name
                        ?? "ID {$subjectId}";
                }
            }
        }

        if (!$subjectName && $subjectId) {
            $subjectName = "ID {$subjectId}";
        }

        /*
        |--------------------------------------------------------------------------
        | DESCRIPTION
        |--------------------------------------------------------------------------
        */
        if ($subjectType) {
            $description = "{$user->name} ({$user->role}) {$action} {$subjectType} {$subjectName}.";
        } else {
            $description = "{$user->name} ({$user->role}) accessed {$routeName}.";
        }

        /*
        |--------------------------------------------------------------------------
        | SAVE LOG
        |--------------------------------------------------------------------------
        */
        ActivityLog::create([
            'user_id'      => $user->id,
            'user_name'    => $user->name,
            'role'         => $user->role,
            'action'       => $action,
            'subject_type' => $subjectType,
            'subject_id'   => $subjectId,
            'subject_name' => $subjectName,
            'description'  => $description,
            'ip_address'   => $request->ip(),
        ]);

        return $response;
    }
}
