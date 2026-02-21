<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Firmware
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
            <button wire:click="update()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded my-3">Download new firmware from github</button>
            <table class="table-fixed min-w-full">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-700">
                        <th class="px-4 py-2">Name</th>
                        <th class="px-4 py-2">Channel</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($firmware as $f)
                    <tr>
                        <td class="border dark:border-gray-600 px-4 py-2">{{ $f->name }}</td>
                        <td class="border dark:border-gray-600 px-4 py-2">{{ $f->channel }}</td>
                    </tr>
                    @empty
                    <tr><td class="border dark:border-gray-600 px-4 py-2" colspan="2">No entries</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>