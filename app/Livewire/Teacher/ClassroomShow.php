<?php

namespace App\Livewire\Teacher;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class ClassroomShow extends Component
{
    use WithPagination;

    public Classroom $classroom;

    public $name = '';
    public $nisn = '';
    public $search = '';
    public $sessionName = '';
    public $durationMinutes = 10;
    public $moveStudentId = null;
    public $moveTargetClassroom = null;
    public array $selectedUnassignedStudentIds = [];
    public $removingStudentId = null;
    public $removingStudentName = '';

    protected $queryString = ['search'];

    public function mount(Classroom $classroom)
    {
        // Ensure the teacher owns this classroom
        if ($classroom->teacher_id !== auth()->id()) {
            abort(403);
        }

        $this->classroom = $classroom;
    }

    public function confirmDeleteClassroom()
    {
        \Flux\Flux::modal('confirm-delete-classroom')->show();
    }

    public function deleteClassroom()
    {
        $this->classroom->delete();
        \Flux\Flux::modal('confirm-delete-classroom')->close();
        $this->redirectRoute('teacher.classrooms.index', navigate: true);
    }

    public function openMoveModal($studentId)
    {
        $this->moveStudentId = $studentId;
        $this->moveTargetClassroom = null;
        \Flux\Flux::modal('move-student')->show();
    }

    public function moveStudent()
    {
        $this->validate([
            'moveStudentId' => 'required|exists:users,id',
            'moveTargetClassroom' => 'required|exists:classrooms,id',
        ]);

        // Detach from current classroom
        $this->classroom->students()->detach($this->moveStudentId);

        // Attach to target classroom
        $targetClassroom = Classroom::find($this->moveTargetClassroom);
        $targetClassroom->students()->attach($this->moveStudentId);

        \Flux\Flux::modal('move-student')->close();
        $this->moveStudentId = null;
        $this->moveTargetClassroom = null;
    }

    public function confirmRemoveStudent($studentId, $studentName)
    {
        $this->removingStudentId = $studentId;
        $this->removingStudentName = $studentName;
        \Flux\Flux::modal('confirm-remove-student')->show();
    }

    public function executeRemoveStudent()
    {
        if ($this->removingStudentId) {
            $this->classroom->students()->detach($this->removingStudentId);
            $this->removingStudentId = null;
            $this->removingStudentName = '';
        }
        \Flux\Flux::modal('confirm-remove-student')->close();
    }

    public function addExistingStudent()
    {
        $this->validate([
            'selectedUnassignedStudentIds' => 'required|array|min:1',
            'selectedUnassignedStudentIds.*' => 'exists:users,id',
        ], [
            'selectedUnassignedStudentIds.required' => 'Pilih minimal satu siswa untuk dimasukkan ke kelas.',
            'selectedUnassignedStudentIds.min' => 'Pilih minimal satu siswa untuk dimasukkan ke kelas.',
        ]);

        $this->classroom->students()->syncWithoutDetaching($this->selectedUnassignedStudentIds);
        $this->selectedUnassignedStudentIds = [];
        \Flux\Flux::modal('add-unassigned-student')->close();
    }

    public function createSession()
    {
        $this->validate([
            'sessionName' => 'required|string|max:255',
            'durationMinutes' => 'required|integer|min:1|max:180',
        ]);

        $session = $this->classroom->gameSessions()->create([
            'teacher_id' => auth()->id(),
            'name' => $this->sessionName,
            'duration_minutes' => $this->durationMinutes,
            'status' => 'waiting',
        ]);

        \Flux\Flux::modal('create-session')->close();
        $this->redirectRoute('teacher.sessions.show', ['session' => $session], navigate: true);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function saveStudent()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'nisn' => 'required|string|max:50|unique:users,nisn',
        ]);

        $defaultPassword = Hash::make('12345678');

        $student = User::create([
            'name' => $this->name,
            'email' => null,
            'username' => $this->nisn,
            'nisn' => $this->nisn,
            'password' => $defaultPassword,
            'role' => 'student',
            'school_id' => $this->classroom->school_id,
        ]);

        // Attach to classroom pivot
        $this->classroom->students()->attach($student->id);

        $this->reset(['name', 'nisn']);
        \Flux\Flux::modal('create-student')->close();
    }

    public function render()
    {
        $students = $this->classroom->students()
            ->when($this->search, function ($query) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('nisn', 'like', '%' . $this->search . '%')
                      ->orWhere('username', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(15);

        $unassignedStudents = User::where('role', 'student')
            ->where('school_id', auth()->user()->school_id)
            ->doesntHave('studentClassrooms')
            ->get();

        return view('livewire.teacher.classroom-show', [
            'students' => $students,
            'otherClassrooms' => auth()->user()->classrooms()->where('id', '!=', $this->classroom->id)->get(),
            'unassignedStudents' => $unassignedStudents,
        ]);
    }
}
