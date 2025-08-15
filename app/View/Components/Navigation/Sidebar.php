<?php

namespace App\View\Components\Navigation;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class Sidebar extends Component
{
    public array $menuItems;

    /**
     * Create a new component instance.
     */
    public function __construct(array $menuItems = [])
    {
        $this->menuItems = $menuItems ?: $this->getFilteredMenuItems();
    }

    /**
     * Filter menu items based on user permissions.
     */
    private function filterMenuItem(array $item, ?Authenticatable $user): ?array
    {
        // Skip if user lacks required permission
        if (isset($item['permission']) && (!$user || !$user->can($item['permission']))) {
            return null;
        }

        // Recursively filter children
        if (isset($item['children'])) {
            $filteredChildren = array_filter(
                array_map(fn($child) => $this->filterMenuItem($child, $user), $item['children']),
                fn($child) => !is_null($child)
            );

            // Skip group if no children remain
            if (empty($filteredChildren)) {
                return null;
            }

            $item['children'] = array_values($filteredChildren);
        }

        return $item;
    }

    /**
     * Get filtered menu items based on user permissions.
     */
    private function getFilteredMenuItems(): array
    {
        $user = Auth::user();
        $menuItems = $this->getMenuItems();

        $filteredItems = array_values(array_filter(
            array_map(fn($item) => $this->filterMenuItem($item, $user), $menuItems),
            fn($item) => !is_null($item)
        ));

        return $filteredItems;
    }

    /**
     * Render the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.navigation.sidebar');
    }

    /**
     * Get all menu items configuration.
     */
    private function getMenuItems(): array
    {
        return [
            $this->getDashboardMenuItem(),
            $this->getAcademicMenuGroup(),
            $this->getHousingMenuGroup(),
            $this->getResidentsMenuGroup(),
            $this->getReservationsMenuGroup(),
            $this->getPaymentsMenuGroup(),
        ];
    }

    /**
     * Get dashboard menu item.
     */
    private function getDashboardMenuItem(): array
    {
        return [
            'title' => __('Dashboard'),
            'icon' => 'bx bx-home-circle',
            'route' => route('home'),
            'active' => in_array(request()->route()->getName(), ['home', 'admin.home', 'advisor.home']),
            'permission' => 'dashboard.view',
        ];
    }

    /**
     * Get academic menu group.
     */
    private function getAcademicMenuGroup(): array
    {
        return [
            'title' => __('Academic'),
            'icon' => 'bx bx-book',
            'route' => '#',
            'type' => 'group',
            'active' => request()->routeIs('academic.*'),
            'children' => [
                [
                    'title' => __('Academic Terms'),
                    'icon' => 'bx bx-calendar-event',
                    'route' => route('academic.academic_terms.index'),
                    'active' => str_starts_with(request()->route()->getName(), 'academic.academic_terms.'),
                    'permission' => 'academic.academic_terms.view',
                ],
                [
                    'title' => __('Faculties'),
                    'icon' => 'bx bx-building-house',
                    'route' => route('academic.faculties.index'),
                    'active' => str_starts_with(request()->route()->getName(), 'academic.faculties.'),
                    'permission' => 'academic.faculties.view',
                ],
                [
                    'title' => __('Programs'),
                    'icon' => 'bx bx-book-content',
                    'route' => route('academic.programs.index'),
                    'active' => str_starts_with(request()->route()->getName(), 'academic.programs.'),
                    'permission' => 'academic.programs.view',
                ],
            ],
        ];
    }

    /**
     * Get housing management menu group.
     */
    private function getHousingMenuGroup(): array
    {
        return [
            'title' => __('Housing Management'),
            'icon' => 'bx bx-buildings',
            'route' => '#',
            'type' => 'group',
            'active' => request()->routeIs('housing.*'),
            'children' => [
                [
                    'title' => __('Building'),
                    'icon' => 'bx bx-buildings',
                    'route' => route('housing.buildings.index'),
                    'active' => str_starts_with(request()->route()->getName(), 'housing.buildings.'),
                    'permission' => 'housing.buildings.view',
                ],
                [
                    'title' => __('Apartment'),
                    'icon' => 'bx bx-building',
                    'route' => route('housing.apartments.index'),
                    'active' => str_starts_with(request()->route()->getName(), 'housing.apartments.'),
                    'permission' => 'housing.apartments.view',
                ],
                [
                    'title' => __('Room'),
                    'icon' => 'bx bx-door-open',
                    'route' => route('housing.rooms.index'),
                    'active' => str_starts_with(request()->route()->getName(), 'housing.rooms.'),
                    'permission' => 'housing.rooms.view',
                ],
            ],
        ];
    }

