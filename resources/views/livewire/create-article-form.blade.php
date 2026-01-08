<form class="bg-body-tertiary shadow rounded p-5 my-5" wire:submit="store">
    <div class="mb-3">
        <label for="title" class="form-label">Titolo:</label>
        <input
        type="text"
        class="form-control @error('title') is-invalid @enderror"
        id="title"
        wire:model.blur="title"
        >
        @error('title')
        <p class="fst-italic text-danger">{{ $message }}</p>
        @enderror
    </div>
    
    <div class="mb-3">
        <label for="description" class="form-label">Descrizione:</label>
        <textarea
        id="description"
        cols="30"
        rows="10"
        class="form-control @error('description') is-invalid @enderror"
        wire:model.blur="description"
        ></textarea>
        @error('description')
        <p class="fst-italic text-danger">{{ $message }}</p>
        @enderror
    </div>
    
    <div class="mb-3">
        <label for="price" class="form-label">Prezzo:</label>
        <input
        type="text"
        class="form-control @error('price') is-invalid @enderror"
        id="price"
        wire:model.blur="price"
        >
        @error('price')
        <p class="fst-italic text-danger">{{ $message }}</p>
        @enderror
    </div>
    
    <div class="mb-3">
        <select
        id="category"
        wire:model.blur="category"
        class="form-control @error('category') is-invalid @enderror"
        >
        <option label disabled>Seleziona una categoria</option>
        @foreach ($categories as $category)
        <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
    </select>
    
    @error('category')
    <p class="fst-italic text-danger">{{ $message }}</p>
    @enderror
</div>

@if (session()->has('success'))
<div class="alert alert-success text-center">
    {{ session('success') }}
</div>
@endif


<div class="d-flex justify-content-center">
    <button type="submit" class="btn btn-dark">Crea</button>
</div>
</form>

{{-- Come vediamo, i wire:model hanno il modificatore .blur , così da vedere l’errore non solo al click del bottone di submit, ma anche
quando l’utente clicca fuori dall’input senza aver rispettato tutte le regole.
Alternativamente, è possibile utilizzare il modificatore .live per vedere le modifiche in tempo reale, o .debounce , per vedere le modifiche
dopo un tempo da noi settato. --}}
