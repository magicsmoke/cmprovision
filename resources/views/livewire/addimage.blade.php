<div class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400">
  <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

    <div class="fixed inset-0 transition-opacity">
      <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>

    <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>​

    <div x-data="{ uploading: false, progress: 0, error: '' }" class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
      <form method="post" action="/addImage" enctype="multipart/form-data"
            @submit.prevent="
              const fileInput = $el.querySelector('#image');
              if (!fileInput.files.length) return;

              uploading = true;
              progress = 0;
              error = '';

              const formData = new FormData();
              formData.append('image', fileInput.files[0]);
              formData.append('_token', '{{ csrf_token() }}');

              const xhr = new XMLHttpRequest();
              xhr.open('POST', '/addImage');
              xhr.setRequestHeader('Accept', 'application/json');

              xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                  progress = Math.round((e.loaded / e.total) * 100);
                }
              });

              xhr.addEventListener('load', () => {
                if (xhr.status >= 200 && xhr.status < 300) {
                  window.location.href = '/images';
                } else {
                  try {
                    const resp = JSON.parse(xhr.responseText);
                    error = resp.errors ? Object.values(resp.errors).flat().join(' ') : (resp.message || 'Upload failed.');
                  } catch (e) {
                    error = 'Upload failed (status ' + xhr.status + ').';
                  }
                  uploading = false;
                  progress = 0;
                }
              });

              xhr.addEventListener('error', () => {
                error = 'Network error. Check your connection and try again.';
                uploading = false;
                progress = 0;
              });

              xhr.send(formData);
            ">
      <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
        <div class="">
              <div class="mb-4">
                  <label for="image" class="block text-gray-700 dark:text-gray-200 text-sm font-bold mb-2">Image file (.gz/.bz2/.xz):</label>
                  <input type="file" id="image" name="image" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 leading-tight focus:outline-none focus:shadow-outline">
              </div>
              <div class="mb-4">
                <b>Notes:</b><br><br>
                  <ul class="px-6 list-disc">
                    <li> Images must be uploaded compressed (.gz/.bz2/.xz).
                    <li> Do NOT use tar.
                    <li> Upload file size limit configured in php.ini: {{ number_format($maxfilesize/1024/1024/1024, 1) }} GiB.
                    <li> Disk space available: {{ number_format( $freediskspace/1024/1024/1024, 1) }} GiB (should be at least twice the size of image).
                    <li> Be aware that after upload is finished it will compute the sha256sum on the server, which may take some time.
                  </ul>
              </div>

              <template x-if="error">
                <div class="bg-red-100 dark:bg-red-900 border-t-4 border-red-500 rounded-b text-red-900 dark:text-red-100 px-4 py-3 shadow-md my-3" role="alert">
                  <p class="text-sm" x-text="error"></p>
                </div>
              </template>

              @if ($os32bit)
                <div class="bg-teal-100 dark:bg-teal-900 border-t-4 border-teal-500 rounded-b text-teal-900 dark:text-teal-100 px-4 py-3 shadow-md my-3" role="alert">
                  <div class="flex">
                    <div>
                      <p class="text-sm">The provisioning system seems to be running on a 32-bit OS. As a result image upload size is limited to 2 GB.</p>
                    </div>
                  </div>
                </div>
              @endif
        </div>
      </div>

      <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
        <template x-if="!uploading">
          <div class="sm:flex sm:flex-row-reverse w-full">
            <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
              <button type="submit" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-green-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-green-500 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                Upload
              </button>
            </span>
            <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
              <button wire:click="closeModal()" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 dark:border-gray-600 px-4 py-2 bg-white dark:bg-gray-700 text-base leading-6 font-medium text-gray-700 dark:text-gray-200 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                Cancel
              </button>
            </span>
          </div>
        </template>
        <template x-if="uploading">
          <div class="w-full">
            <div class="flex justify-between mb-1">
              <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Uploading...</span>
              <span class="text-sm font-medium text-gray-700 dark:text-gray-200" x-text="progress + '%'"></span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-4">
              <div class="bg-green-600 h-4 rounded-full transition-all duration-150" :style="'width: ' + progress + '%'"></div>
            </div>
          </div>
        </template>
        </form>
      </div>

    </div>
  </div>
</div>
