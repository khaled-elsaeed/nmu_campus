<?php

return [
    'page' => [
        'title' => 'Building Management | AcadOps',
        'header' => [
            'title' => 'Buildings',
            'description' => 'Manage all building records and add new buildings.',
        ],
    ],

    'stats' => [
        'total_buildings' => 'Total Buildings',
        'male_buildings' => 'Male Buildings',
        'female_buildings' => 'Female Buildings',
    ],

    'buttons' => [
        'add_building' => 'Add Building',
        'search' => 'Search',
        'clear_filters' => 'Clear Filters',
        'close' => 'Close',
        'save' => 'Save',
    ],

    'search' => [
        'title' => 'Advanced Search',
        'labels' => [
            'gender_restriction' => 'Gender Restriction',
            'active_status' => 'Active Status',
        ],
        'placeholders' => [
            'all' => 'All',
            'select_gender' => 'Select Gender',
            'select_status' => 'Select Status',
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
            'total_apartments' => 'Total Apartments',
            'total_rooms' => 'Total Rooms',
            'has_double_room' => 'Has Double Room',
            'gender' => 'Gender',
            'active' => 'Active',
            'current_occupancy' => 'Current Occupancy',
            'actions' => 'Actions',
        ],
        'type' => 'Building',
    ],

    'modal' => [
        'title' => 'Add/Edit Building',
        'add_title' => 'Add Building',
        'edit_title' => 'Edit Building',
        'view_title' => 'Building Details',
        'labels' => [
            'number' => 'Number',
            'total_apartments' => 'Total Apartments',
            'total_rooms' => 'Total Rooms',
            'gender_restriction' => 'Gender Restriction',
            'active' => 'Active',
            'created_at' => 'Created At',
        ],
    ],

    'form' => [
        'labels' => [
            'building_number' => 'Building Number',
            'total_apartments' => 'Total Apartments',
            'rooms_per_apartment' => 'Rooms Per Apartment',
            'has_double_rooms' => 'This building has double rooms',
            'gender_restriction' => 'Gender Restriction',
        ],
        'placeholders' => [
            'select_gender_restriction' => 'Select Gender Restriction',
        ],
        'options' => [
            'male' => 'Male',
            'female' => 'Female',
            'mixed' => 'Mixed',
        ],
    ],

    'status' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'activating' => 'Activating...',
        'deactivating' => 'Deactivating...',
    ],

    'apartment' => [
        'title' => 'Apartment',
        'double_rooms' => 'Double Rooms',
        'room' => 'Room',
    ],

    'messages' => [
        'stats_fetched_successfully' => 'Stats fetched successfully.',
        'created_successfully' => 'Building created successfully.',
        'updated_successfully' => 'Building updated successfully.',
        'deleted_successfully' => 'Building deleted successfully.',
        'activated_successfully' => 'Building activated successfully.',
        'deactivated_successfully' => 'Building deactivated successfully.',
        'details_fetched_successfully' => 'Building details fetched successfully.',
        'fetched_successfully' => 'Buildings fetched successfully.',
        'not_found' => 'Building not found.',
        'cannot_delete_has_apartments' => 'Cannot delete building that has apartments.',
        'cannot_delete_has_residents' => 'Cannot delete building that has residents.',
        'internal_server_error' => 'Internal server error.',
        'load_stats_error' => 'Failed to load building statistics',
        'load_building_error' => 'Failed to load building data',
        'save_error' => 'An error occurred. Please check your input.',
        'delete_error' => 'Failed to delete building.',
        'activate_error' => 'Failed to activate building.',
        'deactivate_error' => 'Failed to deactivate building.',
        'saved' => 'Building has been saved successfully.',
        'deleted' => 'Building has been deleted.',
        'activated' => 'Building activated successfully.',
        'deactivated' => 'Building deactivated successfully.',
    ],

    'confirm' => [
        'activate' => [
            'title' => 'Activate Building?',
            'text' => 'Are you sure you want to activate this building? This will make it available for use.',
            'button' => 'Yes, activate!',
        ],
        'deactivate' => [
            'title' => 'Deactivate Building?',
            'text' => 'Are you sure you want to deactivate this building? This will make it unavailable for new reservations.',
            'button' => 'Yes, deactivate!',
        ],
        'delete' => [
            'title' => 'Are you sure?',
            'text' => "You won't be able to revert this!",
            'button' => 'Yes, delete it!',
        ],
    ],
];
