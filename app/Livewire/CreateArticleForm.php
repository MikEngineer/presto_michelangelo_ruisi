<?php

namespace App\Livewire;

use App\Models\Article;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;

class CreateArticleForm extends Component
{
    #[Validate('required|min:5')]
    public $title;
    
    #[Validate('required|min:10')]
    public $description;
    
    #[Validate('required|numeric')]
    public $price;
    
    #[Validate('required')]
    public $category;
    
    public $article;
    
    public function store()
    {
        $this->validate();
        
        $this->article = Article::create([
            'title'       => $this->title,
            'description' => $this->description,
            'price'       => $this->price,
            'category'    => $this->category,
            'user_id'     => Auth::id(),
        ]);

        $this->reset();

        session()->flash('success', 'Articolo creato correttamente');
        // $this->cleanForm();
    }

    // Alternativamente possiamo creare un metodo custom:
    
    // protected function cleanForm()
    // {
    //     $this->title = '';
    //     $this->description = '';
    //     $this->category = '';
    //     $this->price = '';
    // }
    
    
    public function render()
    {
        return view('livewire.create-article-form');
    }
}
