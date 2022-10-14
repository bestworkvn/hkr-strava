<x-layout>
    <x-setting heading="Profile" >
        <x-panel>
            <h1 class="text-left font-bold text-xl">Edit profile</h1>
            <form method="POST" action="/profile/{{ $user->id }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <x-form.input name="title" :value="old('name', $user->name)" required />
                <x-form.input name="username" :value="old('username', $user->username)" required />
                <x-form.input name="email" :value="old('email', $user->email)" required />

                <x-form.button>Update</x-form.button>
            </form>
        </x-panel>

        <x-panel class="my-4">
            <h1 class="text-left font-bold text-xl">Change password</h1>
            <form method="POST" action="/profile/{{ $user->id }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <x-form.input name="Old password" required />
                <x-form.input name="New password" required />

                <x-form.button>Update</x-form.button>
            </form>
        </x-panel>

        <x-panel class="my-4">
            <h1 class="text-left font-bold text-xl my-4">Connect</h1>
            <a href="/profile/strava/auth"
               class="bg-blue-500 rounded-full text-xs font-semibold text-white uppercase py-3 px-5">
                {{ $user->is_connect_strava ? "Disconnect Strava" : "Connect Strava" }}
            </a>
        </x-panel>
    </x-setting>
</x-layout>
