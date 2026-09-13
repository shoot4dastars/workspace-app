<?php

namespace App\Http\Requests;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:255',
                Rule::unique('tasks', 'title')
                    ->where('project_id', $this->route('project')->id)
                    ->ignore($this->task),
            ],
            'description' => 'nullable|string|min:5|max:2000',
            'status' => ['required', Rule::enum(TaskStatus::class)],
            'priority' => ['nullable', Rule::enum(TaskPriority::class)],
            'assigned_to' => ['nullable', 'exists:users,id',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if ($value === null) {
                        return;
                    }

                    $project = $this->route('project') ?? optional($this->route('task'))->project;

                    if (! $project) {
                        $fail('The selected project context is invalid.');

                        return;
                    }

                    $isWorkspaceMember = $project->workspace->users()
                        ->where('users.id', $value)
                        ->exists();

                    if (! $isWorkspaceMember) {
                        $fail('The assigned user must be a member of the workspace.');
                    }
                },
            ],
            'due_date' => 'nullable|date',
        ];
    }
}
