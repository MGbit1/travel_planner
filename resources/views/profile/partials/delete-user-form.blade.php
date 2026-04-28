<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            刪除帳號
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            帳號刪除後，所有資料（行程、貼文、留言）將永久移除且無法復原。若有需要保留的資料，請先自行備份。
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >刪除帳號</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900">
                確定要刪除帳號嗎？
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                此操作無法復原，所有資料將永久刪除。請輸入您的密碼以確認刪除。
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="密碼" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="輸入密碼確認"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    取消
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    確認刪除帳號
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
