<?php

return [
    'page_title' => 'Academic Terms',

    'stats' => [
        'total_terms' => 'Total Terms',
        'active_terms' => 'Active Terms',
        'inactive_terms' => 'Inactive Terms',
        'current_term' => 'Current Term',
        'no_current_term' => 'No Current Term',
    ],

    'header' => [
        'title' => 'Academic Terms',
        'description' => 'Manage academic terms',
    ],

    'buttons' => [
        'add_term' => 'Add Term',
        'add' => 'Add',
        'search' => 'Search',
        'filter' => 'Filter',
        'clear_filters' => 'Clear Filters',
        'close' => 'Close',
    ],

    'search' => [
        'title' => 'Search Academic Terms',
        'labels' => [
            'season' => 'Season',
            'academic_year' => 'Academic Year',
            'status' => 'Status',
        ],
        'placeholders' => [
            'all_seasons' => 'All Seasons',
            'all_years' => 'All Years',
            'all_status' => 'All Status',
            'select_season' => 'Select Season',
            'select_year' => 'Select Year',
            'select_status' => 'Select Status',
            'select_semester' => 'Select Semester',
        ],
        'options' => [
            'fall' => 'Fall',
            'spring' => 'Spring',
            'summer' => 'Summer',
        ],
    ],

    'table' => [
        'headers' => [
            'season' => 'Season',
            'year' => 'Year',
            'code' => 'Code',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'reservations' => 'Reservations',
            'status' => 'Status',
            'action' => 'Action',
        ],
    ],

    'form' => [
        'labels' => [
            'season' => 'Season',
            'year' => 'Year',
            'semester_number' => 'Semester Number',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'code' => 'Code',
            'active' => 'Active',
            'current' => 'Current',
            'activated_at' => 'Activated At',
            'started_at' => 'Started At',
            'ended_at' => 'Ended At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ],
        'placeholders' => [
            'year' => 'YYYY-YYYY',
        ],
    ],

    'modal' => [
        'add_term_title' => 'Add Academic Term',
        'edit_term_title' => 'Edit Academic Term',
        'view_term_title' => 'View Academic Term',
        'save_button' => 'Save',
        'update_button' => 'Update',
        'yes' => 'Yes',
        'no' => 'No',
    ],

    'messages' => [
        'success' => [
            'created' => 'Academic term created successfully.',
            'updated' => 'Academic term updated successfully.',
            'deleted' => 'Academic term deleted successfully.',
            'activated' => 'Academic term activated successfully.',
            'deactivated' => 'Academic term deactivated successfully.',
            'started' => 'Academic term started successfully.',
            'ended' => 'Academic term ended successfully.',
            'fetched' => 'Academic terms fetched successfully.',
            'reservations_activated' => ':count reservation(s) activated.',
            'no_reservations_activated' => 'No reservations were activated for this term.',
        ],
        'error' => [
            'stats_load_failed' => 'Failed to load term statistics.',
            'load_failed' => 'Failed to load data.',
            'save_failed' => 'Failed to save academic term.',
            'delete_failed' => 'Failed to delete academic term.',
            'term_missing' => 'Term not found.',
            'start_failed' => 'Failed to start academic term.',
            'end_failed' => 'Failed to end academic term.',
            'activate_failed' => 'Failed to activate academic term.',
            'deactivate_failed' => 'Failed to deactivate academic term.',
            'season_required' => 'Season is required.',
            'year_required' => 'Academic year is required.',
            'semester_required' => 'Semester number is required.',
            'start_date_required' => 'Start date is required.',
            'invalid_year_format' => 'Year must be in YYYY-YYYY format.',
            'invalid_date_range' => 'End date must be after start date.',
            'term_not_active' => 'Term must be active before it can be started.',
            'term_already_current' => 'This term is already set as current.',
            'old_year' => 'Cannot start terms from previous academic years.',
            'term_cannot_start' => "Cannot start term ':new_term' while ':current_term' is currently active.",
            'duplicate_term' => 'A term with these details already exists.',
            'term_not_found' => 'Academic term not found.',
            'delete_failed' => 'Cannot delete term while it has reservations.',
            'term_not_current' => 'Term is not currently active.',
            'term_has_reservations' => 'Cannot end term while there are active reservations.',
        ],
    ],

    'confirm' => [
        'delete' => [
            'title' => 'Delete Academic Term',
            'text' => 'Are you sure you want to delete this academic term?',
            'button' => 'Delete',
        ],
        'start' => [
            'title' => 'Start Academic Term',
            'text' => 'Are you sure you want to start this academic term?',
            'button' => 'Start',
        ],
        'end' => [
            'title' => 'End Academic Term',
            'text' => 'Are you sure you want to end this academic term?',
            'button' => 'End',
        ],
        'activate' => [
            'title' => 'Activate Academic Term',
            'text' => 'Are you sure you want to activate this academic term?',
            'button' => 'Activate',
        ],
        'deactivate' => [
            'title' => 'Deactivate Academic Term',
            'text' => 'Are you sure you want to deactivate this academic term?',
            'button' => 'Deactivate',
        ],
    ],

    'term' => 'Term',

    'status' => [
        'active' => 'Active',
        'inactive' => 'Inactive'
    ],
    'actions' => [
        'activate' => 'Activate',
        'deactivate' => 'Deactivate',
        'start_term' => 'Start Term',
        'end_term' => 'End Term'
    ],
];