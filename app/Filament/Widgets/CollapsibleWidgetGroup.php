<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;

class CollapsibleWidgetGroup extends Widget
{
    protected static string $view = 'filament.widgets.collapsible-widget-group';

    public string $title = '';
    public bool $collapsible = true;
    public bool $collapsed = false;
    public array $widgets = [];
    public string $icon = '';

    public function __construct(
        string $title,
        array $widgets,
        bool $collapsible = true,
        bool $collapsed = false,
        string $icon = ''
    ) {
        $this->title = $title;
        $this->widgets = $widgets;
        $this->collapsible = $collapsible;
        $this->collapsed = $collapsed;
        $this->icon = $icon;
    }

    public function render(): View
    {
        return view(static::$view, [
            'title' => $this->title,
            'collapsible' => $this->collapsible,
            'collapsed' => $this->collapsed,
            'widgets' => $this->widgets,
            'icon' => $this->icon,
        ]);
    }

    /**
     * تحديد عرض العمود
     */
    protected int | string | array $columnSpan = 'full';

    /**
     * ترتيب منخفض للظهور في الأعلى
     */
    protected static ?int $sort = 0;
}
