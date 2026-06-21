<header class="bg-white shadow px-6 py-4">

    <div class="flex justify-between">

        <h2 class="text-xl font-bold">Dashboard</h2>

        <div class="flex items-center gap-4">

            <div>
                {{ auth()->user()->name }}
                <div class="text-xs text-gray-500">
                    {{ auth()->user()->role }}
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="bg-red-600 text-white px-3 py-1 rounded">
                    Logout
                </button>
            </form>

        </div>

    </div>

</header>