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
                'title' => 'Dashboard',
                'icon' => 'bx bx-home-circle',
                'route' => route('home'),
                'active' => in_array(request()->route()->getName(), ['home', 'admin.home', 'advisor.home']),
            ],
            [
                'title' => 'Academic',
                'icon' => 'bx bx-book',
                'route' => '#',
                'type' => 'group',
                'active' => request()->routeIs('academic.*'),
                'children' => [
                    [
                        'title' => 'Academic Terms',
                        'icon' => 'bx bx-calendar-event',
                        'route' => route('academic.academic_terms.index'),
                        'active' => str_starts_with(request()->route()->getName(), 'academic.academic_terms.'),
                    ],
                    [
                        'title' => 'Faculties',
                        'icon' => 'bx bx-building-house',
                        'route' => route('academic.faculties.index'),
                        'active' => str_starts_with(request()->route()->getName(), 'academic.faculties.'),
                    ],
                    [
                        'title' => 'Programs',
                        'icon' => 'bx bx-book-content',
                        'route' => route('academic.programs.index'),
                        'active' => str_starts_with(request()->route()->getName(), 'academic.programs.'),
                    ],
                ]
            ],
            [
                'title' => 'Housing Management',
                'icon' => 'bx bx-buildings',
                'route' => '#',
                'type' => 'group',
                'active' => request()->routeIs('housing.*'),
                'children' => [
                    [
                        'title' => 'Building',
                        'icon' => 'bx bx-buildings',
                        'route' => route('housing.buildings.index'),
                        'active' => str_starts_with(request()->route()->getName(), 'housing.buildings.'),
                        'permission' => '',
                    ],
                    [
                        'title' => 'Apartment',
                        'icon' => 'bx bx-building',
                        'route' => route('housing.apartments.index'),
                        'active' => str_starts_with(request()->route()->getName(), 'housing.apartments.'),
                        'permission' => '',
                    ],
                    [
                        'title' => 'Room',
                        'icon' => 'bx bx-door-open',
                        'route' => route('housing.rooms.index'),
                        'active' => str_starts_with(request()->route()->getName(), 'housing.rooms.'),
                        'permission' => '',
                    ]
                ]
            ],
            [
                'title' => 'Residents',
                'icon' => 'bx bx-group',
                'route' => '#',
                'type' => 'group',
                'active' => request()->routeIs('resident.*'),
                'children' => [
                    [
                        'title' => 'Students',
                        'icon' => 'bx bx-user',
                        'route' => route('resident.students.index'),
                        'active' => str_starts_with(request()->route()->getName(), 'resident.students.'),
                        'permission' => '',
                    ],
                    [
                        'title' => 'Staff',
                        'icon' => 'bx bx-id-card',
                        'route' => route('resident.staff.index'),
                        'active' => str_starts_with(request()->route()->getName(), 'resident.staff.'),
                        'permission' => '',
                    ],
                ]
            ],
            [
                'title' => 'Reservations',
                'icon' => 'bx bx-calendar',
                'route' => route('reservations.index'),
                'active' => str_starts_with(request()->route()->getName(), 'reservations.') || str_starts_with(request()->route()->getName(), 'reservation-requests.'),
                'children' => [
                    [
                        'title' => 'View Reservations',
                        'icon' => 'bx bx-list-ul',
                        'route' => route('reservations.index'),
                        'active' => request()->routeIs('reservations.index'),
                    ],
                    [
                        'title' => 'Add Reservation',
                        'icon' => 'bx bx-plus',
                        'route' => route('reservations.create'),
                        'active' => request()->routeIs('reservations.create'),
                    ],
                    [
                        'title' => 'Check In / Out',
                        'icon' => 'bx bx-log-in-circle',
                        'route' => route('reservations.check-in'),
                        'active' => request()->routeIs('reservations.check-in'),
                    ],
                    [
                        'title' => 'Reservation Requests',
                        'icon' => 'bx bx-calendar-check',
                        'route' => route('reservation-requests.index'),
                        'active' => str_starts_with(request()->route()->getName(), 'reservation-requests.'),
                    ],
                ],
            ],
            [
                'title' => 'Payments',
                'icon' => 'bx bx-money',
                'route' => route('payments.index'),
                'active' => str_starts_with(request()->route()->getName(), 'payments.') || str_starts_with(request()->route()->getName(), 'insurances.'),
                'children' => [
                    [
                        'title' => 'View Payments',
                        'icon' => 'bx bx-money',
                        'route' => route('payments.index'),
                        'active' => request()->routeIs('payments.index'),
                    ],
                    [
                        'title' => 'View Insurances',
                        'icon' => 'bx bx-shield',
                        'route' => route('insurances.index'),
                        'active' => request()->routeIs('insurances.index'),
                    ],
                ],
            ],
        ];
    }
}