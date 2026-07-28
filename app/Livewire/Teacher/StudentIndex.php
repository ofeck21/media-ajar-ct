<?php

namespace App\Livewire\Teacher;

use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class StudentIndex extends Component
{
    use WithPagination;

    public $search = '';

    // Form fields for editing
    public $studentId = null;
    public $name = '';
    public $nisn = '';
    public $password = '';

    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openEditModal(User $student)
    {
        $this->studentId = $student->id;
        $this->name = $student->name;
        $this->nisn = $student->nisn;
        $this->password = '';
        
        Flux::modal('edit-student-modal')->show();
    }

    public function updateStudent()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'nisn' => 'required|string|max:50|unique:users,nisn,' . $this->studentId,
            'password' => 'nullable|string|min:6',
        ]);

        $student = User::where('role', 'student')->findOrFail($this->studentId);

        $data = [
            'name' => $this->name,
            'username' => $this->nisn,
            'nisn' => $this->nisn,
        ];

        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        $student->update($data);

        Flux::modal('edit-student-modal')->close();
        $this->dispatch('close-modal', 'edit-student-modal');
        $this->reset(['studentId', 'name', 'nisn', 'password']);
    }

    public function deleteStudent(User $student)
    {
        if ($student->role === 'student') {
            $student->delete();
        }
    }

    public function render()
    {
        $user = auth()->user();

        $students = User::where('role', 'student')
            ->where('school_id', $user->school_id)
            ->with('studentClassrooms')
            ->when($this->search, function ($query) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('nisn', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(15);

        return view('livewire.teacher.student-index', [
            'students' => $students,
        ]);
    }
}
