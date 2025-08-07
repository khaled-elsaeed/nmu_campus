<?php

return [
    'page' => [
        'title' => 'Apartment Management | AcadOps',
        'header' => [
            'title' => 'Apartments',
            'description' => 'Manage all apartment records and view apartment details.',
        ],
    ],

    'stats' => [
        'total_apartments' => 'Total Apartments',
        'male_apartments' => 'Male Apartments',
        'female_apartments' => 'Female Apartments',
    ],

    'buttons' => [
        'search' => 'Search',
        'clear_filters' => 'Clear Filters',
        'close' => 'Close',
    ],

    'search' => [
        'title' => 'Advanced Search',
        'labels' => [
            'building_number' => 'Building Number',
            'apartment_number' => 'Apartment Number',
            'gender_restriction' => 'Gender Restriction',
            'active_status' => 'Active Status',
        ],
        'placeholders' => [
            'all' => 'All',
        ],
        'options' => [
            'male' => 'Male',
            'female' => 'Female',
            'mixed' => 'Mixed',
            'active' => 'Active',
            'inactive' => 'Inactive',
        ],
    ],

    'table' => [
        'headers' => [
            'number' => 'Number',
            'building' => 'Building',
            'total_rooms' => 'Total Rooms',
            'gender' => 'Gender',
            'active' => 'Active',
            'created_at' => 'Created At',
            'actions' => 'Actions',
        ],
        'type' => 'Apartment',
    ],

    'modal' => [
        'view_title' => 'Apartment Details',
        'labels' => [
            'number' => 'Number',
            'building' => 'Building',
            'total_rooms' => 'Total Rooms',
            'gender_restriction' => 'Gender Restriction',
            'active' => 'Active',
            'created_at' => 'Created At',
        ],
    ],

    'placeholders' => [
        'select_building' => 'Select Building',
        'select_apartment' => 'Select Apartment',
        'select_building_first' => 'Select Building First',
        'no_apartments' => 'No Apartments',
    ],

    'status' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ],

    'messages' => [
        'stats_fetched_successfully' => 'Stats fetched successfully.',
        'details_fetched_successfully' => 'Apartment details fetched successfully.',
        'fetched_successfully' => 'Apartments fetched successfully.',
        'activated_successfully' => 'Apartment activated successfully.',
        'deactivated_successfully' => 'Apartment deactivated successfully.',
        'deleted_successfully' => 'Apartment deleted successfully.',
        'not_found' => 'Apartment not found.',
        'cannot_delete_has_residents' => 'Cannot delete apartment that has residents.',
        'internal_server_error' => 'Internal server error.',
        'load_stats_error' => 'Failed to load apartment statistics',
        'load_apartment_error' => 'Failed to load apartment data',
        'delete_error' => 'Failed to delete apartment.',
        'operation_failed' => 'Operation failed',
        'activated' => 'Apartment activated successfully',
        'deactivated' => 'Apartment deactivated successfully',
        'deleted' => 'Apartment has been deleted.',
    ],

    'confirm' => [
        'activate' => [
            'title' => 'Activate Apartment?',
            'text' => 'Are you sure you want to activate this apartment?',
            'button' => 'Yes, activate it!',
        ],
        'deactivate' => [
            'title' => 'Deactivate Apartment?',
            'text' => 'Are you sure you want to deactivate this apartment?',
            'button' => 'Yes, deactivate it!',
        ],
        'delete' => [
            'title' => 'Are you sure?',
            'text' => "You won't be able to revert this!",
            'button' => 'Yes, delete it!',
        ],
    ],
];
