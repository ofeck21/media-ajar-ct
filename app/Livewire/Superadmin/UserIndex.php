<?php

namespace App\Livewire\Superadmin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = '';

    protected $queryString = ['search', 'roleFilter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $users = User::with('school')
            ->when($this->search, function ($query) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('username', 'like', '%' . $this->search . '%')
                      ->orWhere('nisn', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->roleFilter, function ($query) {
                $query->where('role', $this->roleFilter);
            })
            ->latest()
            ->paginate(15);

        return view('livewire.superadmin.user-index', [
            'users' => $users,
        ]);
    }
}
