<?php

namespace App\View\Components\Ui\DataTable;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TableActions extends Component
{
    public string $mode;
    public string $type;
    public string $icon;
    public ?string $id;
    public array $actions;
    public string $action;
    public array $singleActions;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $mode = 'dropdown',
        string $type = 'primary',
        string $icon = 'bx bx-pie-chart-alt',
        ?string $id = null,
        array $actions = ['view', 'edit', 'delete'],
        string $action = 'default',
        array $singleActions = []
    ) {
        $this->mode = $mode;
        $this->type = $type;
        $this->icon = $icon;
        $this->id = $id;
        $this->actions = $actions;
        $this->action = $action;
        $this->singleActions = $this->normalizeSingleActions($singleActions);
    }

    /**
     * Normalize single actions to ensure consistent format
     */
    private function normalizeSingleActions(array $singleActions): array
    {
        $normalized = [];
        
        foreach ($singleActions as $key => $value) {
            if (is_string($key)) {
                // Format: ['view' => 'bx bx-show']
                $normalized[] = [
                    'action' => $key,
                    'icon' => $value,
                    'class' => 'btn-secondary',
                    'label' => ucfirst($key)
                ];
            } elseif (is_array($value)) {
                // Format: [['action' => 'view', 'icon' => 'bx bx-show', 'class' => 'btn-info']]
                $normalized[] = array_merge([
                    'action' => 'default',
                    'icon' => 'bx bx-cog',
                    'class' => 'btn-secondary',
                    'label' => 'Action'
                ], $value);
            } else {
                // Format: ['view', 'edit']
                $normalized[] = [
                    'action' => $value,
                    'icon' => $this->getDefaultIcon($value),
                    'class' => 'btn-secondary',
                    'label' => ucfirst($value)
                ];
            }
        }
        
        return $normalized;
    }

    /**
     * Get default icon for common actions
     */
    private function getDefaultIcon(string $action): string
    {
        $icons = [
            'view' => 'bx bx-show',
            'edit' => 'bx bx-edit-alt',
            'delete' => 'bx bx-trash',
            'download' => 'bx bx-download',
            'print' => 'bx bx-printer',
            'share' => 'bx bx-share',
            'copy' => 'bx bx-copy',
            'archive' => 'bx bx-archive',
            'restore' => 'bx bx-refresh',
            'duplicate' => 'bx bx-duplicate',
        ];

        return $icons[$action] ?? 'bx bx-cog';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ui.datatable.table-actions', [
            'mode' => $this->mode,
            'type' => $this->type,
            'icon' => $this->icon,
            'id' => $this->id,
            'actions' => $this->actions,
            'action' => $this->action,
            'singleActions' => $this->singleActions,
        ]);
    }
}