<?php

return [
    'page' => [
        'title' => 'Faculty Management',
        'header' => [
            'title' => 'Faculties',
            'description' => 'Manage all faculty records and add new faculties using the options on the right.',
        ],
    ],

    'stats' => [
        'total_faculties' => 'Total Faculties',
        'with_programs' => 'Faculties with Programs',
        'without_programs' => 'Faculties without Programs',
    ],

    'buttons' => [
        'add_faculty' => 'Add Faculty',
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
            'faculty_name' => 'Faculty Name',
        ],
        'placeholders' => [
            'faculty_name' => 'Faculty Name',
        ],
    ],

    'table' => [
        'headers' => [
            'name' => 'Name',
            'programs' => 'Programs',
            'students' => 'Students',
            'staff' => 'Staff',
            'action' => 'Action',
        ],
    ],

    'modal' => [
        'title' => 'Add/Edit Faculty',
        'add_title' => 'Add Faculty',
        'edit_title' => 'Edit Faculty',
    ],

    'form' => [
        'labels' => [
            'name_en' => 'Faculty Name (EN)',
            'name_ar' => 'Faculty Name (AR)',
        ],
    ],

    'messages' => [
        'stats_fetched_successfully' => 'Stats fetched successfully.',
        'created_successfully' => 'Faculty created successfully.',
        'updated_successfully' => 'Faculty updated successfully.',
        'deleted_successfully' => 'Faculty deleted successfully.',
        'details_fetched_successfully' => 'Faculty details fetched successfully.',
        'fetched_successfully' => 'Faculties fetched successfully.',
        'save_success' => 'Faculty has been saved successfully.',
        'delete_success' => 'Faculty has been deleted.',
        'not_found' => 'Faculty not found.',
        'cannot_delete_has_students_or_staff' => 'Cannot delete faculty that has students assigned or staff.',
        'cannot_delete_programs_have_students' => 'Cannot delete faculty that has programs with students assigned.',
        'internal_server_error' => 'Internal server error.',
        'stats_error' => 'Failed to load statistics',
        'fetch_error' => 'Failed to fetch faculty data.',
        'delete_error' => 'Failed to delete faculty.',
        'input_error' => 'An error occurred. Please check your input.',
    ],

    'confirm' => [
        'title' => 'Are you sure?',
        'text' => "You won't be able to revert this!",
        'confirm_button' => 'Yes, delete it!',
    ],
];
