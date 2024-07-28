<div>
    <h1>Завдання</h1>
    <ul>
        @foreach($tasks as $task)
            <li>{{ $task->name }}</li>
        @endforeach
    </ul>
    <form wire:submit.prevent="addTask">
        <input type="text" wire:model="newTaskName">
        <button type="submit">Додати завдання</button>
    </form>
</div>
