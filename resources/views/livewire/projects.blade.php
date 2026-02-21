<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Projects
    </h2>
</x-slot>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
            <button wire:click="create()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded my-3">Add project</button>
            @if($isOpen)
                @include('livewire.editproject')
            @endif
            <table class="table-fixed w-full">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-700">
                        <th class="px-4 py-2">Project name</th>
                        <th class="px-4 py-2">Uses image</th>
                        <th class="px-4 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $p)
                    <tr>
                        <td class="border dark:border-gray-600 px-4 py-2">@if ($activeProject == $p->id) <b>{{ $p->name }}</b> (active project) @else {{ $p->name }} @endif</td>
                        <td class="border dark:border-gray-600 px-4 py-2">@if ($p->image) {{ $p->image->filename }} @else - @endif</td>
                        <td class="border dark:border-gray-600 px-4 py-2">
                            <button wire:click="edit({{ $p->id }})" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Edit</button>
                            <button wire:click="setActive({{ $p->id }})" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Set active</button>
                            <button wire:click="delete({{ $p->id }})" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td class="border dark:border-gray-600 px-4 py-2" colspan="3">No entries</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>