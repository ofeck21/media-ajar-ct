<?php

namespace App\Livewire\Teacher;

use App\Models\Classroom;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

class ClassroomIndex extends Component
{
    use WithPagination;

    public $name = '';
    public $search = '';

    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = auth()->user();

        Classroom::create([
            'name' => $this->name,
            'teacher_id' => $user->id,
            'school_id' => $user->school_id,
        ]);

        $this->name = '';
        Flux::modal('create-classroom')->close();
        $this->dispatch('close-modal', 'create-classroom');
    }

    public function render()
    {
        $classrooms = auth()->user()->classrooms()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(12);

        return view('livewire.teacher.classroom-index', [
            'classrooms' => $classrooms,
        ]);
    }
}
