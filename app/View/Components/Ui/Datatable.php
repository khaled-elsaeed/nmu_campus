<?php

namespace App\View\Components\Ui;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Datatable extends Component
{
    public array $headers;
    public array $columns;
    public string $ajaxUrl;
    public array $filterFields;
    public string $tableId;

    /**
     * Create a new component instance.
     */
    public function __construct(
        array $headers = [],
        array $columns = [],
        string $ajaxUrl = '',
        array $filterFields = [],
        string $tableId = 'datatable'
    ) {
        $this->headers = $headers;
        $this->columns = $columns;
        $this->ajaxUrl = $ajaxUrl;
        $this->filterFields = $filterFields;
        $this->tableId = $tableId;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ui.datatable');
    }
}