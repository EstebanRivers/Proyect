<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public $currentView = 'dashboard';
    public $user;

    public function mount()
    {
        $this->user = Auth::user();
    }

    public function navigateTo($view)
    {
        // Verificar permisos según el rol
        if (!$this->canAccessView($view)) {
            session()->flash('error', 'No tienes permisos para acceder a esta sección.');
            return;
        }
        
        $this->currentView = $view;
    }

    private function canAccessView($view)
    {
        switch ($view) {
            case 'courses':
                return $this->user->hasAnyRole(['teacher', 'student', 'admin']);
            case 'billing':
                return $this->user->hasAnyRole(['billing', 'admin']);
            case 'admin':
                return $this->user->hasRole('admin');
            default:
                return true;
        }
    }

    public function render()
    {
        return view('livewire.dashboard')->layout('layouts.app');
    }
}