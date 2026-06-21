<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Mail\EmployeeWelcomeMail;
use App\Models\User;
use App\Services\AdminActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::employees()->orderBy('name');

        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        return view('admin.employees.index', [
            'employees' => $query->get(),
            'roleOptions' => UserRole::employeeOptions(),
            'createRoleOptions' => $this->availableRolesForCreate(),
            'groupOptions' => $this->groupOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedEmployee($request);
        $plainPassword = Str::password(10, letters: true, numbers: true, symbols: false);
        $data['password'] = $plainPassword;
        $data['joined_at'] = now()->toDateString();

        $employee = User::create($data);

        AdminActivityLogger::log('user', $employee->id, 'created', "إضافة حساب موظف: {$employee->name}");

        $mailSent = $this->sendWelcomeEmail($employee, $plainPassword);

        $message = $mailSent
            ? 'تمت إضافة الموظف وإرسال بيانات الدخول إلى بريده الإلكتروني.'
            : 'تمت إضافة الموظف، لكن تعذّر إرسال بريد الترحيب. شارك بيانات الدخول يدوياً.';

        return back()->with('success', $message);
    }

    private function sendWelcomeEmail(User $employee, string $plainPassword): bool
    {
        try {
            Mail::to($employee->email)->send(new EmployeeWelcomeMail($employee, $plainPassword));

            return true;
        } catch (\Throwable $e) {
            Log::error('Employee welcome email failed', [
                'employee_id' => $employee->id,
                'email' => $employee->email,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function update(Request $request, User $employee): RedirectResponse
    {
        $this->ensureEmployee($employee);

        $data = $this->validatedEmployee($request, $employee);
        $employee->update($data);

        AdminActivityLogger::log('user', $employee->id, 'updated', "تحديث حساب الموظف: {$employee->name}");

        return back()->with('success', 'تم حفظ التعديلات.');
    }

    public function toggleStatus(User $employee): RedirectResponse
    {
        $this->ensureEmployee($employee);

        $willActivate = $employee->status !== 'active';

        if ($willActivate) {
            $conflict = $this->findActiveAccountConflict(
                $employee->role,
                $employee->assigned_group,
                $employee->id,
            );

            if ($conflict) {
                return back()->with(
                    'error',
                    "لا يمكن تفعيل هذا الحساب. يوجد حساب مفعّل حالياً لمنصب «{$employee->role}».",
                );
            }
        }

        $employee->update([
            'status' => $willActivate ? 'active' : 'inactive',
        ]);

        $label = $employee->status === 'active' ? 'تفعيل' : 'إيقاف';

        AdminActivityLogger::log('user', $employee->id, 'status', "{$label} حساب: {$employee->name}");

        return back()->with(
            'success',
            $employee->status === 'active' ? 'تم تفعيل الحساب.' : 'تم تعطيل الحساب.',
        );
    }

    private function ensureEmployee(User $employee): void
    {
        if (! $employee->isEmployeeAccount()) {
            abort(404);
        }
    }

    /** @return array<string, mixed> */
    private function validatedEmployee(Request $request, ?User $employee = null): array
    {
        $role = $request->string('role')->toString();
        $roleEnum = UserRole::tryFrom($role);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($employee?->id),
            ],
            'role' => ['required', Rule::in(UserRole::employeeOptions())],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'assigned_group' => [
                Rule::requiredIf($roleEnum?->requiresAssignedGroup() ?? false),
                'nullable',
                'string',
                'max:255',
                Rule::in(animal_groups()),
            ],
        ];

        $data = $request->validate($rules, [
            'email.unique' => 'هذا البريد الإلكتروني مسجّل مسبقاً. لا يمكن إضافة نفس الحساب مرتين.',
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'name.required' => 'اسم الموظف مطلوب.',
            'role.required' => 'يجب اختيار الدور الوظيفي.',
            'assigned_group.required' => 'يجب اختيار المجموعة المسندة لهذا الدور.',
        ]);

        if (! ($roleEnum?->requiresAssignedGroup())) {
            $data['assigned_group'] = null;
        }

        if ($roleEnum?->requiresAssignedGroup() && filled($data['assigned_group'] ?? null)) {
            $duplicateExists = User::query()
                ->where('role', $data['role'])
                ->where('assigned_group', $data['assigned_group'])
                ->where('status', 'active')
                ->when($employee, fn ($query) => $query->whereKeyNot($employee->id))
                ->exists();

            if ($duplicateExists) {
                $roleLabel = $data['role'];
                $groupLabel = $data['assigned_group'];

                return throw \Illuminate\Validation\ValidationException::withMessages([
                    'assigned_group' => ["يوجد {$roleLabel} مفعّل مسبقاً للمجموعة «{$groupLabel}». لا يمكن إضافة أكثر من حساب بنفس الدور لنفس المجموعة."],
                ]);
            }
        }

        if (($data['status'] ?? 'inactive') === 'active') {
            $conflict = $this->findActiveAccountConflict(
                $data['role'],
                $data['assigned_group'] ?? null,
                $employee?->id,
            );

            if ($conflict) {
                return throw \Illuminate\Validation\ValidationException::withMessages([
                    'role' => ["يوجد حساب مفعّل حالياً لمنصب «{$data['role']}». لا يمكن تفعيل أكثر من حساب لنفس المنصب."],
                ]);
            }
        }

        if (! $employee && $roleEnum?->isSingleAccountRole()) {
            $occupied = User::query()
                ->where('role', $data['role'])
                ->where('status', 'active')
                ->exists();

            if ($occupied) {
                return throw \Illuminate\Validation\ValidationException::withMessages([
                    'role' => ["يوجد حساب مفعّل حالياً لمنصب «{$data['role']}». لا يمكن إنشاء حساب جديد لنفس المنصب."],
                ]);
            }
        }

        return $data;
    }

    /** @return list<string> */
    private function availableRolesForCreate(): array
    {
        $occupiedSingleRoles = User::query()
            ->employees()
            ->where('status', 'active')
            ->whereIn('role', collect(UserRole::employeeOptions())
                ->filter(fn (string $role) => UserRole::tryFrom($role)?->isSingleAccountRole())
                ->values()
                ->all())
            ->pluck('role')
            ->all();

        return array_values(array_filter(
            UserRole::employeeOptions(),
            fn (string $role) => ! in_array($role, $occupiedSingleRoles, true),
        ));
    }

    private function findActiveAccountConflict(string $role, ?string $assignedGroup, ?int $ignoreUserId = null): bool
    {
        $roleEnum = UserRole::tryFrom($role);

        if (! $roleEnum) {
            return false;
        }

        $query = User::query()
            ->employees()
            ->where('role', $role)
            ->where('status', 'active')
            ->when($ignoreUserId, fn ($builder) => $builder->whereKeyNot($ignoreUserId));

        if ($roleEnum->requiresAssignedGroup()) {
            if (! filled($assignedGroup)) {
                return false;
            }

            $query->where('assigned_group', $assignedGroup);
        }

        return $query->exists();
    }

    /** @return list<string> */
    private function groupOptions(): array
    {
        return animal_groups();
    }
}
