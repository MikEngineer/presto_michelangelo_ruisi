<x-layout>
    <div class="container pt-5">
        <div class="row justify-content-center">
            <div class="col-12 text-center">
                <h1 class="display-4 pt-5">
                    Pubblica un articolo
                </h1>
            </div>
        </div>
        
        <div class="row justify-content-center align-items-center height-custom">
            <div class="col-12 col-md-6">
                <livewire:create-article-form />
                {{-- Le sintassi <livewire:create-article-form /> e @livewire('create-article-form') si equivalgono --}}
            </div>
        </div>
    </div>
</x-layout>
