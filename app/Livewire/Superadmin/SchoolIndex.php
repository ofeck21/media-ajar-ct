<?php

namespace App\Livewire\Superadmin;

use App\Models\School;
use Livewire\Component;
use Livewire\WithPagination;

class SchoolIndex extends Component
{
    use WithPagination;

    public $search = '';

    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $schools = School::withCount('users')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('address', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.superadmin.school-index', [
            'schools' => $schools,
        ]);
    }
}
