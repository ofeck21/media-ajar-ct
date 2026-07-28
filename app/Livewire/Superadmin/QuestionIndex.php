<?php

namespace App\Livewire\Superadmin;

use App\Models\DiagnosticQuestion;
use Livewire\Component;
use Livewire\WithPagination;

class QuestionIndex extends Component
{
    use WithPagination;

    public $search = '';

    public $isEditing = false;
    public $questionId = null;
    public $question = '';
    public $options = ['', '', '', ''];
    public $correct_answer_index = 0;

    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->reset(['isEditing', 'questionId', 'question', 'options', 'correct_answer_index']);
        $this->options = ['', '', '', ''];
        $this->dispatch('open-modal', 'question-modal');
    }

    public function openEditModal(DiagnosticQuestion $question)
    {
        $this->isEditing = true;
        $this->questionId = $question->id;
        $this->question = $question->question;
        $this->options = $question->options;
        $this->correct_answer_index = $question->correct_answer_index;
        
        $this->dispatch('open-modal', 'question-modal');
    }

    public function save()
    {
        $this->validate([
            'question' => 'required|string',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string',
            'correct_answer_index' => 'required|integer|min:0|max:' . (count($this->options) - 1),
        ]);

        if ($this->isEditing) {
            $q = DiagnosticQuestion::findOrFail($this->questionId);
            $q->update([
                'question' => $this->question,
                'options' => $this->options,
                'correct_answer_index' => (int) $this->correct_answer_index,
            ]);
        } else {
            DiagnosticQuestion::create([
                'question' => $this->question,
                'options' => $this->options,
                'correct_answer_index' => (int) $this->correct_answer_index,
            ]);
        }

        \Flux\Flux::modal('question-modal')->close();
        $this->dispatch('close-modal', 'question-modal');
    }

    public function delete(DiagnosticQuestion $question)
    {
        $question->delete();
    }

    public function render()
    {
        $questions = DiagnosticQuestion::when($this->search, function ($query) {
                $query->where('question', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(15);

        return view('livewire.superadmin.question-index', [
            'questions' => $questions,
        ]);
    }
}
