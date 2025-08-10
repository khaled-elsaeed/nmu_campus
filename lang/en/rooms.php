<?php

return [
        'page_title' => 'Room Management',
        
        'page' => [
            'header' => [
                'title' => 'Rooms',
                'description' => 'Manage room records and view room details.',
            ],
        ],

        'stats' => [
            'total_rooms' => 'Total Rooms',
            'total_double_rooms' => 'Total Double Rooms',
            'total_beds' => 'Total Beds',
            'total_available_beds' => 'Total Available Beds',
            'male_rooms' => 'Male Rooms',
            'female_rooms' => 'Female Rooms',
            'male_double_rooms' => 'Male Double Rooms',
            'female_double_rooms' => 'Female Double Rooms',
            'male_beds' => 'Male Beds',
            'female_beds' => 'Female Beds',
            'available_male_beds' => 'Available Male Beds',
            'available_female_beds' => 'Available Female Beds',
        ],

        'buttons' => [
            'search' => 'Search',
            'clear_filters' => 'Clear Filters',
            'close' => 'Close',
            'save' => 'Save',
        ],

        'search' => [
            'title' => 'Advanced Search',
            'labels' => [
                'building_number' => 'Building',
                'apartment_number' => 'Apartment',
                'gender_restriction' => 'Gender Restriction',
                'type' => 'Room Type',
                'purpose' => 'Purpose',
            ],
            'placeholders' => [
                'all' => 'All',
                'select_gender' => 'Select Gender',
                'select_type' => 'Select Type',
                'select_purpose' => 'Select Purpose',
            ],
            'options' => [
                'male' => 'Male',
                'female' => 'Female',
                'mixed' => 'Mixed',
                'single' => 'Single',
                'double' => 'Double',
                'housing' => 'Housing',
                'staff_housing' => 'Staff Housing',
                'office' => 'Office',
                'storage' => 'Storage',
            ],
        ],

        'table' => [
            'headers' => [
                'number' => 'Number',
                'apartment' => 'Apartment',
                'building' => 'Building',
                'type' => 'Type',
                'purpose' => 'Purpose',
                'gender' => 'Gender',
                'available_capacity' => 'Available Capacity',
                'active' => 'Active',
                'actions' => 'Actions',
            ],
        ],

        'modals' => [
            'edit' => [
                'title' => 'Edit Room',
                'labels' => [
                    'type' => 'Type',
                    'purpose' => 'Purpose',
                    'description' => 'Description',
                ],
            ],
            'view' => [
                'title' => 'Room Details',
                'labels' => [
                    'number' => 'Number',
                    'apartment' => 'Apartment',
                    'building' => 'Building',
                    'type' => 'Type',
                    'gender_restriction' => 'Gender Restriction',
                    'active' => 'Active',
                    'capacity' => 'Capacity',
                    'current_occupancy' => 'Current Occupancy',
                    'available_capacity' => 'Available Capacity',
                    'created_at' => 'Created At',
                    'updated_at' => 'Updated At',
                ],
            ],
        ],

        'placeholders' => [
            'select_building' => 'Select Building',
            'select_apartment' => 'Select Apartment',
            'select_building_first' => 'Select Building First',
            'no_apartments' => 'No Apartments',
            'select_gender' => 'Select Gender',
            'select_type' => 'Select Type',
            'select_purpose' => 'Select Purpose',
        ],

        'status' => [
            'active' => 'Active',
            'inactive' => 'Inactive',
        ],

        'messages' => [
            'load_stats_error' => 'Failed to load room statistics',
            'load_room_error' => 'Failed to load room data',
            'delete_error' => 'Failed to delete room',
            'operation_failed' => 'Operation failed',
            'activated' => 'Room activated successfully',
            'deactivated' => 'Room deactivated successfully',
            'deleted' => 'Room has been deleted',
            'saved' => 'Room has been saved successfully',
        ],

        'confirm' => [
            'activate' => [
                'title' => 'Activate Room?',
                'text' => 'Are you sure you want to activate this room?',
                'button' => 'Yes, activate it!',
            ],
            'deactivate' => [
                'title' => 'Deactivate Room?',
                'text' => 'Are you sure you want to deactivate this room?',
                'button' => 'Yes, deactivate it!',
            ],
            'delete' => [
                'title' => 'Delete Room?',
                'text' => "You won't be able to revert this!",
                'button' => 'Yes, delete it!',
            ],
        ],
];
