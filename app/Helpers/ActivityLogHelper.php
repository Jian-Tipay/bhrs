<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class ActivityLogHelper
{
   
    public static function log($action, $subject_id = null, $subject_type = null, $description = null)
    {
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();
        $role = ucfirst($user->role); // User, Admin, Landlord

        // Detect route
        $routeName = Route::currentRouteName();
        $method = request()->method();

        // Detect model (if passed)
        $modelName = $data['model_name'] ?? null;
        $modelTitle = $data['model_title'] ?? null;
        $modelId = $data['model_id'] ?? null;

        // Auto-generate description
        $description = self::generateDescription(
            $role,
            $method,
            $routeName,
            $modelName,
            $modelTitle,
            $modelId,
            $event
        );

         ActivityLog::create([
            'user_id'      => $user->id,
            'user_name'    => $user->name,
            'role'         => $user->role,
            'action'       => $action,
            'subject_type' => $subjectType,
            'subject_id'   => $subjectId,
            'subject_name' => $subjectName,
            'description'  => $description,
            'ip_address'   => request()->ip(),
        ]);
    }

    private static function generateDescription(
        $role,
        $method,
        $routeName,
        $modelName,
        $modelTitle,
        $modelId,
        $event
    ) {
        // If developer manually passed an event
        if ($event) {
            return "$role $event";
        }

        // Login & Logout
        if ($routeName === 'auth.login.process') {
            return "$role logged in";
        }
        if ($routeName === 'logout') {
            return "$role logged out";
        }

        // If Model exists, create nice messages:
        if ($modelName) {
            $title = $modelTitle ? ": $modelTitle" : " #$modelId";

            return match ($method) {
                "POST"   => "$role created new $modelName$title",
                "PUT", 
                "PATCH"  => "$role updated $modelName$title",
                "DELETE" => "$role deleted $modelName$title",
                default  => "$role viewed $modelName$title"
            };
        }

       // Default route visit logs (with subject detection)
        if ($routeName) {

            $segments = explode('.', $routeName);

            // Extract module name (example: admin.users.index → users)
            $module = $segments[1] ?? null;

            // Convert module to readable form
            $subject = $module ? ucfirst(str_replace('-', ' ', $module)) : 'System';

            // Convert route into a clean verb phrase
            $cleanRoute = ucfirst(str_replace(['.', '-'], ' ', $routeName));

            $this->storeLog(
                action: "Visited $cleanRoute",
                description: "{$user->role} {$user->name} accessed $cleanRoute.",
                subjectType: $subject,
                subjectId: null,
                subjectName: $subject
            );
        }

        return "$role performed an action";
    }
}
