<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        List of CMs
    </h2>
</x-slot>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg px-4 py-4">

        <span class="dark:text-gray-200">Filter by project:</span>
        <select wire:model="projectId" class="dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600">
            <option value="0">--all projects--</option>
            @foreach ($projects as $project)
            <option value="{{ $project->id }}">{{ $project->name }} ({{ $project->cms_count }})</option>
            @endforeach
        </select>
        </div><br>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg px-4 py-4">
            @if (session()->has('message'))
                <div class="bg-teal-100 dark:bg-teal-900 border-t-4 border-teal-500 rounded-b text-teal-900 dark:text-teal-100 px-4 py-3 shadow-md my-3" role="alert">
                  <div class="flex">
                    <div>
                      <p class="text-sm">{{ session('message') }}</p>
                    </div>
                  </div>
                </div>
            @endif
            @if($isOpen)
                @include('livewire.viewcm')
            @endif
            <table class="table-fixed w-full">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-700">
                        <th class="px-4 py-2">CM serial</th>
                        <th class="px-4 py-2">MAC</th>
                        <th class="px-4 py-2">Module type</th>
                        <th class="px-4 py-2">Provisioning complete</th>
                        <th class="px-4 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($CMs as $c)
                    <tr>
                        <td class="border dark:border-gray-600 px-4 py-2 dark:text-gray-200">{{ $c->serial }}</td>
                        <td class="border dark:border-gray-600 px-4 py-2 dark:text-gray-200">{{ $c->mac }}</td>
                        <td class="border dark:border-gray-600 px-4 py-2 dark:text-gray-200">{{ $c->model }}</td>
                        <td class="border dark:border-gray-600 px-4 py-2 dark:text-gray-200">@if ($c->provisioning_complete_at) {{ $c->provisioning_complete_at }} @else No @endif</td>
                        <td class="border dark:border-gray-600 px-4 py-2">
                            <button wire:click="edit({{ $c->id }})" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">View</button>
                            <button wire:click="delete({{ $c->id }})" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan=5 class="border dark:border-gray-600 px-4 py-2 dark:text-gray-200">No entries</td></tr>
                    @endforelse
                </tbody>
            </table>

            <br>
            <button wire:click="exportCSV()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Export as CSV</button>
        </div>
    </div>
</div>