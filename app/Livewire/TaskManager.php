<?php

namespace App\Http\Livewire;

use Illuminate\Foundation\Application;
use Illuminate\View\Factory;
use Illuminate\View\View;
use Livewire\Component;
use App\Models\Task;

class TaskManager extends Component
{
    public $tasks;

    public function mount(): void
    {
        $this->tasks = Task::all();
    }

    public function render(): Factory|Application|View
    {
        return view('livewire.task-manager');
    }

    /**
     * @var
     */
    public $newTaskName;

    public function addTask()
    {
        Task::create(['name' => $this->newTaskName]);
        $this->newTaskName = '';
        $this->tasks = Task::all();
    }

}
