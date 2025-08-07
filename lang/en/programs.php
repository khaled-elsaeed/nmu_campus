<?php

return [
    'page' => [
        'title' => 'Program Management',
        'header' => [
            'title' => 'Programs',
            'description' => 'Manage all program records and add new programs using the options on the right.',
        ],
    ],

    'stats' => [
        'total_programs' => 'Total Programs',
        'with_students' => 'Programs with Students',
        'without_students' => 'Programs without Students',
    ],

    'buttons' => [
        'add_program' => 'Add Program',
        'search' => 'Search',
        'clear_filters' => 'Clear Filters',
        'close' => 'Close',
        'save' => 'Save',
        'update' => 'Update',
        'saving' => 'Saving...',
        'updating' => 'Updating...',
    ],

    'search' => [
        'title' => 'Advanced Search',
        'labels' => [
            'program_name' => 'Program Name',
            'faculty' => 'Faculty',
        ],
        'placeholders' => [
            'program_name' => 'Program Name',
            'select_faculty' => 'Select Faculty',
        ],
    ],

    'table' => [
        'headers' => [
            'name' => 'Name',
            'faculty' => 'Faculty',
            'students_count' => 'Students Count',
            'action' => 'Action',
        ],
        'type' => 'Program',
    ],

    'modal' => [
        'title' => 'Add/Edit Program',
        'add_title' => 'Add Program',
        'edit_title' => 'Edit Program',
    ],

    'form' => [
        'labels' => [
            'name_en' => 'Program Name (EN)',
            'name_ar' => 'Program Name (AR)',
            'duration_years' => 'Duration (Years)',
            'faculty' => 'Faculty',
        ],
        'placeholders' => [
            'select_duration' => 'Select Duration',
            'select_faculty' => 'Select Faculty',
        ],
    ],

    'dropdown' => [
        'select_faculty' => 'Select Faculty',
    ],

    'messages' => [
        'stats_fetched_successfully' => 'Stats fetched successfully.',
        'created_successfully' => 'Program created successfully.',
        'updated_successfully' => 'Program updated successfully.',
        'deleted_successfully' => 'Program deleted successfully.',
        'details_fetched_successfully' => 'Program details fetched successfully.',
        'fetched_successfully' => 'Programs fetched successfully.',
        'save_success' => 'Program has been saved successfully.',
        'delete_success' => 'Program has been deleted.',
        'not_found' => 'Program not found.',
        'cannot_delete_has_students' => 'Cannot delete program that has students assigned.',
        'internal_server_error' => 'Internal server error.',
        'stats_error' => 'Failed to load program statistics',
        'fetch_error' => 'Failed to fetch program data.',
        'delete_error' => 'Failed to delete program.',
        'input_error' => 'An error occurred. Please check your input.',
        'dropdown_error' => 'An error occurred.',
    ],

    'confirm' => [
        'title' => 'Are you sure?',
        'text' => "You won't be able to revert this!",
        'confirm_button' => 'Yes, delete it!',
    ],
];