    /**
     * Get residents menu group.
     */
    private function getResidentsMenuGroup(): array
    {
        return [
            'title' => __('Residents'),
            'icon' => 'bx bx-group',
            'route' => '#',
            'type' => 'group',
            'active' => request()->routeIs('resident.*'),
            'children' => [
                [
                    'title' => __('Students'),
                    'icon' => 'bx bx-user',
                    'route' => route('resident.students.index'),
                    'active' => str_starts_with(request()->route()->getName(), 'resident.students.'),
                    'permission' => 'resident.students.view',
                ],
                [
                    'title' => __('Staff'),
                    'icon' => 'bx bx-id-card',
                    'route' => route('resident.staff.index'),
                    'active' => str_starts_with(request()->route()->getName(), 'resident.staff.'),
                    'permission' => 'resident.staff.view',
                ],
            ],
        ];
    }

    /**
     * Get reservations menu group.
     */
    private function getReservationsMenuGroup(): array
    {
        $route = request()->route();
        $routeName = $route ? $route->getName() : '';

        return [
            'title' => __('Reservations'),
            'icon' => 'bx bx-calendar',
            'route' => route('reservations.index'),
            'active' => str_starts_with(request()->route()->getName(), 'reservations.') ||
                       str_starts_with(request()->route()->getName(), 'reservation-requests.'),
            'permission' => 'reservations.view',
            'children' => [
                [
                    'title' => __('View'),
                    'icon' => 'bx bx-list-ul',
                    'route' => route('reservations.index'),
                    'active' => request()->routeIs('reservations.index'),
                    'permission' => 'reservations.view',
                ],
                [
                    'title' => __('Create'),
                    'icon' => 'bx bx-plus',
                    'route' => route('reservations.create'),
                    'active' => request()->routeIs('reservations.create'),
                    'permission' => 'reservations.create',
                ],
                [
                    'title' => __('Check In/Out'),
                    'icon' => 'bx bx-log-in-circle',
                    'route' => '#',
                    'type' => 'group',
                    'active' => request()->routeIs('reservations.check-in') || 
                               request()->routeIs('reservations.check-out'),
                    'permission' => 'reservations.check_in_out',
                    'children' => [
                        [
                            'title' => __('Check In'),
                            'icon' => 'bx bx-log-in',
                            'route' => route('reservations.check-in'),
                            'active' => request()->routeIs('reservations.check-in'),
                            'permission' => 'reservations.check_in_out',
                        ],
                        [
                            'title' => __('Check Out'),
                            'icon' => 'bx bx-log-out',
                            'route' => route('reservations.check-out'),
                            'active' => request()->routeIs('reservations.check-out'),
                            'permission' => 'reservations.check_in_out',
                        ],
                    ],
                ],
                [
                    'title' => __('Requests'),
                    'icon' => 'bx bx-calendar-check',
                    'route' => '#',
                    'type' => 'group',
                    'active' => str_starts_with($routeName, 'reservation-requests.'),
                    'permission' => 'reservation_requests.view',
                    'children' => [
                        [
                            'title' => __('View'),
                            'icon' => 'bx bx-list-check',
                            'route' => route('reservation-requests.index'),
                            'active' => request()->routeIs('reservation-requests.index'),
                            'permission' => 'reservation_requests.view',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Get payments menu group.
     */
    private function getPaymentsMenuGroup(): array
    {
        return [
            'title' => __('Payments'),
            'icon' => 'bx bx-money',
            'route' => '#',
            'type' => 'group',
            'active' => str_starts_with(request()->route()->getName(), 'payments.') ||
                       str_starts_with(request()->route()->getName(), 'insurances.'),
            'permission' => 'payments.view',
            'children' => [
                [
                    'title' => __('View Payments'),
                    'icon' => 'bx bx-money',
                    'route' => route('payments.index'),
                    'active' => request()->routeIs('payments.index'),
                    'permission' => 'payments.view',
                ],
                [
                    'title' => __('View Insurances'),
                    'icon' => 'bx bx-shield',
                    'route' => route('insurances.index'),
                    'active' => request()->routeIs('insurances.index'),
                    'permission' => 'insurances.view',
                ],
            ],
        ];
    }
}