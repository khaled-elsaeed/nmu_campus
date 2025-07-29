<?php

namespace App\View\Components\Navigation;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Sidebar extends Component
{
    public array $menuItems;

    /**
     * Create a new component instance.
     */
    public function __construct(array $menuItems = [])
    {
        $this->menuItems = $this->getGroupedMenu();
    }

    public function render(): View|Closure|string
    {
        return view('components.navigation.sidebar');
    }

    private function getGroupedMenu(): array
    {
        return array_merge(
            $this->getDashboardMenu()
        );
    }

    private function getDashboardMenu(): array
    {
        return [
            [
                'title' => __('sidebar.dashboard'),
                'icon' => 'bx bx-home-circle',
                'route' => route('home'),
                'active' => in_array(request()->route()->getName(), ['home', 'admin.home', 'advisor.home']),
            ],
            [
                'title' => __('sidebar.academic'),
                'icon' => 'bx bx-book',
                'route' => '#',
                'type' => 'group',
                'active' => request()->routeIs('academic.*'),
                'children' => [
                    [
                        'title' => __('sidebar.academic_terms'),
                        'icon' => 'bx bx-calendar-event',
                        'route' => route('academic.academic_terms.index'),
                        'active' => str_starts_with(request()->route()->getName(), 'academic.academic_terms.'),
                    ],
                    [
                        'title' => __('sidebar.faculties'),
                        'icon' => 'bx bx-building-house',
                        'route' => route('academic.faculties.index'),
                        'active' => str_starts_with(request()->route()->getName(), 'academic.faculties.'),
                    ],
                    [
                        'title' => __('sidebar.programs'),
                        'icon' => 'bx bx-book-content',
                        'route' => route('academic.programs.index'),
                        'active' => str_starts_with(request()->route()->getName(), 'academic.programs.'),
                    ],
                ]
            ],
            [
                'title' => __('sidebar.housing_management'),
                'icon' => 'bx bx-buildings',
                'route' => '#',
                'type' => 'group',
                'active' => request()->routeIs('housing.*'),
                'children' => [
                    [
                        'title' => __('sidebar.building'),
                        'icon' => 'bx bx-buildings',
                        'route' => route('housing.buildings.index'),
                        'active' => str_starts_with(request()->route()->getName(), 'housing.buildings.'),
                        'permission' => '',
                    ],
                    [
                        'title' => __('sidebar.apartment'),
                        'icon' => 'bx bx-building',
                        'route' => route('housing.apartments.index'),
                        'active' => str_starts_with(request()->route()->getName(), 'housing.apartments.'),
                        'permission' => '',
                    ],
                    [
                        'title' => __('sidebar.room'),
                        'icon' => 'bx bx-door-open',
                        'route' => route('housing.rooms.index'),
                        'active' => str_starts_with(request()->route()->getName(), 'housing.rooms.'),
                        'permission' => '',
                    ]
                ]
            ],
            [
                'title' => __('sidebar.residents'),
                'icon' => 'bx bx-group',
                'route' => '#',
                'type' => 'group',
                'active' => request()->routeIs('resident.*'),
                'children' => [
                    [
                        'title' => __('sidebar.students'),
                        'icon' => 'bx bx-user',
                        'route' => route('resident.students.index'),
                        'active' => str_starts_with(request()->route()->getName(), 'resident.students.'),
                        'permission' => '',
                    ],
                    [
                        'title' => __('sidebar.staff'),
                        'icon' => 'bx bx-id-card',
                        'route' => route('resident.staff.index'),
                        'active' => str_starts_with(request()->route()->getName(), 'resident.staff.'),
                        'permission' => '',
                    ],
                ]
            ],
            [
                'title' => __('sidebar.reservations'),
                'icon' => 'bx bx-calendar',
                'route' => route('reservations.index'),
                'active' => str_starts_with(request()->route()->getName(), 'reservations.') || str_starts_with(request()->route()->getName(), 'reservation-requests.'),
                'children' => [
                    [
                        'title' => __('sidebar.view_reservations'),
                        'icon' => 'bx bx-list-ul',
                        'route' => route('reservations.index'),
                        'active' => request()->routeIs('reservations.index'),
                    ],
                    [
                        'title' => __('sidebar.add_reservation'),
                        'icon' => 'bx bx-plus',
                        'route' => route('reservations.create'),
                        'active' => request()->routeIs('reservations.create'),
                    ],
                    [
                        'title' => __('sidebar.check_in_out'),
                        'icon' => 'bx bx-log-in-circle',
                        'route' => route('reservations.check-in'),
                        'active' => request()->routeIs('reservations.check-in'),
                    ],
                    [
                        'title' => __('sidebar.reservation_requests'),
                        'icon' => 'bx bx-calendar-check',
                        'route' => route('reservation-requests.index'),
                        'active' => str_starts_with(request()->route()->getName(), 'reservation-requests.'),
                    ],
                ],
            ],
            [
                'title' => __('sidebar.payments'),
                'icon' => 'bx bx-money',
                'route' => route('payments.index'),
                'active' => str_starts_with(request()->route()->getName(), 'payments.') || str_starts_with(request()->route()->getName(), 'insurances.'),
                'children' => [
                    [
                        'title' => __('sidebar.view_payments'),
                        'icon' => 'bx bx-money',
                        'route' => route('payments.index'),
                        'active' => request()->routeIs('payments.index'),
                    ],
                    [
                        'title' => __('sidebar.view_insurances'),
                        'icon' => 'bx bx-shield',
                        'route' => route('insurances.index'),
                        'active' => request()->routeIs('insurances.index'),
                    ],
                ],
            ],
        ];
    }
}