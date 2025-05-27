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

    public function __construct()
    {
        // Default constructor for Filament auto-discovery
        // No parent::__construct() call needed for Filament widgets
    }

    /**
     * Create a collapsible widget group with configuration
     */
    public static function create(
        string $title,
        array $widgets,
        bool $collapsible = true,
        bool $collapsed = false,
        string $icon = ''
    ): static {
        $instance = new static();
        $instance->title = $title;
        $instance->widgets = $widgets;
        $instance->collapsible = $collapsible;
        $instance->collapsed = $collapsed;
        $instance->icon = $icon;
        
        return $instance;
    }

    /**
     * Set the title for this widget group
     */
    public function title(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Set the widgets for this group
     */
    public function widgets(array $widgets): static
    {
        $this->widgets = $widgets;
        return $this;
    }

    /**
     * Make this widget group collapsible
     */
    public function collapsible(bool $collapsible = true): static
    {
        $this->collapsible = $collapsible;
        return $this;
    }

    /**
     * Set initial collapsed state
     */
    public function collapsed(bool $collapsed = true): static
    {
        $this->collapsed = $collapsed;
        return $this;
    }

    /**
     * Set the icon for this widget group
     */
    public function icon(string $icon): static
    {
        $this->icon = $icon;
        return $this;
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
