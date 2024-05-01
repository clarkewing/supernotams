<x-layout.gradient-backdrop class="min-h-full">
    <div class="mx-auto max-w-2xl text-center">
        <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
            Stop missing critical NOTAMs
        </h2>
        <p class="mt-4 text-lg leading-8 text-gray-600">
            Get started with SuperNOTAMs by dropping in your ATC flight plan below.<br>
            Seriously, that’s all it takes!
        </p>
    </div>
    <form wire:submit="process" class="mx-auto mt-12 max-w-xl sm:mt-16">
        <div class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label for="message" class="sr-only">Paste your ATC flight plan here…</label>
                <div class="mt-2.5">
                    <textarea
                        name="fpl"
                        id="fpl"
                        wire:model="form.fpl"
                        rows="8"
                        placeholder="Paste your ATC flight plan here…"
                        @error('form.fpl')
                        aria-invalid="true" aria-describedby="fpl-error"
                        @enderror
                        class="block w-full rounded-md border-0 px-3.5 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-800 sm:text-sm sm:leading-6"
                    ></textarea>
                    @error('form.fpl')
                    <p class="mt-2 text-sm text-red-600" id="fpl-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        <div class="mt-8">
            <x-button class="w-full" size="lg" type="submit">Generate SuperBRIEF</x-button>
        </div>
    </form>
</x-layout.gradient-backdrop>
